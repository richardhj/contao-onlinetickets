<?php

namespace OnlineTicket\Model;

use Contao\Model;
use Isotope\Model\Address;


/**
 * @property int    $tstamp       The timestamp activated
 * @property int    $event_id     The related event
 * @property int    $product_id   The related product
 * @property int    $order_id     The related product collection
 * @property int    $item_id      The related product collection item
 * @property int    $agency_id    The related ticket agency
 * @property string $hash         The unique hash
 * @property int    $checkin      The check in timestamp or 0 otherwise
 * @property int    $checkin_user The user who operated checkin
 */
class Ticket extends Model
{

    /**
     * The table name
     *
     * @var string
     */
    protected static $strTable = 'tl_onlinetickets_tickets';


    /**
     * Find tickets by a referenced user
     *
     * @param integer $memberId
     * @param array   $options
     *
     * @return \Model\Collection|null|Ticket
     */
    public static function findByUser($memberId, array $options = [])
    {
        $events = Event::findByUser($memberId);

        if (null === $events) {
            return null;
        }

        return static::findByEvent($events->fetchEach('id'), $options);
    }


    /**
     * Find tickets by event
     *
     * @param array|integer $eventId
     * @param array         $options
     *
     * @return Model\Collection|null|Ticket
     */
    public static function findByEvent($eventId, array $options = [])
    {
        $events = (array) $eventId;

        if (empty($events)) {
            return null;
        }

        $t = static::$strTable;

        $column = ["$t.event_id IN(" . implode(',', array_map('intval', $events)) . ")"];
        $value  = null;

        // Check for options that must not be overwritten but merged
        foreach ($options as $k => $v) {
            switch ($k) {
                case 'column':
                    $column = array_merge($column, $v);
                    unset($options[$k]);

                    break;
                case 'value':
                    $value = array_merge($value, $v);
                    unset ($options[$k]);

                    break;
            }
        }

        return static::findBy(
            $column,
            $value,
            array_merge(
                [
                    'order' => 'tstamp,id,' . \Database::getInstance()->findInSet("$t.event_id", $events)
                ],
                $options
            )
        );
    }


    /**
     * Find online sold tickets
     *
     * @param array|integer $eventId
     * @param array         $options
     *
     * @return Model\Collection|null|Ticket
     */
    public static function findOnlineByEvent($eventId, array $options = [])
    {
        return static::findByEvent(
            $eventId,
            array_merge(
                [
                    'column' => ['order_id<>0']
                ],
                $options
            )
        );
    }


    /**
     * Find tickets by its agency
     *
     * @param integer $agencyId
     * @param array   $options
     *
     * @return \Model\Collection|null|Ticket
     */
    public static function findByAgency($agencyId, array $options = [])
    {
        return static::findBy('agency_id', $agencyId, $options);
    }


    /**
     * Find tickets by order
     *
     * @param integer $orderId
     * @param array   $options
     *
     * @return Model\Collection|null|Ticket
     */
    public static function findByOrder($orderId, array $options = [])
    {
        return static::findBy('order_id', $orderId, $options);
    }


    /**
     * Find ticket by ticket code aka hash
     *
     * @param string $ticketCode
     *
     * @return Ticket
     */
    public static function findByTicketCode($ticketCode)
    {
        // Ticket code is barcode
        if (false !== strpos($ticketCode, '.')) {
            list($intEventId, $intTicketId) = array_map('intval', trimsplit('.', $ticketCode));

            $t = static::$strTable;

            return static::findOneBy(["$t.event_id=?", "$t.id=?"], [$intEventId, $intTicketId]);
        }

        return static::findOneBy('hash', $ticketCode);
    }


    /**
     * Get the assigned address model
     *
     * @return Address
     */
    public function getAddress()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedClassInspection */
        return Address::findOneBy(
            ['pid=?', 'ptable=?'],
            [$this->order_id, 'tl_iso_product_collection']
        );
    }


    /**
     * Get the ticket status
     *
     * @return bool True if activated
     */
    public function isActivated()
    {
        return (0 != $this->checkin);
    }


    /**
     * Check if check in possible
     *
     * @return bool True if check in is possible
     */
    public function checkInPossible()
    {
        // Check in possible if activation timestamp set and check in timestamp not set
        return (0 != $this->tstamp && 0 == $this->checkin);
    }


    /**
     * Return if ticket was sold online by using the an order id as identifier
     *
     * @return bool True if sold online
     */
    public function isOnline()
    {
        return (0 != $this->order_id);
    }
}

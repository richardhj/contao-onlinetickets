<?php


namespace OnlineTicket\Model;

use Contao\Model;


/**
 * @property int    $pid                    The event id
 * @property int    $tstamp                 The timestamp created
 * @property string $name                   The ticket agency name
 * @property int    $count_tickets_recalled The count of tickets recalled
 * @property float  $ticket_price           The ticket price
 * @property int    tickets_generated       The count of generated tickets
 * @property int    tickets_sold            The count of selled tickets
 * @property int    tickets_checkedin       The count of checked in tickets
 */
class Agency extends Model
{

    /**
     * The table name
     *
     * @var string
     */
    protected static $strTable = 'tl_onlinetickets_agencies';


    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        switch ($key) {
            case 'tickets_generated':
                return Ticket::countBy('agency_id', $this->id);
                break;

            case 'tickets_sold':
                return $this->tickets_generated - $this->count_tickets_recalled;
                break;

            case 'tickets_checkedin':
                return Ticket::countBy(['agency_id=?', 'checkin<>0'], array($this->id));
                break;

            // Get agency's ticket price but inherit event's ticket price
            case 'ticket_price':
                /** @noinspection PhpUndefinedMethodInspection */

                return (strlen(parent::__get($key)))
                    ? parent::__get($key)
                    : Event::findByPk($this->pid)->ticket_price;
                break;
        }

        return parent::__get($key);
    }


    /**
     * {@inheritdoc}
     */
    public function __isset($key)
    {
        switch ($key) {
            // Pseudo fields
            case 'tickets_generated':
            case 'tickets_sold':
            case 'tickets_checkedin':
                return true;
        }

        return parent::__isset($key);
    }

    /**
     * Find agency by a referenced user
     *
     * @param integer $memberId
     * @param array   $options
     *
     * @return \Model\Collection|null|static
     */
    public static function findByUser($memberId, array $options = [])
    {
        $events = Event::findByUser($memberId);

        if (null === $events) {
            return null;
        }

        $eventIds = $events->fetchEach('id');

        if (empty($eventIds)) {
            return null;
        }

        $eventIds = implode(',', array_map('intval', $eventIds));

        $t = static::$strTable;

        return static::findBy(
            ["$t.pid IN(" . $eventIds . ")"],
            null,
            array_merge(
                [
                    'order' => \Database::getInstance()->findInSet("$t.pid", $eventIds)
                ],
                $options
            )
        );
    }
}

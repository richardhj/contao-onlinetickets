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
    public function __get($strKey)
    {
        switch ($strKey) {
            case 'tickets_generated':
                return Ticket::countBy('agency_id', $this->id);
                break;

            case 'tickets_sold':
                return $this->tickets_generated - $this->count_tickets_recalled;
                break;

            case 'tickets_checkedin':
                return Ticket::countBy(array('agency_id=?', 'checkin<>0'), array($this->id));
                break;

            // Get agency's ticket price but inherit event's ticket price
            case 'ticket_price':
                /** @noinspection PhpUndefinedMethodInspection */

                return (strlen(parent::__get($strKey)))
                    ? parent::__get($strKey)
                    : Event::findByPk($this->pid)->ticket_price;
                break;
        }

        return parent::__get($strKey);
    }


    /**
     * {@inheritdoc}
     */
    public function __isset($strKey)
    {
        switch ($strKey) {
            // Pseudo fields
            case 'tickets_generated':
            case 'tickets_sold':
            case 'tickets_checkedin':
                return true;
        }

        return parent::__isset($strKey);
    }

    /**
     * Find agency by a referenced user
     *
     * @param integer $intMemberId
     * @param array   $arrOptions
     *
     * @return \Model\Collection|null|static
     */
    public static function findByUser($intMemberId, $arrOptions = array())
    {
        $objEvents = Event::findByUser($intMemberId);

        if (null === $objEvents) {
            return null;
        }

        $arrEvents = $objEvents->fetchEach('id');

        if (empty($arrEvents)) {
            return null;
        }

        $arrEvents = implode(',', array_map('intval', $arrEvents));

        $t = static::$strTable;

        return static::findBy
        (
            array("$t.pid IN(" . $arrEvents . ")"),
            null,
            array_merge
            (
                array
                (
                    'order' => \Database::getInstance()->findInSet("$t.pid", $arrEvents)
                ),
                $arrOptions
            )
        );
    }
}

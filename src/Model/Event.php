<?php

/**
 * This file is part of richardhj/contao-onlinetickets.
 *
 * Copyright (c) 2016-2017 Richard Henkenjohann
 *
 * @package   richardhj/contao-onlinetickets
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-onlinetickets/blob/master/LICENSE
 */


namespace Richardhj\Isotope\OnlineTickets\Model;

use Contao\Database;
use Haste\Model\Model as HasteModel;
use Contao\Model\Collection;

/**
 * Class Event
 *
 * @package Event
 * @property int    $id                    The ID
 * @property int    $tstamp                The timestamp created
 * @property string $name                  The ticket agency name
 * @property int    $date                  The event's date as timestamp
 * @property bool   $preprinted_tickets    True if pre printed tickets exist
 * @property mixed  $ticket_width          The pre printed ticket width
 * @property mixed  $ticket_height         The pre printed ticket height
 * @property mixed  $ticket_elements       The elements which will be generated for the pre printed tickets. Most cases
 *           it is a serialized array
 * @property string $ticket_font_family    The font family that will be used for the pre printed tickets
 * @property mixed  $ticket_font_style     The font style(s) that will be used for the pre printed tickets. It is a
 *           serialized array with each style (B, I, U, O, D) as an array element
 * @property mixed  $ticket_font_size      The font size that will be used for the pre printed tickets. It is a
 *           serialized array from the inputUnit widget
 * @property int    $ticket_fill_number    The count of figures the ticket number (ID) will be filled up with zeros
 * @property mixed  $ticket_barcode_height The height of the barcode. It is a serialized array from the inputUnit
 *           widget
 * @property mixed  $ticket_qrcode_width   The width and heigt of the qr code. It is a serialized array from the
 *           inputUnit widget
 */
class Event extends HasteModel
{

    /**
     * The table name
     *
     * @var string
     */
    protected static $strTable = 'tl_onlinetickets_events';


    /**
     * Get a collection of events by a referenced member
     *
     * @param integer $memberId
     *
     * @return Collection|null
     * @throws \Exception
     */
    public static function findByUser($memberId)
    {
        $events = static::getReferenceValues(static::$strTable, 'users', $memberId);
        if (!is_array($events) || empty($events)) {
            return null;
        }

        $events = implode(',', $events);
        $t      = static::$strTable;

        return static::findBy(
            ["$t.id IN(" . $events . ")"],
            null,
            ['order' => Database::getInstance()->findInSet("$t.id", $events)]
        );
    }
}

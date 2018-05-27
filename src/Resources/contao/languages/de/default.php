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

use Richardhj\IsotopeOnlineTicketsBundle\Api\ApiErrors;


/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][ApiErrors::UNKNOWN_TERMINAL] = 'Unbekanntes Terminal';
$GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][ApiErrors::TICKET_NOT_FOUND] = 'Ticket nicht gefunden';
$GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][ApiErrors::NO_EVENTS]        = 'Keine Veranstaltungen mit aktiven Ticktes gefunden';

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['onlinetickets_listview'] = '%s <span style="color:#b3b3b3;padding-left:3px">[%u Tickets]</span>';
$GLOBALS['TL_LANG']['MSC']['ticketExportPdfTitle']   = 'Tickets Vorverkaufsstelle ID %u für %s';
$GLOBALS['TL_LANG']['MSC']['agencySaveConfirmation'] = 'Es wurden %2$u Tickets erstellt. Für die VVK-Stelle "%1$s" existieren nun %3$u Tickets.';
// Report
$GLOBALS['TL_LANG']['MSC']['ticket_report']     = 'Kartenverkauf';
$GLOBALS['TL_LANG']['MSC']['ticket_price']      = 'Verkaufspreis';
$GLOBALS['TL_LANG']['MSC']['tickets_generated'] = 'Tickets generiert';
$GLOBALS['TL_LANG']['MSC']['tickets_sold']      = 'Tickets verkauft';
$GLOBALS['TL_LANG']['MSC']['tickets_checkedin'] = 'Tickets eingelassen';
$GLOBALS['TL_LANG']['MSC']['tickets_sales']     = 'Einnahmen';
$GLOBALS['TL_LANG']['MSC']['total']             = 'Gesamt';
// Box office
$GLOBALS['TL_LANG']['MSC']['boxoffice']['checkin']             = 'Einlassdatum';
$GLOBALS['TL_LANG']['MSC']['boxoffice']['agency']              = 'Ticketstelle';
$GLOBALS['TL_LANG']['MSC']['boxoffice']['checkin_user']        = 'Benutzer';
$GLOBALS['TL_LANG']['MSC']['boxoffice']['undo']                = 'Checkin rückgängig machen';
$GLOBALS['TL_LANG']['MSC']['boxoffice']['count_sold']          = 'Anzahl Tickets verkauft';
$GLOBALS['TL_LANG']['MSC']['boxoffice']['count_checked_in']    = 'Anzahl Tickets eingelassen';
$GLOBALS['TL_LANG']['MSC']['boxoffice']['count_check_in_left'] = 'Noch Einlass zu gewähren';

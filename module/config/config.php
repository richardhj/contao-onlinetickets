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

use Richardhj\Isotope\OnlineTickets\Helper\Checkout;
use Richardhj\Isotope\OnlineTickets\Helper\DataHandling;
use Richardhj\Isotope\OnlineTickets\Model\Agency;
use Richardhj\Isotope\OnlineTickets\Model\Event;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Richardhj\Isotope\OnlineTickets\Module\BoxOffice;


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['isotope']['onlinetickets_events'] = [
    'tables'     => [Event::getTable(), Agency::getTable()],
    'icon'       => 'system/modules/calendar/assets/icon.gif',
    'report'     => [DataHandling::class, 'exportEventReport'],
    'export'     => [DataHandling::class, 'exportAgencyBarcodes'],
    'export_pdf' => [DataHandling::class, 'exportPreprintedTicketsPdf'],
];


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['application']['boxoffice'] = BoxOffice::class;


/**
 * Models
 */
$GLOBALS['TL_MODELS'][Event::getTable()]  = Event::class;
$GLOBALS['TL_MODELS'][Ticket::getTable()] = Ticket::class;
$GLOBALS['TL_MODELS'][Agency::getTable()] = Agency::class;


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['preCheckout'][]  = [Checkout::class, 'setTicketsInDatabase'];
$GLOBALS['ISO_HOOKS']['postCheckout'][] = [Checkout::class, 'activateTicketsInDatabase'];

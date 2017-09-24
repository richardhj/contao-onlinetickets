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


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['isotope']['onlinetickets_events'] = [
    'tables'     => ['tl_onlinetickets_events', 'tl_onlinetickets_agencies'],
    'icon'       => 'system/modules/calendar/assets/icon.gif',
    'report'     => ['Richardhj\Isotope\OnlineTickets\Helper\DataHandling', 'exportEventReport'],
    'export'     => ['Richardhj\Isotope\OnlineTickets\Helper\DataHandling', 'exportAgencyBarcodes'],
    'export_pdf' => ['Richardhj\Isotope\OnlineTickets\Helper\DataHandling', 'exportPreprintedTicketsPdf']
];


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['application']['boxoffice'] = 'Richardhj\Isotope\OnlineTickets\Module\BoxOffice';


/**
 * Models
 */
$GLOBALS['TL_MODELS'][\Richardhj\Isotope\OnlineTickets\Model\Event::getTable()]  = 'Richardhj\Isotope\OnlineTickets\Model\Event';
$GLOBALS['TL_MODELS'][\Richardhj\Isotope\OnlineTickets\Model\Ticket::getTable()] = 'Richardhj\Isotope\OnlineTickets\Model\Ticket';
$GLOBALS['TL_MODELS'][\Richardhj\Isotope\OnlineTickets\Model\Agency::getTable()] = 'Richardhj\Isotope\OnlineTickets\Model\Agency';


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['preCheckout'][]  = ['Richardhj\Isotope\OnlineTickets\Helper\Checkout', 'setTicketsInDatabase'];
$GLOBALS['ISO_HOOKS']['postCheckout'][] = ['Richardhj\Isotope\OnlineTickets\Helper\Checkout', 'activateTicketsInDatabase'];

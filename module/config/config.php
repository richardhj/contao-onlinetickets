<?php

/**
 * Contao Open Source CMS
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
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

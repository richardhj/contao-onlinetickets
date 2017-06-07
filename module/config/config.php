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
    'report'     => ['OnlineTicket\Helper\DataHandling', 'exportEventReport'],
    'export'     => ['OnlineTicket\Helper\DataHandling', 'exportAgencyBarcodes'],
    'export_pdf' => ['OnlineTicket\Helper\DataHandling', 'exportPreprintedTicketsPdf']
];


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['application']['boxoffice'] = 'OnlineTicket\Module\BoxOffice';


/**
 * Models
 */
$GLOBALS['TL_MODELS'][\OnlineTicket\Model\Event::getTable()]  = 'OnlineTicket\Model\Event';
$GLOBALS['TL_MODELS'][\OnlineTicket\Model\Ticket::getTable()] = 'OnlineTicket\Model\Ticket';
$GLOBALS['TL_MODELS'][\OnlineTicket\Model\Agency::getTable()] = 'OnlineTicket\Model\Agency';


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['preCheckout'][]  = ['OnlineTicket\Helper\Checkout', 'setTicketsInDatabase'];
$GLOBALS['ISO_HOOKS']['postCheckout'][] = ['OnlineTicket\Helper\Checkout', 'activateTicketsInDatabase'];

<?php

/**
 * Contao Open Source CMS
 * Copyright (c) 2005-2015 Leo Feyer
 * @license LGPL-3.0+
 */


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['isotope']['onlinetickets_events'] = array
(
	'tables'     => array('tl_onlinetickets_events', 'tl_onlinetickets_agencies'),
	'icon'       => 'system/modules/calendar/assets/icon.gif',
	'report'     => array('OnlineTicket\Helper\DataHandling', 'exportEventReport'),
	'export'     => array('OnlineTicket\Helper\DataHandling', 'exportAgencyBarcodes'),
	'export_pdf' => array('OnlineTicket\Helper\DataHandling', 'exportPreprintedTicketsPdf')
);


/**
 * Models
 */
$GLOBALS['TL_MODELS'][\OnlineTicket\Model\Event::getTable()] = 'OnlineTicket\Model\Event';
$GLOBALS['TL_MODELS'][\OnlineTicket\Model\Ticket::getTable()] = 'OnlineTicket\Model\Ticket';
$GLOBALS['TL_MODELS'][\OnlineTicket\Model\Agency::getTable()] = 'OnlineTicket\Model\Agency';


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['preCheckout'][] = array('OnlineTicket\Helper\Checkout', 'setTicketsInDatabase');
$GLOBALS['ISO_HOOKS']['postCheckout'][] = array('OnlineTicket\Helper\Checkout', 'activateTicketsInDatabase');

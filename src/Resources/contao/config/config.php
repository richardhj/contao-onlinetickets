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

use Richardhj\IsotopeOnlineTicketsBundle\Helper\Checkout;
use Richardhj\IsotopeOnlineTicketsBundle\Helper\DataHandling;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Agency;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Event;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket;
use Richardhj\IsotopeOnlineTicketsBundle\Module\BoxOffice;


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['isotope']['onlinetickets_events'] = [
    'tables'     => ['tl_onlinetickets_events', 'tl_onlinetickets_agencies'],
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
$GLOBALS['TL_MODELS']['tl_onlinetickets_events']   = Event::class;
$GLOBALS['TL_MODELS']['tl_onlinetickets_tickets']  = Ticket::class;
$GLOBALS['TL_MODELS']['tl_onlinetickets_agencies'] = Agency::class;


/**
 * Hooks
 */
$GLOBALS['ISO_HOOKS']['preCheckout'][]  = [Checkout::class, 'setTicketsInDatabase'];
$GLOBALS['ISO_HOOKS']['postCheckout'][] = [Checkout::class, 'activateTicketsInDatabase'];

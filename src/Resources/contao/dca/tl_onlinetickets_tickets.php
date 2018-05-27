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


use Contao\UserModel;
use Isotope\Model\Product;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionItem;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Agency;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Event;

/**
 * Table tl_onlinetickets_tickets
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_tickets'] = [

    // Config
    'config' => [
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // Fields
    'fields' => [
        'id'           => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['id'],
            'sql'   => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp'       => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
        'event_id'     => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['event_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => 'tl_onlinetickets_events',
            ],
        ],
        'product_id'   => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['product_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => 'tl_iso_product',
            ],
        ],
        'order_id'     => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['order_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => 'tl_iso_product_collection',
            ],
        ],
        'item_id'      => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['item_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => 'tl_iso_product_collection_item',
            ],
        ],
        'agency_id'    => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['agency_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => 'tl_onlinetickets_agencies',
            ],
        ],
        'hash'         => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['hash'],
            'sql'   => "varchar(32) NOT NULL default ''",
        ],
        'checkin'      => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['checkin'],
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
        'checkin_user' => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['checkin_user'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => 'tl_user',
            ],
        ],
    ],
];

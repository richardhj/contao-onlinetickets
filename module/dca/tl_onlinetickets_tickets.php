<?php


/**
 * Table tl_onlinetickets_tickets
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_tickets'] = [

    // Config
    'config' => [
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],

    // Fields
    'fields' => [
        'id'           => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['id'],
            'sql'   => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp'       => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'event_id'     => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['event_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => \Richardhj\Isotope\OnlineTickets\Model\Event::getTable()
            ],
        ],
        'product_id'   => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['product_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => \Isotope\Model\Product::getTable()
            ],
        ],
        'order_id'     => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['order_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => \Isotope\Model\ProductCollection\Order::getTable()
            ],
        ],
        'item_id'      => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['item_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => \Isotope\Model\ProductCollectionItem::getTable()
            ],
        ],
        'agency_id'    => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['agency_id'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => \Richardhj\Isotope\OnlineTickets\Model\Agency::getTable()
            ],
        ],
        'hash'         => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['hash'],
            'sql'   => "varchar(32) NOT NULL default ''"
        ],
        'checkin'      => [
            'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['checkin'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'checkin_user' => [
            'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['checkin_user'],
            'sql'      => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type'  => 'belongsTo',
                'load'  => 'lazy',
                'table' => \Contao\UserModel::getTable()
            ],
        ],
    ]
];

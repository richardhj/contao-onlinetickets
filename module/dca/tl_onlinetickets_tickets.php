<?php


/**
 * Table tl_onlinetickets_tickets
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_tickets'] = array
(

	// Config
	'config' => array
	(
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id'           => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['id'],
			'sql'   => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp'       => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'"
		),
		'event_id'     => array
		(
			'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['event_id'],
			'sql'      => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'lazy', 'table' => \OnlineTicket\Model\Event::getTable()),
		),
		'product_id'   => array
		(
			'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['product_id'],
			'sql'      => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'lazy', 'table' => \Isotope\Model\Product::getTable()),
		),
		'order_id'     => array
		(
			'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['order_id'],
			'sql'      => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'lazy', 'table' => \Isotope\Model\ProductCollection\Order::getTable()),
		),
		'item_id'      => array
		(
			'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['item_id'],
			'sql'      => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'lazy', 'table' => \Isotope\Model\ProductCollectionItem::getTable()),
		),
		'agency_id'    => array
		(
			'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['agency_id'],
			'sql'      => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'lazy', 'table' => \OnlineTicket\Model\Agency::getTable()),
		),
		'hash'         => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['hash'],
			'sql'   => "varchar(32) NOT NULL default ''"
		),
		'checkin'      => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['checkin'],
			'sql'   => "int(10) unsigned NOT NULL default '0'"
		),
		'checkin_user' => array
		(
			'label'    => &$GLOBALS['TL_LANG']['tl_onlinetickets_tickets']['checkin_user'],
			'sql'      => "int(10) unsigned NOT NULL default '0'",
			'relation' => array('type' => 'belongsTo', 'load' => 'lazy', 'table' => \UserModel::getTable()),
		),
	)
);

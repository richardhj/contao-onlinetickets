<?php


/**
 * Table tl_onlinetickets_agencies
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_agencies'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_onlinetickets_events',
		//'ctable'                      => array('tl_onlinetickets_agencies_tickets'),
		//'switchToEdit'                => true,
		//'enableVersioning'            => true,
		'onload_callback' => array
		(
			//array('tl_onlinetickets_agencies', 'checkPermission'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('name'),
			'panelLayout'             => 'filter;search,limit',
			'headerFields'            => array('name', 'attendee_name'),
			'child_record_callback'   => array('OnlineTicket\Helper\Dca', 'listAgency'),
			//'child_record_class'      => 'no_padding'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['edit'],
				'href'                => 'table=tl_onlinetickets_agencies&amp;act=edit',
				'icon'                => 'edit.gif',
				//'button_callback'     => array('tl_onlinetickets_agencies', 'editHeader')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'button_callback'     => array('OnlineTicket\Helper\Dca', 'buttonForAgencyDelete'),
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'export' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['export'],
				'href'                => 'key=export',
				'icon'                => 'theme_export.gif'
			),
			'export_pdf' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['export_pdf'],
				'href'                => 'key=export_pdf',
				'icon'                => 'theme_export.gif',
				'button_callback'     => array('OnlineTicket\Helper\Dca', 'buttonForExportPreprintedTicketsPdf'),
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},name;{config_legend},count_tickets,count_tickets_recalled,ticket_price'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_onlinetickets_events.name',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['name'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array('mandatory'=>true),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'count_tickets' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['count_tickets'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50', 'doNotSaveEmpty'=>true, 'maxlength'=>5),
			'load_callback'           => array(array('OnlineTicket\Helper\Dca', 'loadAgencyTickets')),
			'save_callback'           => array(array('OnlineTicket\Helper\Dca', 'saveAgencyTickets')),
		),
		'count_tickets_recalled' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['count_tickets_recalled'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'natural', 'maxlength'=>5),
			'save_callback'           => array(array('OnlineTicket\Helper\Dca', 'saveAgencyTicketsRecalled')),
			'sql'                     => "int(5) NOT NULL default '0'"
		),
		'ticket_price' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['ticket_price'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		)
	)
);

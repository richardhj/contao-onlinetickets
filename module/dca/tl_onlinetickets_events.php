<?php


/**
 * Table tl_onlinetickets_events
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_events'] = array
(

	// Config
	'config'      => array
	(
		'dataContainer'    => 'Table',
		'ctable'           => array('tl_onlinetickets_agencies'),
		'enableVersioning' => true,
		'sql'              => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list'        => array
	(
		'sorting'           => array
		(
			'mode'        => 1,
			'fields'      => array('name'),
			'flag'        => 1,
			'panelLayout' => 'filter;search,limit'
		),
		'label'             => array
		(
			'fields' => array('name')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'     => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
			'copy'     => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			),
			'delete'   => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['delete'],
				'href'            => 'act=delete',
				'icon'            => 'delete.gif',
				'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback' => array('OnlineTicket\Helper\Dca', 'buttonForEventDelete')
			),
			'show'     => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			),
			'agencies' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['agencies'],
				'href'  => 'table=tl_onlinetickets_agencies',
				'icon'  => 'tablewizard.gif',
			),
			'report' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['report'],
				'href'  => 'key=report',
				'icon'  => 'tablewizard.gif',
			)
		)
	),

	// Palettes
	'palettes'    => array
	(
		'__selector__' => array('preprinted_tickets'),
		'default'      => '{title_legend},name,users,date,ticket_price;{preprinted_legend:hide},preprinted_tickets'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'preprinted_tickets' => 'ticket_width,ticket_height,ticket_elements,ticket_font_family,ticket_font_style,ticket_font_size,ticket_fill_number,ticket_barcode_height,ticket_qrcode_width'
	),

	// Fields
	'fields'      => array
	(
		'id'                    => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp'                => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'name'                  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['name'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 255),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'users'                 => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['users'],
			'exclude'    => true,
			'inputType'  => 'select',
			'foreignKey' => 'tl_user.name',
			'eval'       => array('mandatory' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'w50'),
			'relation'   => array
			(
				'type'  => 'haste-ManyToMany',
				'load'  => 'lazy',
				'table' => 'tl_user'
			)
		),
		'date'                  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['date'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'date', 'mandatory' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "int(10) unsigned NOT NULL default '0'"
		),
		'ticket_price' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_price'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'preprinted_tickets'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['preprinted_tickets'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''"
		),
		'ticket_width'          => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_width'],
			'inputType'        => 'inputUnit',
			'options_callback' => array('\OnlineTicket\Helper\Dca', 'pdfFormatUnitOptions'),
			'eval'             => array('mandatory' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'),
			'sql'              => "varchar(64) NOT NULL default ''"
		),
		'ticket_height'         => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_height'],
			'inputType'        => 'inputUnit',
			'options_callback' => array('\OnlineTicket\Helper\Dca', 'pdfFormatUnitOptions'),
			'eval'             => array('mandatory' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'),
			'sql'              => "varchar(64) NOT NULL default ''"
		),
		'ticket_elements'       => array
		(

			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_elements'],
			'exclude'   => true,
			'inputType' => 'multiColumnWizard',
			'eval'      => array
			(
				'columnFields' => array
				(
					'te_element'    => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_element'],
						'inputType' => 'select',
						'eval'      => array
						(
							'style' => 'width:250px'
						),
						'options'   => array
						(
							'id',
							'name',
							'date',
							'C128',
							'C39',
                            'QRCODE,M'
						),
						'reference' => $GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_element_values']
					),
					'te_position_x' => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_position_x'],
						'inputType' => 'text',
						'eval'      => array('style' => 'width:180px', 'rgxp' => 'digit', 'maxlength' => 5)
					),
					'te_position_y' => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['te_position_y'],
						'inputType' => 'text',
						'eval'      => array('style' => 'width:180px', 'rgxp' => 'digit', 'maxlength' => 5)
					),
				)
			),
			'sql'       => 'text NULL'
		),
		'ticket_font_family'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_family'],
			'inputType' => 'select',
			'options'   => array('helvetica', 'courier'),
			'eval'      => array('includeBlankOption' => true, 'tl_class' => 'w50', 'chosen' => true),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'ticket_font_size'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_size'],
			'inputType' => 'inputUnit',
			'options'   => array('pt'),
			'eval'      => array('rgxp' => 'digit_auto_inherit', 'maxlength' => 5, 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'ticket_font_style'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_style'],
			'inputType' => 'select',
			'options'   => array('B', 'I', 'U', 'D', 'O'),
			'reference' => $GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_font_style_values'],
			'eval'      => array('includeBlankOption' => true, 'multiple' => true, 'tl_class' => 'w50', 'chosen' => true),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'ticket_fill_number'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_fill_number'],
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50'),
			'sql'       => "varchar(5) NOT NULL default ''"
		),
		'ticket_barcode_height' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_barcode_height'],
			'inputType'        => 'inputUnit',
			'options_callback' => array('\OnlineTicket\Helper\Dca', 'pdfFormatUnitOptions'),
			'eval'             => array('rgxp' => 'digit_auto_inherit', 'maxlength' => 5, 'tl_class' => 'w50'),
			'sql'              => "varchar(64) NOT NULL default ''"
		),
        'ticket_qrcode_width' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_onlinetickets_events']['ticket_qrcode_width'],
            'inputType'        => 'inputUnit',
            'options_callback' => array('\OnlineTicket\Helper\Dca', 'pdfFormatUnitOptions'),
            'eval'             => array('rgxp' => 'digit_auto_inherit', 'maxlength' => 5, 'tl_class' => 'w50'),
            'sql'              => "varchar(64) NOT NULL default ''"
        )
	)
);

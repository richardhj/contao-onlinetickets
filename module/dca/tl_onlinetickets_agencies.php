<?php


/**
 * Table tl_onlinetickets_agencies
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_agencies'] = [

    // Config
    'config'       => [
        'dataContainer'   => 'Table',
        'ptable'          => 'tl_onlinetickets_events',
        //'ctable'                      => array('tl_onlinetickets_agencies_tickets'),
        //'switchToEdit'                => true,
        //'enableVersioning'            => true,
        'onload_callback' => [
            //array('tl_onlinetickets_agencies', 'checkPermission'),
        ],
        'sql'             => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],

    // List
    'list'         => [
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => [
                'name'
            ],
            'panelLayout'           => 'filter;search,limit',
            'headerFields'          => [
                'name',
                'attendee_name'
            ],
            'child_record_callback' => ['OnlineTicket\Helper\Dca', 'listAgency'],
            //'child_record_class'      => 'no_padding'
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations'        => [
            'edit'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['edit'],
                'href'  => 'table=tl_onlinetickets_agencies&amp;act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'button_callback' => ['OnlineTicket\Helper\Dca', 'buttonForAgencyDelete'],
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                     . '\'))return false;Backend.getScrollOffset()"'
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ],
            'export'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['export'],
                'href'  => 'key=export',
                'icon'  => 'theme_export.gif'
            ],
            'export_pdf' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['export_pdf'],
                'href'            => 'key=export_pdf',
                'icon'            => 'theme_export.gif',
                'button_callback' => ['OnlineTicket\Helper\Dca', 'buttonForExportPreprintedTicketsPdf'],
            ]
        ]
    ],

    // Palettes
    'metapalettes' => [
        'default' => [
            'title'  => [
                'name'
            ],
            'config' => [
                'count_tickets',
                'count_tickets_recalled',
                'ticket_price',
                'box_office_checkin'
            ],
        ]
    ],

    // Fields
    'fields'       => [
        'id'                     => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid'                    => [
            'foreignKey' => 'tl_onlinetickets_events.name',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => [
                'type' => 'belongsTo',
                'load' => 'lazy'
            ]
        ],
        'tstamp'                 => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'name'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['name'],
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'flag'      => 1,
            'eval'      => [
                'mandatory' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'count_tickets'          => [
            'label'         => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['count_tickets'],
            'inputType'     => 'text',
            'eval'          => [
                'mandatory'      => true,
                'tl_class'       => 'w50',
                'doNotSaveEmpty' => true,
                'maxlength'      => 5
            ],
            'load_callback' => [
                ['OnlineTicket\Helper\Dca', 'loadAgencyTickets']
            ],
            'save_callback' => [
                ['OnlineTicket\Helper\Dca', 'saveAgencyTickets']
            ],
        ],
        'count_tickets_recalled' => [
            'label'         => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['count_tickets_recalled'],
            'inputType'     => 'text',
            'eval'          => [
                'tl_class'  => 'w50',
                'rgxp'      => 'natural',
                'maxlength' => 5
            ],
            'save_callback' => [
                ['OnlineTicket\Helper\Dca', 'saveAgencyTicketsRecalled']
            ],
            'sql'           => "int(5) NOT NULL default '0'"
        ],
        'ticket_price'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['ticket_price'],
            'inputType' => 'text',
            'eval'      => [
                'tl_class' => 'w50',
                'rgxp'     => 'digit'
            ],
            'sql'       => "varchar(32) NOT NULL default ''"
        ],
        'box_office_checkin'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['box_office_checkin'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class' => 'w50 m12'
            ],
            'sql'       => "char(1) NOT NULL default ''"
        ]
    ]
];

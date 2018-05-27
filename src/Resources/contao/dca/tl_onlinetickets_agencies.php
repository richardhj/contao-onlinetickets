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

use Richardhj\IsotopeOnlineTicketsBundle\Dca\Agency as Dca;

/**
 * Table tl_onlinetickets_agencies
 */
$GLOBALS['TL_DCA']['tl_onlinetickets_agencies'] = [

    // Config
    'config'       => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_onlinetickets_events',
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list'         => [
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => [
                'name',
            ],
            'panelLayout'           => 'filter;search,limit',
            'headerFields'          => [
                'name',
                'attendee_name',
            ],
            'child_record_callback' => [Dca::class, 'listAgency'],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['edit'],
                'href'  => "table='tl_onlinetickets_agencies'&amp;act=edit",
                'icon'  => 'edit.gif',
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'button_callback' => [Dca::class, 'buttonForAgencyDelete'],
                'attributes'      => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                     .'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
            'export'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['export'],
                'href'  => 'key=export',
                'icon'  => 'theme_export.gif',
            ],
            'export_pdf' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['export_pdf'],
                'href'            => 'key=export_pdf',
                'icon'            => 'theme_export.gif',
                'button_callback' => [Dca::class, 'buttonForExportPreprintedTicketsPdf'],
            ],
        ],
    ],

    // Palettes
    'metapalettes' => [
        'default' => [
            'title'  => [
                'name',
            ],
            'config' => [
                'count_tickets',
                'count_tickets_recalled',
                'ticket_price',
                'box_office_checkin',
            ],
        ],
    ],

    // Fields
    'fields'       => [
        'id'                     => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'                    => [
            'foreignKey' => "'tl_onlinetickets_events'.name",
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => [
                'type' => 'belongsTo',
                'load' => 'lazy',
            ],
        ],
        'tstamp'                 => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'name'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['name'],
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'flag'      => 1,
            'eval'      => [
                'mandatory' => true,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'count_tickets'          => [
            'label'         => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['count_tickets'],
            'inputType'     => 'text',
            'eval'          => [
                'mandatory'      => true,
                'tl_class'       => 'w50',
                'doNotSaveEmpty' => true,
                'maxlength'      => 5,
            ],
            'load_callback' => [
                [Dca::class, 'loadAgencyTickets'],
            ],
            'save_callback' => [
                [Dca::class, 'saveAgencyTickets'],
            ],
        ],
        'count_tickets_recalled' => [
            'label'         => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['count_tickets_recalled'],
            'inputType'     => 'text',
            'eval'          => [
                'tl_class'  => 'w50',
                'rgxp'      => 'natural',
                'maxlength' => 5,
            ],
            'save_callback' => [
                [Dca::class, 'saveAgencyTicketsRecalled'],
            ],
            'sql'           => "int(5) NOT NULL default '0'",
        ],
        'ticket_price'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['ticket_price'],
            'inputType' => 'text',
            'eval'      => [
                'tl_class' => 'w50',
                'rgxp'     => 'digit',
            ],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'box_office_checkin'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_onlinetickets_agencies']['box_office_checkin'],
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class' => 'w50 m12',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
    ],
];

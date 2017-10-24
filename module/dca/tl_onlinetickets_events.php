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


use Richardhj\Isotope\OnlineTickets\Helper\Dca;
use Richardhj\Isotope\OnlineTickets\Model\Agency;
use Richardhj\Isotope\OnlineTickets\Model\Event;

$table  = Event::getTable();
$ctable = Agency::getTable();


/**
 * Table tl_onlinetickets_events
 */
$GLOBALS['TL_DCA'][$table] = [

    // Config
    'config'      => [
        'dataContainer'    => 'Table',
        'ctable'           => [$ctable],
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list'        => [
        'sorting'           => [
            'mode'        => 1,
            'fields'      => [
                'name',
            ],
            'flag'        => 1,
            'panelLayout' => 'filter;search,limit',
        ],
        'label'             => [
            'fields' => ['name'],
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
            'edit'     => [
                'label' => &$GLOBALS['TL_LANG'][$table]['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'     => [
                'label' => &$GLOBALS['TL_LANG'][$table]['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete'   => [
                'label'           => &$GLOBALS['TL_LANG'][$table]['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'attributes'      => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                     .'\'))return false;Backend.getScrollOffset()"',
                'button_callback' => [Dca::class, 'buttonForEventDelete'],
            ],
            'show'     => [
                'label' => &$GLOBALS['TL_LANG'][$table]['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
            'agencies' => [
                'label' => &$GLOBALS['TL_LANG'][$table]['agencies'],
                'href'  => "table=$ctable",
                'icon'  => 'tablewizard.gif',
            ],
            'report'   => [
                'label' => &$GLOBALS['TL_LANG'][$table]['report'],
                'href'  => 'key=report',
                'icon'  => 'tablewizard.gif',
            ],
        ],
    ],

    // Palettes
    'palettes'    => [
        '__selector__' => ['preprinted_tickets'],
        'default'      => '{title_legend},name,users,date,ticket_price;{preprinted_legend:hide},preprinted_tickets',
    ],

    // Subpalettes
    'subpalettes' => [
        'preprinted_tickets' => 'ticket_width,ticket_height,ticket_elements,ticket_font_family,ticket_font_style,ticket_font_size,ticket_fill_number,ticket_barcode_height,ticket_qrcode_width',
    ],

    // Fields
    'fields'      => [
        'id'                    => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp'                => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'name'                  => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['name'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 255,
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'users'                 => [
            'label'      => &$GLOBALS['TL_LANG'][$table]['users'],
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_user.name',
            'eval'       => [
                'mandatory' => true,
                'multiple'  => true,
                'chosen'    => true,
                'tl_class'  => 'w50',
            ],
            'relation'   => [
                'type'  => 'haste-ManyToMany',
                'load'  => 'lazy',
                'table' => 'tl_user',
            ],
        ],
        'date'                  => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['date'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'rgxp'       => 'date',
                'mandatory'  => true,
                'datepicker' => true,
                'tl_class'   => 'w50 wizard',
            ],
            'sql'       => "int(10) unsigned NOT NULL default '0'",
        ],
        'ticket_price'          => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['ticket_price'],
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50', 'rgxp' => 'digit'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'preprinted_tickets'    => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['preprinted_tickets'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'ticket_width'          => [
            'label'            => &$GLOBALS['TL_LANG'][$table]['ticket_width'],
            'inputType'        => 'inputUnit',
            'options_callback' => [Dca::class, 'pdfFormatUnitOptions'],
            'eval'             => [
                'mandatory' => true,
                'rgxp'      => 'digit_auto_inherit',
                'maxlength' => 20,
                'tl_class'  => 'w50',
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'ticket_height'         => [
            'label'            => &$GLOBALS['TL_LANG'][$table]['ticket_height'],
            'inputType'        => 'inputUnit',
            'options_callback' => [Dca::class, 'pdfFormatUnitOptions'],
            'eval'             => [
                'mandatory' => true,
                'rgxp'      => 'digit_auto_inherit',
                'maxlength' => 20,
                'tl_class'  => 'w50',
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'ticket_elements'       => [

            'label'     => &$GLOBALS['TL_LANG'][$table]['ticket_elements'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'columnFields' => [
                    'te_element'    => [
                        'label'     => &$GLOBALS['TL_LANG'][$table]['te_element'],
                        'inputType' => 'select',
                        'eval'      => [
                            'style' => 'width:250px',
                        ],
                        'options'   => [
                            'id',
                            'name',
                            'date',
                            'C128',
                            'C39',
                            'QRCODE,M',
                        ],
                        'reference' => $GLOBALS['TL_LANG'][$table]['te_element_values'],
                    ],
                    'te_position_x' => [
                        'label'     => &$GLOBALS['TL_LANG'][$table]['te_position_x'],
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:180px', 'rgxp' => 'digit', 'maxlength' => 5],
                    ],
                    'te_position_y' => [
                        'label'     => &$GLOBALS['TL_LANG'][$table]['te_position_y'],
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:180px', 'rgxp' => 'digit', 'maxlength' => 5],
                    ],
                ],
            ],
            'sql'       => 'text NULL',
        ],
        'ticket_font_family'    => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['ticket_font_family'],
            'inputType' => 'select',
            'options'   => ['helvetica', 'courier'],
            'eval'      => [
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
                'chosen'             => true,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'ticket_font_size'      => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['ticket_font_size'],
            'inputType' => 'inputUnit',
            'options'   => ['pt'],
            'eval'      => [
                'rgxp'      => 'digit_auto_inherit',
                'maxlength' => 5,
                'tl_class'  => 'w50',
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'ticket_font_style'     => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['ticket_font_style'],
            'inputType' => 'select',
            'options'   => ['B', 'I', 'U', 'D', 'O'],
            'reference' => $GLOBALS['TL_LANG'][$table]['ticket_font_style_values'],
            'eval'      => [
                'includeBlankOption' => true,
                'multiple'           => true,
                'tl_class'           => 'w50',
                'chosen'             => true,
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'ticket_fill_number'    => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['ticket_fill_number'],
            'inputType' => 'text',
            'eval'      => [
                'rgxp'      => 'digit',
                'maxlength' => 5,
                'tl_class'  => 'w50',
            ],
            'sql'       => "varchar(5) NOT NULL default ''",
        ],
        'ticket_barcode_height' => [
            'label'            => &$GLOBALS['TL_LANG'][$table]['ticket_barcode_height'],
            'inputType'        => 'inputUnit',
            'options_callback' => [Dca::class, 'pdfFormatUnitOptions'],
            'eval'             => [
                'rgxp'      => 'digit_auto_inherit',
                'maxlength' => 5,
                'tl_class'  => 'w50',
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'ticket_qrcode_width'   => [
            'label'            => &$GLOBALS['TL_LANG'][$table]['ticket_qrcode_width'],
            'inputType'        => 'inputUnit',
            'options_callback' => [Dca::class, 'pdfFormatUnitOptions'],
            'eval'             => [
                'rgxp'      => 'digit_auto_inherit',
                'maxlength' => 5,
                'tl_class'  => 'w50',
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
    ],
];

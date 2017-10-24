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


/**
 * Table tl_user
 */
foreach ($GLOBALS['TL_DCA']['tl_user']['palettes'] as $name => $palette) {
    if ('__selector__' === $name) {
        continue;
    }

    $GLOBALS['TL_DCA']['tl_user']['palettes'][$name] = str_replace(
        '{account_legend}',
        '{onlinetickets_legend},tickets_testmode,tickets_defineMode;{account_legend}',
        $GLOBALS['TL_DCA']['tl_user']['palettes'][$name]
    );
}

$GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][]        = 'tickets_defineMode';
$GLOBALS['TL_DCA']['tl_user']['subpalettes']['tickets_defineMode'] = 'tickets_defineModeAgencyId';


$GLOBALS['TL_DCA']['tl_user']['fields']['tickets_testmode'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['tickets_testmode'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50 m12'
    ],
    'sql'       => "char(1) NOT NULL default ''",
];


$GLOBALS['TL_DCA']['tl_user']['fields']['tickets_defineMode'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['tickets_defineMode'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class'       => 'w50 m12 clr',
        'submitOnChange' => true,
    ],
    'sql'       => "char(1) NOT NULL default ''",
];


$GLOBALS['TL_DCA']['tl_user']['fields']['tickets_defineModeAgencyId'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_user']['tickets_defineModeAgencyId'],
    'exclude'          => true,
    'inputType'        => 'select',
    'eval'             => [
        'tl_class' => 'w50',
    ],
    'options_callback' => function () {
        $agencies = Richardhj\Isotope\OnlineTickets\Model\Agency::findAll();
        $return   = [];

        while ($agencies->next()) {
            $return[$agencies->getRelated('pid')->name][$agencies->id] = $agencies->name;
        }

        return $return;
    },
    'sql'              => "int(10) NOT NULL default '0'",
];

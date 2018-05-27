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
 * Table tl_iso_producttype
 */
foreach ($GLOBALS['TL_DCA']['tl_iso_producttype']['palettes'] as $name => $palette) {
    if ('__selector__' === $name) {
        continue;
    }

    $GLOBALS['TL_DCA']['tl_iso_producttype']['palettes'][$name] = str_replace(
        ',fallback',
        ',fallback,isTicket',
        $GLOBALS['TL_DCA']['tl_iso_producttype']['palettes'][$name]
    );
}

$GLOBALS['TL_DCA']['tl_iso_producttype']['fields']['isTicket'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_iso_producttype']['isTicket'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'doNotCopy' => true,
        'tl_class'  => 'w50 m12'
    ],
    'sql'       => "char(1) NOT NULL default ''",
];

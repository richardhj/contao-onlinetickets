<?php


/**
 * Table tl_iso_producttype
 */
foreach ($GLOBALS['TL_DCA']['tl_iso_producttype']['palettes'] as $name => $palette)
{
	if ($name == '__selector__')
	{
		continue;
	}

	$GLOBALS['TL_DCA']['tl_iso_producttype']['palettes'][$name] = str_replace(',fallback', ',fallback,isTicket', $GLOBALS['TL_DCA']['tl_iso_producttype']['palettes'][$name]);
}

$GLOBALS['TL_DCA']['tl_iso_producttype']['fields']['isTicket'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_iso_producttype']['isTicket'],
	'exclude'               => true,
	'inputType'             => 'checkbox',
	'eval'                  => array('doNotCopy'=>true, 'tl_class'=>'w50 m12'),
	'sql'                   => "char(1) NOT NULL default ''",
);

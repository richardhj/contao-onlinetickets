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


namespace Richardhj\Isotope\OnlineTickets\Dca;

use Contao\Backend;
use Contao\Image;
use Richardhj\Isotope\OnlineTickets\Model\Agency;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;


/**
 * Class Dca
 *
 * @package Richardhj\Isotope\OnlineTickets\Dca
 */
class Event extends Backend
{

    /**
     * Disable "delete" button if event's tickets has been sold or agency tickets has been created
     *
     * @category button_callback
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function buttonForEventDelete($row, $href, $label, $title, $icon, $attributes)
    {
        $countTickets = 0;
        $agencies     = Agency::findBy('pid', $row['id']);

        // Count tickets for all event's ticket agencies
        if (null !== $agencies) {
            $tickets = Ticket::findMultipleByIds($agencies->fetchEach('id'));

            $countTickets = $tickets->count();
        }

        if (Ticket::countBy('event_id', $row['id']) || $countTickets) {
            return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
        }

        return '<a href="' . static::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title)
               . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
    }

    /**
     * @return array
     */
    public function pdfFormatUnitOptions()
    {
        // Include TCPDF config
        require_once TL_ROOT.'/system/config/tcpdf.php';

        return [PDF_UNIT];
    }
}

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

namespace Richardhj\IsotopeOnlineTicketsBundle\Api;


final class ApiErrors
{

    /**
     * "Unbekanntes Terminal"
     * Standard-Fehler, auch bspw. bei falschen Zugangsdaten bzw. falschem Token
     */
    public const UNKNOWN_TERMINAL = 1;

    /**
     * "Ticket nicht gefunden"
     */
    public const TICKET_NOT_FOUND = 4;

    /**
     * "Keine Veranstaltungen mit aktiven Ticktes gefunden"
     */
    public const NO_EVENTS = 6;
}

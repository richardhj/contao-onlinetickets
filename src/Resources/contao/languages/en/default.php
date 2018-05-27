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

use Richardhj\IsotopeOnlineTicketsBundle\Api\ApiErrors;


/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][ApiErrors::UNKNOWN_TERMINAL] = 'Unbekanntes Terminal';
$GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][ApiErrors::TICKET_NOT_FOUND] = 'Ticket nicht gefunden';
$GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][ApiErrors::NO_EVENTS]        = 'Keine Veranstaltungen mit aktiven Ticktes gefunden';

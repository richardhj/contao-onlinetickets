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


namespace Richardhj\Isotope\OnlineTickets\Api\Action;

use Richardhj\Isotope\OnlineTickets\Api\AbstractApi;
use Richardhj\Isotope\OnlineTickets\Api\ApiErrors;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class SetTicketAsRegistered
 *
 * @package Richardhj\Isotope\OnlineTickets\Api\Action
 */
class SetTicketAsRegistered extends AbstractApi
{

    /**
     * Set ticket as registered if not yet
     */
    public function run()
    {
        $this->authenticateToken();

        $ticket = Ticket::findByTicketCode($this->getParameter('ticketcode'));

        // Exit if ticket not found
        if (null === $ticket) {
            $this->exitTicketNotFound();
        }

        $success = false;

        if ($this->user->tickets_defineMode) {
            $ticket->agency_id = $this->user->tickets_defineModeAgencyId;
            $ticket->save();
        } else {
            // Check if check in possible and user is not in test mode
            if ($ticket->checkInPossible() && !$this->user->tickets_testmode) {
                $ticket->checkin      = time();
                $ticket->checkin_user = $this->user->id;

                if ($ticket->save()) {
                    $success = true;
                }
            }
        }

        $response = new JsonResponse(
            [
                'Checkin' => [
                    'Result' => $success,
                ],
            ]
        );

        $response->send();
    }


    /**
     * Output json formatted error for not existent ticket
     */
    protected function exitTicketNotFound()
    {
        $this->exitWithError(ApiErrors::TICKET_NOT_FOUND);
    }
}

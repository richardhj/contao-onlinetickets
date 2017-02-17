<?php

namespace OnlineTicket\Api;

use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api;
use OnlineTicket\Model\Ticket;


class SetTicketAsRegistered extends Api
{

    /**
     * Set ticket as registered if not yet
     */
    public function run()
    {
        // Authenticate token
        $this->authenticateToken();

        $ticket = Ticket::findByTicketCode($this->get('ticketcode'));

        // Exit if ticket not found
        if (null === $ticket) {
            $this->exitTicketNotFound();
        }

        $success = false;

        if ($this->objUser->tickets_defineMode) {
            $ticket->agency_id = $this->objUser->tickets_defineModeAgencyId;
            $ticket->save();
        } else {
            // Check if check in possible and user is not in test mode
            if ($ticket->checkInPossible() && !$this->objUser->tickets_testmode) {
                $ticket->checkin      = time();
                $ticket->checkin_user = $this->objUser->id;

                if ($ticket->save()) {
                    $success = true;
                }
            }
        }

        $response = new JsonResponse(
            [
                'Checkin' =>
                    [
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
        $response = new JsonResponse(
            [
                'Errorcode'    => 4,
                'Errormessage' => $GLOBALS['TL_LANG']['ERR']['onlinetickets_ticket_not_found'],
            ]
        );

        $response->send();
    }
}

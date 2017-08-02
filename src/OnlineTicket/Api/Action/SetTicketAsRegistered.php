<?php

namespace OnlineTicket\Api\Action;

use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api\AbstractApi;
use OnlineTicket\Model\Ticket;


/**
 * Class SetTicketAsRegistered
 *
 * @package OnlineTicket\Api\Action
 */
class SetTicketAsRegistered extends AbstractApi
{

    /**
     * Set ticket as registered if not yet
     */
    public function run()
    {
        // Authenticate token
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
        $response = new JsonResponse(
            [
                'Errorcode'    => 4,
                'Errormessage' => $GLOBALS['TL_LANG']['ERR']['onlinetickets_ticket_not_found'],
            ]
        );

        $response->send();
    }
}

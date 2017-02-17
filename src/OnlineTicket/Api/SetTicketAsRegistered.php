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

        $objTicket = Ticket::findByTicketCode($this->get('ticketcode'));

        // Exit if ticket not found
        if (null === $objTicket) {
            $this->exitTicketNotFound();
        }

        $blnSuccess = false;

        if ($this->objUser->tickets_defineMode) {
            $objTicket->agency_id = $this->objUser->tickets_defineModeAgencyId;
            $objTicket->save();
        } else {
            // Check if check in possible and user is not in test mode
            if ($objTicket->checkInPossible() && !$this->objUser->tickets_testmode) {
                $objTicket->checkin = time();
                $objTicket->checkin_user = $this->objUser->id;

                if ($objTicket->save()) {
                    $blnSuccess = true;
                }
            }
        }

        $objResponse = new JsonResponse(
            array
            (
                'Checkin' => array
                (
                    'Result' => $blnSuccess,
                ),
            )
        );

        $objResponse->send();
    }


    /**
     * Output json formatted error for not existent ticket
     */
    protected function exitTicketNotFound()
    {
        $objResponse = new JsonResponse(
            array
            (
                'Errorcode'    => 4,
                'Errormessage' => $GLOBALS['TL_LANG']['ERR']['onlinetickets_ticket_not_found'],
            )
        );

        $objResponse->send();
    }
}

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

use Contao\Date;
use Richardhj\Isotope\OnlineTickets\Api\AbstractApi;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class GetTicketsByToken
 *
 * @package Richardhj\Isotope\OnlineTickets\Api\Action
 */
class GetTicketsByToken extends AbstractApi
{

    /**
     * Output all member assigned tickets
     */
    public function run()
    {
        $this->authenticateToken();

        $tickets = Ticket::findByUser($this->user->id);
        $return  = [];

        if (null !== $tickets) {
            while ($tickets->next()) {
                // Do not include if ticket is older than submitted timestamp
                if ($this->getParameter('timestamp') > 1
                    && ($tickets->tstamp < $this->getParameter('timestamp')
                        || ($tickets->checkin && $tickets->checkin < $this->getParameter('timestamp'))
                    )) {
                    continue;
                }

                $address = $tickets->current()->getAddress();
                $order   = $tickets->getRelated('order_id');
                $status  = (null === $order) ? null : $order->getRelated('order_status');

                $ticket = [
                    'TicketId'          => (int)$tickets->id,
                    'EventId'           => (int)$tickets->event_id,
                    'OrderId'           => (int)$tickets->order_id ?: -(int)$tickets->agency_id,
                    'TicketCode'        => $tickets->hash,
                    'AttendeeName'      => (null !== $address) ? sprintf(
                        '%s %s',
                        $address->firstname,
                        $address->lastname
                    ) : 'Anonym', // @todo lang
                    'TicketStatus'      => $tickets->current()->isActivated(),
                    'Status'            => (null !== $status) ? $status->name : '',
                    'CheckinPossible'   => $tickets->current()->checkInPossible(),
                    'TicketType'        => $tickets->getRelated('product_id')->name,
                    'TicketTags'        => '', // comma separated string
                    'TicketCheckinTime' => $tickets->checkin ? Date::parse('Y-m-d H:i:s O', $tickets->checkin) : '',
                    'TicketInfo'        => $tickets->getRelated('order_id')->notes ?: '',
                    'TicketBarcode'     => $tickets->event_id.'.'.$tickets->id,
                ];

                $return[] = $ticket;
            }
        }

        $response = new JsonResponse(
            [
                'Tickets' => $return,
            ]
        );

        $response->send();
    }
}

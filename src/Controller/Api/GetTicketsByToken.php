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


namespace Richardhj\IsotopeOnlineTicketsBundle\Controller\Api;

use Contao\Date;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket;
use Richardhj\IsotopeOnlineTicketsBundle\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class GetTicketsByToken
 *
 * @package Richardhj\IsotopeOnlineTicketsBundle\Api\Action
 */
class GetTicketsByToken extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \LogicException
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var ApiUser $user */
        $user = $this->getUser();

        $tickets = Ticket::findByUser($user->id);
        $return  = [];

        $timestamp = $request->query->get('timestamp');

        if (null !== $tickets) {
            while ($tickets->next()) {
                // Do not include if ticket is older than submitted timestamp
                if ($timestamp > 1
                    && ($tickets->tstamp < $timestamp || ($tickets->checkin && $tickets->checkin < $timestamp))) {
                    continue;
                }

                /** @var Ticket $ticketModel */
                $ticketModel = $tickets->current();

                $address = $ticketModel->getAddress();
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
                    'TicketStatus'      => $ticketModel->isActivated(),
                    'Status'            => (null !== $status) ? $status->name : '',
                    'CheckinPossible'   => $ticketModel->checkInPossible(),
                    'TicketType'        => $tickets->getRelated('product_id')->name,
                    'TicketTags'        => '', // comma separated string
                    'TicketCheckinTime' => $tickets->checkin ? Date::parse('Y-m-d H:i:s O', $tickets->checkin) : '',
                    'TicketInfo'        => $tickets->getRelated('order_id')->notes ?: '',
                    'TicketBarcode'     => $tickets->event_id.'.'.$tickets->id,
                ];

                $return[] = $ticket;
            }
        }

        return new JsonResponse(
            [
                'Tickets' => $return,
            ]
        );
    }
}

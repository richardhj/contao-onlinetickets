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

use Richardhj\IsotopeOnlineTicketsBundle\Model\Agency;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Order;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket;
use Richardhj\IsotopeOnlineTicketsBundle\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class GetOrdersByToken
 *
 * @package Richardhj\IsotopeOnlineTicketsBundle\Api\Action
 */
class GetOrdersByToken extends Controller
{

    /**
     * Output all member assigned orders
     *
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

        $timestamp = $request->query->get('timestamp');

        $return = [];
        $orders = Order::findByUser($user->id);

        if (null !== $orders) {
            while ($orders->next()) {
                // Do not include if order is older than submitted timestamp
                if ($timestamp > 1 && $orders->tstamp < $timestamp) {
                    continue;
                }

                $address      = $orders->current()->getAddress();
                $isotopeOrder = $orders->getRelated('order_id');
                if (null === $isotopeOrder) {
                    continue;
                }

                $status  = $isotopeOrder->getRelated('order_status');
                $tickets = Ticket::findByOrder($orders->order_id);
                if (null === $tickets) {
                    continue;
                }

                $order = [
                    'OrderId'          => (int)$orders->order_id,
                    'CustomerName'     => sprintf('%s %s', $address->firstname, $address->lastname),
                    'TicketsCount'     => $tickets->count(),
                    'TicketsCheckedIn' => Ticket::countBy(['order_id=?', 'checkin<>0'], [$orders->order_id]),
                    'OrderStatus'      => (null !== $status) ? $status->name : '',
                    // ['approved', 'invited', 'chargeback']
                    'EventId'          => (int)$orders->event_id,
                    'OrderTickets'     => array_map('\intval', array_values($tickets->fetchEach('id'))),
                ];

                $return[] = $order;
            }
        }

        // Fetch agencies too
        $agencies = Agency::findByUser($user->id);

        if (null !== $agencies) {
            while ($agencies->next()) {
                // Do not include if agency is older than submitted timestamp
                if ($timestamp > 1 && $agencies->tstamp < $timestamp) {
                    continue;
                }

                $tickets = Ticket::findByAgency($agencies->id);
                if (null === $tickets) {
                    continue;
                }

                $order = [
                    'OrderId'          => -(int)$agencies->id, # prefix minus to differentiate from online orders
                    'CustomerName'     => $agencies->name,
                    'TicketsCount'     => $tickets->count(),
                    'TicketsCheckedIn' => Ticket::countBy(['agency_id=?', 'checkin<>0'], [$agencies->id]),
                    'OrderStatus'      => '',
                    'EventId'          => (int)$agencies->pid,
                    'OrderTickets'     => array_map('\intval', array_values($tickets->fetchEach('id'))),
                ];

                $return[] = $order;
            }
        }

        return new JsonResponse(
            [
                'Orders' => $return,
            ]
        );
    }
}

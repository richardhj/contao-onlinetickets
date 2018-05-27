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

use Richardhj\IsotopeOnlineTicketsBundle\Model\Event;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket;
use Richardhj\IsotopeOnlineTicketsBundle\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class GetEventsByToken
 *
 * @package Richardhj\IsotopeOnlineTicketsBundle\Api\Action
 */
class GetEventsByToken extends Controller
{

    /**
     * Output all member assigned events
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

        $events = Event::findByUser($user->id);
        $return = [];

        if (null !== $events) {
            while ($events->next()) {
                $event = [
                    'EventId'               => (int)$events->id,
                    'EventName'             => $events->name,
                    'EventDate'             => (int)$events->date,
                    'CountSoldTickets'      => Ticket::countBy('event_id', $events->id),
                    'CountCheckedInTickets' => Ticket::countBy(['event_id=?', 'checkin<>0'], [$events->id]),
                ];

                $return[] = $event;
            }
        }

        return new JsonResponse(
            [
                'Events' => $return,
            ]
        );
    }
}

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
use Richardhj\Isotope\OnlineTickets\Model\Event;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class GetEventsByToken
 *
 * @package Richardhj\Isotope\OnlineTickets\Api\Action
 */
class GetEventsByToken extends AbstractApi
{

    /**
     * Output all member assigned events
     */
    public function run()
    {
        $this->authenticateToken();

        $events = Event::findByUser($this->user->id);
        $return = [];

        while ($events->next()) {
            $event = [
                'EventId'               => (int) $events->id,
                'EventName'             => $events->name,
                'EventDate'             => (int) $events->date,
                'CountSoldTickets'      => Ticket::countBy('event_id', $events->id),
                'CountCheckedInTickets' => Ticket::countBy(['event_id=?', 'checkin<>0'], [$events->id]),
            ];

            $return[] = $event;
        }

        $response = new JsonResponse(
            [
                'Events' => $return
            ]
        );

        $response->send();
    }
}

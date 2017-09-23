<?php

namespace Richardhj\Isotope\OnlineTickets\Api\Action;

use Richardhj\Isotope\OnlineTickets\Api\AbstractApi;
use Richardhj\Isotope\OnlineTickets\Model\Event;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Haste\Http\Response\JsonResponse;


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
        // Authenticate token
        $this->authenticateToken();

        /** @var \Model\Collection|Event $events */
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

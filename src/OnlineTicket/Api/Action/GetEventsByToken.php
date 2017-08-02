<?php

namespace OnlineTicket\Api\Action;

use OnlineTicket\Api\AbstractApi;
use OnlineTicket\Model\Event;
use OnlineTicket\Model\Ticket;
use Haste\Http\Response\JsonResponse;


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

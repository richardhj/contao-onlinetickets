<?php

namespace OnlineTicket\Api\Action;

use Contao\Date;
use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api\AbstractApi;
use OnlineTicket\Model\Ticket;


class GetTicketsByToken extends AbstractApi
{

    /**
     * Output all member assigned tickets
     */
    public function run()
    {
        // Authenticate token
        $this->authenticateToken();

        $tickets = Ticket::findByUser($this->user->id);
        $return = [];

        if (null !== $tickets) {
            while ($tickets->next()) {
                // Do not include if ticket is older than submitted timestamp
                if ($this->get('timestamp') > 1
                    && ($tickets->tstamp < $this->get('timestamp')
                        || ($tickets->checkin
                            && $tickets->checkin < $this->get('timestamp')))
                ) {
                    continue;
                }

                /** @var \Isotope\Model\Address $address */
                /** @noinspection PhpUndefinedMethodInspection */
                $address = $tickets->current()->getAddress();

                /** @var \Isotope\Model\ProductCollection $order */
                $order = $tickets->getRelated('order_id');

                /** @var \Isotope\Model\OrderStatus $status */
                /** @noinspection PhpUndefinedMethodInspection */
                $status = (null === $order) ? null : $order->getRelated('order_status');

                /** @noinspection PhpUndefinedMethodInspection */
                $ticket = [
                    'TicketId'          => (int) $tickets->id,
                    'EventId'           => (int) $tickets->event_id,
                    'OrderId'           => (int) $tickets->order_id ?: -(int) $tickets->agency_id,
                    'TicketCode'        => $tickets->hash,
                    'AttendeeName'      => (null !== $address) ? sprintf(
                        '%s %s',
                        $address->firstname,
                        $address->lastname
                    ) : 'Anonym', // @todo lang
                    'TicketStatus'      => $tickets->current()->isActivated(),
                    'Status'            => (null !== $status) ? $status->getName() : '',
                    'CheckinPossible'   => $tickets->current()->checkInPossible(),
                    'TicketType'        => $tickets->getRelated('product_id')->name,
                    'TicketTags'        => '', // comma separated string
                    'TicketCheckinTime' => $tickets->checkin ? Date::parse('d. F, H:i', $tickets->checkin) : '',
                    'TicketInfo'        => $tickets->getRelated('order_id')->notes ?: '',
                    'TicketBarcode'     => $tickets->event_id . '.' . $tickets->id
                ];

                $return[] = $ticket;
            }
        }

        $response = new JsonResponse(
            [
                'Tickets' => $return
            ]
        );

        $response->send();
    }
}

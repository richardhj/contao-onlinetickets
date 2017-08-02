<?php

namespace OnlineTicket\Api;

use Haste\Http\Response\JsonResponse;
use OnlineTicket\Model\Agency;
use OnlineTicket\Model\Order;
use OnlineTicket\Model\Ticket;


class GetOrdersByToken extends AbstractApi
{

    /**
     * Output all member assigned orders
     */
    public function run()
    {
        // Authenticate token
        $this->authenticateToken();

        /** @type \Contao\Model\Collection|Ticket $orders */
        $orders = Order::findByUser($this->user->id);
        $return = [];

        if (null !== $orders) {
            while ($orders->next()) {
                // Do not include if order is older than submitted timestamp
                if ($this->get('timestamp') > 1 && $orders->tstamp < $this->get('timestamp')) {
                    continue;
                }

                /** @var \Isotope\Model\Address $address */
                $address = $orders->current()->getAddress();

                $isotopeOrder = $orders->getRelated('order_id');
                if (null === $isotopeOrder) {
                    continue;
                }

                /** @var \Isotope\Model\OrderStatus $status */
                $status = $isotopeOrder->getRelated('order_status');

                /** @type \Contao\Model\Collection $tickets */
                $tickets = Ticket::findByOrder($orders->order_id);

                $order = [
                    'OrderId'          => (int) $orders->order_id,
                    'CustomerName'     => sprintf('%s %s', $address->firstname, $address->lastname),
                    'TicketsCount'     => $tickets->count(),
                    'TicketsCheckedIn' => Ticket::countBy(['order_id=?', 'checkin<>0'], [$orders->order_id]),
                    'OrderStatus'      => (null !== $status) ? $status->getName() : '',
                    // ['approved', 'invited', 'chargeback']
                    'EventId'          => (int) $orders->event_id,
                    'OrderTickets'     => array_map('intval', array_values($tickets->fetchEach('id')))
                ];

                $return[] = $order;
            }
        }

        // Fetch agencies too
        /** @var \Model\Collection|Agency $agencies */
        $agencies = Agency::findByUser($this->user->id);

        if (null !== $agencies) {
            while ($agencies->next()) {
                // Do not include if agency is older than submitted timestamp
                if ($this->get('timestamp') > 1 && $agencies->tstamp < $this->get('timestamp')) {
                    continue;
                }

                /** @type \Contao\Model\Collection $objTickets */
                $tickets = Ticket::findByAgency($agencies->id);
                if (null === $tickets) {
                    continue;
                }

                $order = [
                    'OrderId'          => -(int) $agencies->id, # prefix minus to differentiate from online orders
                    'CustomerName'     => $agencies->name,
                    'TicketsCount'     => $tickets->count(),
                    'TicketsCheckedIn' => Ticket::countBy(['agency_id=?', 'checkin<>0'], [$agencies->id]),
                    'OrderStatus'      => '',
                    'EventId'          => (int) $agencies->pid,
                    'OrderTickets'     => array_map('intval', array_values($tickets->fetchEach('id')))
                ];

                $return[] = $order;
            }
        }

        $response = new JsonResponse(
            [
                'Orders' => $return
            ]
        );

        $response->send();
    }
}

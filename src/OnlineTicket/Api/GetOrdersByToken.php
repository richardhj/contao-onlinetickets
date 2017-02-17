<?php

namespace OnlineTicket\Api;

use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api;
use OnlineTicket\Model\Agency;
use OnlineTicket\Model\Order;
use OnlineTicket\Model\Ticket;


class GetOrdersByToken extends Api
{

	/**
	 * Output all member assigned orders
	 */
	public function run()
	{
		// Authenticate token
		$this->authenticateToken();

		/** @type \Contao\Model\Collection|Ticket $objOrders */
		$objOrders = Order::findByUser($this->objUser->id);
		$arrOrders = array();

		if (null !== $objOrders)
		{
			while ($objOrders->next())
			{
				// Do not include if order is older than submitted timestamp
				if ($this->get('timestamp') > 1 && $objOrders->tstamp < $this->get('timestamp'))
				{
					continue;
				}

				/** @var \Isotope\Model\Address $objAddress */
				/** @noinspection PhpUndefinedMethodInspection */
				$objAddress = $objOrders->current()->getAddress();

				/** @var \Isotope\Model\OrderStatus $objStatus */
				$objStatus = $objOrders->getRelated('order_id')->getRelated('order_status');

				/** @type \Contao\Model\Collection $objTickets */
				$objTickets = Ticket::findByOrder($objOrders->order_id);

				$arrOrder = array
				(
					'OrderId'          => (int)$objOrders->order_id,
					'CustomerName'     => sprintf('%s %s', $objAddress->firstname, $objAddress->lastname),
					'TicketsCount'     => $objTickets->count(),
					'TicketsCheckedIn' => Ticket::countBy(array('order_id=?', 'checkin<>0'), array($objOrders->order_id)),
					'OrderStatus'      => (null !== $objStatus) ? $objStatus->getName() : '', // ['approved', 'invited', 'chargeback']
					'EventId'          => (int)$objOrders->event_id,
					'OrderTickets'     => array_map('intval', array_values($objTickets->fetchEach('id')))
				);

				$arrOrders[] = $arrOrder;
			}
		}

		// Fetch agencies too
		/** @var \Model\Collection|Agency $objAgencies */
		$objAgencies = Agency::findByUser($this->objUser->id);

		if (null !== $objAgencies)
		{
			while ($objAgencies->next())
			{
				// Do not include if agency is older than submitted timestamp
				if ($this->get('timestamp') > 1 && $objAgencies->tstamp < $this->get('timestamp'))
				{
					continue;
				}

				/** @type \Contao\Model\Collection $objTickets */
				$objTickets = Ticket::findByAgency($objAgencies->id);

				$arrOrder = array
				(
					'OrderId'          => -(int)$objAgencies->id, # prefix minus to differentiate from online orders
					'CustomerName'     => $objAgencies->name,
					'TicketsCount'     => $objTickets->count(),
					'TicketsCheckedIn' => Ticket::countBy(array('agency_id=?', 'checkin<>0'), array($objAgencies->id)),
					'OrderStatus'      => '',
					'EventId'          => (int)$objAgencies->pid,
					'OrderTickets'     => array_map('intval', array_values($objTickets->fetchEach('id')))
				);

				$arrOrders[] = $arrOrder;
			}
		}

		$objResponse = new JsonResponse(array
		(
			'Orders' => $arrOrders
		));

		$objResponse->send();
	}
}

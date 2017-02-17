<?php

namespace OnlineTicket\Api;

use Contao\Date;
use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api;
use OnlineTicket\Model\Ticket;


class GetTicketsByToken extends Api
{

	/**
	 * Output all member assigned tickets
	 */
	public function run()
	{
		// Authenticate token
		$this->authenticateToken();

		$objTickets = Ticket::findByUser($this->objUser->id);
		$arrTickets = array();

		if (null !== $objTickets)
		{
			while ($objTickets->next())
			{
				// Do not include if ticket is older than submitted timestamp
				if ($this->get('timestamp') > 1 && ($objTickets->tstamp < $this->get('timestamp') || ($objTickets->checkin && $objTickets->checkin < $this->get('timestamp'))))
				{
					continue;
				}

				/** @var \Isotope\Model\Address $objAddress */
				/** @noinspection PhpUndefinedMethodInspection */
				$objAddress = $objTickets->current()->getAddress();

				/** @var \Isotope\Model\ProductCollection $objOrder */
				$objOrder = $objTickets->getRelated('order_id');

				/** @var \Isotope\Model\OrderStatus $objStatus */
                /** @noinspection PhpUndefinedMethodInspection */
                $objStatus = (null === $objOrder) ? null : $objOrder->getRelated('order_status');

				/** @noinspection PhpUndefinedMethodInspection */
				$arrTicket = array
				(
					'TicketId'          => (int)$objTickets->id,
					'EventId'           => (int)$objTickets->event_id,
					'OrderId'           => (int)$objTickets->order_id ?: -(int)$objTickets->agency_id,
					'TicketCode'        => $objTickets->hash,
					'AttendeeName'      => (null !== $objAddress) ? sprintf('%s %s', $objAddress->firstname, $objAddress->lastname) : 'Anonym', // @todo lang
					'TicketStatus'      => $objTickets->current()->isActivated(),
					'Status'            => (null !== $objStatus) ? $objStatus->getName() : '',
					'CheckinPossible'   => $objTickets->current()->checkInPossible(),
					'TicketType'        => $objTickets->getRelated('product_id')->name,
					'TicketTags'        => '', // comma separated string
					'TicketCheckinTime' => $objTickets->checkin ? Date::parse('d. F, H:i', $objTickets->checkin) : '',
					'TicketInfo'        => $objTickets->getRelated('order_id')->notes ?: '',
					'TicketBarcode'     => $objTickets->event_id .'.'. $objTickets->id
				);

				$arrTickets[] = $arrTicket;
			}
		}

		$objResponse = new JsonResponse(array
		(
			'Tickets' => $arrTickets
		));

		$objResponse->send();
	}
}

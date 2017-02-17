<?php

namespace OnlineTicket\Api;

use OnlineTicket\Api;
use OnlineTicket\Model\Event;
use OnlineTicket\Model\Ticket;
use Haste\Http\Response\JsonResponse;


class GetEventsByToken extends Api
{

	/**
	 * Output all member assigned events
	 */
	public function run()
	{
		// Authenticate token
		$this->authenticateToken();

		/** @var \Model\Collection|Event $objEvents */
		$objEvents = Event::findByUser($this->objUser->id);
		$arrEvents = array();

		while ($objEvents->next())
		{
			$arrEvent = array
			(
				'EventId'               => (int)$objEvents->id,
				'EventName'             => $objEvents->name,
				'EventDate'             => (int)$objEvents->date,
				'CountSoldTickets'      => Ticket::countBy('event_id', $objEvents->id),
				'CountCheckedInTickets' => Ticket::countBy(array('event_id=?', 'checkin<>0'), array($objEvents->id)),
			);

			$arrEvents[] = $arrEvent;
		}

		$objResponse = new JsonResponse(array
		(
			'Events' => $arrEvents
		));

		$objResponse->send();
	}
}

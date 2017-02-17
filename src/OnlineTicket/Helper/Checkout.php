<?php

namespace OnlineTicket\Helper;

use Contao\System;
use OnlineTicket\Model\Ticket;
use Isotope\Model\ProductCollection\Order;


class Checkout
{
	/**
	 * Set tickets in database
	 * @category preCheckout hook
	 *
	 * @param Order $objOrder
	 *
	 * @return boolean
	 */
	public function setTicketsInDatabase($objOrder)
	{
		/** @type \Contao\Model $objItem */
		foreach ($objOrder->getItems() as $objItem)
		{
			$objProduct = $objItem->getRelated('product_id');

			// Skip if order's item is not a ticket
			if (!$objProduct->getRelated('type')->isTicket)
			{
				continue;
			}

			// Consider item's quantity
			for ($i=0; $i<$objItem->quantity; $i++)
			{
				$objTicket = new Ticket();

				$objTicket->event_id = $objProduct->event;
				$objTicket->order_id = $objOrder->id;
				$objTicket->item_id = $objItem->id;
				$objTicket->hash = md5(implode('-', array($objOrder->uniqid, $objItem->id, $i)));
				$objTicket->product_id = $objItem->product_id;

				if (!$objTicket->save())
				{
					System::log(sprintf('Could not save ticket for order ID %u and item ID %u in database.', $objOrder->id, $objTicket->id), __METHOD__, TL_ERROR);

					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Activate tickets in database by setting timestamp
	 * @category postCheckout hook
	 *
	 * @param Order $objOrder
	 * @param array $arrTokens
	 */
	public function activateTicketsInDatabase($objOrder, $arrTokens)
	{
		$time = time();
		$objTickets = Ticket::findByOrder($objOrder->id);

		while ($objTickets->next())
		{
			$objTickets->tstamp = $time;
			$objTickets->save();
		}
	}
}

<?php


namespace OnlineTicket\Model;

use Contao\Model\Collection;


class Order
{

	/**
	 * Get a particular attribute
	 * The key id is deceptive and disabled therefore
	 *
	 * @param string $strKey
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get($strKey)
	{
		if ($strKey == 'id')
		{
			throw new \Exception('Key "id" can not be used. Use key "order_id" instead.');
		}

		return static::__get($strKey);
	}


	/**
	 * Get all orders by a referenced member
	 * It's a Ticket model call with orders grouped
	 *
	 * @param integer $intMemberId
	 *
	 * @return Collection|null|Ticket
	 */
	public static function findByUser($intMemberId)
	{
		return Ticket::findByUser($intMemberId, array
		(
			'column' => array('agency_id=0'),
			'group' => 'order_id'
		));
	}
}

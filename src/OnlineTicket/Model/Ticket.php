<?php

namespace OnlineTicket\Model;

use Contao\Model;
use Isotope\Model\Address;


/**
 * @property int    $tstamp       The timestamp activated
 * @property int    $event_id     The related event
 * @property int    $product_id   The related product
 * @property int    $order_id     The related product collection
 * @property int    $item_id      The related product collection item
 * @property int    $agency_id    The related ticket agency
 * @property string $hash         The unique hash
 * @property int    $checkin      The check in timestamp or 0 otherwise
 * @property int    $checkin_user The user who operated checkin
 */
class Ticket extends Model
{

	/**
	 * The table name
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_onlinetickets_tickets';


	/**
	 * Find tickets by a referenced user
	 *
	 * @param integer $intMemberId
	 * @param array   $arrOptions
	 *
	 * @return \Model\Collection|null|Ticket
	 */
	public static function findByUser($intMemberId, $arrOptions=array())
	{
		$objEvents = Event::findByUser($intMemberId);

		if (null === $objEvents)
		{
			return null;
		}

		return static::findByEvent($objEvents->fetchEach('id'), $arrOptions);
	}


	/**
	 * Find tickets by event
	 *
	 * @param array|integer $varEventId
	 * @param array         $arrOptions
	 *
	 * @return Model\Collection|null|Ticket
	 */
	public static function findByEvent($varEventId, $arrOptions=array())
	{
		$arrEvents = (array)$varEventId;

		if (empty($arrEvents))
		{
			return null;
		}

		$strEvents = implode(',', array_map('intval', $arrEvents));

		$t = static::$strTable;

		$arrColumn = array("$t.event_id IN(" . $strEvents . ")");
		$arrValue = null;

		// Check for options that must not be overwritten but merged
		foreach ($arrOptions as $k => $v)
		{
			switch ($k)
			{
				case 'column':
					$arrColumn = array_merge($arrColumn, $v);
					unset($arrOptions[$k]);

					break;
				case 'value':
					$arrValue = array_merge($arrValue, $v);
					unset ($arrOptions[$k]);

					break;
			}
		}

		return static::findBy
		(
			$arrColumn,
			$arrValue,
			array_merge
			(
				array
				(
					'order' => 'tstamp,id,' . \Database::getInstance()->findInSet("$t.event_id", $arrEvents)
				),
				$arrOptions
			)
		);
	}


	/**
	 * Find online sold tickets
	 *
	 * @param array|integer $varEventId
	 * @param array         $arrOptions
	 *
	 * @return Model\Collection|null|Ticket
	 */
	public static function findOnlineByEvent($varEventId, $arrOptions = array())
	{
		return static::findByEvent($varEventId, array_merge
		(
			array
			(
				'column' => array('order_id<>0')
			),
			$arrOptions
		));
	}


	/**
	 * Find tickets by its agency
	 *
	 * @param integer $intAgencyId
	 * @param array   $arrOptions
	 *
	 * @return \Model\Collection|null|Ticket
	 */
	public static function findByAgency($intAgencyId, $arrOptions=array())
	{
		return static::findBy('agency_id', $intAgencyId, $arrOptions);
	}


	/**
	 * Find tickets by order
	 *
	 * @param integer $intOrderId
	 * @param array   $arrOptions
	 *
	 * @return Model\Collection|null|Ticket
	 */
	public static function findByOrder($intOrderId, $arrOptions=array())
	{
		return static::findBy('order_id', $intOrderId, $arrOptions);
	}


	/**
	 * Find ticket by ticket code aka hash
	 *
	 * @param string $strTicketCode
	 *
	 * @return Ticket
	 */
	public static function findByTicketCode($strTicketCode)
	{
		// Ticket code is barcode
		if (strpos($strTicketCode, '.') !== false)
		{
			list($intEventId, $intTicketId) = array_map('intval', trimsplit('.', $strTicketCode));

			$t = static::$strTable;

			return static::findOneBy(array("$t.event_id=?", "$t.id=?"), array($intEventId, $intTicketId));
		}

		return static::findOneBy('hash', $strTicketCode);
	}


	/**
	 * Get the assigned address model
	 *
	 * @return Address
	 */
	public function getAddress()
	{
		/** @noinspection PhpUndefinedMethodInspection */
		/** @noinspection PhpUndefinedClassInspection */
		return Address::findOneBy
		(
			array('pid=?', 'ptable=?'),
			array($this->order_id, 'tl_iso_product_collection')
		);
	}


	/**
	 * Get the ticket status
	 *
	 * @return bool True if activated
	 */
	public function isActivated()
	{
		return ($this->checkin != 0);
	}


	/**
	 * Check if check in possible
	 *
	 * @return bool True if check in is possible
	 */
	public function checkInPossible()
	{
		// Check in possible if activation timestamp set and check in timestamp not set
		return ($this->tstamp != 0 && $this->checkin == 0);
	}


	/**
	 * Return if ticket was sold online by using the an order id as identifier
	 *
	 * @return bool True if sold online
	 */
	public function isOnline()
	{
		return ($this->order_id != 0);
	}
}

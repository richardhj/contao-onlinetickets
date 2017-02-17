<?php


namespace OnlineTicket\Model;

use Haste\Model\Model;
use Contao\Model\Collection;

/**
 * @property int    $id                    The ID
 * @property int    $tstamp                The timestamp created
 * @property string $name                  The ticket agency name
 * @property int    $date                  The event's date as timestamp
 * @property bool   $preprinted_tickets    True if pre printed tickets exist
 * @property mixed  $ticket_width          The pre printed ticket width
 * @property mixed  $ticket_height         The pre printed ticket height
 * @property mixed  $ticket_elements       The elements which will be generated for the pre printed tickets. Most cases
 *           it is a serialized array
 * @property string $ticket_font_family    The font family that will be used for the pre printed tickets
 * @property mixed  $ticket_font_style     The font style(s) that will be used for the pre printed tickets. It is a
 *           serialized array with each style (B, I, U, O, D) as an array element
 * @property mixed  $ticket_font_size      The font size that will be used for the pre printed tickets. It is a
 *           serialized array from the inputUnit widget
 * @property int    $ticket_fill_number    The count of figures the ticket number (ID) will be filled up with zeros
 * @property mixed  $ticket_barcode_height The height of the barcode. It is a serialized array from the inputUnit
 *           widget
 * @property mixed  $ticket_qrcode_width   The width and heigt of the qr code. It is a serialized array from the inputUnit widget
 */
class Event extends Model
{

	/**
	 * The table name
	 * @var string
	 */
	protected static $strTable = 'tl_onlinetickets_events';


	/**
	 * Get a collection of events by a referenced member
	 *
	 * @param integer $intMemberId
	 *
	 * @return Collection|null
	 * @throws \Exception
	 */
	public static function findByUser($intMemberId)
	{
		$arrEvents = static::getReferenceValues(static::$strTable, 'users', $intMemberId);

		if (!is_array($arrEvents) || empty($arrEvents))
		{
			return null;
		}

		$arrEvents = implode(',', $arrEvents);

		$t = static::$strTable;

		return static::findBy
		(
			array("$t.id IN(" . $arrEvents . ")"),
			null,
			array('order' => \Database::getInstance()->findInSet("$t.id", $arrEvents))
		);
	}
}

<?php

namespace OnlineTicket\Helper;

use Contao\Backend;
use Contao\Image;
use Contao\Message;
use Contao\System;
use Contao\DataContainer;
use Database\Result;
use OnlineTicket\Model\Agency;
use OnlineTicket\Model\Event;
use OnlineTicket\Model\Ticket;


class Dca extends Backend
{

	/**
	 * Disable "delete" button if event's tickets has been sold or agency tickets has been created
	 * @category button_callback
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function buttonForEventDelete($row, $href, $label, $title, $icon, $attributes)
	{
		$intAgencyTickets = 0;
		$objAgencies = Agency::findBy('pid', $row['id']);

		// Count tickets for all event's ticket agencies
		if (null !== $objAgencies)
		{
			$objAgencyTickets = Ticket::findMultipleByIds($objAgencies->fetchEach('id'));

			$intAgencyTickets = $objAgencyTickets->count();
		}

		if (Ticket::countBy('event_id', $row['id']) || $intAgencyTickets)
		{
			return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
		}

		return '<a href="' . static::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
	}


	/**
	 * Remove "export pdf" button if event does not have preprinted tickets
	 * @category button_callback
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function buttonForExportPreprintedTicketsPdf($row, $href, $label, $title, $icon, $attributes)
	{
		$objEvent = Event::findByPk($row['pid']);

		if (!$objEvent->preprinted_tickets)
		{
			return '';
		}

		return '<a href="' . static::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
	}


	/**
	 * Disable "delete" button if agency's tickets has been created
	 * @category button_callback
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function buttonForAgencyDelete($row, $href, $label, $title, $icon, $attributes)
	{
		if (Ticket::countBy('agency_id', $row['id']))
		{
			return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
		}

		return '<a href="' . static::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
	}


	public function pdfFormatUnitOptions()
	{
		// Include TCPDF config
		require_once TL_ROOT . '/system/config/tcpdf.php';

		return array(PDF_UNIT);
	}


	/**
	 * List agency in list view
	 * @category child_record_callback
	 *
	 * @param array $row
	 *
	 * @return string
	 */
	public function listAgency($row)
	{
		return sprintf
		(
			$GLOBALS['TL_LANG']['MSC']['onlinetickets_listview'],
			$row['name'],
			Ticket::countBy('agency_id', $row['id'])
		);
	}


	/**
	 * Return the tickets count for this agency as field
	 * @category load_callback
	 *
	 * @param mixed         $varValue
	 * @param DataContainer $dc
	 *
	 * @return int
	 */
	public function loadAgencyTickets($varValue, $dc)
	{
		return Ticket::countBy('agency_id', $dc->activeRecord->id);
	}


	/**
	 * Return the agency's ticket count and create new tickets
	 * @category save_callback
	 *
	 * @param mixed         $varValue
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function saveAgencyTickets($varValue, $dc)
	{
		$varValue = static::processSimpleCalculation($varValue, $dc->field);

		$intOldValue = Ticket::countBy('agency_id', $dc->activeRecord->id);

		if ($varValue < $intOldValue)
		{
			throw new \Exception('Ticket count for this ticket agency must not become smaller.'); //@todo lang
		}
		elseif ($varValue > $intOldValue)
		{
			$intToCreate = $varValue - $intOldValue;

			$objAgency = $dc->activeRecord;

			if ($objAgency instanceof Result)
			{
				$objAgency = new Agency($objAgency);
			}

			$objEvent = $objAgency->getRelated('pid');
			$time = time();

			// Consider item's quantity
			for ($i=0; $i<$intToCreate; $i++)
			{
				$objTicket = new Ticket();

				$objTicket->event_id = $objEvent->id;
				$objTicket->agency_id = $objAgency->id;
				$objTicket->tstamp = $time;
				$objTicket->hash = md5(implode('-', array($objEvent->id, $objAgency->id, uniqid('', true))));

				if (!$objTicket->save())
				{
					System::log(sprintf('Could not save ticket for event ID %u and agency ID %u in database.', $objEvent->id, $objAgency->id), __METHOD__, TL_ERROR);

					throw new \Exception('An error while saving the tickets occurred.');
				}
			}

			Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['MSC']['agencySaveConfirmation'], $objAgency->name, $intToCreate, $intToCreate + $intOldValue));
		}

		return '';
	}


	/**
	 * Return the count of recalled agency tickets
	 * @category save_callback
	 *
	 * @param mixed         $varValue
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function saveAgencyTicketsRecalled($varValue, $dc)
	{
		$varValue = static::processSimpleCalculation($varValue, $dc->field);

		/** @var Agency $objAgency */
		$objAgency = Agency::findByPk($dc->activeRecord->id);

		if ($varValue > $objAgency->tickets_generated - $objAgency->tickets_checkedin)
		{
			throw new \Exception('Count of recalled tickets has to be smaller/equal than generated tickets and checked in tickets.');
		}

		return $varValue;
	}


	/**
	 * Expect a natural number or a simple calculation with a natural number as result
	 *
	 * @param mixed  $varValue The field value
	 * @param string $strField The field name
	 *
	 * @return string The result as natural number
	 * @throws \Exception
	 */
	private static function processSimpleCalculation($varValue, $strField)
	{
		// Except a digit and an optional simple calculation (e.g. 10+10)
		if (!preg_match('/^(\d+)([\+\-\*\/]\d+)?$/', $varValue))
		{
			throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['natural'], $strField));
		}

		eval("\$varValue = $varValue;");

		// Use the default validator for natural numbers
		if (!\Validator::isNatural($varValue))
		{
			throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['natural'], $strField));
		}

		return $varValue;
	}
}

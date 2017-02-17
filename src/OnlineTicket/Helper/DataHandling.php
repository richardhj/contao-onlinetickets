<?php

namespace OnlineTicket\Helper;

use Contao\DataContainer;
use Contao\File;
use Haste\Haste;
use Haste\IO\Reader\ModelCollectionReader;
use Haste\IO\Writer\CsvFileWriter;
use Haste\IO\Writer\ExcelFileWriter;
use Haste\Util\Format;
use OnlineTicket\Model\Agency;
use OnlineTicket\Model\Event;
use OnlineTicket\Model\Ticket;


class DataHandling
{

	/**
	 * @param DataContainer $dc
	 */
	public function exportAgencyBarcodes($dc)
	{
		/** @type \Model\Collection $objTickets */
		$objTickets = Ticket::findByAgency($dc->id);

		$objReader = new ModelCollectionReader($objTickets);
		$objWriter = new CsvFileWriter();
		$objWriter->enableHeaderFields();

		$objReader->setHeaderFields(array
		(
			'Event',
			'TicketAgency',
			'Ticket',
			'Barcode'
		));

		$objWriter->setRowCallback(function ($arrRow)
		{
			return array
			(
				$arrRow['event_id'],
				$arrRow['agency_id'],
				$arrRow['id'],
				sprintf('%s.%s', $arrRow['event_id'], $arrRow['id'])
			);
		});

		$objWriter->writeFrom($objReader);

		$objFile = new File($objWriter->getFilename());
		$objFile->sendToBrowser();
	}


	/**
	 * @param DataContainer $dc
	 */
	public function exportPreprintedTicketsPdf($dc)
	{
		$objTickets = Ticket::findByAgency($dc->id);
		/** @var Event $objEvent */
		$objEvent = $objTickets->getRelated('event_id');
		$objTickets->reset(); # getRelated() increases index

		// TCPDF configuration
		$l = array();
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_charset'] = $GLOBALS['TL_CONFIG']['characterSet'];
		$l['a_meta_language'] = substr($GLOBALS['TL_LANGUAGE'], 0, 2);
		$l['w_page'] = 'page';

		// Include TCPDF config
		require_once TL_ROOT . '/system/config/tcpdf.php';

		$arrPageFormat = array_map('static::getInputUnitValue', array($objEvent->ticket_width, $objEvent->ticket_height));
		$varPageFormat = (count(array_filter($arrPageFormat)) == 2) ? $arrPageFormat : PDF_PAGE_FORMAT;

		// Create new PDF document
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $varPageFormat, true);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle(sprintf($GLOBALS['TL_LANG']['MSC']['ticketExportPdfTitle'], $dc->id, $objEvent->name));

		// Prevent font subsetting (huge speed improvement)
		$pdf->setFontSubsetting(false);

		// Remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set some language-dependent strings
		$pdf->setLanguageArray($l);

		// Set font
		$pdf->SetFont
		(
			$objEvent->ticket_font_family ?: PDF_FONT_NAME_MAIN,
			implode('', deserialize($objEvent->ticket_font_style)) ?: '',
			static::getInputUnitValue($objEvent->ticket_font_size) ?: PDF_FONT_SIZE_MAIN);

		/*
		 * Write every ticket to pdf
		 */
		$arrElements = deserialize($objEvent->ticket_elements);

		while ($objTickets->next())
		{
			$pdf->AddPage();

			foreach ($arrElements as $element)
			{
				switch ($element['te_element'])
				{
					// Collection: Ticket
					case 'id':
						$pdf->MultiCell(0, 0, str_pad($objTickets->$element['te_element'], $objEvent->ticket_fill_number, '0', STR_PAD_LEFT), 0, 'J', false, 1, $element['te_position_x'], $element['te_position_y']);
						break;

					// Collection: Event
					case 'name':
					case 'date':
						$pdf->MultiCell(0, 0, $objEvent->$element['te_element'], 0, 'J', false, 1, $element['te_position_x'], $element['te_position_y']);
						break;

					// Barcodes
					case 'C128':
					case 'C39':
						$pdf->write1DBarcode(sprintf
						(
							'%u.%u',
							$objTickets->event_id,
							$objTickets->id
						), $element['te_element'], $element['te_position_x'], $element['te_position_y'], '', static::getInputUnitValue($objEvent->ticket_barcode_height));
						break;

                    // QR code
                    case 'QRCODE,M':
                        $pdf->write2DBarcode($objTickets->hash, $element['te_element'], $element['te_position_x'], $element['te_position_y'], static::getInputUnitValue($objEvent->ticket_qrcode_width), static::getInputUnitValue($objEvent->ticket_qrcode_width));
                        break;
				}
			}
		}

		$pdf->lastPage();

		$pdf->Output(
			sprintf('ticket-agency_%u.pdf', $dc->id),
			'D'
		);
	}


	/**
	 * @param DataContainer $dc
	 */
	public function exportEventReport($dc)
	{
		$objWriter = new ExcelReport();

		/** @var Event $objEvent */
		/** @noinspection PhpUndefinedMethodInspection */
		$objEvent = Event::findByPk($dc->id);

		/** @var Agency|\Model\Collection $objAgencies */
		$objAgencies = Agency::findBy('pid', $objEvent->id);

		/*
		 * Write head
		 */
		$objWriter->writeRow(array
		(
			static::getLabelFromLanguageFile('ticket_report')
		));

		$objWriter->setH1Style();

		// Write event data
		foreach (array('name', 'date', 'id') as $field)
		{
			/** @noinspection PhpUndefinedMethodInspection */
			$objWriter->writeRow(array
			(
				static::getLabelFromLanguageFile($field, Event::getTable()),
				($field == 'date') ? Format::date($objEvent->$field) : $objEvent->$field
			));

			$objWriter->setStyle()->getFont()->setBold(true);
			$objWriter->setStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		}

		// Set event id aligned left
		$objWriter->setStyle(1, 1, 0, 1)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$objWriter->writeRow(array());

		/*
		 * Total sales listing
		 */
		$arrSalesColumns = array
		(
			'name',
			'ticket_price',
			'tickets_generated',
			'tickets_sold',
			'tickets_checkedin',
			'tickets_sales'
		);

		// Write table head
		$objWriter->writeRow(array_map
		(
			'static::getLabelFromLanguageFile',
			$arrSalesColumns
		));

		// Set h3 style for table head
		$objWriter->setH3Style(count($arrSalesColumns));

		$intRows = 0;

		// Write row for online sales and each agency sales
		do
		{
			$row = array();

			foreach ($arrSalesColumns as $field)
			{
				if ($field == 'tickets_sales')
				{
					// Formula for multiplying ticket_price by tickets_sold
					$row[] = sprintf
					(
						'=%2$s%1$u*%3$s%1$u',
						$objWriter->getCurrentRow() + 1,
						\PHPExcel_Cell::stringFromColumnIndex(array_search('ticket_price', $arrSalesColumns)),
						\PHPExcel_Cell::stringFromColumnIndex(array_search('tickets_sold', $arrSalesColumns))
					);

					continue;
				}

				// Write online sales row initially
				if (!$intRows)
				{
					// Write online sales column
					$row[] = $this->getPropertyForOnlineSales($objEvent->id, $field);

					continue;
				}

				// Write agency sales column
				$row[] = $objAgencies->$field;
			}

			$objWriter->writeRow($row);
			$intRows++;

		} while ($objAgencies !== null && $objAgencies->next());

		$row = array();

		// Sum line
		foreach ($arrSalesColumns as $i => $field)
		{
			switch ($field)
			{
				case 'name':
					$row[] = static::getLabelFromLanguageFile('total');
					break;

				default:
					$row[] = sprintf('=SUM(%1$s%2$u:%1$s%3$u)', \PHPExcel_Cell::stringFromColumnIndex($i), $objWriter->getCurrentRow() + 1 - $intRows, $objWriter->getCurrentRow());
			}
		}

		$objWriter->writeRow($row);
		$objWriter->setStyle(1, count($arrSalesColumns))->getFont()->setBold(true);

		// Merge cell with row title and align right
		$objWriter->getPHPExcel()->getActiveSheet()->mergeCellsByColumnAndRow(0, $objWriter->getCurrentRow(), 1, $objWriter->getCurrentRow());
		$objWriter->setStyle(1, 1)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		// Set the currency format for corresponding columns
		foreach (array('ticket_price', 'tickets_sales') as $field)
		{
			$objWriter->setStyle($intRows + 1, 1, $objWriter->getCurrentRow() - $intRows, array_search($field, $arrSalesColumns))->getNumberFormat()->setFormatCode(str_replace('EUR', '€', \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE));
		}

		$objWriter->writeRow(array());

		/*
		 * Tickets single listing
		 */
		$objTickets = Ticket::findByEvent($dc->id);

		$arrTicketsColumns = array
		(
			'id',
			'tstamp',
			'agency_id',
			'checkin',
			'checkin_user'
		);

		// Write table head
		$objWriter->writeRow(array_map
		(
			'static::getLabelFromLanguageFile',
			$arrTicketsColumns,
			array_fill(0, count($arrTicketsColumns), Ticket::getTable())
		));

		// Set id head right aligned
		$objWriter->setStyle(1,1, 0, array_search('id', $arrTicketsColumns))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		// Set h3 style for table head
		$objWriter->setH3Style(count($arrTicketsColumns));

		while ($objTickets->next())
		{
			$row = array();

			foreach ($arrTicketsColumns as $field)
			{
				$value = $objTickets->$field;

				/** @noinspection PhpUndefinedMethodInspection */
				if ($objTickets->current()->isOnline() && $field == 'agency_id')
				{
					$value = 'Online'; //@todo lang
				}
				// Might be timestamp
				elseif (preg_match('/\d{10}/', $value))
				{
					$value = Format::datim($value);
				}
				elseif (!$value)
				{
					$value = '';
				}
				else
				{
					// Do we have a related data set?
					try
					{
						/** @type \Model $objRelated */
						$objRelated = $objTickets->getRelated($field);
						$value = $objRelated->name;

					} catch (\Exception $e) {}
				}

				$row[] = $value;
			}

			$objWriter->writeRow($row);
		}

		/*
		 * Entrances graph
		 */
		// Create new sheet for auxiliary calculations
		$intAuxiliaryLine = $objWriter->getCurrentRow() - $objTickets->count();
		$objWriter->switchSheet(1);

		for ($i = 0; $i < $objTickets->count(); $i++)
		{
			$startRow = $objWriter->getCurrentRow() + 1 - $i;
			$currentRow = $objWriter->getCurrentRow() + 1;
			$endRow = $objWriter->getCurrentRow() - $i + $objTickets->count();
			$firstColumn = \PHPExcel_Cell::stringFromColumnIndex(0);
			$secondColumn = \PHPExcel_Cell::stringFromColumnIndex(1);
			$thirdColumn = \PHPExcel_Cell::stringFromColumnIndex(2);
			$fourthColumn = \PHPExcel_Cell::stringFromColumnIndex(3);
			$fifthColumn = \PHPExcel_Cell::stringFromColumnIndex(4);

			$objWriter->writeRow(array
			(
				sprintf
				(
					'=HOUR(%s!%s%u)/24',
					'Worksheet',
					\PHPExcel_Cell::stringFromColumnIndex(array_search('checkin', $arrTicketsColumns)),
					++$intAuxiliaryLine
				),
				sprintf
				(
					'=COUNTIF($%1$s$%3$u:%1$s%2$u,%1$s%2$u)', // Returns 1 if checkin time occurs initially
					$firstColumn,
					$currentRow,
					$startRow
				),
				sprintf
				(
					'=IFERROR('
					. 'INDEX(' // Return cell value by its index
					. '%1$s:%1$s,AGGREGAT(15,6,ROW($%1$s$%3$u:$%1$s$%4$u)/($%2$s$%3$u:$%2$s$%4$u=1),ROW())' // Returns row index where B column value is 1
					. ')'
					. ',"")',
					$firstColumn,
					$secondColumn,
					$startRow,
					$endRow
				),
				sprintf
				(
					'=IFERROR('
					. 'AGGREGAT(15,6,$%1$s$%2$u:$%1$s$%3$u,ROW())' // Sort values ascending
					. ',"")',
					$thirdColumn,
					$startRow,
					$endRow
				),
				sprintf
				(
					'=COUNTIF($%1$s$%2$u:$%1$s$%5$u,%4$s%3$s)',
					$firstColumn,
					$startRow,
					$currentRow,
					$fourthColumn,
					$endRow
				),
				sprintf
				(
					'=SUM($%1$s$%2$u:%1$s%3$u)',
					$fifthColumn,
					$startRow,
					$currentRow
				)
			));
		}

		// Set time format for specific columns
		$objWriter->setStyle($objTickets->count(), 1, $objWriter->getCurrentRow() + 1 - $objTickets->count() /* Start row */)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);
		$objWriter->setStyle($objTickets->count(), 1, $objWriter->getCurrentRow() + 1 - $objTickets->count() /* Start row */, 2 /* Third column */)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);
		$objWriter->setStyle($objTickets->count(), 1, $objWriter->getCurrentRow() + 1 - $objTickets->count() /* Start row */, 3 /* Fourth column */)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);

		// Jump back to main sheet
		$objWriter->switchSheet(0);

		/*
		 * Finish and output file
		 */
		$objWriter->finish();

		$objFile = new File($objWriter->getFilename());
		$objFile->sendToBrowser();
	}


	/**
	 * Return a sales property vor online sales
	 *
	 * @param integer $intEventId
	 * @param string  $strField
	 *
	 * @return mixed
	 */
	public static function getPropertyForOnlineSales($intEventId, $strField)
	{
		switch ($strField)
		{
			case 'name':
				return 'Online';
				break;

			case 'ticket_price':
				# Different ticket prices are possible so we calculate the average of tickets sold
				$objTickets = Ticket::findOnlineByEvent($intEventId); // @todo returns wrong collection (all tickets)

				if (null === $objTickets)
				{
					return '';
				}

				// Sum all online tickets prices
				$total = array_sum
				(
					array_map
					(
						function ($id, $key) use ($objTickets)
						{
							// Filter disabled tickets
							if (!$objTickets->offsetGet($key)->tstamp)
							{
								return 0;
							}

							return $objTickets->offsetGet($key)->getRelated('item_id')->price;
						},
						$objTickets->fetchEach('id'),
						array_keys($objTickets->fetchEach('id'))
					)
				);

				// Divide online tickets total by count of sold tickets
				return $total / static::getPropertyForOnlineSales($intEventId, 'tickets_sold'); # can not use ->count() because of disabled tickets
				break;

			case 'tickets_generated':
				return Ticket::countBy
				(
					array('order_id<>0', 'event_id=?'),
					array($intEventId)
				);
				break;

			case 'tickets_sold':
				return Ticket::countBy
				(
					array('order_id<>0', 'event_id=?', 'tstamp<>0'),
					array($intEventId)
				);
				break;

			case 'tickets_checkedin':
				return Ticket::countBy
				(
					array('order_id<>0', 'event_id=?', 'checkin<>0'),
					array($intEventId)
				);
				break;

			default:
				return '';
		}
	}


	/**
	 * Extract the value of a widget with type "inputUnit"
	 *
	 * @param mixed $varInput The serialized string or already unserialized array
	 *
	 * @return string
	 */
	protected static function getInputUnitValue($varInput)
	{
		$varInput = deserialize($varInput);

		if (!is_array($varInput))
		{
			return '';
		}

		return $varInput['value'];
	}


	/**
	 * Return the language label
	 *
	 * @param string $strName  The field's name or language key
	 * @param string $strTable The table name the field belongs to or empty string to use the MSC language array
	 *
	 * @return string
	 */
	protected static function getLabelFromLanguageFile($strName, $strTable = '')
	{
		if ($strTable)
		{
			return Format::dcaLabel($strTable, $strName);
		}

		return Format::dcaLabelFromArray(array('name' => $strName));
	}
}

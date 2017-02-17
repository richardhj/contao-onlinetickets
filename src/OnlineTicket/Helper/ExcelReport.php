<?php
/**
 * FERIENPASS extension for Contao Open Source CMS built on the MetaModels extension
 * Copyright (c) 2015-2015 Richard Henkenjohann
 * @package Ferienpass
 * @author  Richard Henkenjohann <richard-ferienpass@henkenjohann.me>
 */

namespace OnlineTicket\Helper;

use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\ExcelFileWriter;

class ExcelReport extends ExcelFileWriter
{
	protected $arrTmpCurrentRows;

	/**
	 * {@inheritdoc}
	 */
	public function __construct($strFile = '', $strExtension = '.xlsx')
	{
		parent::__construct($strFile, $strExtension);

		if (parent::prepare(new ArrayReader()))
		{
			// Set default font size to 12
			$this->objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
		}
	}


	/**
	 * Return the current row
	 *
	 * @return int
	 */
	public function getCurrentRow()
	{
		return $this->currentRow;
	}


	/**
	 * Return the PHPExcel instance
	 *
	 * @return \PHPExcel
	 */
	public function getPHPExcel()
	{
		return $this->objPHPExcel;
	}


	/**
	 * Write row to CSV file
	 *
	 * @param   array
	 *
	 * @return  bool
	 */
	public function writeRow(array $arrData)
	{
		if (!is_array($arrData))
		{
			return false;
		}

		$this->currentRow += 1;
		$currentColumn = 0;

		foreach ($arrData as $varValue)
		{
			$this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow
			(
				$currentColumn++,
				$this->currentRow,
				(string)$varValue
			);
		}

		return true;
	}


	public function switchSheet($intIndex)
	{
		// Save current row
		$this->arrTmpCurrentRows[$this->objPHPExcel->getActiveSheetIndex()] = $this->currentRow;

		try
		{
			$this->objPHPExcel->setActiveSheetIndex($intIndex);
		}
		catch (\PHPExcel_Exception $e)
		{
			$this->objPHPExcel->createSheet($intIndex);
			$this->objPHPExcel->setActiveSheetIndex($intIndex);
		}

		// Load current row from temp array
		$this->currentRow = $this->arrTmpCurrentRows[$intIndex] ?: 0;
	}


	/**
	 * {@inheritdoc}
	 */
	public function finish()
	{
		//parent::finish();

		$objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->strFormat);

		// Disable pre calculation to be able to use new formulas
		/** @noinspection PhpUndefinedMethodInspection */
		$objWriter->setPreCalculateFormulas(false);

		$objWriter->save(TL_ROOT . '/' . $this->strFile);
	}


	/**
	 * Set the headline 1 style for a given length of columns
	 *
	 * @param int $intColumnLength
	 */
	public function setH1Style($intColumnLength = 1)
	{
		$this->setStyle(1, $intColumnLength)->getFont()
			->setBold(true)
			->setSize(18);
	}

	/**
	 * Set the headline 2 style for a given length of columns
	 *
	 * @param int $intColumnLength
	 */
	public function setH2Style($intColumnLength = 1)
	{
		$this->setStyle(1, $intColumnLength)->getFont()
			->setBold(true)
			->setSize(16);
	}


	/**
	 * Set the headline 1 style for a given length of columns
	 *
	 * @param int $intColumnLength
	 */
	public function setH3Style($intColumnLength = 1)
	{
		$this->setStyle(1, $intColumnLength)->getFont()
			->setBold(true);
	}


	/**
	 * @param int $intRowLength    Quantity of rows selected
	 * @param int $intColumnLength Quantity of columns selected
	 * @param int $intRowStart     Start point of row selection or <0> to use current row
	 * @param int $intColumnStart  Start point of column selection
	 *
	 * @return \PHPExcel_Style
	 */
	public function setStyle($intRowLength = 1, $intColumnLength = 1, $intRowStart = 0, $intColumnStart = 0)
	{
		$intRowStart = $intRowStart ?: $this->currentRow;

		return $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow
		(
			$intColumnStart,
			$intRowStart,
			$intColumnStart + $intColumnLength - 1,
			$intRowStart + $intRowLength - 1
		);
	}
}

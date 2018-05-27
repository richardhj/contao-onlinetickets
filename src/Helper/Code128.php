<?php

/**
 * This file is part of richardhj/contao-onlinetickets.
 *
 * Copyright (c) 2016-2017 Richard Henkenjohann
 *
 * @package   richardhj/contao-onlinetickets
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-onlinetickets/blob/master/LICENSE
 */


namespace Richardhj\IsotopeOnlineTicketsBundle\Helper;


/**
 * Class Code128
 *
 * @package Richardhj\IsotopeOnlineTicketsBundle\Helper
 */
class Code128
{

    /**
     * The barcode data
     *
     * @var string
     */
    protected $strData;


    /**
     * The subtype
     *
     * @var integer
     */
    protected $intType;


    /**
     * The character map
     *
     * @var array
     */
    protected $arrCharacterMap;


    /**
     * Subtypes
     */
    const TYPE_A = 1; // ASCII 00-95 (0-9, A-Z, Control codes, and some special chars)
    const TYPE_B = 2; // ASCII 32-127 (0-9, A-Z, a-z, special chars)
    const TYPE_C = 3; // Numbers 00-99 (two digits per code)


    public function __construct($strData = '', $intType = 0)
    {
        $this->setData($strData);

        if (in_array($intType, array(self::TYPE_A, self::TYPE_B, self::TYPE_C))) {
            $this->intType = $intType;
        } else {
            $this->setSubtype();
        }

        $this->generateMap();
    }

    /**
     * Generate character map
     *
     * @see http://www.idautomation.com/barcode-fonts/code-128/user-manual.html#Barcode_Font_Character_Set
     * @see http://www.ascii-code.com
     */
    protected function generateMap()
    {
        $this->arrCharacterMap = array();

        if ($this->intType == self::TYPE_C) {
            return;
        }

        // ASCII printable characters (character code 32-95)
        for ($i = 32; $i <= 95; $i++) {
            $this->arrCharacterMap[] = chr($i);
        }

        switch ($this->intType) {
            case self::TYPE_A:
                // ASCII control characters (character code 0-31)
                for ($i = 0; $i <= 31; $i++) {
                    $this->arrCharacterMap[] = chr($i);
                }
                break;

            case self::TYPE_B:
                // ASCII printable characters (character code 96-127)
                for ($i = 96; $i <= 127; $i++) {
                    $this->arrCharacterMap[] = chr($i);
                }
                break;
        }
    }


    /**
     * Set the data string
     *
     * @param $strData
     */
    public function setData($strData)
    {
        $this->strData = $strData;
    }


    /**
     * Set best subtype by data string
     */
    protected function setSubtype()
    {
        $arrChars = str_split($this->strData);

        if (preg_match('/\d{2,}/', $this->strData) && (!(strlen(($this->strData)) % 2))) {
            $this->intType = self::TYPE_C;

            return;
        }

        foreach ($arrChars as $k => $char) {
            if (ord($char) > 95) {
                $this->intType = self::TYPE_B;

                return;
            }
        }

        $this->intType = self::TYPE_A;
    }


    /**
     * Get the start character by the current subtype
     *
     * @return string
     * @throws \Exception
     */
    protected function getStartChar()
    {
        switch ($this->intType) {
            case self::TYPE_A:
                return chr(203); // Ë
                break;

            case self::TYPE_B:
                return chr(204); // Ì
                break;

            case self::TYPE_C:
                return chr(205); // Í
                break;
        }

        throw new \Exception('Barcode type is not set.');
    }

    /**
     * Get the stop character
     *
     * @return string
     */
    protected function getStopChar()
    {
        return chr(206); // Î
    }


    protected function getCheckCharacter()
    {
        switch ($this->intType) {
            case self::TYPE_A:
            case self::TYPE_B:
                $arrChars = str_split($this->strData);

                if ($this->intType == self::TYPE_A) {
                    $total = 103;
                } else {
                    $total = 104;
                }

                foreach ($arrChars as $k => $char) {
                    $i = $k + 1;

                    $position = array_search($char, $this->arrCharacterMap);

                    if ($position === false) {
                        throw new \Exception(sprintf('Character at position %d not found', $i));
                    }

                    $total += $i * $position;
                }

                return $this->arrCharacterMap[$total % 103];
        }
    }


    public function getBarcode()
    {
        return $this->getStartChar() . $this->strData . $this->getCheckCharacter() . $this->getStopChar();
    }
}

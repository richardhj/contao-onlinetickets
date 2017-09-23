<?php

namespace Richardhj\Isotope\OnlineTickets\Helper;

use Contao\File;
use Contao\Request;


/**
 * Class QrCode
 *
 * @package Richardhj\Isotope\OnlineTickets\Helper
 */
class QrCode
{

    /**
     * The api url
     */
    const API_URL = 'http://api.qrserver.com/v1/create-qr-code/';


    /**
     * Get the external image path
     *
     * @param        $strData
     * @param int    $intSize
     * @param string $strEcc
     * @param int    $intMargin
     * @param int    $intQzone
     * @param null   $strColor
     * @param null   $strBgColor
     * @param string $strFormat
     *
     * @return string
     */
    public static function getImagePath(
        $strData,
        $intSize = 200,
        $strEcc = 'L',
        $intMargin = 1,
        $intQzone = 4,
        $strColor = null,
        $strBgColor = null,
        $strFormat = 'png'
    ) {
        return static::buildRequestUrl(
            $strData,
            $intSize,
            $strEcc,
            $intMargin,
            $intQzone,
            $strColor,
            $strBgColor,
            $strFormat
        );
    }


    /**
     * Get the local image path
     *
     * @param string $strData
     * @param bool   $blnPermanentSave
     * @param int    $intSize
     * @param string $strEcc
     * @param int    $intMargin
     * @param int    $intQzone
     * @param null   $strColor
     * @param null   $strBgColor
     * @param string $strFormat
     *
     * @return string
     */
    public static function getLocalPath(
        $strData,
        $blnPermanentSave = false,
        $intSize = 200,
        $strEcc = 'L',
        $intMargin = 1,
        $intQzone = 4,
        $strColor = null,
        $strBgColor = null,
        $strFormat = 'png'
    ) {
        $strFileName = md5(serialize(func_get_args()));
        $strPath     = $blnPermanentSave
            ?
            'files/qrcodes/'
            :
            'system/tmp/';

        $objRequest = new Request();
        $objRequest->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $objRequest->send(
            static::API_URL,
            static::buildRequestUrl(
                $strData,
                $intSize,
                $strEcc,
                $intMargin,
                $intQzone,
                $strColor,
                $strBgColor,
                $strFormat
            ),
            'POST'
        );

        if ($objRequest->hasError()) {
            \System::log(sprintf('QR Code call failed.'), __METHOD__, TL_ERROR);

            return '';
        }

        $objFile = new File($strPath . $strFileName . '.' . $strFormat);
        $objFile->write($objRequest->response);

        return $objFile->path;
    }


    /**
     * Return the api server request url
     *
     * @param string $strData           The data or text
     *
     * @param int    $intSize           The size in pixels
     *
     * @param string $strEcc            The error correction code
     *                                  Options:
     *                                  L (low)
     *                                  M (middle)
     *                                  Q (quality)
     *                                  H (high)
     *
     * @param int    $intMargin         The qr code's margin/border in pixels filled up with the defined bg color
     *
     * @param int    $intQzone          The qr code's quit zone in relative unit which is the outer white space
     *
     * @param string $strColor          The data block's color formatted <255-255-255> or <ffffff>
     *                                  Default: black
     *
     * @param string $strBgColor        The background color formatted <255-255-255> or <ffffff>
     *                                  Default: white
     *
     * @param string $strFormat         The output file type
     *                                  Options:
     *                                  png, gif, jpeg, jpg,
     *                                  svg, eps
     *
     * @see http://goqr.me/de/api/doc/create-qr-code/
     * @return string
     */
    protected static function buildRequestUrl(
        $strData,
        $intSize = 200,
        $strEcc = 'L',
        $intMargin = 1,
        $intQzone = 4,
        $strColor = null,
        $strBgColor = null,
        $strFormat = 'png'
    ) {
        $arrParams = array
        (
            'data'    => urlencode($strData),
            'size'    => $intSize . 'x' . $intSize,
            'ecc'     => $strEcc,
            'margin'  => $intMargin,
            'qzone'   => $intQzone,
            'color'   => $strColor,
            'bgcolor' => $strBgColor,
            'format'  => strtolower($strFormat)
        );

        return http_build_query($arrParams, null, '&');
    }
}

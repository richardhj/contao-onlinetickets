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


namespace Richardhj\Isotope\OnlineTickets\Api;

use Contao\Controller;
use Contao\Input;
use Richardhj\Isotope\OnlineTickets\Helper\ApiUser;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class AbstractApi
 *
 * @package Richardhj\Isotope\OnlineTickets\Api
 */
abstract class AbstractApi
{

    /**
     * @var ApiUser
     */
    protected $user;

    /**
     * AbstractApi constructor.
     */
    public function __construct()
    {
        Controller::loadLanguageFile('default');
        $this->user = ApiUser::getInstance();
    }

    /**
     * Get a submitted parameter
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getParameter($key)
    {
        return Input::get($key);
    }

    /**
     * Authenticate the submitted token or exit otherwise
     */
    protected function authenticateToken()
    {
        $userHash = $this->getParameter('token');
        if (!$this->user->setHash($userHash)->authenticate()) {
            $this->exitWithError();
        }
    }

    /**
     * Exit with json formatted error message
     *
     * @param int $code The error code as defined in @see ApiErrors
     */
    protected function exitWithError($code = null)
    {
        if (null === $code) {
            $code = ApiErrors::UNKNOWN_TERMINAL;
        }

        $response = new JsonResponse(
            [
                'Errorcode'    => $code,
                'Errormessage' => $GLOBALS['TL_LANG']['ERR']['onlinetickets_api'][$code],
            ]
        );

        $response->send();
        exit;
    }


    abstract public function run();
}

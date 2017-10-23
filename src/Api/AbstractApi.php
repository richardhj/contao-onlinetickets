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
        if (!$this->user->setHash($this->getParameter('token'))->authenticate()) {
            $this->exitWithError($GLOBALS['TL_LANG']['ERR']['onlinetickets_authentication_error']);
        }
    }

    /**
     * Exit with json formatted error message
     *
     * @param string $message
     */
    protected function exitWithError($message = '')
    {
        $response = new JsonResponse(
            [
                'Errorcode'    => 1,
                'Errormessage' => ('' !== $message)
                    ? $message
                    : $GLOBALS['TL_LANG']['ERR']['onlinetickets_default']
            ]
        );

        $response->send();
    }


    abstract public function run();
}

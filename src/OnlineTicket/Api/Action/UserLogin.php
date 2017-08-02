<?php

namespace OnlineTicket\Api\Action;

use Contao\Input;
use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api\AbstractApi;


/**
 * Class UserLogin
 *
 * @package OnlineTicket\Api\Action
 */
class UserLogin extends AbstractApi
{

    /**
     * Login the user
     */
    public function run()
    {
        // System login demands post variables
        Input::setPost('username', $this->getParameter('username'));
        Input::setPost('password', $this->getParameter('password'));

        // Login user or exit
        if (false === ($hash = $this->user->login())) {
            $this->exitWithError($GLOBALS['TL_LANG']['ERR']['onlinetickets_login_error']);
        }

        // Return session hash as token
        $response = new JsonResponse(
            [
                'Token' => $hash
            ]
        );

        $response->send();
    }
}

<?php

namespace OnlineTicket\Api;

use Contao\Input;
use Haste\Http\Response\JsonResponse;


class UserLogin extends AbstractApi
{

    /**
     * Login the user
     */
    public function run()
    {
        // System login demands post variables
        Input::setPost('username', $this->get('username'));
        Input::setPost('password', $this->get('password'));

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

<?php

namespace OnlineTicket\Api;

use Contao\Input;
use Haste\Http\Response\JsonResponse;
use OnlineTicket\Api;


class UserLogin extends Api
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
		if (($strHash = $this->objUser->login()) === false)
		{
			$this->exitWithError($GLOBALS['TL_LANG']['ERR']['onlinetickets_login_error']);
		}

		// Return session hash as token
		$objResponse = new JsonResponse(array
		(
			'Token' => $strHash
		));

		$objResponse->send();
	}
}

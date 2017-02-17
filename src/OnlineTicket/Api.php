<?php

namespace OnlineTicket;

use Contao\Controller;
use Haste\Http\Response\JsonResponse;
use OnlineTicket\Helper\ApiUser;


abstract class Api extends Controller
{

	/**
	 * The submitted parameters
	 *
	 * @var array
	 */
	protected $arrParams;


	/**
	 * @var ApiUser
	 */
	protected $objUser;


	/**
	 * The allowed parameters
	 *
	 * @var array
	 */
	public static $allowedParams = array('token', 'timestamp', 'ticketcode', 'username', 'password', 'vendorid');


	/**
	 * Process parameters
	 *
	 * @param array $arrParams
	 */
	public function __construct($arrParams)
	{
		parent::__construct();

		$this->loadLanguageFile('default');

		$this->objUser = ApiUser::getInstance();

		$this->arrParams = $arrParams;

		if (empty($this->arrParams))
		{
			throw new \InvalidArgumentException('Arguments are missing.');
		}
	}


	/**
	 * Get a submitted parameter
	 *
	 * @param string $strKey
	 *
	 * @return mixed
	 */
	protected function get($strKey)
	{
		if (isset($this->arrParams[$strKey]))
		{
			return $this->arrParams[$strKey];
		}

		throw new \RuntimeException(sprintf('Invalid key %s', $strKey));
	}


	/**
	 * Authenticate the submitted token or exit otherwise
	 */
	protected function authenticateToken()
	{
		if (!$this->objUser->authenticate($this->get('token')))
		{
			$this->exitWithError($GLOBALS['TL_LANG']['ERR']['onlinetickets_authentication_error']);
		}
	}


	/**
	 * Exit with json formatted error message
	 *
	 * @param string $strMessage
	 */
	protected function exitWithError($strMessage='')
	{
		$objResponse = new JsonResponse(array
		(
			'Errorcode'    => 1,
			'Errormessage' => ($strMessage != '') ? $strMessage : $GLOBALS['TL_LANG']['ERR']['onlinetickets_default']
		));

		$objResponse->send();
	}


	abstract public function run();
}

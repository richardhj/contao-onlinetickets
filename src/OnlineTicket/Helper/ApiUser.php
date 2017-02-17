<?php

namespace OnlineTicket\Helper;

use Contao\User;
use Contao\Environment;
use Contao\SessionModel;


class ApiUser extends User
{

	/**
	 * Current object instance (do not remove)
	 *
	 * @var object
	 */
	protected static $objInstance;


	/**
	 * Name of the corresponding table
	 *
	 * @var string
	 */
	protected $strTable = 'tl_user';


	/**
	 * Name of the current cookie
	 *
	 * @var string
	 */
	protected $strCookie = 'API_USER_AUTH';


	/**
	 * Initialize the object
	 */
	protected function __construct()
	{
		parent::__construct();

		$this->strIp = Environment::get('ip');
	}


	/**
	 * Authenticate a user
	 *
	 * @param string $strHash
	 *
	 * @return bool
	 */
	public function authenticate($strHash)
	{
		$this->strHash = $strHash;

		$objSession = SessionModel::findBy(array('hash=?', 'name=?'), array($this->strHash, $this->strCookie));

		// Try to find the session in the database
		if (null === $objSession)
		{
			$this->log('Could not find the session record', __METHOD__, TL_ACCESS);

			return false;
		}

		$time = time();

		// Validate the session
		if ((!\Config::get('disableIpCheck') && $objSession->ip != $this->strIp) || $objSession->hash != $this->strHash || ($objSession->tstamp + \Config::get('sessionTimeout')) < $time)
		{
			$this->log('Could not verify the session', __METHOD__, TL_ACCESS);

			return false;
		}

		$this->intId = $objSession->pid;

		// Load the user object
		if ($this->findBy('id', $this->intId) == false)
		{
			$this->log('Could not find the session user', __METHOD__, TL_ACCESS);

			return false;
		}

		$this->setUserFromDb();

		// Update session
		$objSession->tstamp = $time;
		$objSession->save();

		return true;
	}


	/**
	 * Login the user and return the session hash or false otherwise
	 *
	 * @return string|false
	 */
	public function login()
	{
		if (parent::login())
		{
			return $this->strHash;
		}

		return false;
	}


	/**
	 * Set all user properties from a database record
	 * Warning: bypasses backend permissions
	 */
	protected function setUserFromDb()
	{
		$this->intId = $this->id;

		// Unserialize values
		foreach ($this->arrData as $k=>$v)
		{
			if (!is_numeric($v))
			{
				$this->$k = deserialize($v);
			}
		}

		$GLOBALS['TL_USERNAME'] = $this->username;
		$GLOBALS['TL_LANGUAGE'] = str_replace('_', '-', $this->language);

		// Restore session
		if (is_array($this->session))
		{
			$this->Session->setData($this->session);
		}
		else
		{
			$this->session = array();
		}
	}


	/**
	 * Generate a session
	 */
	protected function generateSession()
	{
		$time = time();

		// Generate the cookie hash
		$this->strHash = sha1(session_id() . (!\Config::get('disableIpCheck') ? $this->strIp : '') . $this->strCookie);

		// Clean up old sessions
		$this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
			->execute(($time - \Config::get('sessionTimeout')), $this->strHash);

		// Save the session in the database
		$this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
			->execute($this->intId, $time, $this->strCookie, session_id(), $this->strIp, $this->strHash);
	}
}

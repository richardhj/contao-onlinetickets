<?php

namespace OnlineTicket;

use Contao\Environment;
use Contao\Input;
use Contao\PageModel;
use Haste\Http\Response\Response;


/**
 * Set the script name
 */
define('TL_SCRIPT', 'api/index.php');


/**
 * Initialize the system
 */
require_once('../system/initialize.php');

define('TL_MODE', 'FE');
define('BYPASS_TOKEN_CHECK', true);

$objPage = PageModel::findPublishedFallbackByHostname(Environment::get('httpHost'));

if (null !== $objPage)
{
	// Set language
	$GLOBALS['TL_LANGUAGE'] = $objPage->language;
}


class ApiListener extends \Frontend
{
    /**
     * The api action to call
     *
     * @var string
     */
    protected $strAction;


	/**
	 * The submitted params
	 *
	 * @var array
	 */
	protected $arrParams;


    /**
     * Construct the class
     */
    public function __construct()
    {
        parent::__construct();

	    $this->setAction((string) strtok(basename(Environment::get('requestUri')), '?'));

	    foreach (Api::$allowedParams as $param)
	    {
		    $this->setParam($param, Input::get($param));
	    }
    }


	/**
	 * Set the action
	 *
	 * @param string $strAction
	 */
    public function setAction($strAction)
    {
        $this->strAction = $strAction;
    }


    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->strAction;
    }


	/**
	 * Set a parameter
	 *
	 * @param string $strKey
	 * @param mixed  $varValue
	 */
	public function setParam($strKey, $varValue)
	{
		if (!is_null($varValue))
		{
			$this->arrParams[$strKey] = $varValue;
		}
	}


    /**
     * Run the controller
     */
    public function run()
    {
	    // Log every request
	    $this->logRequest();

	    try
	    {
		    $strClass = '\OnlineTicket\Api\\' . ucfirst($this->getAction());

		    if (class_exists($strClass))
		    {
			    /** @type Api $objAction */
			    $objAction = new $strClass($this->arrParams);

			    $objAction->run();
		    }
		    else
		    {
			    $objResponse = new Response('Bad Request', 400);
			    $objResponse->send();
		    }
	    }
	    catch (\Exception $e)
	    {
			$objResponse = new Response(sprintf('Internal Server Error. Message: %s', $e->getMessage()), 500);
		    $objResponse->send();
	    }
    }


    /**
     * Log every api request to our log file
     */
    private function logRequest()
    {
        $headers = array();

        foreach ($_SERVER as $key => $value)
        {
            if (0 === strpos($key, 'HTTP_'))
            {
                $headers[substr($key, 5)] = $value;
            }
        }

        log_message
        (
            sprintf
            (
                "New request to %s.\n\nHeaders: %s\n\n\$_GET: %s\n\nBody:\n%s\n",
                Environment::get('base') . Environment::get('request'),
                var_export($headers, true),
                var_export($_GET, true),
                file_get_contents("php://input")
            ),
            'onlinetickets_api.log'
        );
    }
}


/**
 * Instantiate controller
 */
$objPostSale = new ApiListener();
$objPostSale->run();

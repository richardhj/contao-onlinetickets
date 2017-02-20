<?php

namespace OnlineTicket\Api;

use Haste\Http\Response\Response;
use OnlineTicket\Api;


class Listener extends \Frontend
{
    /**
     * The api action to call
     *
     * @var string
     */
    protected $action;


    /**
     * The submitted params
     *
     * @var array
     */
    protected $params;


    /**
     * Construct the class
     */
    public function __construct()
    {
        parent::__construct();

        if (null !== ($page = \PageModel::findPublishedFallbackByHostname(\Environment::get('httpHost')))) {
            // Set language
            $GLOBALS['TL_LANGUAGE'] = $page->language;
        }

        $this->setAction((string) strtok(basename(\Environment::get('requestUri')), '?'));

        foreach (Api::$allowedParams as $param) {
            $this->setParam($param, \Input::get($param));
        }
    }


    /**
     * Set the action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }


    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * Set a parameter
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setParam($key, $value)
    {
        if (!is_null($value)) {
            $this->params[$key] = $value;
        }
    }


    /**
     * Run the controller
     */
    public function run()
    {
        // Log every request
        $this->logRequest();

        try {
            $class = '\OnlineTicket\Api\\' . ucfirst($this->getAction());

            if (class_exists($class)) {
                /** @type Api $action */
                $action = new $class($this->params);

                $action->run();
            } else {
                $response = new Response('Bad Request', 400);
                $response->send();
            }
        } catch (\Exception $e) {
            $response = new Response(sprintf('Internal Server Error. Message: %s', $e->getMessage()), 500);
            $response->send();
        }
    }


    /**
     * Log every api request to our log file
     */
    private function logRequest()
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            }
        }

        log_message(
            sprintf(
                "New request to %s.\n\nHeaders: %s\n\n\$_GET: %s\n\nBody:\n%s\n",
                \Environment::get('base') . \Environment::get('request'),
                var_export($headers, true),
                var_export($_GET, true),
                file_get_contents("php://input")
            ),
            'onlinetickets_api.log'
        );
    }
}

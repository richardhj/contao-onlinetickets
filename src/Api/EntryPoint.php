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

use Contao\Environment;
use Contao\PageModel;
use Exception;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class EntryPoint
 *
 * @package Richardhj\Isotope\OnlineTickets\Api
 */
class EntryPoint
{

    /**
     * The api action to call
     *
     * @var string
     */
    private $action;

    /**
     * Construct the class
     */
    public function __construct()
    {
        if (null !== ($page = PageModel::findPublishedFallbackByHostname(Environment::get('httpHost')))) {
            // Set language
            $GLOBALS['TL_LANGUAGE'] = $page->language;
        }

        $this->setAction((string) strtok(basename(Environment::get('requestUri')), '?'));
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
     * Run the controller
     */
    public function run()
    {
        // Log every request
        $this->logRequest();

        try {
            $class = __NAMESPACE__ . '\Action\\' . ucfirst($this->getAction());

            if (class_exists($class)) {
                /** @type AbstractApi $action */
                $action = new $class();
                $action->run();
            } else {
                $response = new Response('Bad Request', Response::HTTP_BAD_REQUEST);
                $response->send();
            }
        } catch (Exception $e) {
            $response = new Response(
                sprintf('Internal Server Error. Message: %s', $e->getMessage()),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
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
                Environment::get('base') . Environment::get('request'),
                var_export($headers, true),
                var_export($_GET, true),
                file_get_contents("php://input")
            ),
            'onlinetickets_api.log'
        );
    }
}

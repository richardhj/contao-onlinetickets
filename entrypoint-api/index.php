<?php


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

/**
 * Run the controller
 */
$controller = new \OnlineTicket\Api\Listener();
$controller->run();

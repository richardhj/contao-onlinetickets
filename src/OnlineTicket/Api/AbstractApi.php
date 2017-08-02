<?php

namespace OnlineTicket\Api;

use Contao\Controller;
use Haste\Http\Response\JsonResponse;
use OnlineTicket\Helper\ApiUser;


abstract class AbstractApi extends Controller
{

    /**
     * The submitted parameters
     *
     * @var array
     */
    protected $params;


    /**
     * @var ApiUser
     */
    protected $user;


    /**
     * The allowed parameters
     *
     * @var array
     */
    public static $allowedParams = ['token', 'timestamp', 'ticketcode', 'username', 'password', 'vendorid'];


    /**
     * AbstractApi constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->loadLanguageFile('default');
        $this->user = ApiUser::getInstance();
    }

    /**
     * @param array $params
     *
     * @return AbstractApi
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }


    /**
     * Get a submitted parameter
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function get($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        throw new \RuntimeException(sprintf('Invalid key %s', $key));
    }


    /**
     * Authenticate the submitted token or exit otherwise
     */
    protected function authenticateToken()
    {
        if (!$this->user->setHash($this->get('token'))->authenticate()) {
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

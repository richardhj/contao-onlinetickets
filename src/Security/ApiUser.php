<?php


namespace Richardhj\IsotopeOnlineTicketsBundle\Security;


use Contao\StringUtil;
use Contao\User;

class ApiUser extends User
{

    /**
     * Symfony authentication roles
     *
     * @var array
     */
    protected $roles = ['ROLE_ONLINETICKETS_API'];

    /**
     * Set all user properties from a database record
     */
    protected function setUserFromDb()
    {
        $this->intId = $this->id;

        foreach ($this->arrData as $k => $v) {
            if (!is_numeric($v)) {
                $this->$k = StringUtil::deserialize($v);
            }
        }
    }
}

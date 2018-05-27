<?php


namespace Richardhj\IsotopeOnlineTicketsBundle\Security;


use Contao\UserModel;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyUserProvider implements UserProviderInterface
{

    /**
     * @param $apiKey
     *
     * @return null|string
     */
    public function getUsernameForApiKey($apiKey): ?string
    {
        $user = UserModel::findBy('onlinetickets_api_key', $apiKey);
        if (null === $user) {
            return null;
        }

        return $user->username;
    }

    /**
     * @param string $username
     *
     * @return UserInterface|static
     */
    public function loadUserByUsername($username)
    {
        return ApiUser::loadUserByUsername($username);
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface|void
     *
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return ApiUser::class === $class;
    }
}

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


namespace Richardhj\IsotopeOnlineTicketsBundle\Controller\Api;

use Contao\BackendUser;
use Contao\CoreBundle\Security\Authentication\Provider\AuthenticationProvider;
use Richardhj\IsotopeOnlineTicketsBundle\Api\ApiErrors;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class UserLogin
 *
 * @package Richardhj\IsotopeOnlineTicketsBundle\Api\Action
 */
class UserLogin extends Controller
{

    /**
     * @var AuthenticationProvider
     */
    private $authenticationProvider;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $providerKey;

    public function __construct(
        AuthenticationProvider $authenticationProvider,
        TokenGeneratorInterface $tokenGenerator,
        TranslatorInterface $translator,
        string $providerKey = 'contao_backend'
    ) {

        $this->authenticationProvider = $authenticationProvider;
        $this->tokenGenerator         = $tokenGenerator;
        $this->translator             = $translator;
        $this->providerKey            = $providerKey;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $username = $request->query->get('username');
        $password = $request->query->get('password');

        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->providerKey
        );

        try {
            $authenticatedToken = $this
                ->authenticationProvider
                ->authenticate($unauthenticatedToken);

        } catch (AuthenticationException $exception) {
            return new JsonResponse(
                [
                    'Errorcode'    => ApiErrors::UNKNOWN_TERMINAL,
                    'Errormessage' => $this->translator->trans(
                        'ERR.onlinetickets_api.'.ApiErrors::UNKNOWN_TERMINAL,
                        [],
                        'contao_default'
                    ),
                ]
            );
        }

        if (null === Ticket::findByUser($authenticatedToken->getUser()->id)) {
            return new JsonResponse(
                [
                    'Errorcode'    => ApiErrors::NO_EVENTS,
                    'Errormessage' => $this->translator->trans(
                        'ERR.onlinetickets_api.'.ApiErrors::NO_EVENTS,
                        [],
                        'contao_default'
                    ),
                ]
            );
        }

        $apiKey = $this->tokenGenerator->generateToken();

        $user = $authenticatedToken->getUser();
        if ($user instanceof BackendUser) {
            $user->onlinetickets_api_key = $apiKey;
            $user->save();
        }

        return new JsonResponse(
            [
                'Token' => $apiKey,
            ]
        );
    }
}

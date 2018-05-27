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

use Richardhj\IsotopeOnlineTicketsBundle\Api\ApiErrors;
use Richardhj\IsotopeOnlineTicketsBundle\Model\Ticket;
use Richardhj\IsotopeOnlineTicketsBundle\Security\ApiUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class SetTicketAsRegistered
 *
 * @package Richardhj\IsotopeOnlineTicketsBundle\Api\Action
 */
class SetTicketAsRegistered extends Controller
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * SetTicketAsRegistered constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \LogicException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $ticket = Ticket::findByTicketCode($request->query->get('ticketcode'));
        if (null === $ticket) {
            return new JsonResponse(
                [
                    'Errorcode'    => ApiErrors::TICKET_NOT_FOUND,
                    'Errormessage' => $this->translator->trans(
                        'ERR.onlinetickets_api.'.ApiErrors::TICKET_NOT_FOUND,
                        [],
                        'contao_default'
                    ),
                ]
            );
        }

        $success = false;

        /** @var ApiUser $user */
        $user = $this->getUser();

        if ($user->tickets_defineMode) {
            $ticket->agency_id = $user->tickets_defineModeAgencyId;
            $ticket->save();
        } else {
            // Check if check in possible and user is not in test mode
            if ($ticket->checkInPossible() && !$user->tickets_testmode) {
                $ticket->checkin      = time();
                $ticket->checkin_user = $user->id;

                if ($ticket->save()) {
                    $success = true;
                }
            }
        }

        return new JsonResponse(
            [
                'Checkin' => [
                    'Result' => $success,
                ],
            ]
        );
    }
}

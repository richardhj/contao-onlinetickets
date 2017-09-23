<?php

namespace Richardhj\Isotope\OnlineTickets\Module;

use Contao\Database;
use Contao\Environment;
use Contao\Model\Collection;
use Contao\PageError403;
use Contao\UserModel;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\RedirectEvent;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Haste\Form\Form;
use Haste\Frontend\AbstractFrontendModule;
use Richardhj\Isotope\OnlineTickets\Model\Agency;
use Richardhj\Isotope\OnlineTickets\Model\Event;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Class BoxOffice
 *
 * @package Richardhj\Isotope\OnlineTickets\Module
 */
class BoxOffice extends AbstractFrontendModule
{

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_box_office';

    /**
     * Compile the current element
     */
    protected function compile()
    {
        global $container, $objPage;
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container['event-dispatcher'];

        $this->Template->translator = $container['translator'];

        $member = $container['user'];
        $user   = UserModel::findBy('assignedMember', $member->id);
        if (null === $user) {
            return;
        }

        $urlBuilder = UrlBuilder::fromUrl(Environment::get('uri'));
        $eventId    = $urlBuilder->getQueryParameter('event_id');
//        $eventId = Input::get('event_id');
        //TODO Check permission
//        /** @var Model $event */
        $event = Event::findByPk($eventId);
        if (null === $event) {
            /** @var Collection|Event $events */
            $events = Event::findByUser($user->id);
            if (null === $events) {
                $this->Template->noEvents    = true;
                $this->Template->noEventsMsg = 'Keine Events';
                return;
            } elseif ($events->count() > 1) {
//                var_dump('Events ambiguous');
                return;
            }
        }

        if ('undo' === $urlBuilder->getQueryParameter('action')) {
            $ticketId = $urlBuilder->getQueryParameter('ticket_id');
            $ticket   = Ticket::findByPk($ticketId);
            if (!$user->admin && $ticket->checkin_user !== $user->id) {
                $pageHandler = new PageError403();
                $pageHandler->generate($objPage->id);
                exit;
            }

            $ticket->checkin = 0;
            $ticket->save();

            $dispatcher->dispatch(
                ContaoEvents::CONTROLLER_REDIRECT,
                new RedirectEvent(
                    $urlBuilder
                        ->unsetQueryParameter('action')
                        ->unsetQueryParameter('ticket_id')
                        ->getUrl()
                )
            );
        }

        $this->Template->headline = $event->name;

        $manualCheckInForm = new Form(
            'check_in', 'POST', function (Form $haste) {
            return \Input::post('FORM_SUBMIT') === $haste->getFormId();
        }
        );

        $manualCheckInForm->addFormField(
            'count',
            [
                'label'     => 'Anzahl',
                'default'   => 1,
                'inputType' => 'text',
                'eval'      => [
                    'rgxp' => 'digit'
                ]
            ]
        );

        $manualCheckInForm->addFormField(
            'agency',
            [
                'label'            => 'Ticketstelle',
                'inputType'        => 'select',
                //                'default' => ;
                'options_callback' => function () use ($event) {
                    /** @var Collection|Agency $agencies */
                    $agencies = Agency::findBy(['pid=?', 'box_office_checkin=1'], $event->id);
                    if (null === $agencies) {
                        return [];
                    }
                    return $agencies->fetchEach('name');
                },
                'eval'             => [
                    'mandatory' => true,
                    //                    'rgxp' => 'digit'
                ]
            ]
        );

        $manualCheckInForm->addSubmitFormField('submit', 'Check in');

        if ($manualCheckInForm->validate()) {
            $time = time();
            for ($i = 0; $i < $manualCheckInForm->fetch('count'); $i++) {
                $newTicket               = new Ticket();
                $newTicket->tstamp       = $time;
                $newTicket->agency_id    = $manualCheckInForm->fetch('agency');
                $newTicket->event_id     = $event->id;
                $newTicket->checkin      = $time;
                $newTicket->checkin_user = $user->id;
                $newTicket->hash         =
                    md5(implode('-', [$event->id, $manualCheckInForm->fetch('agency'), uniqid('', true)]));
                $newTicket->save();
            }
        }

        $table = [];

        /** @var Collection|Ticket $lastCheckedIn */
        $lastCheckedIn =
            Ticket::findBy(['event_id=?', 'checkin<>0'], [$event->id], ['limit' => 10, 'order' => 'checkin DESC']);
        if (null !== $lastCheckedIn) {
            while ($lastCheckedIn->next()) {
                $row                 = [];
                $agency              = Agency::findByPk($lastCheckedIn->agency_id);
                $row['checkin']      = date('D H:i:s', $lastCheckedIn->checkin);
                $row['agency']       = $agency->name;
                $row['checkin_user'] = $lastCheckedIn->getRelated('checkin_user')->name;
                $row['undo']         = ($lastCheckedIn->checkin_user === $user->id) ? sprintf(
                    '<a href="%s" onclick="if(!confirm(\'Möchten Sie den Check-In wirklich zurücksetzen?\')) return false;" class="undo">%s</a>',
                    $urlBuilder
                        ->setQueryParameter('action', 'undo')
                        ->setQueryParameter('ticket_id', $lastCheckedIn->id)
                        ->getUrl(),
                    'rückgängig'
                ) : '';
                $table[]             = $row;
            }
        }

        $countQuery = Database::getInstance()->prepare(
            <<<SQL
SELECT
  IF(ISNULL(a.name), 'Online', a.name) AS name,
  COUNT(t.id)                          AS countSold,
  count(CASE t.checkin
        WHEN 0
          THEN NULL
        ELSE 1 END)                    AS countCheckedIn
FROM tl_onlinetickets_tickets t
  LEFT JOIN tl_onlinetickets_agencies a ON t.agency_id = a.id
WHERE t.event_id = 9
      AND t.tstamp <> 0
GROUP BY a.id
SQL
        )->execute($event->id);

        $this->Template->countCheckedInTickets = array_sum(
            array_map(
                function ($arr) {
                    return $arr['countCheckedIn'];
                },
                $countQuery->fetchAllAssoc()
            )
        );

        $this->Template->manualCheckInForm     = $manualCheckInForm->generate();
        $this->Template->lastCheckedInHeadline = 'Zuletzt eingelassen';
        $this->Template->ticketCountsHeadline  = 'Eingelassen nach Ticketstelle';
        $this->Template->lastCheckedInTable    = $table;
        $this->Template->ticketCounts          = $countQuery->fetchAllAssoc();
    }
}

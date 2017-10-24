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


namespace Richardhj\Isotope\OnlineTickets\Dca;

use Contao\Backend;
use Contao\Database\Result;
use Contao\DataContainer;
use Contao\Image;
use Contao\Validator;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Message\AddMessageEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LogEvent;
use Exception;
use Richardhj\Isotope\OnlineTickets\Model\Agency as AgencyModel;
use Richardhj\Isotope\OnlineTickets\Model\Event;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Class Dca
 *
 * @package Richardhj\Isotope\OnlineTickets\Dca
 */
class Agency extends Backend
{

    /**
     * Remove "export pdf" button if event does not have preprinted tickets
     *
     * @category button_callback
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function buttonForExportPreprintedTicketsPdf($row, $href, $label, $title, $icon, $attributes)
    {
        $event = Event::findByPk($row['pid']);

        if (!$event->preprinted_tickets) {
            return '';
        }

        return '<a href="'.static::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title)
               .'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Disable "delete" button if agency's tickets has been created
     *
     * @category button_callback
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function buttonForAgencyDelete($row, $href, $label, $title, $icon, $attributes)
    {
        if (Ticket::countBy('agency_id', $row['id'])) {
            return Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
        }

        return '<a href="'.static::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title)
               .'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * List agency in list view
     *
     * @category child_record_callback
     *
     * @param array $row
     *
     * @return string
     */
    public function listAgency($row)
    {
        return sprintf(
            $GLOBALS['TL_LANG']['MSC']['onlinetickets_listview'],
            $row['name'],
            Ticket::countBy('agency_id', $row['id'])
        );
    }

    /**
     * Return the tickets count for this agency as field
     *
     * @category load_callback
     *
     * @param mixed         $value
     * @param DataContainer $dc
     *
     * @return int
     */
    public function loadAgencyTickets($value, $dc)
    {
        return Ticket::countBy('agency_id', $dc->activeRecord->id);
    }


    /**
     * Return the agency's ticket count and create new tickets
     *
     * @category save_callback
     *
     * @param mixed         $value
     * @param DataContainer $dc
     *
     * @return mixed
     * @throws Exception
     */
    public function saveAgencyTickets($value, $dc)
    {
        $value = static::processSimpleCalculation($value, $dc->field);

        $oldValue = Ticket::countBy('agency_id', $dc->activeRecord->id);

        if ($value < $oldValue) {
            throw new Exception('Ticket count for this ticket agency must not become smaller.'); //@todo lang
        } elseif ($value > $oldValue) {
            $toCreate = $value - $oldValue;

            $agency = $dc->activeRecord;

            if ($agency instanceof Result) {
                $agency = new AgencyModel($agency);
            }

            $event = $agency->getRelated('pid');
            $time  = time();

            // Consider item's quantity
            for ($i = 0; $i < $toCreate; $i++) {
                $ticket = new Ticket();

                $ticket->event_id  = $event->id;
                $ticket->agency_id = $agency->id;
                $ticket->tstamp    = $time;
                $ticket->hash      = md5(implode('-', [$event->id, $agency->id, uniqid('', true)]));

                if (!$ticket->save()) {
                    $this->getEventDispatcher()->dispatch(
                        ContaoEvents::SYSTEM_LOG,
                        new LogEvent(
                            sprintf(
                                'Could not save ticket for event ID %u and agency ID %u in database.',
                                $event->id,
                                $agency->id
                            ),
                            __METHOD__,
                            TL_ERROR
                        )
                    );

                    throw new Exception('An error while saving the tickets occurred.');
                }
            }

            $this->getEventDispatcher()->dispatch(
                ContaoEvents::MESSAGE_ADD,
                AddMessageEvent::createConfirm(
                    sprintf(
                        $GLOBALS['TL_LANG']['MSC']['agencySaveConfirmation'],
                        $agency->name,
                        $toCreate,
                        $toCreate + $oldValue
                    )
                )
            );

        }

        return '';
    }

    /**
     * Return the count of recalled agency tickets
     *
     * @category save_callback
     *
     * @param mixed         $value
     * @param DataContainer $dc
     *
     * @return mixed
     * @throws Exception
     */
    public function saveAgencyTicketsRecalled($value, $dc)
    {
        $value  = static::processSimpleCalculation($value, $dc->field);
        $agency = Agency::findByPk($dc->activeRecord->id);

        if ($value > $agency->tickets_generated - $agency->tickets_checkedin) {
            throw new Exception(
                'Count of recalled tickets has to be smaller/equal than generated tickets and checked in tickets.'
            );
        }

        return $value;
    }


    /**
     * Expect a natural number or a simple calculation with a natural number as result
     *
     * @param mixed  $value The field value
     * @param string $field The field name
     *
     * @return string The result as natural number
     * @throws Exception
     */
    private static function processSimpleCalculation($value, $field)
    {
        // Except a digit and an optional simple calculation (e.g. 10+10)
        if (!preg_match('/^(\d+)([\+\-\*\/]\d+)?$/', $value)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['natural'], $field));
        }

        eval("\$value = $value;");

        // Use the default validator for natural numbers
        if (!Validator::isNatural($value)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['natural'], $field));
        }

        return $value;
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return $GLOBALS['container']['event-dispatcher'];
    }
}

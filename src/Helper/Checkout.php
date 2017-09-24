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


namespace Richardhj\Isotope\OnlineTickets\Helper;

use Contao\System;
use Isotope\Model\ProductCollectionItem;
use Richardhj\Isotope\OnlineTickets\Model\Ticket;
use Isotope\Model\ProductCollection\Order;


/**
 * Class Checkout
 *
 * @package Richardhj\Isotope\OnlineTickets\Helper
 */
class Checkout
{

    /**
     * Set tickets in database
     *
     * @category preCheckout hook
     *
     * @param Order $order
     *
     * @return boolean
     */
    public function setTicketsInDatabase(Order $order)
    {
        /** @var ProductCollectionItem|\Model $item */
        foreach ($order->getItems() as $item) {
            $product = $item->getRelated('product_id');

            // Skip if order's item is not a ticket
            if (!$product->getRelated('type')->isTicket) {
                continue;
            }

            // Consider item's quantity
            for ($i = 0; $i < $item->quantity; $i++) {
                $ticket = new Ticket();

                $ticket->event_id   = $product->event;
                $ticket->order_id   = $order->id;
                $ticket->item_id    = $item->id;
                $ticket->hash       = md5(implode('-', [$order->uniqid, $item->id, $i]));
                $ticket->product_id = $item->product_id;

                if (!$ticket->save()) {
                    System::log(
                        sprintf(
                            'Could not save ticket for order ID %u and item ID %u in database.',
                            $order->id,
                            $ticket->id
                        ),
                        __METHOD__,
                        TL_ERROR
                    );

                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Activate tickets in database by setting timestamp
     *
     * @category postCheckout hook
     *
     * @param Order $order
     *
     * @internal param array $tokens
     */
    public function activateTicketsInDatabase(Order $order)
    {
        $time    = time();
        $tickets = Ticket::findByOrder($order->id);

        while ($tickets->next()) {
            $tickets->tstamp = $time;
            $tickets->save();
        }
    }
}

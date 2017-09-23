<?php


namespace Richardhj\Isotope\OnlineTickets\Model;


class Order
{

    /**
     * Get a particular attribute
     * The key id is deceptive and disabled therefore
     *
     * @param string $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function __get($key)
    {
        if ('id' === $key) {
            throw new \Exception('Key "id" can not be used. Use key "order_id" instead.');
        }

        return static::__get($key);
    }


    /**
     * Get all orders by a referenced member
     * It's a Ticket model call with orders grouped
     *
     * @param integer $memberId
     *
     * @return \Model\Collection|null|Ticket
     */
    public static function findByUser($memberId)
    {
        return Ticket::findByUser(
            $memberId,
            [
                'column' => [
                    'agency_id=0'
                ],
                'group'  => 'order_id'
            ]
        );
    }
}

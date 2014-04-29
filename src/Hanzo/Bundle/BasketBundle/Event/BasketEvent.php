<?php

namespace Hanzo\Bundle\BasketBundle\Event;

use Hanzo\Model\Orders;
use Hanzo\Model\Products;
use Symfony\Component\EventDispatcher\Event;

class BasketEvent extends Event
{
    /**
     * @var \Hanzo\Model\Orders
     */
    private $order;

    /**
     * @var \Hanzo\Model\Products
     */
    private $product;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @param Orders   $order
     * @param Products $product
     * @param int      $quantity
     */
    public function __construct(Orders $order, Products $product, $quantity = null)
    {
        $this->order    = $order;
        $this->product  = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Products
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}

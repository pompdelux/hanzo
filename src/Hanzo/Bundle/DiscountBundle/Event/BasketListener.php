<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Hanzo\Bundle\DiscountBundle\Handlers\CouponHandler;
use Hanzo\Bundle\DiscountBundle\Handlers\QuantityDiscountHandler;
use Hanzo\Core\Hanzo;
use Hanzo\Model\Customers;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\ProductsQuantityDiscountQuery;
use Hanzo\Model\ProductsQuery;
use Symfony\Bridge\Monolog\Logger;
use Hanzo\Bundle\BasketBundle\Event\BasketEvent;

class BasketListener
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var \Hanzo\Bundle\DiscountBundle\Handlers\CouponHandler
     */
    private $coupon_handler;

    /**
     * @var \Hanzo\Bundle\DiscountBundle\Handlers\QuantityDiscountHandler
     */
    private $quantity_discount_handler;

    /**
     * @param Logger                  $logger
     * @param CouponHandler           $coupon_handler
     * @param QuantityDiscountHandler $quantity_discount_handler
     */
    public function __construct(Logger $logger, CouponHandler $coupon_handler, QuantityDiscountHandler $quantity_discount_handler)
    {
        $this->logger                    = $logger;
        $this->coupon_handler            = $coupon_handler;
        $this->quantity_discount_handler = $quantity_discount_handler;
    }

    /**
     * @param BasketEvent $event
     */
    public function onBasketChange(BasketEvent $event)
    {
        $product = $event->getProduct();
        $order   = $event->getOrder();

        $order = $this->coupon_handler->initialize($order)->handle();
        $order = $this->quantity_discount_handler->initialize($order, $product)->handle();
    }
}

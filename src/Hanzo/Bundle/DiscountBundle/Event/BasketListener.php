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

/**
 * Class BasketListener
 *
 * @package Hanzo\Bundle\DiscountBundle\Event
 */
class BasketListener
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var \Hanzo\Bundle\DiscountBundle\Handlers\CouponHandler
     */
    private $couponHandler;

    /**
     * @var \Hanzo\Bundle\DiscountBundle\Handlers\QuantityDiscountHandler
     */
    private $quantityDiscountHandler;

    /**
     * @param Logger                  $logger
     * @param CouponHandler           $couponHandler
     * @param QuantityDiscountHandler $quantityDiscountHandler
     */
    public function __construct(Logger $logger, CouponHandler $couponHandler, QuantityDiscountHandler $quantityDiscountHandler)
    {
        $this->logger                  = $logger;
        $this->couponHandler           = $couponHandler;
        $this->quantityDiscountHandler = $quantityDiscountHandler;
    }

    /**
     * @param BasketEvent $event
     */
    public function onBasketChange(BasketEvent $event)
    {
        $product = $event->getProduct();
        $order   = $event->getOrder();

        $order = $this->couponHandler->initialize($order)->handle();
        $order = $this->quantityDiscountHandler->initialize($order, $product)->handle();
    }
}

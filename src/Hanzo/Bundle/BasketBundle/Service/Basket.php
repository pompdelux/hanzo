<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\BasketBundle\Service;

use Hanzo\Bundle\BasketBundle\Event\BasketEvent;
use Hanzo\Bundle\PaymentBundle\Methods\Pensio\InvalidOrderStateException;
use Hanzo\Bundle\StockBundle\Stock;
use Hanzo\Model\Orders;
use Hanzo\Model\Products;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class Basket
 *
 * @package Hanzo\Bundle\BasketBundle
 */
class Basket
{
    /**
     * @var Stock
     */
    private $stock;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var Orders
     */
    private $order;

    /**
     * @param Stock                    $stock
     * @param EventDispatcherInterface $eventDispatcher
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(Stock $stock, EventDispatcherInterface $eventDispatcher, SecurityContextInterface $securityContext)
    {
        $this->stock           = $stock;
        $this->eventDispatcher = $eventDispatcher;
        $this->securityContext = $securityContext;
    }

    /**
     * @param Orders $order
     *
     * @throws InvalidOrderStateException
     */
    public function setOrder(Orders $order)
    {
        if ($order->getState() >= Orders::STATE_PRE_PAYMENT) {
            throw new InvalidOrderStateException('order.state_pre_payment.locked');
        }

        $this->order = $order;
    }

    /**
     * @param Products $product
     * @param int      $quantity
     *
     * @return mixed
     * @throws OutOfStockException
     * @throws InvalidSessionException
     * @throws \InvalidArgumentException
     */
    public function addProduct(Products $product, $quantity = 1)
    {
        if (false === $this->stock->check($product, $quantity)) {
            throw new OutOfStockException('product.out.of.stock');
        }

        if (!$this->order instanceof Orders) {
            throw new \InvalidArgumentException('order.object.not.set');
        }

        $date = $this->stock->decrease($product, $quantity);

        $this->order->setOrderLineQty($product, $quantity, false, $date);
        $this->order->setUpdatedAt(time());

        try {
            $this->order->save();
            $this->eventDispatcher->dispatch('basket.product.post_add', new BasketEvent($this->order, $product, $quantity));
        } catch (\PropelException $e) {
            throw new InvalidSessionException('user.session.invalid');
        }

        return $date;
    }

    /**
     * @return int
     * @throws \Exception
     * @throws \PropelException
     */
    public function flush()
    {
        if (!$this->order instanceof Orders) {
            throw new \InvalidArgumentException('order.object.not.set');
        }

        $this->order->clearOrdersLiness();

        return $this->order->save();
    }
}

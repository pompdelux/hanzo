<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\PromotionBundle\Event\Listener;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Hanzo\Bundle\StockBundle\Stock;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsQuery;

/**
 * Class OnFinalizeOrderEvent
 *
 * @package Hanzo\Bundle\PromotionBundle\Event\Listener
 */
class OnFinalizeOrderEvent
{
    /**
     * @var Stock
     */
    private $stock;

    /**
     * @param Stock $stock
     */
    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    /**
     * @param FilterOrderEvent $event
     */
    public function listener(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $this->xmas2104BigBag($order);
    }

    /**
     * Add a "POMP BIG BAG" (id: 12299) to orders between 7/12 ad 11/12 2014
     *
     * @param Orders $order
     *
     * @return mixed
     */
    private function xmas2104BigBag(Orders $order)
    {
        $date = $order->getCreatedAt('YmdHi');

        // order date must be between 7/12 2014 and 11/12 2014 - both days incl.
        if (($date < '201412070000') ||
            ($date > '201412112359')
        ) {
//            return;
        }

        // if the promotion gift is already on the order, distribute the discount accordingly
        $criteria = new \Criteria();
        $criteria->add(OrdersLinesPeer::PRODUCTS_ID, 12299);
        $criteria->add(OrdersLinesPeer::TYPE, 'product');
        $lines = $order->getOrdersLiness($criteria, $order->getDBConnection());

        if ($lines->count()) {
            /** @var \Hanzo\Model\OrdersLines $line */
            $line = $lines->getFirst();
            if ($line->getQuantity() == 1) {
                $line->setPrice(0.00);
            } else {
                $price = $line->getOriginalPrice();
                $pct   = (100 / $line->getQuantity());

                $line->setPrice($price - ($price / 100 * $pct));
            }

            $line->save();

            return;
        }

        $product = ProductsQuery::create()
            ->findOneById(12299);

        // only apply
        if (false === $this->stock->check($product, 1)) {
            return;
        }

        $date = $this->stock->decrease($product, 1);

        $price  = ProductsDomainsPricesPeer::getProductsPrices([$product->getId()]);
        $price  = array_shift($price);
        $oPrice = $price['normal'];
        $price  = array_shift($price);

        $line = new OrdersLines();
        $line->setOrdersId($order->getId());
        $line->setProductsId($product->getId());
        $line->setProductsName('POMP BIG BAG');
        $line->setProductsSku($product->getSku());
        $line->setProductsColor($product->getColor());
        $line->setProductsSize($product->getSize());
        $line->setQuantity(1);
        $line->setPrice(0.00);
        $line->setOriginalPrice($oPrice['price']);
        $line->setVat($price['vat']);
        $line->setType('product');
        $line->setUnit('Stk.');
        $line->setExpectedAt($date);
        $order->addOrdersLines($line);
    }
}

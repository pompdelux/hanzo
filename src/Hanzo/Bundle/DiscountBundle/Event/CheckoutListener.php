<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Propel;
use PropelCollection;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLinesQuery;

use Hanzo\Model\ProductsDomainsPricesPeer;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class CheckoutListener
{
    public function __construct() {}

    public function onFinalize(FilterOrderEvent $event)
    {
Tools::log(__METHOD__);
        $order = $event->getOrder();

        $customer = $order->getCustomers();
        $hanzo    = Hanzo::getInstance();

        $discount = 0;

        // apply group and private discounts if discounts is not disabled
        if (0 == $hanzo->get('webshop.disable_discounts')) {
            if ($customer->getDiscount()) {
                $discount_label = 'discount.private';
                $discount = $customer->getDiscount();
            } else {
                if ($customer->getGroups()) {
                    $discount_label = 'discount.group';
                    $discount = $customer->getGroups()->getDiscount();
                }
            }
        }

        if ($discount <> 0.00) {
            // // we do not stack discounts, so we need to recalculate the orderlines
            // $lines = $order->getOrdersLiness();

            // $product_ids = array();
            // foreach ($lines as $line) {
            //     if('product' == $line->getType()) {
            //         $product_ids[] = $line->getProductsId();
            //     }
            // }

            // OrdersLinesQuery::create()
            //     ->filterByOrdersId($order->getId())
            //     ->delete()
            // ;

            // $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);
            // $collection = new PropelCollection();

            // foreach ($lines as $line) {
            //     if('product' == $line->getType()) {
            //         $price = $prices[$line->getProductsId()];

            //         $line->setPrice($price['normal']['price']);
            //         $line->setVat($price['normal']['vat']);
            //         $line->setOriginalPrice($price['normal']['price']);
            //     }

            //     $collection->prepend($line);
            // }

            // $order->setOrdersLiness($collection);

            $total = $order->getTotalProductPrice();

            // so far _all_ discounts are handled as % discounts
            $discount_amount = ($total / 100) * $discount;
            $order->setDiscountLine($discount_label, $discount_amount, $discount);
        }
    }
}

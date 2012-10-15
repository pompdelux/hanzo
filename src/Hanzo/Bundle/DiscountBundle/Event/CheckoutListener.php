<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class CheckoutListener
{
    public function __construct() {}

    public function onFinalize(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        $customer = $order->getCustomers();
        $hanzo    = Hanzo::getInstance();
        $discount = 0;

        if (0 == $hanzo->get('webshop.disable_discounts')) {
            if ($customer->getDiscount()) {
                $discount       = $customer->getDiscount();
                $discount_label = 'discount.private';
            } else {
                $discount       = $customer->getGroups()->getDiscount();
                $discount_label = 'discount.group';
            }
        }

        if ($discount <> 0.00) {
            $total = $order->getTotalProductPrice();

            // so far _all_ discounts are handled as % discounts
            $discount_amount = ($total / 100) * $discount;
            $order->setDiscountLine($discount_label, $discount_amount, $discount);
        }
    }
}

<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Propel;
use PropelCollection;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\GiftCards;
use Hanzo\Model\GiftCardsPeer;
use Hanzo\Model\GiftCardsQuery;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Symfony\Bridge\Monolog\Logger;

class CheckoutListener
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onFinalize(FilterOrderEvent $event)
    {
        $order    = $event->getOrder();
        $customer = $order->getCustomers();

        // strange edge case, but way better than a fatal error.
        if (!$customer instanceof Customers) {
            return;
        }

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

        // we do not stack discounts, so we need to recalculate the orderlines
        if ($discount <> 0.00) {
            $lines = $order->getOrdersLiness();

            $product_ids = array();
            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $product_ids[] = $line->getProductsId();
                }
            }
            $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

            $total = 0;
            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $price = $prices[$line->getProductsId()];

                    $line->setPrice($price['normal']['price']);
                    $line->setVat($price['normal']['vat']);
                    $line->setOriginalPrice($price['normal']['price']);

                    $total += ($line->getPrice() * $line->getQuantity());
                }
            }

            // so far _all_ discounts are handled as % discounts
            $discount_amount = ($total / 100) * $discount;
            $order->setDiscountLine($discount_label, $discount_amount, $discount);
        }


    }


    /**
     * Register GiftCards if any is bought.
     *
     * @param  FilterOrderEvent $event event object
     */
    public function onPaymentCollected(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->getState() < Orders::STATE_PAYMENT_OK ) {
            $this->logger->addDebug('Could not process order, state is: '.$order->getState());
            return;
        }

        $lines = $order->getOrdersLiness();

        $from_ts = new \DateTime();
        $to_ts   = $from_ts->modify('+3 years');

        foreach ($lines as $line) {
            if ($line->getIsVoucher()) {

                if ($line->getNote()) {
                    $c = explode(';', $line->getNote());
                    GiftCardsQuery::create()
                        ->filterById($c)
                        ->delete();
                }

                $codes = [];
                for ($i=0;$i<$line->getQuantity();$i++) {

                    $gift_card = new GiftCards();
                    $gift_card->setCode(GiftCardsPeer::generateCode());
                    $gift_card->setAmount($line->getPrice());
                    $gift_card->setActiveFrom($from_ts);
                    $gift_card->setActiveTo($to_ts);
                    $gift_card->setCurrencyCode($order->getCurrencyCode());
                    $gift_card->save();

                    $codes[] = $gift_card->getCode();
                }

                $line->setNote(implode(';', $codes));
                $line->save();
            }
        }
    }
}

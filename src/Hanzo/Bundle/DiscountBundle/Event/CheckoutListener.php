<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Hanzo\Bundle\DiscountBundle\Handlers\CouponHandler;
use Hanzo\Bundle\DiscountBundle\Handlers\PersonalDiscountHandler;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;
use Hanzo\Model\GiftCards;
use Hanzo\Model\GiftCardsPeer;
use Hanzo\Model\GiftCardsQuery;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Symfony\Bridge\Monolog\Logger;

class CheckoutListener
{
    protected $logger;
    protected $coupon_handler;
    protected $personal_discount_handler;

    public function __construct(Logger $logger, CouponHandler $coupon_handler, PersonalDiscountHandler $personal_discount_handler)
    {
        $this->logger                    = $logger;
        $this->coupon_handler            = $coupon_handler;
        $this->personal_discount_handler = $personal_discount_handler;
    }

    public function onFinalize(FilterOrderEvent $event)
    {
        $order    = $event->getOrder();
        $customer = $order->getCustomers();

        // strange edge case, but way better than a fatal error.
        if (!$customer instanceof Customers) {
            return;
        }

        $order = $this->personal_discount_handler->initialize($order)->handle();
        $order = $this->coupon_handler->initialize($order)->handle();
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

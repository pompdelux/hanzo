<?php

namespace Hanzo\Bundle\DiscountBundle\Event;

use Hanzo\Bundle\DiscountBundle\Handlers\CouponHandler;
use Hanzo\Bundle\DiscountBundle\Handlers\PersonalDiscountHandler;
use Hanzo\Bundle\DiscountBundle\Handlers\QuantityDiscountHandler;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;
use Hanzo\Model\GiftCards;
use Hanzo\Model\GiftCardsPeer;
use Hanzo\Model\GiftCardsQuery;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Symfony\Bridge\Monolog\Logger;

class CheckoutListener
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var CouponHandler
     */
    protected $couponHandler;

    /**
     * @var PersonalDiscountHandler
     */
    protected $personalDiscountHandler;

    /**
     * @var QuantityDiscountHandler
     */
    protected $quantityDiscountHandler;

    /**
     * CheckoutListener constructor.
     *
     * @param Logger                  $logger
     * @param CouponHandler           $couponHandler
     * @param PersonalDiscountHandler $personalDiscountHandler
     * @param QuantityDiscountHandler $quantityDiscountHandler
     */
    public function __construct(Logger $logger, CouponHandler $couponHandler, PersonalDiscountHandler $personalDiscountHandler, QuantityDiscountHandler $quantityDiscountHandler)
    {
        $this->logger                  = $logger;
        $this->couponHandler           = $couponHandler;
        $this->personalDiscountHandler = $personalDiscountHandler;
        $this->quantityDiscountHandler = $quantityDiscountHandler;
    }

    /**
     * @param FilterOrderEvent $event
     */
    public function onFinalize(FilterOrderEvent $event)
    {
        $order    = $event->getOrder();
        $customer = $order->getCustomers();

        // strange edge case, but way better than a fatal error.
        if (!$customer instanceof Customers) {
            return;
        }

        $order = $this->personalDiscountHandler->initialize($order)->handle();
        $order = $this->quantityDiscountHandler->initialize($order)->reCalculate();
        $order = $this->couponHandler->initialize($order)->handle();
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

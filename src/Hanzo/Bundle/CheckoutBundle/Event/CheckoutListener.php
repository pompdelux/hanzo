<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Core\Tools;
use Hanzo\Bundle\ServiceBundle\Services\MailService;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CheckoutListener
{
    protected $mailer;

    public function __construct(MailService $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onPaymentCollected(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        // send confirmation mail to customer and bcc to store owner

        $email = $order->getEmail();
        $name  = trim($order->getFirstName() . ' ' . $order->getLastName());


        $params = array(
            'order' => $order,
            'payment_address' => Tools::orderAddress('payment', $order),
            'company_address' => '',
            'delivery_address' => Tools::orderAddress('shipping', $order),
            'customer_id' => $order->getCustomersId(),
            'order_date' => $order->getCreatedAt('Y-m-d'),
            'payment_method' => $order->getBillingMethod(),
            'shipping_title' => $order->getShippingMethod(),
            'shipping_cost' => '', // todo
            'payment_fee' => '', // todo
            'expected_at' => '', // todo
            'username' => $order->getCustomers()->getEmail(),
            'password' => $order->getCustomers()->getPasswordClear(),
            'conditions' => '', // todo
        );

        // only set if not null
        $params['event_id'] = '';
        $params['card_type'] = '';
        $params['transaction_id'] = '';
        $params['payment_gateway_id'] = '';
        $params['coupon_amount'] = '';
        $params['coupon_name'] = '';
        $params['hostess_discount'] = '';
        $params['hostess_discount_title'] = '';
        $params['gothia_fee'] = '';
        $params['gothia_fee_title'] = '';

        try {
            $this->mailer->setMessage('account.create', $params);
            $this->mailer->setTo($email, $name);
            $this->mailer->send();
        } catch (\Swift_TransportException $e) {
            Tools::log($e->getMessage());
        }

        // trigger ax sync
        Tools::log(get_class($this->mailer));
    }
}

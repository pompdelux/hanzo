<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Bundle\ServiceBundle\Services\AxService;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CheckoutListener
{
    protected $mailer;
    protected $ax;
    protected $translator;

    public function __construct(MailService $mailer, AxService $ax, Translator $translator)
    {
        $this->mailer = $mailer;
        $this->ax = $ax;
        $this->translator = $translator;
    }

    public function onPaymentCollected(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->getState() < Orders::STATE_PAYMENT_OK ) {
            // woopsan!
            return;
        }

        $order->setState( Orders::STATE_PENDING );

        $attributes = $order->getAttributes();
        $email = $order->getEmail();
        $name  = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shipping_title = $this->translator->trans('shipping_method_name_' . $order->getDeliveryMethod(), array(), 'shipping');

        $shipping_cost = 0.00;
        foreach ($order->getOrderLineShipping() as $line) {
            $shipping_cost += $line->getPrice();
        }

        $card_type = '';
        if (isset($attributes->payment->cc_type)) {
            $card_type = $attributes->payment->cc_type;
        }

        $params = array(
            'order' => $order,
            'payment_address' => Tools::orderAddress('payment', $order),
            'company_address' => '',
            'delivery_address' => Tools::orderAddress('shipping', $order),
            'customer_id' => $order->getCustomersId(),
            'order_date' => $order->getCreatedAt('Y-m-d'),
            'payment_method' => $order->getBillingMethod(),
            'shipping_title' => $shipping_title,
            'shipping_cost' => $shipping_cost,
            'payment_fee' => $order->getPaymentFee(),
            'expected_at' => $order->getExpectedDeliveryDate(),
            'username' => $order->getCustomers()->getEmail(),
            'password' => $order->getCustomers()->getPasswordClear(),
            'card_type' => $card_type,
        );

        if (isset($attributes->event->id)) {
            $params['event_id'] = $attributes->event->id;
        }

        if (isset($attributes->payment->transaction_id)) {
            $params['transaction_id'] = $attributes->payment->transaction_id;
        }

        if ( !is_null($order->getPaymentGatewayId()) ) {
          $params['payment_gateway_id'] = $order->getPaymentGatewayId();
        }


        if (isset($attributes->coupon->amount)) {
            $params['coupon_amount'] = $attributes->coupon->amount;
            $params['coupon_name'] = $attributes->coupon->text;
        }


        foreach ($order->getOrdersLiness() as $line) {
            if ($line->gettype('discount') && $line->getProductsSku() == 'hostess_discount')
            $params['hostess_discount'] = $line->getPrice();
            $params['hostess_discount_title'] = $line->getProductsName();

            if ($line->getType('payment.fee') && $line->getProductsName() == 'gothia') // or Sku == 91 ?
            {
              $params['gothia_fee'] = $line->getPrice();
              $params['gothia_fee_title'] = $this->translator->trans('payment.fee.gothia.title',array(),'checkout');
            }
        }

        // Handle payment canceling of old order
        if ($order->getInEdit()) {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if (!($currentVersion < 2)) {
                $oldOrderVersion = ( $currentVersion - 1);
                $oldOrder = $order->getOrderAtVersion($oldOrderVersion);
                $oldOrder->cancelPayment();
            }
        }

        try {
            $this->mailer->setMessage('order.confirmation', $params);
            $this->mailer->setTo($email, $name);
            // NICETO: not hardcoded
            $this->mailer->setBcc('order@pompdelux.dk');
            $this->mailer->send();
        } catch (\Swift_TransportException $e) {
            Tools::log($e->getMessage());
        }

        // trigger ax sync
        $this->ax->sendOrder($order);

        $order->setInEdit(false);
        $order->save();
    }
}

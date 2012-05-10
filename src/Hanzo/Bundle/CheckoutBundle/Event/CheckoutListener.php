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

        $email = $order->getEmail();
        $name  = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shippinng = $this->translator->trans('shipping_method_name_' . $order->getDeliveryMethod(), array(), 'shipping');

        $params = array(
            'order' => $order,
            'payment_address' => Tools::orderAddress('payment', $order),
            'company_address' => '',
            'delivery_address' => Tools::orderAddress('shipping', $order),
            'customer_id' => $order->getCustomersId(),
            'order_date' => $order->getCreatedAt('Y-m-d'),
            'payment_method' => $order->getBillingMethod(),
            'shipping_title' => $shippinng,
            'shipping_cost' => 0.00, // TODO
            'payment_fee' => 0.00, // TODO
            'expected_at' => '', // TODO
            'username' => $order->getCustomers()->getEmail(),
            'password' => $order->getCustomers()->getPasswordClear(),
            'conditions' => '', // TODO
            'expected_at' => '',
            'card_type' => '',
            'transaction_id' => '',
        );

        // TODO: only set if not null
        if(0){
            $params['event_id'] = '';
            $params['payment_gateway_id'] = '';
            $params['coupon_amount'] = 0.00;
            $params['coupon_name'] = '';
            $params['hostess_discount'] = '';
            $params['hostess_discount_title'] = '';
            $params['gothia_fee'] = 0.00;
            $params['gothia_fee_title'] = '';
        }

        // Handle payment canceling of old order
        if ( $order->getInEdit() )
        {
          $currentVersion = $order->getVersionId();

          if ( !( $currentVersion < 2 ) ) // If the version number is less than 2 there is no previous version
          {
            $oldOrderVersion = ( $currentVersion - 1);
            $oldOrder = $order->getOrderAtVersion($oldOrderVersion);
            $oldOrder->cancelPayment();
          }
        }

        try {
            $this->mailer->setMessage('order.confirmation', $params);
            $this->mailer->setTo($email, $name);
            // TODO: send confirmation mail to customer and bcc to store owner
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

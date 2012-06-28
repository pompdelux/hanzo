<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Core\Hanzo;
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

    /**
     * onPaymentFailed
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function onPaymentFailed(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $host  = gethostname();
        $hanzo = Hanzo::getInstance();
        $session = $hanzo->getSession();
        $domainKey = $hanzo->get('core.domain_key');

        $message = 'Order id: '.$order->getID().'<br>
            Order id i session: '. $session->get('order_id') .'<br>
            Kunde navn: '. $order->getFirstName() .' '. $order->getLastName() .'<br> 
            Kunde email: '. $order->getEmail() .'<br> 
            Host name: '.$host.'<br>
            Domain key: '.$domainKey.'<br>
            Order state: '. $order->getState() .'<br>
            Billing method: '. $order->getBillingMethod() .'<br>
            In edit: '. $order->getInEdit() .'<br>
            ';

        Tools::log('Payment failed: '.str_replace('<br>',"", $message ));

        try
        {
            $this->mailer->setSubject( sprintf('[FEJL] Ordre nr: %d fejlede', $order->getId()) )
            ->setBody('Beskeden er i HTML format')
            ->addPart($message,'text/html')
            ->setTo( 'hd@pompdelux.dk' , 'Mr. HD' )
            ->setCc( 'hf@bellcom.dk', 'Mr. HF' )
            ->send();
        } 
        catch (\Swift_TransportException $e) 
        {
            Tools::log($e->getMessage());
        }
    }

    public function onPaymentCollected(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->getState() < Orders::STATE_PAYMENT_OK ) {
            error_log(__LINE__.':'.__FILE__.' Could not sync order, state is: '.$order->getState()); // hf@bellcom.dk debugging
            // woopsan!
            return;
        }

        // need copy for later
        $in_edit = $order->getInEdit();

        $order->setState( Orders::STATE_PENDING );
        $order->setInEdit(false);
        $order->setSessionId($order->getId());
        $order->save();

        $attributes = $order->getAttributes();
        $email = $order->getEmail();
        $name  = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shipping_title = $this->translator->trans('shipping_method.name.' . $order->getDeliveryMethod(), array(), 'shipping');

        $shipping_cost = 0.00;
        $shipping_fee = 0.00;
        foreach ($order->getOrderLineShipping() as $line)
        {
            switch ($line->getType())
            {
              case 'shipping':
                $shipping_cost += $line->getPrice();
                break;
              case 'shipping.fee':
                $shipping_fee += $line->getPrice();
                break;
            }
        }

        $card_type = '';
        if (isset($attributes->payment->paytype)) {
            switch ($attributes->payment->paytype)
            {
              case 'V-DK':
                  $card_type = 'VISA/DanKort';
                break;
              case 'DK':
                  $card_type = 'DanKort';
                  break;
              case 'MC':
              case 'MC(DK)':
              case 'MC(SE)':
                  $card_type = 'MasterCard';
                  break;
              case 'VISA':
                  $card_type ='Visa';
                  break;
              case 'ELEC':
                  $card_type = 'Visa Electron';
                  break;
            }
        }

        // hf@bellcom.dk, 13-jun-2012: hack... I'm tired -->>
        $company_address = $this->translator->trans('store.address',array());
        $company_address = str_replace( ' · ', "\n", $company_address );
        // <<-- hf@bellcom.dk, 13-jun-2012: hack... I'm tired

        $params = array(
            'order' => $order,
            'payment_address' => Tools::orderAddress('payment', $order),
            'company_address' => $company_address,
            'delivery_address' => Tools::orderAddress('shipping', $order),
            'customer_id' => $order->getCustomersId(),
            'order_date' => $order->getCreatedAt('Y-m-d'),
            'payment_method' => $order->getBillingMethod(),
            'shipping_title' => $shipping_title,
            'shipping_cost' => $shipping_cost,
            'payment_fee' => $order->getPaymentFee(),
            'shipping_fee' => $shipping_fee,
            'expected_at' => $order->getExpectedDeliveryDate( 'd-m-Y' ),
            'username' => $order->getCustomers()->getEmail(),
            'password' => $order->getCustomers()->getPasswordClear(),
            'card_type' => $card_type,
        );

        if (isset($attributes->event->id)) {
            $params['event_id'] = $attributes->event->id;
        }

        if (isset($attributes->payment->transact)) {
            $params['transaction_id'] = $attributes->payment->transact;
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
            {
                $params['hostess_discount'] = $line->getPrice();
                $params['hostess_discount_title'] = $line->getProductsName();
            }

            if ($line->getType('payment.fee') && $line->getProductsName() == 'gothia') // or Sku == 91 ?
            {
                $params['gothia_fee'] = $line->getPrice();
                $params['gothia_fee_title'] = $this->translator->trans('payment.fee.gothia.title',array(),'checkout');
            }
        }

        // Handle payment canceling of old order
        if ($in_edit) {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if (!($currentVersion < 2)) {
                $oldOrderVersion = ( $currentVersion - 1);
                $oldOrder = $order->getOrderAtVersion($oldOrderVersion);
                try 
                {
                  $oldOrder->cancelPayment();
                }
                catch (\Exception $e)
                {
                  Tools::log( 'Could not cancel payment for old order, id: '. $oldOrder->getId() .' error was: '. $e->getMessage());
                }
            }
        }

        try {
            switch ($attributes->global->domain_key) {
                case 'SE':
                    $bcc = 'order@pompdelux.se';
                    break;
                case 'NO':
                    $bcc = 'order@pompdelux.no';
                    break;
                default:
                    $bcc = 'order@pompdelux.dk';
                    break;
            }

            $this->mailer->setMessage('order.confirmation', $params);
            $this->mailer->setTo($email, $name);
            // NICETO: not hardcoded
            $this->mailer->setBcc($bcc);
            $this->mailer->send();
        } catch (\Swift_TransportException $e) {
            Tools::log($e->getMessage());
        }

        // trigger ax sync
        $this->ax->sendOrder($order);
    }
}

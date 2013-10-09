<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Propel;
use PropelCollection;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersSyncLogQuery;

use Hanzo\Bundle\AccountBundle\AddressFormatter;
use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Bundle\AxBundle\Actions\Out\AxService;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;

class CheckoutListener
{
    protected $mailer;
    protected $ax;
    protected $translator;
    protected $session;
    protected $formatter;

    public function __construct(MailService $mailer, AxService $ax, Translator $translator, Session $session, AddressFormatter $formatter)
    {
        $this->mailer     = $mailer;
        $this->ax         = $ax;
        $this->translator = $translator;
        $this->session    = $session;
        $this->formatter  = $formatter;
    }

    /**
     * onPaymentFailed
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function onPaymentFailed(FilterOrderEvent $event)
    {
        if ($this->session->has('failed_order_mail_sent')) {
            return;
        }

        $order     = $event->getOrder();
        $host      = gethostname();
        $hanzo     = Hanzo::getInstance();
        $session   = $hanzo->getSession();
        $domainKey = $hanzo->get('core.domain_key');

        $message =
            '  Order id..........: '.$order->getID()."\n".
            '  Order id i session: '.$session->get('order_id')."\n".
            '  Kunde navn........: '.$order->getFirstName() .' '. $order->getLastName()."\n".
            '  Kunde email.......: '.$order->getEmail()."\n".
            '  Host name.........: '.$host."\n".
            '  Domain key........: '.$domainKey."\n".
            '  Order state.......: '.$order->getState()."\n".
            '  Billing method....: '.$order->getBillingMethod()."\n".
            '  In edit...........: '.$order->getInEdit()."\n".
            ' - - - - - - - - - - - - - - - - '
        ;

        Tools::log("Payment failed:\n".$message);

        $this->session->set('failed_order_mail_sent', true);
    }


    /**
     * Closing order
     * Note, this MUST be the first event triggered!
     *
     * If the order is in a wrong state, propagation is halted.
     *
     * @param  FilterOrderEvent $event
     * @return void
     */
    public function onPaymentCollectedFirst(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->getState() < Orders::STATE_PAYMENT_OK ) {
            $this->logger->addError('Order #'.$order->getId().' was in state "'.$order->getState().'" and was stopped in flow!');
            $event->stopPropagation();
            return;
        }

        // need copy for later
        $event->setInEdit($order->getInEdit());

        $order->setState(Orders::STATE_PENDING);
        $order->setInEdit(false);
        $order->setSessionId($order->getId());
        $order->setUpdatedAt(time());
        $order->save();
    }


    /**
     * Build and send order confirmation to customer
     * Event is triggered last.
     *
     * @param  FilterOrderEvent $event
     * @return void
     */
    public function onPaymentCollected(FilterOrderEvent $event)
    {
        $order   = $event->getOrder();
        $in_edit = $event->getInEdit();

        // build and send order confirmation.
        $attributes     = $order->getAttributes();
        $email          = $order->getEmail();
        $name           = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shipping_title = $this->translator->trans('shipping_method.name.' . $order->getDeliveryMethod(), [], 'shipping');

        $shipping_cost = 0.00;
        $shipping_fee  = 0.00;
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
            switch (strtoupper($attributes->payment->paytype)) {
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
                case 'VISA(SE)':
                case 'VISA(DK)':
                    $card_type ='Visa';
                    break;
                case 'ELEC':
                    $card_type = 'Visa Electron';
                    break;
                case 'PENSIO':
                    if ('IDEALPAYMENT' == strtoupper($attributes->payment->nature)) {
                        $card_type = 'iDEAL';
                    }
                    break;
            }
        }

        $company_address = $this->translator->trans('store.address', []);
        $company_address = str_replace( ' · ', "\n", $company_address );

        $event_id = isset($attributes->global->HomePartyId) ? $attributes->global->HomePartyId : '';

        $params = array(
            'order' => $order,
            'payment_address'  => $this->formatter->format($order->getOrderAddress('payment'), 'txt'),
            'company_address'  => $company_address,
            'delivery_address' => $this->formatter->format($order->getOrderAddress('shipping'), 'txt'),
            'customer_id'      => $order->getCustomersId(),
            'order_date'       => $order->getCreatedAt('Y-m-d'),
            'payment_method'   => $this->translator->trans('payment.'. $order->getBillingMethod() .'.title', [],'checkout'),
            'shipping_title'   => $shipping_title,
            'shipping_cost'    => $shipping_cost,
            'shipping_fee'     => $shipping_fee,
            'expected_at'      => $order->getExpectedDeliveryDate( 'd-m-Y' ),
            'username'         => $order->getCustomers()->getEmail(),
            'password'         => $order->getCustomers()->getPasswordClear(),
            'event_id'         => $event_id,
        );

        // hf@bellcom.dk, 04-sep-2012: only show if > 0 -->>
        $payment_fee = $order->getPaymentFee();
        if ( $payment_fee > 0 ) {
          $params['payment_fee'] = $payment_fee;
        }
        // <<-- hf@bellcom.dk, 04-sep-2012: only show if > 0

        // hf@bellcom.dk, 04-sep-2012: order confirmation checks if card_type is defined, if not, it will use payment_method, e.g. Gothia -->>
        if ( !empty($card_type) ) {
          $params['card_type'] = $card_type;
        }
        // <<-- hf@bellcom.dk, 04-sep-2012: order confirmation checks if card_type is defined, if not, it will use payment_method, e.g. Gothia

        if (isset($attributes->payment->transact)) {
            $params['transaction_id'] = $attributes->payment->transact;
        }

        if ( !is_null($order->getPaymentGatewayId()) ) {
            $params['payment_gateway_id'] = $order->getPaymentGatewayId();
        }

        if (isset($attributes->gift_card->amount)) {
            $params['gift_card_amount'] = $attributes->gift_card->amount;
            $params['gift_card_name'] = $attributes->gift_card->text;
        }

        foreach ($order->getOrdersLiness() as $line) {
            if ('discount' == $line->getType()) {
                if (empty($params['hostess_discount'])) {
                    $params['hostess_discount'] = $line->getPrice();
                    $params['hostess_discount_title'] = $this->translator->trans($line->getProductsSku(), [], 'checkout');
                }
            }

            // or Sku == 91 ?
            if ($line->getType('payment.fee') && $line->getProductsName() == 'gothia') {
                $params['gothia_fee'] = $line->getPrice();
                $params['gothia_fee_title'] = $this->translator->trans('payment.fee.gothia.title', [], 'checkout');

                // hf@bellcom.dk, 27-aug-2012: currently payment.fee is gothia fee, so to avoid 2 lines on the confirmation mail, payment_fee is unset here -->>
                if (isset($params['payment_fee'])) {
                  unset( $params['payment_fee'] );
                }
                // <<-- hf@bellcom.dk, 27-aug-2012: currently payment.fee is gothia fee, so to avoid 2 lines on the confirmation mail, payment_fee is unset here
            }
        }

        // close event if this is the hostess purchase.
        if ($order->getEventsId() && isset($attributes->event->is_hostess_order)) {
            $event = $order->getEvents();
            $event->setIsOpen(false);
            $event->save();
        }

        // Handle payment canceling of old order
        if ($in_edit && ('gothia' !== $order->getBillingMethod())) {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if (!($currentVersion < 2)) {
                $oldOrderVersion = ( $currentVersion - 1);
                $oldOrder = $order->getOrderAtVersion($oldOrderVersion);

                try {
                    $oldOrder->cancelPayment();
                } catch (\Exception $e) {
                    Tools::log( 'Could not cancel payment for old order, id: '. $oldOrder->getId() .' error was: '. $e->getMessage());
                }
            }
        }

        try {
            $bcc = Tools::getBccEmailAddress('order', $order);

            $this->mailer->setMessage('order.confirmation', $params);
            $this->mailer->setTo($email, $name);
            if ($bcc) {
                $this->mailer->setBcc($bcc);
            }
            $this->mailer->send();
        } catch (\Swift_TransportException $e) {
            Tools::log($e->getMessage());
        }
    }


    public function onFinalize(FilterOrderEvent $event)
    {
        $order    = $event->getOrder();
        $customer = $order->getCustomers();
        $hanzo    = Hanzo::getInstance();

        // if for some reason a shipping method without data is set, cleanup.
        if ($order->getDeliveryMethod() && ('' == $order->getDeliveryFirstName())) {
            OrdersLinesQuery::create()
                ->filterByType('shipping')
                ->_or()
                ->filterByType('shipping.fee')
                ->filterByOrdersId($order->getId())
                ->delete()
            ;
            $order->setDeliveryMethod(null);

            // ??? maby this is not so safe after all..
            $order->setBillingMethod(null);
            $order->clearPaymentAttributes();
        }

        // set once, newer touch agian
        $domain_key = $hanzo->get('core.domain_key');
        if (!$order->getInEdit() && (false === strpos($domain_key, 'Sales'))) {
            $order->setAttribute('HomePartyId', 'global', 'WEB ' . $domain_key);
            $order->setAttribute('SalesResponsible', 'global', 'WEB ' . $domain_key);
        }
    }
}

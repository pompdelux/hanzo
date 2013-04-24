<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Propel;
use PropelCollection;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersSyncLogQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;

use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Bundle\ServiceBundle\Services\AxService;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;

class CheckoutListener
{
    protected $mailer;
    protected $ax;
    protected $translator;
    protected $session;

    public function __construct(MailService $mailer, AxService $ax, Translator $translator, Session $session)
    {
        $this->mailer     = $mailer;
        $this->ax         = $ax;
        $this->translator = $translator;
        $this->session    = $session;
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

        $this->session->set('failed_order_mail_sent', true);
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
        $order->setUpdatedAt(time());
        $order->save();

        // trigger ax sync
        $this->ax->sendOrder($order);

        // ONLY send email and cancel payments if the order is logged !
        $logged = OrdersSyncLogQuery::create()
            ->select('State')
            ->filterByOrdersId($order->getId())
            ->findOne(Propel::getConnection(null, Propel::CONNECTION_WRITE))
        ;
        if (!$logged) {
            return;
        }

        // build and send order confirmation.

        $attributes = $order->getAttributes();
        $email = $order->getEmail();
        $name  = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shipping_title = $this->translator->trans('shipping_method.name.' . $order->getDeliveryMethod(), [], 'shipping');

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
            'payment_address'  => Tools::orderAddress('payment', $order),
            'company_address'  => $company_address,
            'delivery_address' => Tools::orderAddress('shipping', $order),
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

        if (isset($attributes->coupon->amount)) {
            $params['coupon_amount'] = $attributes->coupon->amount;
            $params['coupon_name'] = $attributes->coupon->text;
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
        $order = $event->getOrder();

        $customer = $order->getCustomers();
        $hanzo = Hanzo::getInstance();

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
            // we do not stack discounts, so we need to recalculate the orderlines
            $lines = $order->getOrdersLiness();

            $product_ids = array();
            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $product_ids[] = $line->getProductsId();
                }
            }

            $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);
            $collection = new PropelCollection();

            foreach ($lines as $line) {
                if('product' == $line->getType()) {
                    $price = $prices[$line->getProductsId()];

                    $line->setPrice($price['normal']['price']);
                    $line->setVat($price['normal']['vat']);
                    $line->setOriginalPrice($price['normal']['price']);
                }

                $collection->prepend($line);
            }

            $order->setOrdersLiness($collection);

            $total = $order->getTotalProductPrice();

            // so far _all_ discounts are handled as % discounts
            $discount_amount = ($total / 100) * $discount;
            $order->setDiscountLine($discount_label, $discount_amount, $discount);
        }

        $domain_key = $hanzo->get('core.domain_key');

        // set once, newer touch agian
        if (!$order->getInEdit() && (false === strpos($domain_key, 'Sales'))) {
            $order->setAttribute('HomePartyId', 'global', 'WEB ' . $domain_key);
            $order->setAttribute('SalesResponsible', 'global', 'WEB ' . $domain_key);
        }
    }
}

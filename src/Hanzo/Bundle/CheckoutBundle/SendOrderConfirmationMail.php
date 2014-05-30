<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\CheckoutBundle;

use Hanzo\Core\Tools;
use Hanzo\Bundle\AccountBundle\AddressFormatter;
use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Model\Orders;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class SendOrderConfirmationMail
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    private $translator;

    /**
     * @var \Hanzo\Bundle\ServiceBundle\Services\MailService
     */
    private $mail_service;

    /**
     * @var \Hanzo\Bundle\AccountBundle\AddressFormatter
     */
    private $address_formatter;

    /**
     * @var bool
     */
    private $is_mail_build = false;

    /**
     * @param Translator       $translator
     * @param MailService      $mail_service
     * @param AddressFormatter $address_formatter
     */
    public function __construct(Translator $translator, MailService $mail_service, AddressFormatter $address_formatter)
    {
        $this->translator        = $translator;
        $this->mail_service      = $mail_service;
        $this->address_formatter = $address_formatter;
    }

    /**
     * Send the confirmation mail build with the build() method.
     * @throws \BuildException
     */
    public function send()
    {
        if (false === $this->is_mail_build) {
            throw new \BuildException("Confirmation mail must be build before it can be send !");
        }

        try {
            $this->mail_service->send();
        } catch (\Swift_TransportException $e) {
            Tools::log($e->getMessage());
        }
    }

    /**
     * Build confirmation mail from order and template.
     *
     * @param Orders $order
     */
    public function build(Orders $order)
    {
        // build and send order confirmation.
        $attributes     = $order->getAttributes();
        $email          = $order->getEmail();
        $name           = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shipping_title = $this->translator->trans('shipping_method.name.' . $order->getDeliveryMethod(), [], 'shipping');

        $shipping_cost = 0.00;
        $shipping_fee  = 0.00;
        foreach ($order->getOrderLineShipping() as $line) {
            switch ($line->getType()) {
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

        foreach ($order->getOrdersLiness() as $line) {
            $line->setProductsSize($line->getPostfixedSize($this->translator));
        }

        $params = array(
            'order'            => $order,
            'payment_address'  => $this->address_formatter->format($order->getOrderAddress('payment'), 'txt'),
            'company_address'  => $company_address,
            'delivery_address' => $this->address_formatter->format($order->getOrderAddress('shipping'), 'txt'),
            'customer_id'      => $order->getCustomersId(),
            'order_date'       => $order->getCreatedAt('Y-m-d'),
            'payment_method'   => $this->translator->trans('payment.'. $order->getBillingMethod() .'.title', [], 'checkout'),
            'shipping_title'   => $shipping_title,
            'shipping_cost'    => $shipping_cost,
            'shipping_fee'     => $shipping_fee,
            'expected_at'      => $order->getExpectedDeliveryDate( 'd-m-Y' ),
            'username'         => $order->getCustomers()->getEmail(),
            'password'         => $order->getCustomers()->getPasswordClear(),
            'event_id'         => $event_id,
        );

        $payment_fee = $order->getPaymentFee();
        if ($payment_fee > 0) {
            $params['payment_fee'] = $payment_fee;
        }

        if (!empty($card_type)) {
            $params['card_type'] = $card_type;
        }

        if (isset($attributes->payment->transact)) {
            $params['transaction_id'] = $attributes->payment->transact;
        }

        if (!is_null($order->getPaymentGatewayId())) {
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

                if (isset($params['payment_fee'])) {
                    unset($params['payment_fee']);
                }
            }
        }

        $bcc = Tools::getBccEmailAddress('order', $order);

        $this->mail_service->setMessage('order.confirmation', $params);
        $this->mail_service->setTo($email, $name);

        if ($bcc) {
            $this->mail_service->setBcc($bcc);
        }

        $this->is_mail_build = true;
    }
}

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
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\Orders;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class SendOrderConfirmationMail
 *
 * @package Hanzo\Bundle\CheckoutBundle
 */
class SendOrderConfirmationMail
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    private $translator;

    /**
     * @var \Hanzo\Bundle\ServiceBundle\Services\MailService
     */
    private $mailService;

    /**
     * @var \Hanzo\Bundle\AccountBundle\AddressFormatter
     */
    private $addressFormatter;

    /**
     * @var bool
     */
    private $isMailBuild = false;

    /**
     * @var null|\PropelPDO
     */
    private $dbConn = null;

    /**
     * @param Translator       $translator
     * @param MailService      $mailService
     * @param AddressFormatter $addressFormatter
     */
    public function __construct(Translator $translator, MailService $mailService, AddressFormatter $addressFormatter)
    {
        $this->translator       = $translator;
        $this->mailService      = $mailService;
        $this->addressFormatter = $addressFormatter;
    }


    /**
     * @param \PDO|\PropelPDO $conn
     */
    public function setDBConnection($conn)
    {
        $this->dbConn = $conn;
    }


    /**
     * Send the confirmation mail build with the build() method.
     * @throws \BuildException
     */
    public function send()
    {
        if (false === $this->isMailBuild) {
            throw new \BuildException("Confirmation mail must be build before it can be send !");
        }

        try {
            $this->mailService->send();
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
        $locale = $this->getLocale($order);

        // needs locale to be set to the correct locale to have correct currency labels.
        setLocale(LC_ALL, $locale.'.utf-8', $locale.'.utf8');
        if ('EUR' == $order->getCurrencyCode()) {
            setLocale(LC_MONETARY, 'nl_NL.utf8', 'nl_NL.utf-8');
        }

        // build and send order confirmation.
        $attributes     = $order->getAttributes($this->dbConn);
        $email          = $order->getEmail();
        $name           = trim($order->getFirstName() . ' ' . $order->getLastName());
        $shippingTitle  = $this->translator->trans('shipping_method.name.' . $order->getDeliveryMethod(), [], 'shipping', $locale);

        $shippingCost = 0.00;
        $shippingFee  = 0.00;
        foreach ($order->getOrderLineShipping($this->dbConn) as $line) {
            switch ($line->getType()) {
                case 'shipping':
                    $shippingCost += $line->getPrice();
                    break;
                case 'shipping.fee':
                    $shippingFee += $line->getPrice();
                    break;
            }
        }

        $cardType = '';
        if (isset($attributes->payment->paytype)) {
            switch (strtoupper($attributes->payment->paytype)) {
                case 'V-DK':
                    $cardType = 'VISA/DanKort';
                    break;
                case 'DK':
                    $cardType = 'DanKort';
                    break;
                case 'MC':
                case 'MC(DK)':
                case 'MC(SE)':
                    $cardType = 'MasterCard';
                    break;
                case 'VISA':
                case 'VISA(SE)':
                case 'VISA(DK)':
                    $cardType ='Visa';
                    break;
                case 'ELEC':
                    $cardType = 'Visa Electron';
                    break;
                case 'PENSIO':
                    if ('IDEALPAYMENT' == strtoupper($attributes->payment->nature)) {
                        $cardType = 'iDEAL';
                    }
                    break;
            }
        }

        $companyAddress = $this->translator->trans('store.address', [], null, $locale);
        $companyAddress = str_replace(' · ', "\n", $companyAddress);

        $eventId = isset($attributes->global->HomePartyId) ? $attributes->global->HomePartyId : '';

        foreach ($order->getOrdersLiness(null, $this->dbConn) as $line) {
            $line->setProductsSize($line->getPostfixedSize($this->translator));
        }

        $params = [
            'order'            => $order,
            'payment_address'  => $this->addressFormatter->format($order->getOrderAddress('payment'), 'txt', $locale),
            'company_address'  => $companyAddress,
            'delivery_address' => $this->addressFormatter->format($order->getOrderAddress('shipping'), 'txt', $locale),
            'customer_id'      => $order->getCustomersId(),
            'order_date'       => $order->getCreatedAt('Y-m-d'),
            'payment_method'   => $this->translator->trans('payment.'. $order->getBillingMethod() .'.title', [], 'checkout', $locale),
            'shipping_title'   => $shippingTitle,
            'shipping_cost'    => $shippingCost,
            'shipping_fee'     => $shippingFee,
            'expected_at'      => $order->getExpectedDeliveryDate('d-m-Y'),
            'username'         => $order->getCustomers($this->dbConn)->getEmail(),
            'password'         => $order->getCustomers($this->dbConn)->getPasswordClear(),
            'event_id'         => $eventId,
        ];

        $paymentFee = $order->getPaymentFee();
        if ($paymentFee > 0) {
            $params['payment_fee'] = $paymentFee;
        }

        if (!empty($cardType)) {
            $params['card_type'] = $cardType;
        }

        if (isset($attributes->payment->transact)) {
            $params['transaction_id'] = $attributes->payment->transact;
        }

        if (!is_null($order->getPaymentGatewayId())) {
            $params['payment_gateway_id'] = $order->getPaymentGatewayId();
        }

        if (isset($attributes->gift_card->amount)) {
            $params['gift_card_amount'] = $attributes->gift_card->amount;
            $params['gift_card_name']   = $attributes->gift_card->text;
        }

        foreach ($order->getOrdersLiness(null, $this->dbConn) as $line) {
            if ('discount' == $line->getType()) {
                if (empty($params['hostess_discount'])) {
                    $params['hostess_discount'] = $line->getPrice();
                    $params['hostess_discount_title'] = $this->translator->trans($line->getProductsSku(), [], 'checkout', $locale);
                }
            }

            // or Sku == 91 ?
            if ($line->getType('payment.fee') && $line->getProductsName() == 'gothia') {
                $params['gothia_fee'] = $line->getPrice();
                $params['gothia_fee_title'] = $this->translator->trans('payment.fee.gothia.title', [], 'checkout', $locale);

                if (isset($params['payment_fee'])) {
                    unset($params['payment_fee']);
                }
            }
        }

        $bcc = Tools::getBccEmailAddress('order', $order, $this->dbConn);

        $this->mailService->setMessage('order.confirmation', $params, $locale);
        $this->mailService->setTo($email, $name);

        if ($bcc) {
            $this->mailService->setBcc($bcc);
        }

        $this->isMailBuild = true;

        setLocale(LC_ALL, 'da_DK.utf-8', 'da_DK.utf8');
    }

    private function getLocale(Orders $order)
    {
        return LanguagesQuery::create()
            ->select('Locale')
            ->findOneById($order->getLanguagesId());
    }
}

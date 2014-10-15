<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services;

use Hanzo\Core\Tools;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributes;
use Hanzo\Model\OrdersLines;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class SyncSalesOrder
 *
 * @package Hanzo\Bundle\AxBundle
 */
class SyncSalesOrder extends BaseService
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Orders
     */
    private $order;

    /**
     * @var OrdersLines
     */
    private $orderLines;

    /**
     * @var OrdersAttributes
     */
    private $orderAttributes;

    /**
     * @var bool
     */
    private $inEdit = false;

    /**
     * Constructor
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->reset();
    }

    /**
     * Reset object
     */
    public function reset()
    {
        $this->setDBConnection(null);
        $this->setEndPoint('');

        $this->inEdit          = false;
        $this->orderAttributes = null;
        $this->orderLines      = null;
        $this->order           = null;
        $this->data =          [
            'endpointDomain' => '',
            'salesOrder' => [
                'SalesTable' => []
            ]
        ];
    }

    /**
     * Get the object build for AX sync.
     *
     * @return \stdClass
     */
    public function get()
    {
        $this->data['endpointDomain'] = $this->getEndPoint();
        $this->buildMetaData();
        $this->buildSalesLines();
        // extra order lines
        $this->buildPromotions();
        $this->buildGiftCards();
        $this->buildCoupons();

        return $this->data;
    }

    /**
     * Set order instance
     *
     * @param Orders $order
     */
    public function setOrder(Orders $order)
    {
        $this->order = $order;
    }

    /**
     * Set order lines instance
     *
     * @param \PropelObjectCollection $ordersLines
     */
    public function setOrderLines(\PropelObjectCollection $ordersLines)
    {
        $this->orderLines = $ordersLines;
    }

    /**
     * Set order attributes instance
     *
     * @param \PropelObjectCollection $attributes
     */
    public function setOrderAttributes(\PropelObjectCollection $attributes)
    {
        // map attributes
        $attr = [];
        foreach ($attributes as $a) {
            $ns = str_replace([':', '.'], '_', $a->getNs());

            if (empty($attr[$ns])) {
                $attr[$ns] = [];
            }
            $attr[$ns][$a->getCKey()] = $a->getCValue();
        }

        $this->orderAttributes = $attr;
        unset($attributes, $attr);
    }


    /**
     * Set in edit status for the order
     *
     * @param bool $inEdit
     */
    public function setInEdit($inEdit = false)
    {
        $this->inEdit = $inEdit;
    }


    /**
     * Build the meta/header data for the order
     */
    private function buildMetaData()
    {
        $this->data['salesOrder']['SalesTable'] = [
            'CustAccount'             => $this->order->getCustomersId(),
            'EOrderNumber'            => $this->order->getId(),
            'PaymentId'               => $this->getPaymentTransactionId(),
            'HomePartyId'             => $this->getAttribute('global', 'HomePartyId'),
            'SalesResponsible'        => $this->getAttribute('global', 'SalesResponsible'),
            'CurrencyCode'            => $this->order->getCurrencyCode(),
            'SalesName'               => $this->order->getCustomersName(),
            'InvoiceAccount'          => $this->order->getCustomersId(),
            'FreightType'             => $this->order->getDeliveryMethod(),
            'FreightFeeAmt'           => $this->calculateCost(['shipping']),
            'HandlingFeeAmt'          => $this->calculateCost(['shipping.fee', 'payment.fee']),
            'PayByBillFeeAmt'         => $this->calculateCost(['payment']),
            'CustPaymMode'            => $this->getCustPaymMode(),
            'BankAccountNumber'       => $this->getAttribute('payment', 'bank_account_no'),
            'BankId'                  => $this->getAttribute('payment', 'bank_id'),
            'DeliveryDropPointId'     => $this->order->getDeliveryExternalAddressId(),
            'DeliveryCompanyName'     => $this->order->getDeliveryCompanyName(),
            'DeliveryCity'            => $this->order->getDeliveryCity(),
            'DeliveryName'            => $this->order->getDeliveryFullName($this->translator),
            'DeliveryStreet'          => $this->order->getDeliveryAddressLine1(),
            'DeliveryZipCode'         => $this->order->getDeliveryPostalCode(),
            'DeliveryCountryRegionId' => $this->getIso2CountryCode($this->order->getDeliveryCountriesId()),

            // static info
            'Completed'        => 1,
            'HandlingFeeType'  => 90,
            'PayByBillFeeType' => 91,
            'SalesType'        => 'Sales',
            'TransactionType'  => 'Write',
        ];

        // purge empty
        foreach ($this->data['salesOrder']['SalesTable'] as $key => $value) {
            // some needs to be there - and empty ... ??
            if (in_array($key, ['SmoreContactInfo'])) {
                continue;
            }

            if (empty($value)) {
                unset($this->data['salesOrder']['SalesTable'][$key]);
            }
        }

        // only set SalesGroup on event orders.
        if ($this->order->getEventsId() && ($event = $this->order->getEvents($this->getDBConnection()))) {
            $st['SalesGroup'] = $event
                ->getCustomersRelatedByConsultantsId($this->getDBConnection())
                ->getConsultants($this->getDBConnection())
                ->getInitials();
        }
    }

    /**
     * Build SalesLines
     */
    private function buildSalesLines()
    {
        $hostessDiscount = 0.00;
        $lineDiscount    = 0.00;
        $products        = [];

        /** @var \Hanzo\Model\OrdersLines $line */
        foreach ($this->orderLines as $line) {
            switch ($line->getType()) {
                case 'product':
                    $products[] = $line;
                    break;

                case 'discount':
                    switch ($line->getProductsSku()) {
                        case 'discount.hostess':
                            $hostessDiscount = $line->getPrice();
                            break;
                        case 'discount.gift':
                        case 'discount.friend':
                        case 'discount.group':
                        case 'discount.private':
                            $lineDiscount = $line->getProductsName();
                            if ($lineDiscount < 0) {
                                $lineDiscount = $lineDiscount * -1;
                            }
                            break;
                    }

                    break;
            }
        }

        /** @var \Hanzo\Model\OrdersLines $product */
        foreach ($products as $product) {
            $discountInPercent = 0;

            $itemId = trim(str_replace($product->getproductsColor().' '.$product->getProductsSize(), '', $product->getProductsSku()));

            $line = [
                'ItemId'        => $itemId,
                //'SalesLineText' => $product->getProductsName(), ??
                'SalesPrice'    => number_format($product->getOriginalPrice(), 2, '.', ''),
                'SalesQty'      => $product->getQuantity(),
                'InventColorId' => $product->getProductsColor(),
                'InventSizeId'  => $product->getProductsSize(),
                'SalesUnit'     => $product->getUnit(),
            ];

            $discount = $product->getOriginalPrice() - $product->getPrice();

            if ($product->getOriginalPrice() && $discount > 0) {
                $discountInPercent = 100 / ($product->getOriginalPrice() / $discount);
            }

            if ($discountInPercent) {
                $line['LineDiscPercent'] = number_format($discountInPercent, 4, '.', '');
            } elseif ($lineDiscount) {
                $line['LineDiscPercent'] = $lineDiscount;
            }

            if ($product->getIsVoucher()) {
                $line['VoucherCode'] = $product->getNote();
            }

            $this->data['salesOrder']['SalesTable']['SalesLine'][] = $line;
        }

        $domainKey = str_replace('SALES', '', strtoupper($this->getAttribute('global', 'domain_key')));
        if ($hostessDiscount) {
            $bigBagPrice = 0.00;
            $this->data['salesOrder']['SalesTable']['SalesLine'][] = [
                'ItemId'     =>  'HOSTESSDISCOUNT',
                'SalesPrice' =>  number_format($hostessDiscount, 2, '.', ''),
                'SalesQty'   =>  1,
                'SalesUnit'  =>  'Stk.',
            ];

            switch($domainKey) {
                case 'AT':
                case 'CH':
                case 'COM':
                case 'DE':
                case 'FI':
                case 'NL':
                    $bigBagPrice = '4.95';
                    break;
                case 'DK':
                    $bigBagPrice = '40.00';
                    break;
                case 'NO':
                case 'SE':
                    $bigBagPrice = '60.00';
                    break;
            }

            $this->data['salesOrder']['SalesTable']['SalesLine'][] = [
                'ItemId'          => 'POMPBIGBAGAW14',
                'SalesPrice'      => $bigBagPrice,
                'LineDiscPercent' => 100,
                'SalesQty'        => 1,
                'InventColorId'   => 'Off White',
                'InventSizeId'    => 'One Size',
                'SalesUnit'       => 'Stk.',
            ];
        }
    }

    /**
     * Build promotional SalesLines
     *
     * TODO: Promotions should probably not be handled here, but in events or the like.
     */
    public function buildPromotions()
    {
        $date = date('Ymd');
        $domainKey = str_replace('SALES', '', strtoupper($this->getAttribute('global', 'domain_key')));

        // Add BAG if is an event or either other, gift, private of type.
        if ($this->order->getEventsId() ||
            (in_array($this->getAttribute('purchase', 'type'), ['other', 'gift', 'private', 'friend'], true))
        ) {

            // mellem 19/2'14 og 19/5'14
            if (((20140828 <= $date) && (20141127 >= $date)) ||
                ($this->inEdit && (20141127 >= $this->order->getCreatedAt('Ymd')))
            ) {
                $bagPrice  = 0.00;
                switch($domainKey) {
                    case 'AT':
                    case 'CH':
                    case 'COM':
                    case 'DE':
                    case 'FI':
                    case 'NL':
                        $bagPrice = '1.95';
                        break;
                    case 'DK':
                        $bagPrice = '10.00';
                        break;
                    case 'NO':
                    case 'SE':
                        $bagPrice = '15.00';
                        break;
                }

                $this->data['salesOrder']['SalesTable']['SalesLine'][] = [
                    'ItemId'          => 'POMPBAGAW14',
                    'SalesPrice'      => $bagPrice,
                    'LineDiscPercent' => 100,
                    'SalesQty'        => 1,
                    'InventColorId'   => 'Off White',
                    'InventSizeId'    => 'One Size',
                    'SalesUnit'       => 'Stk.',
                ];

//                // attach voucher between 20140324 and 20140406
//                if ((($date >= 20140324) && ($date <= 20140406)) ||
//                    ( $this->inEdit &&
//                        ($this->order->getCreatedAt('Ymd') <= 20140406) &&
//                        ($this->order->getCreatedAt('Ymd') >= 20140324)
//                    )
//                ) {
//                    $line = new SalesLine([
//                        'ItemId'        => 'VOUCHER',
//                        'SalesPrice'    => 0.00,
//                        'SalesQty'      => 1,
//                        'InventColorId' => $domainKey,
//                        'InventSizeId'  => 'One Size',
//                        'SalesUnit'     => 'Stk.',
//                    ]);
//                    $salesLines[] = $line;
//                }
            }
        }

//        if ($this->order->getEventsId()) {
//            // attach voucher between 20140324 and 20140406
//            if ((($date >= 20140324) && ($date <= 20140406)) ||
//                ($this->inEdit && ($this->order->getCreatedAt('Ymd') <= 20140406) && ($this->order->getCreatedAt('Ymd') >= 20140324))
//            ) {
//                $line = new SalesLine([
//                    'ItemId'        => 'VOUCHER',
//                    'SalesPrice'    => 0.00,
//                    'SalesQty'      => 1,
//                    'InventColorId' => $domainKey,
//                    'InventSizeId'  => 'One Size',
//                    'SalesUnit'     => 'Stk.',
//                ]);
//                $salesLines[] = $line;
//            }
//        }
    }

    /**
     * Build gift card discount SalesLine
     */
    public function buildGiftCards()
    {
        $giftCard = null;

        /** @var \Hanzo\Model\OrdersLines $line */
        foreach ($this->orderLines as $line) {
            if (('discount'       === $line->getType()) &&
                ('gift_card.code' === $line->getProductsName())
            ) {
                $giftCard = $line;
                break;
            }

        }

        if ($giftCard) {
            $this->data['salesOrder']['SalesTable']['SalesLine'][] = [
                'ItemId'      => 'GIFTCARD',
                'SalesPrice'  => number_format(($giftCard->getPrice()), 2, '.', ''),
                'SalesQty'    => 1,
                'SalesUnit'   => 'Stk.',
                'VoucherCode' => $this->getAttribute('gift_card', 'code'),
            ];
        }
    }


    /**
     * Build coupon discount SalesLine
     */
    public function buildCoupons()
    {
        $coupon = null;

        /** @var \Hanzo\Model\OrdersLines $line */
        foreach ($this->orderLines as $line) {
            if (('discount'    === $line->getType()) &&
                ('coupon.code' === $line->getProductsName())
            ) {
                $coupon = $line;
                break;
            }

        }

        if ($coupon) {
            $this->data['salesOrder']['SalesTable']['SalesLine'][] = [
                'ItemId'     => 'COUPON',
                'SalesPrice' => number_format(($coupon->getPrice()), 2, '.', ''),
                'SalesQty'   => 1,
                'SalesUnit'  => 'Stk.',
            ];
        }
    }


    /**
     * Convert country id to iso-2 country code.
     *
     * @param int $countryId
     *
     * @return string|null
     */
    private function getIso2CountryCode($countryId)
    {
        $result = CountriesQuery::create()
            ->select('Iso2')
            ->findOneById($countryId);

        if ($result instanceof Countries) {
            return $result->getIso2();
        }

        return $result;
    }

    /**
     * Get payment method
     *
     * @return string
     */
    private function getCustPaymMode()
    {
        // payment method
        $custPaymMode = 'Bank';

        $payType = trim(strtoupper($this->getAttribute('payment', 'paytype')));
        switch (strtolower($this->order->getBillingMethod()))
        {
            case 'dibs':
                switch ($payType) {
                    case 'VISA':
                    case 'VISA(DK)':
                    case 'VISA(SE)':
                    case 'ELEC':
                        $custPaymMode = 'VISA';
                        break;
                    case 'MC':
                    case 'MC(DK)':
                    case 'MC(SE)':
                    case 'MasterCard':
                        $custPaymMode = 'MasterCard';
                        break;
                    case 'V-DK':
                    case 'VISA-DANKORT':
                    case 'DK':
                    case 'DANKORT':
                        $custPaymMode = 'DanKort';
                        break;
                    default:
                        $custPaymMode = 'VISA';
                        break;
                }
                break;

            case 'gothia':
            case 'gothiade':
                $custPaymMode = 'PayByBill';
                if ('GOTHIA_LV' == $payType) {
                    $custPaymMode = 'ELV';
                }
                break;

            case 'paypal':
                $custPaymMode = 'PayPal';
                break;

            case 'manualpayment':
                $custPaymMode = 'Bank';
                break;

            case 'pensio':
                $custPaymMode = 'iDEAL';
                break;
        }

        return $custPaymMode;
    }

    /**
     * Get payment gateway transaction id
     *
     * @return string
     */
    private function getPaymentTransactionId()
    {
        $id = '';

        if ($t = $this->getAttribute('payment', 'transaction_id')) {
            $this->orderAttributes['payment']['transact'] = $t;
        } elseif ($t = $this->getAttribute('payment', 'TRANSACTIONID')) {
            $this->orderAttributes['payment']['transact'] = $t;
        }

        if ($t = $this->getAttribute('payment', 'transact')) {
            $id = $t;
        }

        return $id;
    }

    /**
     * Calculate cost based on OrdersLines::type
     *
     * @param array $keys
     *
     * @return string
     */
    private function calculateCost(array $keys)
    {
        $cost = 0.00;
        foreach ($this->orderLines as $line) {
            /** @var \Hanzo\Model\OrdersLines $line */
            if (!in_array($line->getType(), $keys)) {
                continue;
            }

            $cost += $line->getPrice();
        }

        return number_format((float) $cost, 2, '.', '');
    }

    /**
     * Get an order attribute value from namespace and key
     *
     * @param string $ns
     * @param string $key
     *
     * @return string
     */
    private function getAttribute($ns, $key)
    {
        if (isset($this->orderAttributes[$ns], $this->orderAttributes[$ns][$key])) {
            return $this->orderAttributes[$ns][$key];
        }

        return '';
    }


    /**
     * {@inheritdoc}
     *
     * @return bool|void
     */
    protected function validate()
    {
        if (empty($this->data['salesOrder']['SalesTable']['HomePartyId']) || empty($this->data['salesOrder']['SalesTable']['SalesResponsible'])) {
            throw new \Exception('Validation error - SyncSalesOrder: Missing SalesResponsible or HomePartyId');
        }

        return true;
    }
}

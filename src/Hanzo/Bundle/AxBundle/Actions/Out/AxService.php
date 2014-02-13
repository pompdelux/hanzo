<?php

namespace Hanzo\Bundle\AxBundle\Actions\Out;

use Propel;
use SoapFault;
use stdClass;
use Exception;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersSyncLog;

class AxService
{
    protected $wsdl;
    protected $logger;
    protected $event_dispatcher;
    protected $client;
    protected $translator;
    protected $ax_state     = false;
    protected $skip_send    = false;
    protected $log_requests = false;

    public function __construct($wsdl, $log_requests, Logger $logger, EventDispatcher $event_dispatcher, Translator $translator)
    {
        $this->wsdl             = $wsdl;
        $this->log_requests     = $log_requests;
        $this->logger           = $logger;
        $this->event_dispatcher = $event_dispatcher;
        $this->translator       = $translator;

        // primarily used in dev mode where ax is not available
        if (empty($wsdl)) {
            $this->skip_send = true;
        }
    }


    /**
     * trigger ax stock sync
     *
     * @param  string   $endpoint  DK/NO/SE/...
     * @param  boolean  $return    Returns the object we intend to send to AX.
     * @return mixed    true on success, false or \SoapFault on error
     */
    public function triggerStockSync($endpoint, $return = false)
    {
        ini_set("default_socket_timeout", 600);

        $data = new stdClass();
        $data->endpointDomain = $endpoint;

        if ($return) {
            return $data;
        }

        $result = $this->Send('SyncInventory', $data);

        if ($result instanceof Exception) {
            $this->logger->addCritical($result->getMessage());
            $this->logger->addCritical($this->client->__getLastRequest());
        }

        return true;
    }


    /**
     * Build and send order to AX
     *
     * @param  Orders    $order
     * @param  boolean   $return  Returns the object we intend to send to AX.
     * @param  PropelPDO $con     Optional Propel db connection
     * @param  boolean   $in_edit Set to true if the order is in edit state.
     * @return boolean
     */
    public function sendOrder(Orders $order, $return = false, $con = null, $in_edit = false)
    {
        if (null === $con) {
            $con = Propel::getConnection(null, Propel::CONNECTION_WRITE);
        }

        // we reload to make sure we have the latest edition :)
        $attributes       = $order->getAttributes($con);
        $lines            = $order->getOrdersLiness(null, $con);
        $products         = array();
        $shipping_cost    = 0;
        $payment_cost     = 0;
        $handeling_fee    = 0;
        $hostess_discount = 0;
        $gift_card        = false;
        $coupon           = false;
        $line_discount    = 0;

        foreach ($lines as $line) {
            switch ($line->getType()) {
                case 'product':
                    $products[] = $line;
                    break;

                case 'shipping':
                    $shipping_cost += $line->getPrice();
                    break;

                case 'payment':
                    $payment_cost += $line->getPrice();
                    break;

                case 'shipping.fee':
                case 'payment.fee':
                    $handeling_fee += $line->getPrice();
                    break;

                case 'discount':
                    switch ($line->getProductsSku()) {
                        case 'discount.hostess':
                            $hostess_discount = $line->getPrice();
                            break;
                        case 'discount.gift':
                        case 'discount.friend':
                        case 'discount.group':
                        case 'discount.private':
                            $line_discount = $line->getProductsName();
                            if ($line_discount < 0) {
                                $line_discount = $line_discount * -1;
                            }
                            break;
                    }

                    if ('gift_card.code' == $line->getProductsName()) {
                        $gift_card = $line;
                    }

                    if ('coupon.code' == $line->getProductsName()) {
                        $coupon = $line;
                    }
                    break;
            }
        }


        $salesLine = array();
        foreach ($products as $product) {
            $discount_in_percent = 0;

            $item_id = trim(str_replace($product->getproductsColor().' '.$product->getProductsSize(), '', $product->getProductsSku()));

            $line = new stdClass();
            $line->ItemId        = $item_id;
            $line->lineText      = $product->getProductsName();
            $line->SalesPrice    = number_format($product->getOriginalPrice(), 2, '.', '');
            $line->SalesQty      = $product->getQuantity();
            $line->InventColorId = $product->getProductsColor();
            $line->InventSizeId  = $product->getProductsSize();
            $line->SalesUnit     = $product->getUnit();

            $discount = $product->getOriginalPrice() - $product->getPrice();

            if ($product->getOriginalPrice() && $discount > 0) {
                $discount_in_percent = 100 / ($product->getOriginalPrice() / $discount);
            }

            if ($discount_in_percent) {
                $line->LineDiscPercent = number_format($discount_in_percent, 2, '.', '');
            } elseif ($line_discount) {
                $line->LineDiscPercent = $line_discount;
            }

            if ($product->getIsVoucher()) {
                $line->VoucherCode = $product->getNote();
            }

            $salesLine[] = $line;
        }

        if ($hostess_discount) {
            $bag_price = 0.00;
            $line = new stdClass();
            $line->ItemId     = 'HOSTESSDISCOUNT';
            $line->SalesPrice = number_format($hostess_discount, 2, '.', '');
            $line->SalesQty   = 1;
            $line->SalesUnit  = 'Stk.';
            $salesLine[]      = $line;

            $domain_key = strtoupper($attributes->global->domain_key);
            switch(str_replace('SALES', '', $domain_key)) {
                case 'AT':
                case 'CH':
                case 'DE':
                case 'FI':
                case 'NL':
                    $bag_price = '4.95';
                    break;
                case 'DK':
                    $bag_price = '40.00';
                    break;
                case 'NO':
                    $bag_price = '60.00';
                    break;
                case 'SE':
                    $bag_price = '60.00';
                    break;
            }

            $line = new stdClass();
            $line->ItemId          = 'POMP BIG BAG';
            $line->SalesPrice      = $bag_price;
            $line->LineDiscPercent = 100;
            $line->SalesQty        = 1;
            $line->InventColorId   = 'Off White';
            $line->InventSizeId    = 'One Size';
            $line->SalesUnit       = 'Stk.';
            $salesLine[]           = $line;
        }

        if ($order->getEventsId()) {
            $date = date('Ymd');

            if (((20130812 <= $date) && (20130901 >= $date)) ||
                ($in_edit && (20130901 >= $order->getCreatedAt('Ymd')))
            ) {
                $line = new stdClass();
                $line->ItemId          = 'VOUCHER';
                $line->SalesPrice      = 0.00;
                //$line->LineDiscPercent = 100;
                $line->SalesQty        = 1;
                $line->InventColorId   = str_replace('Sales', '', $attributes->global->domain_key);
                $line->InventSizeId    = 'One Size';
                $line->SalesUnit       = 'Stk.';
                $salesLine[]           = $line;
            }
        }

        if ($gift_card) {
            $line = new stdClass();
            $line->ItemId      = 'GIFTCARD';
            $line->SalesPrice  = number_format(($gift_card->getPrice()), 2, '.', '');
            $line->SalesQty    = 1;
            $line->SalesUnit   = 'Stk.';
            $line->VoucherCode = $attributes->gift_card->code;
            $salesLine[]       = $line;
        }

        if ($coupon) {
            $line = new stdClass();
            $line->ItemId      = 'COUPON';
            $line->SalesPrice  = number_format(($coupon->getPrice()), 2, '.', '');
            $line->SalesQty    = 1;
            $line->SalesUnit   = 'Stk.';
            $salesLine[]       = $line;
        }

        // payment method
        $custPaymMode = 'Bank';

        switch (strtolower($order->getBillingMethod()))
        {
            case 'dibs':
                switch (trim(strtoupper($attributes->payment->paytype))) {
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
                if ('GOTHIA_LV' == strtoupper($attributes->payment->paytype)) {
                    $custPaymMode = 'ELV';
                }
                break;

            case 'paypal':
                $custPaymMode = 'PayPal';
                if (isset($attributes->payment->TRANSACTIONID)) {
                    $attributes->payment->transact = $attributes->payment->TRANSACTIONID;
                }
                break;

            case 'manualpayment':
                $custPaymMode = 'Bank';
                break;

            case 'pensio':
                if ('IDEALPAYMENT' == strtoupper($attributes->payment->nature)) {
                    $custPaymMode = 'iDEAL';

                    if (isset($attributes->payment->transaction_id)) {
                        $attributes->payment->transact = $attributes->payment->transaction_id;
                    }
                }
                break;
        }

        $freight_type = $order->getDeliveryMethod();

        $salesTable = new stdClass();
        $salesTable->CustAccount             = $order->getCustomersId();
        $salesTable->EOrderNumber            = $order->getId();
        $salesTable->PaymentId               = isset($attributes->payment->transact) ? $attributes->payment->transact : '';
        $salesTable->HomePartyId             = isset($attributes->global->HomePartyId) ? $attributes->global->HomePartyId : '';
        $salesTable->SalesResponsible        = isset($attributes->global->SalesResponsible) ? $attributes->global->SalesResponsible : '';
        $salesTable->CurrencyCode            = $order->getCurrencyCode();
        $salesTable->SalesName               = $order->getFirstName() . ' ' . $order->getLastName();
        $salesTable->SalesType               = 'Sales';
        $salesTable->SalesLine               = $salesLine;
        $salesTable->InvoiceAccount          = $order->getCustomersId();
        $salesTable->FreightFeeAmt           = number_format((float) $shipping_cost, 2, '.', '');
        $salesTable->FreightType             = $freight_type;
        $salesTable->HandlingFeeType         = 90;
        $salesTable->HandlingFeeAmt          = number_format((float) $handeling_fee, 2, '.', '');
        $salesTable->PayByBillFeeType        = 91;
        $salesTable->PayByBillFeeAmt         = number_format((float) $payment_cost, 2, '.', ''); // TODO: only for gothia?
        $salesTable->Completed               = 1;
        $salesTable->TransactionType         = 'Write';
        $salesTable->CustPaymMode            = $custPaymMode;
        $salesTable->SmoreContactInfo        = ''; // NICETO, når s-more kommer på banen igen
        $salesTable->BankAccountNumber       = isset( $attributes->payment->bank_account_no) ? $attributes->payment->bank_account_no : '';
        $salesTable->BankId                  = isset( $attributes->payment->bank_id ) ? $attributes->payment->bank_id : '';
        $salesTable->DeliveryDropPointId     = $order->getDeliveryExternalAddressId();
        $salesTable->DeliveryCompanyName     = $order->getDeliveryCompanyName();
        $salesTable->DeliveryCity            = $order->getDeliveryCity();
        $salesTable->DeliveryName            = trim($order->getDeliveryTitle($this->translator).' '.$order->getDeliveryFirstName() . ' ' . $order->getDeliveryLastName());
        $salesTable->DeliveryStreet          = $order->getDeliveryAddressLine1();
        $salesTable->DeliveryZipCode         = $order->getDeliveryPostalCode();
        $salesTable->DeliveryCountryRegionId = $this->getIso2CountryCode($order->getDeliveryCountriesId());

        $salesTable->SalesGroup = '';
        if ($event = $order->getEvents($con)) {
            $salesTable->SalesGroup = $event
                ->getCustomersRelatedByConsultantsId($con)
                ->getConsultants($con)
                ->getInitials()
            ;
        }

        if ((isset($attributes->purchase->type)) &&
            ('other' == $attributes->purchase->type)
        ) {
            $salesTable->SalesGroup = $attributes->global->SalesResponsible;
        }

        $salesOrder = new stdClass();
        $salesOrder->SalesTable = $salesTable;

        $syncSalesOrder = new stdClass();
        $syncSalesOrder->salesOrder     = $salesOrder;
        $syncSalesOrder->endpointDomain = str_replace('SALES', '', strtoupper($attributes->global->domain_key));

        // NICETO, would be nice if this was not static..
        switch ($syncSalesOrder->endpointDomain) {
            case 'COM':
                $syncSalesOrder->endpointDomain = 'DK';
                break;
        }

        if ($return) {
            return $syncSalesOrder;
        }

        // post validation
        if (empty($salesTable->HomePartyId) || empty($salesTable->SalesResponsible)) {
            $this->logOrderSyncStatus($order->getId(), $syncSalesOrder, 'failed', 'Missing SalesResponsible or HomePartyId', $con);
            return false;
        }

        $this->sendDebtor($order->getCustomers($con), $return, $con);
        $result = $this->Send('SyncSalesOrder', $syncSalesOrder);

        $comment = '';
        if ($result instanceof Exception) {
            $state = 'failed';
            $comment = $result->getMessage();
        } else {
            if ( isset($result->SyncSalesOrderResult->Status) && strtoupper($result->SyncSalesOrderResult->Status) == 'OK') {
                $state = 'ok';
            } else {
                $state = 'failed';
                if (isset($result->SyncSalesOrderResult->Message)) {
                    foreach ($result->SyncSalesOrderResult->Message as $msg) {
                        $comment .= trim($msg) . "\n";
                    }
                }
            }
        }

        // log ax transaction result
        $this->logOrderSyncStatus($order->getId(), $syncSalesOrder, $state, $comment, $con);

        if ($state == 'ok') {
            return true;
        }

        return false;
    }


    /**
     * Build and send debtor info to AX
     *
     * @param  Customers $debitor
     * @param  boolean   $return  Returns the object we intend to send to AX.
     * @param  PropelPDO $con     Optional db connection
     * @return boolean
     */
    public function sendDebtor(Customers $debitor, $return = false, $con = null)
    {
        $ct = new \stdClass();
        $ct->AccountNum = $debitor->getId();

        $address = AddressesQuery::create()
            ->joinWithCountries()
            ->filterByType('payment')
            ->filterByCustomersId($debitor->getId())
            ->findOne($con)
        ;

        $ct->AddressCity            = $address->getCity();
        $ct->AddressCountryRegionId = $address->getCountries()->getIso2();
        $ct->AddressStreet          = $address->getAddressLine1();
        $ct->AddressZipCode         = $address->getPostalCode();
        $ct->CustName               = trim($address->getTitle($this->translator).' '.$address->getFirstName().' '.$address->getLastName());
        $ct->Email                  = $debitor->getEmail();
        $ct->Phone                  = $debitor->getPhone();

        if (2 == $debitor->getGroupsId()) {
            $ct->InitialsId = $debitor->getInitials();
        }

        $cu = new stdClass();
        $cu->CustTable = $ct;

        $sc = new stdClass();
        $sc->customer  = $cu;

        // NICETO: no hardcoded switches
        // Use: $syncSalesOrder->endpointDomain = $attributes->global->domain_key; ??
        $sc->endpointDomain = 'DK';
        switch ($ct->AddressCountryRegionId) {
            case 'AT':
            case 'CH':
            case 'DE':
            case 'FI':
            case 'NL':
            case 'NO':
            case 'SE':
                $sc->endpointDomain = $ct->AddressCountryRegionId;
                break;
        }

        if ($return) {
            return $sc;
        }

        $result = $this->Send('SyncCustomer', $sc);

        if ($result instanceof Exception) {
            $message = sprintf('An error occured while synchronizing debitor "%s", error message: "%s"',
                $debitor->getId(),
                $result->getMessage()
            );
            $this->logger->addCritical($message);

            return false;
        }

        return true;
    }


    /**
     * deleteOrder from ax
     *
     * @param  Order    $order order object
     * @param  Resource $con   database connection or null to use current
     * @throws \Exception
     * @return boolean
     */
    public function deleteOrder($order, $con = null)
    {
        $attributes = $order->getAttributes($con);

        $salesTable = new stdClass();
        $salesTable->CustAccount     = $order->getCustomersId();
        $salesTable->EOrderNumber    = $order->getId();
        $salesTable->PaymentId       = isset( $attributes->payment->transact ) ? $attributes->payment->transact : '';
        $salesTable->SalesType       = 'Sales';
        $salesTable->Completed       = 1;
        $salesTable->TransactionType = 'Delete';

        $salesOrder = new stdClass();
        $salesOrder->SalesTable = $salesTable;

        $syncSalesOrder = new stdClass();
        $syncSalesOrder->salesOrder     = $salesOrder;
        $syncSalesOrder->endpointDomain = str_replace('SALES', '', strtoupper($attributes->global->domain_key));

        // NICETO, would be nice if this was not static..
        switch ($syncSalesOrder->endpointDomain) {
            case 'COM':
                $syncSalesOrder->endpointDomain = 'DK';
                break;
        }

        $result = $this->Send('SyncSalesOrder', $syncSalesOrder);

        if ($result instanceof Exception) {
            $message = sprintf('An error occured while deleting order "%s", error message: "%s"',
                $order->getId(),
                $result->getMessage()
            );
            $this->logger->addCritical($message);

            // log ax transaction result
            $this->logOrderSyncStatus($order->getId(), $syncSalesOrder, 'failed', $result->getMessage(), $con);

            throw $result;
        }

        return true;
    }


    /**
     * lock orders in ax
     *
     * @param  Orders $order
     * @param  bool   $status true locks an order false unlocks
     * @return bool
     */
    public function lockUnlockSalesOrder($order, $status = true)
    {
        $attributes = $order->getAttributes();

        $lock = new stdClass();
        $lock->eOrderNumber   = $order->getId();
        $lock->lockOrder      = $status ? 1 : 0;
        $lock->endpointDomain = str_replace('SALES', '', strtoupper($attributes->global->domain_key));

        // NICETO, would be nice if this was not static..
        switch ($lock->endpointDomain) {
            case 'COM':
                $lock->endpointDomain = 'DK';
                break;
        }

        $result = $this->Send('SalesOrderLockUnlock', $lock);

        if ($result instanceof Exception) {
            $message = sprintf('An error occured while locking the order: "%s", error message: "%s"',
                $order->getId(),
                $result->getMessage()
            );
            $this->logger->addCritical($message);

            return false;
        }

        return true;
    }


    /**
     * transform country code into iso2 code
     *
     * @param  int $country_id
     * @return string
     */
    protected function getIso2CountryCode($country_id)
    {
        $result = CountriesQuery::create()
            ->select('Iso2')
            ->findOneById($country_id);

        if ($result instanceof Countries) {
            return $result->getIso2();
        }

        return $result;
    }


    /**
     * Performs the actiual communication with AX
     *
     * @param string $service Name of the service to call
     * @param object $request Request parameters
     * @return object.
     */
    protected function Send($service, $request)
    {
        if ($this->log_requests) {
            $this->logger->addDebug('Calling: '.$service, (array) $request);
            Tools::log($request);
        }

        if ($this->skip_send) {
            return true;
        }

        if (!$this->client) {
            if (!$this->Connect()) {
                return new Exception('There was an error connecting with the server! Please try again later.');
            }
        }

        try {
            return $this->client->{$service}($request);
        } catch (SoapFault $e) {
            $this->logger->addCritical('Request.: '.$this->client->__getLastRequest());
            $this->logger->addCritical('Response: '.$this->client->__getLastResponse());
            return $e;
        }
    }


    /**
     * test and initiate ax connection
     *
     * @return boolean [description]
     */
    protected function Connect()
    {
        if ($this->skip_send) {
            return true;
        }

        // first we test the connection, soap has lousy timeout handeling
        $c = curl_init();
        curl_setopt_array($c, array(
            CURLOPT_URL            => $this->wsdl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,  // connection
            CURLOPT_TIMEOUT        => 10, // execution timeout
        ));

        $file = curl_exec($c);
        $status = curl_getinfo($c,  CURLINFO_HTTP_CODE);
        curl_close($c);

        // ok the header send was ok, and we have file content.
        if ($status == 200 && $file) {
            $this->ax_state = true;
            unset($file);
        } else {
            return false;
        }

        $this->client = new \SoapClient($this->wsdl, array(
            'trace'              => true,
            'exceptions'         => true,
            'connection_timeout' => 600,
        ));
        $this->client->__setLocation(str_replace('?wsdl', '', $this->wsdl));

        return true;
    }


    /**
     * log sync status
     *
     * @param  int    $order_id id of the order
     * @param  object $data     data to log, should be the complete request object
     * @param  string $state    'failed' or 'ok'
     * @param  string $comment  optional comment
     * @param  mixed  $con      db connection or null
     * @return mixed
     */
    protected function logOrderSyncStatus($order_id, $data, $state = 'ok', $comment = '', $con = null)
    {
        $entry = new OrdersSyncLog();
        $entry->setOrdersId($order_id);
        $entry->setCreatedAt('now');
        $entry->setState($state);
        $entry->setContent(serialize($data));
        if ($comment) {
            $entry->setComment($comment);
        }

        return $entry->save($con);
    }
}

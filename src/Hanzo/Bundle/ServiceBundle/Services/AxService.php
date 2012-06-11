<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Propel;
use SoapClient;
use SoapFault;
use stdClass;
use Exception;

use Symfony\Bridge\Monolog\Logger;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersSyncLog;

class AxService
{
    protected $parameters;
    protected $settings;

    protected $logger;

    protected $wsdl;
    protected $client;
    protected $ax_state = false;
    protected $skip_send = false;

    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof Logger) {
            throw new \InvalidArgumentException('Monolog\Logger expected as first parameter.');
        }

        $this->parameters = $parameters;
        $this->settings = $settings;

        $this->logger = $parameters[0];

        $this->wsdl = 'http://'.$settings['host'].'/DynamicsAxServices.asmx?wsdl';

        if (isset($settings['skip_send']) && $settings['skip_send'] == 1) {
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
        $data = new stdClass();
        $data->endpointDomain = $endpoint;

        if ($return) {
            return $data;
        }

        $result = $this->Send('SyncInventory', $data);
        return true;
    }


    /**
     * Build and send order to AX
     *
     * @param  Orders   $order
     * @param  boolean  $return   Returns the object we intend to send to AX.
     * @return boolean
     */
    public function sendOrder(Orders $order, $return = false)
    {
        Propel::setForceMasterConnection(true);

        $attributes = $order->getAttributes();
        $lines = $order->getOrdersLiness();

        $products = array();
        $shipping_cost = 0;
        $payment_cost = 0;
        $handeling_fee = 0;

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
                case 'payment.fee': // TODO: hf@bellcom.dk: eh... only for gothia?
                    $handeling_fee += $line->getPrice();
                    break;
            }
        }

        $discount_in_percent = 0; // kommer fra arrangementer (orders_attributes ??)

        $salesLine = array();
        foreach ($products as $product) {
            $line = new stdClass();
            $line->ItemId        = $product->getProductsName();
            $line->SalesPrice    = (float) number_format($product->getPrice(), 4, '.', '');
            $line->SalesQty      = $product->getQuantity();
            $line->InventColorId = $product->getProductsColor();
            $line->InventSizeId  = $product->getProductsSize();
            $line->SalesUnit     = $product->getUnit();

            $discount = $product->getOriginalPrice() - $product->getPrice();
/*
            if ( $discount < 0 ) // hf@bellcom.dk, check for discount fubar :)
            {
              throw new Exception( 'Discount is negative: '.$discount .', product item id: '. $line->ItemId. ', order id: '. $order->getId() );
            }
*/
            if ($product->getOriginalPrice() && $discount > 0) {
                $discount_in_percent = 100 / ($product->getOriginalPrice() / $discount);
            }

            if ($discount_in_percent) {
                $line->LineDiscPercent = number_format($discount_in_percent, 4, '.', '');
            }

            $line->lineText = $product->getProductsName();
            $salesLine[] = $line;
        }

        // payment method
        $custPaymMode = 'Bank';

        switch ($order->getBillingMethod())
        {
          case 'dibs':
              switch (strtoupper($attributes->payment->paytype)) {
                  case 'VISA':
                  case 'ELEC':
                      $custPaymMode = 'VISA';
                      break;
                  case 'MC':
                      $custPaymMode = 'MasterCard';
                      break;
                  case 'V-DK':
                  case 'DK':
                      $custPaymMode = 'DanKort';
                      break;
              }
            break;

          case 'gothia':
              $custPaymMode = 'PayByBill';
              break;

          case 'paybybill': // Should be COD, is _not_ Gothia
              $custPaymMode = 'Bank';
              break;
        }

        $freight_type = $order->getDeliveryMethod();

        // NICETO, this should be set elsewhere
        // hf@bellcom.dk: I think this is allready set to P or S..., e.g. external_id is P or S
        /*switch ($attributes->global->domain_key) {
            case 'NO':
                $freight_type = ($freight_type == 10) ? 'P' : 'S';
                break;
            case 'SE':
                $freight_type = 30;
                break;
        }*/

        // Avoid sending stuff like 15.0000, cast to float fixes that... no really

        $salesTable = new stdClass();
        $salesTable->CustAccount             = $order->getCustomersId();
        $salesTable->EOrderNumber            = $order->getId();
        $salesTable->PaymentId               = isset( $attributes->payment->transact ) ? $attributes->payment->transact : '';
        $salesTable->HomePartyId             = $attributes->global->HomePartyId;
        $salesTable->SalesResponsible        = $attributes->global->SalesResponsible;
        $salesTable->CurrencyCode            = $order->getCurrencyCode();
        $salesTable->SalesName               = $order->getFirstName() . ' ' . $order->getLastName();
        $salesTable->SalesType               = 'Sales';
        $salesTable->SalesLine               = $salesLine;
        $salesTable->DeliveryCompanyName     = $order->getDeliveryCompanyName();
        $salesTable->DeliveryCity            = $order->getDeliveryCity();
        $salesTable->DeliveryName            = $order->getFirstName() . ' ' . $order->getLastName();
        $salesTable->DeliveryStreet          = $order->getDeliveryAddressLine1();
        $salesTable->DeliveryZipCode         = $order->getDeliveryPostalCode();
        $salesTable->DeliveryCountryRegionId = $this->getIso2CountryCode($order->getDeliveryCountriesId());
        $salesTable->InvoiceAccount          = $order->getCustomersId();
        $salesTable->FreightFeeAmt           = (float) number_format($shipping_cost, 4, '.', '');
        $salesTable->FreightType             = $freight_type;
        $salesTable->HandlingFeeType         = 90;
        $salesTable->HandlingFeeAmt          = (float) number_format($handeling_fee, 4, '.', '');
        $salesTable->PayByBillFeeType        = 91;
        $salesTable->PayByBillFeeAmt         = (float) number_format($payment_cost, 4, '.', ''); // TODO: only for gothia?
        $salesTable->Completed               = 1;
        $salesTable->TransactionType         = 'Write';
        $salesTable->CustPaymMode            = $custPaymMode;
        $salesTable->SalesGroup              = ''; // FIXME (initialer på konsulent)
        $salesTable->SmoreContactInfo        = ''; // NICETO, når s-more kommer på banen igen

        $salesOrder = new stdClass();
        $salesOrder->SalesTable = $salesTable;

        $syncSalesOrder = new stdClass();
        $syncSalesOrder->salesOrder = $salesOrder;
        $syncSalesOrder->endpointDomain = substr($attributes->global->domain_key, -2);

        if ($return) {
            return $syncSalesOrder;
        }

        $this->sendDebtor($order->getCustomers(), $return);
        $result = $this->Send('SyncSalesOrder', $syncSalesOrder);

        $comment = '';
        if ($result instanceof Exception) {
            $state = 'failed';
            $comment = $result->getMessage();
        } else {
            if ( isset($result->SyncSalesOrderResult->Status) && strtoupper($result->SyncSalesOrderResult->Status) == 'OK') {
                $state = 'ok';
                $order->setState(Orders::STATE_BEING_PROCESSED);
                $order->save();
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
        $entry = new OrdersSyncLog();
        $entry->setOrdersId($order->getId());
        $entry->setCreatedAt('now');
        $entry->setState($state);
        $entry->setContent(serialize($syncSalesOrder));
        if ($comment) {
            $entry->setComment($comment);
        }
        $entry->save();

        if ($state == 'ok') {
            return true;
        }

        return false;
    }


    /**
     * Build and send debtor info to AX
     *
     * @param  Customers $debitor
     * @param  boolean   $return    Returns the object we intend to send to AX.
     * @return boolean
     */
    public function sendDebtor(Customers $debitor, $return = false)
    {
        $ct = new \stdClass();
        $ct->AccountNum = $debitor->getId();

        $address = AddressesQuery::create()
            ->joinWithCountries()
            ->filterByType('payment')
            ->findOneByCustomersId($debitor->getId())
            ;

        $ct->AddressCity = $address->getCity();
        $ct->AddressCountryRegionId = $address->getCountries()->getIso2();

        $ct->AddressStreet = $address->getAddressLine1();
        $ct->AddressZipCode = $address->getPostalCode();
        $ct->CustName = $address->getFirstName() . ' ' . $address->getLastName();
        $ct->Email = $debitor->getEmail();
        if (2 == $debitor->getGroupsId()) {
            $ct->InitialsId = $debitor->getInitials();
        }
        $ct->Phone = $debitor->getPhone();

        $cu = new stdClass();
        $cu->CustTable = $ct;
        $sc = new stdClass();
        $sc->customer = $cu;

        // NICETO: no hardcoded switches
        // Use: $syncSalesOrder->endpointDomain = $attributes->global->domain_key; ??
        $sc->endpointDomain = 'DK';
        switch ($ct->AddressCountryRegionId) {
            case 'SE':
            case 'NO':
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
                $result->getMethod()
            );
            $this->logger->addCritical($message);

            return false;
        }

        return true;
    }


    /**
     * deleteOrder from ax
     *
     * @param  Order $order
     * @return boolean
     */
    public function deleteOrder($order)
    {
        $attributes = $order->getAttributes();

        $salesTable = new stdClass();
        $salesTable->CustAccount = $order->getCustomersId();
        $salesTable->EOrderNumber = $order->getId();
        $salesTable->PaymentId = isset( $attributes->payment->transact ) ? $attributes->payment->transact : '';
        $salesTable->SalesType = 'Sales';
        $salesTable->Completed = 1;
        $salesTable->TransactionType = 'Delete';

        $salesOrder = new stdClass();
        $salesOrder->SalesTable = $salesTable;

        $syncSalesOrder = new stdClass();
        $syncSalesOrder->salesOrder = $salesOrder;

        $syncSalesOrder->endpointDomain = substr($attributes->global->domain_key, -2);

        $result = $this->Send('SyncSalesOrder', $syncSalesOrder);

        if ($result instanceof Exception) {
            $message = sprintf('An error occured while synchronizing debitor "%s", error message: "%s"',
                $debitor->getId(),
                $result->getMethod()
            );
            $this->logger->addCritical($message);

            return false;
        }

        return true;
    }


    /**
     * lock orders in ax
     *
     * @param int $orderId
     * @param bool $status true locks an order false unlocks
     * @return bool
     */
    public function lockUnlockSalesOrder($order, $status = true)
    {
        $attributes = $order->getAttributes();

        $lock = new stdClass();
        $lock->eOrderNumber = $order->getId();
        $lock->lockOrder = $status ? 1 : 0;
        $lock->endpointDomain = substr($attributes->global->domain_key, -2);
        $result = $this->Send('SalesOrderLockUnlock', $lock);

        if ($result instanceof Exception) {
            $message = sprintf('An error occured while locking the order: "%s", error message: "%s"',
                $order->getId(),
                $result->getMethod()
            );
            $this->logger->addCritical($message);

            return false;
        }

        return true;
    }


    protected function getIso2CountryCode($country_id)
    {
        return CountriesQuery::create()
            ->select('Iso2')
            ->findOneById($country_id);
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
        if ($this->skip_send) {
            return true;
        }

        if (!$this->client) {
            if (!$this->Connect()) {
                return false;
            }
        }

        try {
            return $this->client->{$service}($request);
        } catch (SoapFault $e) {
            Tools::log($this->client->__getLastRequest());
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
            CURLOPT_CONNECTTIMEOUT => 5, // connection
            CURLOPT_TIMEOUT        => 6, // execution timeout
            CURLOPT_RETURNTRANSFER => true,
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

        $this->client = new SoapClient($this->wsdl, array(
            'trace'      => true,
            'exceptions' => true,
        ));
        $this->client->__setLocation(str_replace('?wsdl', '', $this->wsdl));

        return true;
    }
}

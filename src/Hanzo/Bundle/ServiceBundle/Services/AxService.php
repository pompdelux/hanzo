<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Symfony\Bridge\Monolog\Logger;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Customers;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Orders;

class AxService
{
    protected $parameters;
    protected $settings;

    protected $logger;

    protected $wsdl;
    protected $client;
    protected $ax_state = false;

    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof Logger) {
            throw new \InvalidArgumentException('Monolog\Logger expected as first parameter.');
        }

        $this->parameters = $parameters;
        $this->settings = $settings;

        $this->logger = $parameters[0];

        $this->wsdl = 'http://'.$settings['host'].'/DynamicsAxServices.asmx?wsdl';
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
        $data = new \stdClass();
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
        // Propel::setForceMasterConnection(true);
        if ($order->getInEdit()) {
            // cancel payment at the correct provider
        }
//Tools::log(get_class_methods($order));
        $attributes = $order->getAttributes(); // TODO: create shortcut in Model\Orders::getAttributes()
Tools::log($attributes);
        $lines = $order->getOrdersLiness();

        $products = array();
        $shipping = array();
        $payment = array();

        foreach ($lines as $line) {
            switch ($line->getType()) {
                case 'product':
                    $products[] = $line;
                    break;

                case 'shipping':
                case 'shipping.fee':
                    $shipping[] = $line;
                    break;

                case 'payment':
                case 'payment.fee':
                    $payment[] = $line;
                    break;
            }
        }

        $discount_in_percent = 0; // kommer fra arrangementer (orders_attributes ??)

        $salesLine = array();
        foreach ($products as $product) {
            $line = new \stdClass();
            $line->ItemId = $product->getProductsSku();
            $line->SalesPrice = $product->getPrice();
            $line->SalesQty = $product->getQuantity();
            $line->InventColorId = $product->getProductsColor();
            $line->InventSizeId = $product->getProductsSize();
            $line->SalesUnit = ''; // FIXME


            if (0.00 != $product->getVat())
            {
                $line->SalesPrice = $line->SalesPrice * (($product->getVat() / 100) + 1);
            }

            $discount = $product->getOriginalPrice() - $product->getPrice();

            if ($product->getOriginalPrice() && $discount != 0) {
                $product_discount_in_prc = 100 / ($product->getOriginalPrice() / $discount);
            } else {
                $product_discount_in_prc = $discount_in_percent;
            }

            if ($product_discount_in_prc)
            {
                $line->LineDiscPercent = $product_discount_in_prc;
            }

            $line->lineText = $product->getProductsName();
            $salesLine[] = $line;
        }

        // FIXME: hostess discount
        if (0)
        {
            $line = new \stdClass();
            $line->ItemId = 'HOSTESSDISCOUNT';
            $line->SalesPrice = '-' . $attributes->event->hostessDiscount;
            $line->SalesQty = 1;
            $line->SalesUnit = 'Stk.';
            $salesLine[] = $line;
        }

        $salesTable = new \stdClass();
        $salesTable->CustAccount             = $order->getCustomersId();
        $salesTable->EOrderNumber            = $order->getId();
        $salesTable->PaymentId               = $order->getPaymentGatewayId();
        $salesTable->HomePartyId             = ''; // FIXME "WEB .."
        $salesTable->SalesResponsible        = ''; // FIXME
        $salesTable->SalesGroup              = ''; // FIXME (initialer på konsulent)
        $salesTable->CurrencyCode            = ''; // FIXME
        $salesTable->SalesName               = $order->getFirstName() . ' ' . $order->getLastName();
        $salesTable->SalesType               = 'Sales';
        $salesTable->DeliveryCompanyName     = $order->getDeliveryCompanyName();
        $salesTable->DeliveryCity            = $order->getDeliveryCity();
        $salesTable->DeliveryName            = $order->getFirstName() . ' ' . $order->getLastName();
        $salesTable->DeliveryStreet          = $order->getDeliveryAddressLine1();
        $salesTable->DeliveryZipCode         = $order->getDeliveryPostalCode();
        $salesTable->DeliveryCountryRegionId = $this->getIso2CountryCode($order->getDeliveryCountriesId());
        $salesTable->InvoiceAccount          = $order->getCustomersId();
        $salesTable->FreightFeeAmt           = ''; // FIXME
        $salesTable->FreightType             = ''; // FIXME
        $salesTable->HandlingFeeType         = 90;
        $salesTable->HandlingFeeAmt          = ''; // FIXME
        $salesTable->PayByBillFeeType        = 91;
        $salesTable->PayByBillFeeAmt         = 0; // FIXME
        $salesTable->Completed               = 1;
        $salesTable->TransactionType         = 'Write';
        $salesTable->CustPaymMode            = 'Bank'; // FIXME: Bank, VISA, MasterCard, PayByBill, DanKort
        $salesTable->SalesLine               = $salesLine;
        $salesTable->SmoreContactInfo        = ''; // FIXME

Tools::log($salesTable);

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

        $cu = new \stdClass();
        $cu->CustTable = $ct;
        $sc = new \stdClass();
        $sc->customer = $cu;

        // TODO: no hardcoded switches
        $sc->endpointDomain = 'DK';
        switch ($ct->AddressCountryRegionId) {
            case 'SE':
            case 'NO':
                $sc->endpointDomain = $$ct->AddressCountryRegionId;
            break;
        }

        if ($return) {
            return $sc;
        }

        $result = $this->Send('SyncCustomer', $sc);

        if ($result instanceof \Exception) {
            $message = sprintf('An error occured while synchronizing debitor "%s", error message: "%s"',
                $debitor->getId(),
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
        // if (!$this->client) {
        //     if (!$this->Connect()) {
        //         return false;
        //     }
        // }

        try {
            return $this->client->{$service}($data);
        } catch (\SoapFault $e) {
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

        $this->client = new SoapClient($wsdl, array(
          'trace'      => true,
          'exceptions' => true,
        ));
        $this->client->__setLocation(str_replace('?wsdl', '', $wsdl));

        return true;
    }
}

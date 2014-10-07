<?php

namespace Hanzo\Bundle\AxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Tools;

/**
 * Class SoapController
 *
 * @package Hanzo\Bundle\AxBundle\Controller
 * @link    http://symfony.com/doc/2.0/cookbook/web_services/php_soap_extension.html
 * @link    http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 */
class SoapController extends Controller
{
    /**
     * @param Request $request
     * @param string  $version
     * @param string  $serviceName
     *
     * @return mixed
     * @throws \Exception
     */
    public function indexAction(Request $request, $version, $serviceName)
    {
        $wsdl = __DIR__ . '/../Actions/In/Soap/' . $serviceName . '/' . $serviceName . '.wsdl';

        if (!is_file($wsdl)) {
            throw new \Exception('Invalid or unknown SOAP service.');
        }

        $service = $this->get('ax.'.$serviceName.'.service');

        $server = new \SoapServer($wsdl);
        $server->setObject($service);
        $response = $service->exec($server);

        // we need this to not send the wsdl twice
        if ($request->query->has('wsdl') || $request->query->has('WSDL')) {
            $response->setContent('');
        }

        return $response;
    }

    /**
     * @return Response
     */
    public function testAction()
    {
        $client = new \SoapClient($this->generateUrl('ax_soap', ['version' => 'v1'], true).'?wsdl', ['trace' => 1]);
        $client->__setLocation('http://'.$_SERVER['HTTP_HOST'].'/da_DK/soap/v1/ECommerceServices/');
        //$client->__setLocation('http://ws.pompdelux.com/nl_NL/soap/v1/ECommerceServices/')


        $c = new \stdClass();
        $c->eOrderNumber = '15164';
        $c->amount = '-13.77';
        $c->initials = 'un';

        try {
            // $res = $client->SalesOrderAddDocument($d);
            $res = $client->SalesOrderCaptureOrRefund($c);
            Tools::log($client->__getLastResponse());
            Tools::log($res);
        } catch (\Exception $e) {
            Tools::log($client->__getLastRequestHeaders());
        }

        $response = new Response();
        $response->setContent('test .. see log');

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function fakeServiceAction(Request $request)
    {
        if ($request->query->has('wsdl')) {
            return new Response(file_get_contents(__DIR__.'/../Resources/ax/wsdl.xml'), 200, [
                'Content-Type: text/xml; charset=utf-8',
                'Cache-Control: private, max-age=0',
            ]);
        }

        $xml = simplexml_load_string($request->getContent(), null, null, "http://schemas.xmlsoap.org/soap/envelope/");
        $ns  = $xml->getNamespaces(true);

        $body = $xml->children($ns['SOAP-ENV'])->Body;
        $ns2 = $body->children($ns['ns2']);

        $response = '';
        switch ($ns2->getName()) {
            case 'SyncCustomer':
                $customer = $ns2->children($ns['ns1']);
                $customer->registerXPathNamespace('ns1', 'http://schemas.pompdelux.dk/webintegration/Customer');

                $matches = $customer->xpath('//ns1:CustTable/ns1:AccountNum');

                while (list(, $id) = each($matches)) {
                    $customerId = $id;
                }

                $response = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><SyncCustomerResponse xmlns="http://pompdelux.dk/webintegration/"><SyncCustomerResult><Message>Info: Anmodning modtaget </Message><Message>Info: Debitor '.$customerId.' er opdateret </Message><Status>Ok</Status></SyncCustomerResult></SyncCustomerResponse></soap:Body></soap:Envelope>';
                break;

            case 'SyncSalesOrder':
                $order = $ns2->children($ns['ns1']);
                $order->registerXPathNamespace('ns1', 'http://schemas.pompdelux.dk/webintegration/Customer');

                $matches = $order->xpath('//ns1:SalesTable/ns1:EOrderNumber');

                while (list(, $id) = each($matches)) {
                    $orderId = $id;
                }

                $response = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><SyncSalesOrderResponse xmlns="http://pompdelux.dk/webintegration/"><SyncSalesOrderResult><Message>Info: Anmodning modtaget SalesOrder </Message><Message>Info: Eordre '.$orderId.' oprettet </Message><Message>Info: Eordre '.$orderId.' afsluttet </Message><Status>Ok</Status></SyncSalesOrderResult></SyncSalesOrderResponse></soap:Body></soap:Envelope>';
                break;
        }

        return new Response($response, 200, [
            'Content-Type: text/xml; charset=utf-8',
            'Cache-Control: private, max-age=0',
        ]);
    }
}
/*

<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><SyncSalesOrderResponse xmlns="http://pompdelux.dk/webintegration/"><SyncSalesOrderResult><Message>Info: Anmodning modtaget SalesOrder </Message><Message>Info: Eordre 1595020 oprettet </Message><Message>Info: Eordre 1595020 afsluttet </Message><Status>Ok</Status></SyncSalesOrderResult></SyncSalesOrderResponse></soap:Body></soap:Envelope>


<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://schemas.pompdelux.dk/webintegration/SalesOrder" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns2="http://pompdelux.dk/webintegration/"><SOAP-ENV:Body><ns2:SyncSalesOrder><ns1:salesOrder><ns1:SalesTable><ns1:BankAccountNumber></ns1:BankAccountNumber><ns1:BankId></ns1:BankId><ns1:Completed>true</ns1:Completed><ns1:CurrencyCode>EUR</ns1:CurrencyCode><ns1:CustAccount>10056</ns1:CustAccount><ns1:CustPaymMode>Bank</ns1:CustPaymMode><ns1:DeliveryCity>Hamburg</ns1:DeliveryCity><ns1:DeliveryCountryRegionId>DE</ns1:DeliveryCountryRegionId><ns1:DeliveryDropPointId xsi:nil="true"/><ns1:DeliveryName>female Fru Heinrich Dalby</ns1:DeliveryName><ns1:DeliveryStreet>Test Strasse 1</ns1:DeliveryStreet><ns1:DeliveryZipCode>20001</ns1:DeliveryZipCode><ns1:EOrderNumber>21009</ns1:EOrderNumber><ns1:FreightFeeAmt>4.95</ns1:FreightFeeAmt><ns1:FreightType>601</ns1:FreightType><ns1:HandlingFeeAmt>0.00</ns1:HandlingFeeAmt><ns1:HandlingFeeType>90</ns1:HandlingFeeType><ns1:HomePartyId>WEB DE</ns1:HomePartyId><ns1:InvoiceAccount>10056</ns1:InvoiceAccount><ns1:PayByBillFeeAmt>0.00</ns1:PayByBillFeeAmt><ns1:PayByBillFeeType>91</ns1:PayByBillFeeType><ns1:PaymentId></ns1:PaymentId><ns1:SalesGroup xsi:nil="true"/><ns1:SalesLine><ns1:InventColorId>Eggplant</ns1:InventColorId><ns1:InventSizeId>80</ns1:InventSizeId><ns1:ItemId>AbbevilleLittleSKIRTAW14</ns1:ItemId><ns1:SalesLineText>Abbeville Little SKIRT</ns1:SalesLineText><ns1:SalesPrice>23.00</ns1:SalesPrice><ns1:SalesQty>1</ns1:SalesQty><ns1:SalesUnit>Stk.</ns1:SalesUnit><ns1:VoucherCode xsi:nil="true"/></ns1:SalesLine><ns1:SalesName>Fru Heinrich Dalby</ns1:SalesName><ns1:SalesResponsible>WEB DE</ns1:SalesResponsible><ns1:SalesType>Sales</ns1:SalesType><ns1:TransactionType>Write</ns1:TransactionType></ns1:SalesTable></ns1:salesOrder><ns2:endpointDomain>DE</ns2:endpointDomain></ns2:SyncSalesOrder></SOAP-ENV:Body></SOAP-ENV:Envelope>
*/

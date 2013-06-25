<?php

namespace Hanzo\Bundle\AxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Tools;

/**
 * @see
 *  http://symfony.com/doc/2.0/cookbook/web_services/php_soap_extension.html
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *
 */
class SoapController extends Controller
{
    public function indexAction(Request $request, $version, $service_name)
    {
        $wsdl = __DIR__ . '/../Actions/In/Soap/' . $service_name . '/' . $service_name . '.wsdl';

        if (!is_file($wsdl)) {
            throw new \Exception('Invalid or unknown SOAP service.');
        }

        $service = $this->get('ax.'.$service_name.'.service');

        $server = new \SoapServer($wsdl);
        $server->setObject($service);
        $response = $service->exec($server);

        // we need this to not send the wsdl twice
        if ($request->query->has('wsdl') || $request->query->has('WSDL')) {
            $response->setContent('');
        }

        return $response;
    }

    public function testAction()
    {
        $client = new \SoapClient($this->generateUrl('ax_soap', ['version' => 'v1'], true).'?wsdl', ['trace' => 1]);
        $client->__setLocation('http://'.$_SERVER['HTTP_HOST'].'/da_DK/soap/v1/ECommerceServices/');
        //$client->__setLocation('http://ws.pompdelux.com/nl_NL/soap/v1/ECommerceServices/')


        $c = new \stdClass();
        $c->eOrderNumber = '15164';
        $c->amount = '-13.77';
        $c->initials = 'un';


        // $d = new \stdClass();
        // $d->eOrderNumber = 1000000000000;
        // $d->fileName = 'xyz.pdf';

        try {
            // $res = $client->SalesOrderAddDocument($d);
            $res = $client->SalesOrderCaptureOrRefund($c);
            Tools::log($client->__getLastResponse());
            Tools::log($res);
        }
        catch (\Exception $e) {
            Tools::log($client->__getLastRequestHeaders());
        }

        $response = new Response();
        $response->setContent('test .. see log');
        return $response;
    }

}

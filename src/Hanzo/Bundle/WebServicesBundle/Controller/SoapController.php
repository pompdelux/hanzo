<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see
 *  http://symfony.com/doc/2.0/cookbook/web_services/php_soap_extension.html
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *
 */
class SoapController extends CoreController
{
    /**
     * @var
     */
    protected $request;

    /**
     * @param Request $request
     * @param         $version
     * @param         $serviceName
     *
     * @throws \Exception
     */
    public function indexAction(Request $request, $version, $serviceName)
    {
        $wsdl = __DIR__ . '/../Services/Soap/' . $serviceName . '/' . $serviceName . '.wsdl';

        if (!is_file($wsdl)) {
            throw new \Exception('Invalid or unknown SOAP service.');
        }

        $serviceClass = str_replace('Controller', 'Services\Soap', __NAMESPACE__) . "\\{$serviceName}\\$serviceName";
        $handler = new $serviceClass (
            $request,
            $this->get('Logger'),
            $this->get('event_dispatcher')
        );

        $service = new \SoapServer($wsdl);
        $service->setObject($handler);
        $handler->exec($service);
        exit;
    }

    public function testAction()
    {

        $handler = new \Hanzo\Bundle\WebServicesBundle\Services\Soap\ECommerceServices\ECommerceServices (
            $this->getRequest(),
            $this->get('Logger'),
            new \Symfony\Component\EventDispatcher\EventDispatcher()

        );

        $d = new \stdClass();
        $d->eOrderNumber = 549453;
        $d->fileName = 'xyz.pdf';
        $handler->SalesOrderAddDocument($d);

        return $this->response('test .. see log');

        $c = new \stdClass();
        $c->ItemId = 'Abby Little PANTSUIT';
        $c->SalesPrice = array();

        $p = new \stdClass();
        $p->AmountCur = 120.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'DKK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 80;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 120.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'DKK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 86;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 120.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'DKK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 92;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 120.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'DKK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 98;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 15.00;
        $p->Currency = 'EUR';
        $p->CustAccount = 'EUR';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 80;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 15.00;
        $p->Currency = 'EUR';
        $p->CustAccount = 'EUR';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 86;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 15.00;
        $p->Currency = 'EUR';
        $p->CustAccount = 'EUR';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 92;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 15.00;
        $p->Currency = 'EUR';
        $p->CustAccount = 'EUR';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 98;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 100.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'SalesDK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 80;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 100.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'SalesDK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 86;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 100.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'SalesDK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 92;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new \stdClass();
        $p->AmountCur = 100.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'SalesDK';
        $p->InventColorId = 'Blue Denim';
        $p->InventSizeId = 98;
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $l = new \stdClass();
        $l->priceList = $c;

        Tools::log($handler->SyncPriceList($l));
        return $this->response('test .. see log');
    }
}

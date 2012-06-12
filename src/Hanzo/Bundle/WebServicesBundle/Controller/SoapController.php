<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

/**
 * @see
 *  http://symfony.com/doc/2.0/cookbook/web_services/php_soap_extension.html
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *
 */
class SoapController extends CoreController
{
    protected $request;

    public function indexAction($version, $service_name)
    {
        $wsdl = __DIR__ . '/../Services/Soap/' . $service_name . '/' . $service_name . '.wsdl';

        if (!is_file($wsdl)) {
            throw new \Exception('Invalid or unknown SOAP service.');
        }

        $service_class = str_replace('Controller', 'Services\Soap', __NAMESPACE__) . "\\{$service_name}\\$service_name";
        $handler = new $service_class (
            $this->getRequest(),
            $this->get('Logger')
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
            $this->get('Logger')
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

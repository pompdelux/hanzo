<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hanzo\Bundle\WebServicesBundle\Services\Soap\ECommerceServices\ECommerceServices;

use Hanzo\Core\Tools;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\AddressesPeer;

class DefaultController extends Controller
{
    public function cookieAction()
    {
        Tools::setCookie('x_mobile_x', '1');
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.$this->getRequest()->getLocale());
        header('cache-control: no-cache');
        exit;
    }

    public function indexAction($name)
    {


$xml = '
<SyncItem xmlns="http://thydata.dk.ECommerceServices/">
  <item xmlns="http://schemas.pompdelux.dk/webintegration/item">
    <InventTable>
      <ItemGroupId>G_Access,LG_Access</ItemGroupId>
      <ItemGroupName>Girl Accessories</ItemGroupName>
      <ItemId>Ada HAT</ItemId>
      <WebEnabled>1</WebEnabled>
      <ItemName>Ada HAT</ItemName>
      <ItemType>Item</ItemType>
      <NetWeight>0.00</NetWeight>
      <BlockedDate>1990-01-01</BlockedDate>
      <InventDim>
        <InventColorId>Dark Grey Melange</InventColorId>
        <InventSizeId>50</InventSizeId>
      </InventDim>
      <InventDim>
        <InventColorId>Violet</InventColorId>
        <InventSizeId>50</InventSizeId>
      </InventDim>

      <InventDim>
        <InventColorId>Dark Grey Melange</InventColorId>
        <InventSizeId>52</InventSizeId>
      </InventDim>
      <InventDim>
        <InventColorId>Violet</InventColorId>
        <InventSizeId>52</InventSizeId>
      </InventDim>

      <InventDim>
        <InventColorId>Dark Grey Melange</InventColorId>
        <InventSizeId>54</InventSizeId>
      </InventDim>
      <InventDim>
        <InventColorId>Violet</InventColorId>
        <InventSizeId>54</InventSizeId>
      </InventDim>

      <WebDomain>COM</WebDomain>
      <WebDomain>DK</WebDomain>
      <WebDomain>SalesDK</WebDomain>
      <Sales>
        <Price>0.00</Price>
        <UnitId>Stk.</UnitId>
      </Sales>
      <WashInstruction/>
    </InventTable>
  </item>
</SyncItem>
';


$xml = '
<SyncPriceList xmlns="http://thydata.dk.ECommerceServices/">
  <priceList xmlns="http://schemas.pompdelux.dk/webintegration/pricelist">
    <ItemId>Ada HAT</ItemId>
<!-- dkk / dkk -->
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>DKK</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>50</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>DKK</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>50</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>

    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>DKK</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>52</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>DKK</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>52</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>

    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>DKK</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>54</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>DKK</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>54</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
<!-- dkk / eur -->
    <SalesPrice>
      <AmountCur>2.50</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>EUR</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>50</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>2.50</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>EUR</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>50</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>

    <SalesPrice>
      <AmountCur>2.50</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>EUR</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>52</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>2.50</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>EUR</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>52</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>

    <SalesPrice>
      <AmountCur>2.50</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>EUR</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>54</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>2.50</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>EUR</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>54</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
<!-- dkk / salesdk -->
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>SalesDK</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>50</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>SalesDK</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>50</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>

    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>SalesDK</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>52</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>SalesDK</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>52</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>

    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>SalesDK</CustAccount>
      <InventColorId>Dark Grey Melange</InventColorId>
      <InventSizeId>54</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
    <SalesPrice>
      <AmountCur>20.00</AmountCur>
      <Currency>DKK</Currency>
      <CustAccount>SalesDK</CustAccount>
      <InventColorId>Violet</InventColorId>
      <InventSizeId>54</InventSizeId>
      <PriceUnit>1.00</PriceUnit>
      <Quantity>1.00</Quantity>
      <UnitId>Stk.</UnitId>
    </SalesPrice>
  </priceList>
</SyncPriceList>
';
$x = json_decode(json_encode(simplexml_load_string($xml)));
$e = new ECommerceServices($this->get('request'), $this->get('logger'));

//$e->SyncItem($x);
$res = $e->SyncPriceList($x);
Tools::log($res);


// \Propel::getConnection()->useDebug(true);

// $lat = 55.494099;
// $lon = 9.459;
// $radius = 100;
// $exclude_pompdelux = array('hdkon@pompdelux.dk','mail@pompdelux.dk','hd@pompdelux.dk','kk@pompdelux.dk','sj@pompdelux.dk','ak@pompdelux.dk','test@pompdelux.dk');
// $exclude_bellcom = '%@bellcom.dk';

// $query = AddressesQuery::create()
//     ->filterByDistanceFrom($lat, $lon, $radius)
//     ->filterByType('payment')
//     ->useCustomersQuery('', 'JOIN')
//         ->filterByGroupsId(2)
//         ->filterByIsActive(true)
//         ->filterByEmail($exclude_pompdelux, \Criteria::NOT_IN)
//         ->filterByEmail($exclude_bellcom, \Criteria::NOT_LIKE)
//     ->endUse()
//     ->limit(10)
//     ->orderBy('Distance')
// ;

// $query
//     ->useCustomersQuery('', 'JOIN')
//         ->useConsultantsQuery('', 'JOIN')
//             ->filterByEventNotes('', \Criteria::NOT_EQUAL)
//         ->endUse()
//     ->endUse()
// ;

// $consultants = $query->find();

// error_log(\Propel::getConnection()->getLastExecutedQuery());


        return $this->render('WebServicesBundle:Default:index.html.twig', array('name' => $name));
    }
}

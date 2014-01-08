<?php

namespace Hanzo\Bundle\AxBundle\Tests\Actions\In\Soap\ECommerceServices;

use Hanzo\Core\Tools;

use Symfony\Bridge\Monolog\Logger;
use Hanzo\Core\Tests\WebTestCase;
use Hanzo\Bundle\AxBundle\Actions\In\Soap\ECommerceServices\ECommerceServices;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesI18n;
use Hanzo\Model\Countries;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Domains;
use Hanzo\Model\Groups;
use Hanzo\Model\Languages;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsQuery;

/**
 * Tests the ECommerceServices SOAP service.
 *
 * Note: This does not test the actual SOAP layer, only the service class.
 */
class ECommerceServicesTest extends WebTestCase
{
    /**
     * Ensure language, category and domain records are setup.
     */
    public function setUp()
    {
        $this->setupDomains();
        $this->setupCountries();
        $this->setupLanguages();
        $this->setupCategories();
    }


    /**
     * Tests SyncItem Service.
     */
    public function testSyncItem()
    {
        $data    = $this->getFakeItem();
        $handler = $this->getHandler();
        $result  = $handler->SyncItem($data);

        $this->assertObjectHasAttribute('SyncItemResult', $result);
        $this->assertEquals($result->SyncItemResult->Status->enc_value, 'Ok');

        $sku             = $data->item->InventTable->ItemId;
        $variant_1_color = $data->item->InventTable->InventDim[0]->InventColorId;
        $variant_1_size  = $data->item->InventTable->InventDim[0]->InventSizeId;

        $product = ProductsQuery::create()->findOneBySku($sku);
        $this->assertEquals($product->getUnit(), '1 Stk.');

        $product = ProductsQuery::create()->findOneBySku($sku.' '.$variant_1_color.' '.$variant_1_size);
        $this->assertEquals($product->getMaster(), $sku);
    }


    /**
     * Test SyncItem service - change name of a product.
     */
    public function testUpdateItemTitle()
    {
        $data    = $this->getFakeItem();
        $handler = $this->getHandler();

        $data->item->InventTable->ItemName = 'some thing else';

        $result  = $handler->SyncItem($data);
        $this->assertEquals($result->SyncItemResult->Status->enc_value, 'Ok');

        $sku = $data->item->InventTable->ItemId;
        $product = ProductsQuery::create()
            ->useProductsI18nQuery()
            ->filterByLocale('da_DK')
            ->endUse()
            ->joinWithProductsI18n()
            ->findOneBySku($sku)
        ;

        $this->assertEquals($product->getTitle(), $data->item->InventTable->ItemName);
    }


    /**
     * Tests SyncPriceList Service.
     */
    public function testSyncPriceList()
    {
        $priceList = new \stdClass();
        $priceList->ItemId     = '';
        $priceList->SalesPrice = [];

        $products = ProductsQuery::create()
            ->filterByMaster(null, \Criteria::ISNOTNULL)
            ->find()
        ;

        $test_product_data = [];
        foreach ($products as $product) {
            if (empty($priceList->ItemId)) {
                $priceList->ItemId = $product->getMaster();
            }

            $test_product_data[$product->getId()] = [
                'prices' => [120, 100, 90],
                'dates' => ['2014-01-01', '2014-02-01', date('Y-m-d', strtotime('-1 year'))],
            ];

            $SalesPrice = new \stdClass();
            $SalesPrice->AmountCur     = 120.00;
            $SalesPrice->Currency      = 'DKK';
            $SalesPrice->CustAccount   = 'DKK';
            $SalesPrice->InventColorId = $product->getColor();
            $SalesPrice->InventSizeId  = $product->getSize();
            $SalesPrice->PriceUnit     = 1.00;
            $SalesPrice->Quantity      = 1.00;
            $SalesPrice->UnitId        = 'Stk.';
            $priceList->SalesPrice[]   = $SalesPrice;

            $SalesPrice = new \stdClass();
            $SalesPrice->AmountCur     = 100.00;
            $SalesPrice->Currency      = 'DKK';
            $SalesPrice->CustAccount   = 'SalesDK';
            $SalesPrice->InventColorId = $product->getColor();
            $SalesPrice->InventSizeId  = $product->getSize();
            $SalesPrice->PriceUnit     = 1.00;
            $SalesPrice->Quantity      = 1.00;
            $SalesPrice->UnitId        = 'Stk.';
            $priceList->SalesPrice[]   = $SalesPrice;

            $SalesPrice = new \stdClass();
            $SalesPrice->AmountCur     = 100.00;
            $SalesPrice->Currency      = 'DKK';
            $SalesPrice->CustAccount   = 'DKK';
            $SalesPrice->InventColorId = $product->getColor();
            $SalesPrice->InventSizeId  = $product->getSize();
            $SalesPrice->PriceUnit     = 1.00;
            $SalesPrice->Quantity      = 1.00;
            $SalesPrice->UnitId        = 'Stk.';
            $SalesPrice->PriceDate     = '2014-01-01';
            $SalesPrice->PriceDateTo   = '2014-02-01';
            $priceList->SalesPrice[]   = $SalesPrice;

            $SalesPrice = new \stdClass();
            $SalesPrice->AmountCur     = 90.00;
            $SalesPrice->Currency      = 'DKK';
            $SalesPrice->CustAccount   = 'SalesDK';
            $SalesPrice->InventColorId = $product->getColor();
            $SalesPrice->InventSizeId  = $product->getSize();
            $SalesPrice->PriceUnit     = 1.00;
            $SalesPrice->Quantity      = 1.00;
            $SalesPrice->UnitId        = 'Stk.';
            $SalesPrice->PriceDate     = '2014-01-01';
            $SalesPrice->PriceDateTo   = '2014-02-01';
            $priceList->SalesPrice[]   = $SalesPrice;
        }

        $data = new \stdClass();
        $data->priceList = $priceList;

        $handler = $this->getHandler();
        $result  = $handler->SyncPriceList($data);

        $this->assertObjectHasAttribute('SyncPriceListResult', $result);
        $this->assertEquals($result->SyncPriceListResult->Status->enc_value, 'Ok');

        $price_count = ProductsDomainsPricesQuery::create()->count();
        $this->assertEquals($price_count, 12);

        $prices = ProductsDomainsPricesQuery::create()
            ->useProductsQuery()
                ->filterByMaster(null, \Criteria::ISNOTNULL)
            ->endUse()
            ->find()
        ;

        foreach ($prices as $price) {
            $this->assertContains(($price->getPrice() + $price->getVat()), $test_product_data[$price->getProductsId()]['prices']);
            $this->assertContains(($price->getFromDate('Y-m-d')), $test_product_data[$price->getProductsId()]['dates']);
        }
    }


    /**
     * Tests SyncInventoryOnHand Service.
     */
    public function testSyncInventoryOnHand()
    {
        $inventoryOnHand = (object) [
            'InventSum' => (object) [
                'ItemId'      => '',
                'LastInCycle' => false,
                'InventDim'   => []
            ],
        ];

        $products = ProductsQuery::create()
            ->filterByMaster(null, \Criteria::ISNOTNULL)
            ->find()
        ;

        $test_data = [];
        $date = date('Y-m-d', strtotime('+2 months'));
        foreach ($products as $product) {
            if (empty($inventoryOnHand->InventSum->ItemId)) {
                $inventoryOnHand->InventSum->ItemId = $product->getMaster();
            }

            $inventoryOnHand->InventSum->InventDim[] = (object) [
                'InventColorId'             => $product->getColor(),
                'InventSizeId'              => $product->getSize(),
                'InventQtyAvailOrdered'     => '',
                'InventQtyAvailOrderedDate' => '',
                'InventQtyAvailPhysical'    => '100',
            ];

            $inventoryOnHand->InventSum->InventDim[] = (object) [
                'InventColorId'             => $product->getColor(),
                'InventSizeId'              => $product->getSize(),
                'InventQtyAvailOrdered'     => '100',
                'InventQtyAvailOrderedDate' => $date,
                'InventQtyAvailPhysical'    => '',
            ];

            $test_data[$inventoryOnHand->InventSum->ItemId.' '.$product->getColor().' '.$product->getSize()] = [
                '2000-01-01' => 100,
                $date => 100
            ];
        }

        $data = new \stdClass();
        $data->inventoryOnHand = $inventoryOnHand;

        $handler = $this->getHandler();
        $result  = $handler->SyncInventoryOnHand($data);

        $this->assertObjectHasAttribute('SyncInventoryOnHandResult', $result);
        $this->assertEquals(ProductsStockQuery::create()->count(), count($inventoryOnHand->InventSum->InventDim));

        $stocks = ProductsStockQuery::create()
            ->useProductsQuery()
                ->filterByMaster(null, \Criteria::ISNOTNULL)
            ->endUse()
            ->find()
        ;

        foreach ($stocks as $stock) {
            $sku = $stock->getProducts()->getSku();
            $date = $stock->getAvailableFrom();

            $this->assertArrayHasKey($sku, $test_data);
            $this->assertArrayHasKey($date, $test_data[$sku]);
            $this->assertEquals($stock->getQuantity(), $test_data[$sku][$date]);
        }
    }


    public function testSyncCustomer()
    {
        $group = new Groups();
        $group->setName('customer');
        $group->setDiscount(0);
        $group->save();

        $group = new Groups();
        $group->setName('consultant');
        $group->setDiscount(20);
        $group->save();

        $group = new Groups();
        $group->setName('employee');
        $group->setDiscount(40);
        $group->save();

        $customer = (object) [
            'CustTable' => (object) [
                'AccountNum' => 109381,
                'InitialsId' => '',
                'CustName' => 'Mamarie M Karlsson',
                'AddressStreet' => 'marknadsvøgen 7',
                'AddressCity' => 'bjærketorp',
                'AddressZipCode' => '51994',
                'AddressCountryRegionId' => 'DK',
                'CustCurrencyCode' => 'DKK',
                'Email' => 'mariedelice@hotmail.com',
                'Phone' => '004632060004',
                'PhoneLocal' => '',
                'PhoneMobile' => '',
                'TeleFax' => '',
                'SalesDiscountPercent' => '',
            ]
        ];

        $data = new \stdClass();
        $data->customer = $customer;

        $handler = $this->getHandler();
        $result  = $handler->SyncCustomer($data);

        $this->assertObjectHasAttribute('SyncCustomerResult', $result);
        $this->assertEquals($result->SyncCustomerResult->Status->enc_value, 'Ok');

        $customer = CustomersQuery::create()->findOne();
        $this->assertEquals($customer->getFirstName(), 'Mamarie');
        $this->assertEquals($customer->getLastName(), 'M Karlsson');
        $this->assertEquals($customer->getEmail(), 'mariedelice@hotmail.com');
        $this->assertEquals($customer->getPhone(), '004632060004');
        $this->assertEquals($customer->getPassword(), sha1('004632060004'));

        $address = AddressesQuery::create()
            ->filterByType('payment')
            ->findOneByCustomersId($customer->getId())
        ;

        $this->assertTrue($address instanceof Addresses);
        $this->assertEquals($address->getFirstName(), 'Mamarie');
        $this->assertEquals($address->getLastName(), 'M Karlsson');
        $this->assertEquals($address->getAddressLine1(), 'marknadsvøgen 7');
        $this->assertEquals($address->getPostalCode(), '51994');
    }



    /**
     * Helper method.
     *
     * This set's up domain records.
     */
    protected function setupDomains()
    {
        static $done;

        if (!empty($done)) {
            return;
        }

        $d = new Domains();
        $d->setDomainName('dk');
        $d->setDomainKey('DK');
        $d->save();

        $d = new Domains();
        $d->setDomainName('dk');
        $d->setDomainKey('SalesDK');
        $d->save();

        $d = new Domains();
        $d->setDomainName('com');
        $d->setDomainKey('COM');
        $d->save();

        $done = true;
    }


    /**
     * Helper method.
     *
     * This set's up language records.
     */
    protected function setupLanguages()
    {
        static $done;

        if (!empty($done)) {
            return;
        }

        $l = new Languages();
        $l->setName('Danish');
        $l->setLocalName('Dansk');
        $l->setLocale('da_DK');
        $l->setIso2('da');
        $l->setDirection('ltr');
        $l->save();

        $done = true;
    }


    protected function setupCountries()
    {
        static $done;

        if (!empty($done)) {
            return;
        }

        $c = new Countries();
        $c->setName('Denmark');
        $c->setLocalName('Danmark');
        $c->setCode(208);
        $c->setIso2('DK');
        $c->setIso3('DNK');
        $c->setContinent('EU');
        $c->setCurrencyId(208);
        $c->setCurrencyCode('DKK');
        $c->setCurrencyName('Danish Kroner');
        $c->setVat(25);
        $c->setCallingCode(45);
        $c->save();

        $done = true;
    }


    /**
     * Helper method.
     *
     * This set's up category records.
     */
    protected function setupCategories()
    {
        static $done;

        if (!empty($done)) {
            return;
        }

        $c = new Categories();
        $c->setContext('G_Access');
        $c->setIsActive(true);
        $c->save();

        $ci = new CategoriesI18n();
        $ci->setId($c->getId());
        $ci->setTitle('Girl Accessories');
        $ci->setLocale('da_DK');
        $ci->setContent('test 1.2.3');
        $ci->save();

        $c = new Categories();
        $c->setContext('LG_Access');
        $c->setIsActive(true);
        $c->save();

        $ci = new CategoriesI18n();
        $ci->setId($c->getId());
        $ci->setTitle('Little Girl Accessories');
        $ci->setLocale('da_DK');
        $ci->setContent('test 1.2.3');
        $ci->save();

        $done = true;
    }


    /**
     * Helper method, setup ECommerceServices object for testing.
     *
     * @return ECommerceServices
     */
    protected function getHandler()
    {
        static $soap;

        if (empty($soap)) {
            $soap = new ECommerceServices(
                new Request(),
                $this->getApplication()->getKernel()->getContainer()->get('Logger'),
                new EventDispatcher()
            );
        }

        return $soap;
    }

    protected function getFakeItem()
    {
        require str_replace('Tests/', '', __DIR__).'/products_id_map.php';

        $product   = array_keys($products_id_map);
        $master    = array_shift($product);
        $variant_1 = array_shift($product);
        $variant_2 = array_shift($product);
        $sku       = str_replace(' ', '', $master).'SS14';

        if (strpos($variant_1, 'one size')) {
            $variant_1_size = 'one size';
            $variant_1_color = trim(str_replace('one size', '', str_replace($master, '', $variant_1)));
        } else {
            preg_match('/([0-9][0-9 -]+)/', $variant_1, $matches);
            $variant_1_size = $matches[1];
            $variant_1_color = trim(str_replace($matches[1], '', str_replace($master, '', $variant_1)));
        }

        if (strpos($variant_2, 'one size')) {
            $variant_2_size = 'one size';
            $variant_2_color = trim(str_replace('one size', '', str_replace($master, '', $variant_2)));
        } else {
            preg_match('/([0-9][0-9 -]+)/', $variant_2, $matches);
            $variant_2_size = $matches[1];
            $variant_2_color = trim(str_replace($matches[1], '', str_replace($master, '', $variant_2)));
        }

        $InventTable = new \stdClass();
        $InventTable->ItemGroupId     = "G_Access,LG_Access";
        $InventTable->ItemGroupName   = "Girl Accessories";
        $InventTable->ItemId          = $sku;
        $InventTable->WebEnabled      = true;
        $InventTable->ItemName        = $master;
        $InventTable->ItemType        = "Item";
        $InventTable->NetWeight       = 0.00;
        $InventTable->BlockedDate     = "1990-01-01";
        $InventTable->WashInstruction = null;
        $InventTable->IsVoucher       = false;
        $InventTable->WebDomain       = ['COM', 'DK', 'SalesDK'];

        $InventTable->Sales = (object) [
            "Price"  => 0.00,
            "UnitId" => "Stk.",
        ];

        $InventTable->InventDim = [
            (object) [
                "InventColorId" => $variant_1_color,
                "InventSizeId"  => $variant_1_size,
            ],
            (object) [
                "InventColorId" => $variant_2_color,
                "InventSizeId"  => $variant_2_size,
            ],
        ];


        $data = new \stdClass();
        $data->item = new \stdClass();
        $data->item->InventTable = $InventTable;

        return $data;
    }
}

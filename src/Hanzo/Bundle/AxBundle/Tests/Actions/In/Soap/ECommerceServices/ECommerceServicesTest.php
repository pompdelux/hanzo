<?php

namespace Hanzo\Bundle\AxBundle\Tests\Actions\In\Soap\ECommerceServices;

use stdClass;

use Hanzo\Core\Tools;
use Hanzo\Core\Tests\WebTestCase;
use Hanzo\Bundle\AxBundle\Actions\In\Soap\ECommerceServices\ECommerceServices;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesI18n;
use Hanzo\Model\Domains;
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
        $this->setupLanguages();
        $this->setupCategories();
        $this->setupDomains();
    }


    /**
     * Tests SyncItem Service.
     */
    public function testSyncItem()
    {
        require str_replace('Tests/', '', __DIR__).'/products_id_map.php';

        $product   = array_keys($products_id_map);
        $master    = array_shift($product);
        $variant_1 = array_shift($product);
        $variant_2 = array_shift($product);

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

        $InventTable = new stdClass();
        $InventTable->ItemGroupId     = "G_Access,LG_Access";
        $InventTable->ItemGroupName   = "Girl Accessories";
        $InventTable->ItemId          = $master;
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

        $handler = $this->getHandler();

        $data = new stdClass();
        $data->item = new stdClass();
        $data->item->InventTable = $InventTable;

        $result = $handler->SyncItem($data);
        $this->assertEquals($result->SyncItemResult->Status->enc_value, 'Ok');

        $product = ProductsQuery::create()->findOneBySku($master);
        $this->assertEquals($product->getUnit(), '1 Stk.');

        $product = ProductsQuery::create()->findOneBySku($master.' '.$variant_1_color.' '.$variant_1_size);
        $this->assertEquals($product->getMaster(), $master);
    }


    /**
     * Tests SyncPriceList Service.
     */
    public function testSyncPriceList()
    {
        $priceList = new stdClass();
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

            $SalesPrice = new stdClass();
            $SalesPrice->AmountCur     = 120.00;
            $SalesPrice->Currency      = 'DKK';
            $SalesPrice->CustAccount   = 'DKK';
            $SalesPrice->InventColorId = $product->getColor();
            $SalesPrice->InventSizeId  = $product->getSize();
            $SalesPrice->PriceUnit     = 1.00;
            $SalesPrice->Quantity      = 1.00;
            $SalesPrice->UnitId        = 'Stk.';
            $priceList->SalesPrice[]   = $SalesPrice;

            $SalesPrice = new stdClass();
            $SalesPrice->AmountCur     = 100.00;
            $SalesPrice->Currency      = 'DKK';
            $SalesPrice->CustAccount   = 'SalesDK';
            $SalesPrice->InventColorId = $product->getColor();
            $SalesPrice->InventSizeId  = $product->getSize();
            $SalesPrice->PriceUnit     = 1.00;
            $SalesPrice->Quantity      = 1.00;
            $SalesPrice->UnitId        = 'Stk.';
            $priceList->SalesPrice[]   = $SalesPrice;

            $SalesPrice = new stdClass();
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

            $SalesPrice = new stdClass();
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

        $data = new stdClass();
        $data->priceList = $priceList;

        $handler = $this->getHandler();
        $result  = $handler->SyncPriceList($data);

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

        $data = new stdClass();
        $data->inventoryOnHand = $inventoryOnHand;

        $handler = $this->getHandler();
        $result  = $handler->SyncInventoryOnHand($data);

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


    /**
     * Helper method.
     *
     * This set's up domain records.
     */
    protected function setupDomains()
    {
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
    }


    /**
     * Helper method.
     *
     * This set's up language records.
     */
    protected function setupLanguages()
    {
        $l = new Languages();
        $l->setName('Danish');
        $l->setLocalName('Dansk');
        $l->setLocale('da_DK');
        $l->setIso2('da');
        $l->setDirection('ltr');
        $l->save();
    }


    /**
     * Helper method.
     *
     * This set's up category records.
     */
    protected function setupCategories()
    {
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
}

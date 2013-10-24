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
use Hanzo\Model\Languages;
use Hanzo\Model\ProductsQuery;

class ECommerceServicesTest extends WebTestCase
{
    public function setUp()
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

        $l = new Languages();
        $l->setName('Danish');
        $l->setLocalName('Dansk');
        $l->setLocale('da_DK');
        $l->setIso2('da');
        $l->setDirection('ltr');
        $l->save();
    }

    public function testSyncItem()
    {
        $handler = new ECommerceServices(
            new Request(),
            $this->getApplication()->getKernel()->getContainer()->get('Logger'),
            new EventDispatcher()

        );

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


    public function testSyncPriceList()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $handler = new ECommerceServices(
            new Request(),
            $this->getApplication()->getKernel()->getContainer()->get('Logger'),
            new EventDispatcher()

        );

        $c = new stdClass();
        $c->ItemId = 'Ada HAT';
        $c->SalesPrice = array();

        $p = new stdClass();
        $p->AmountCur = 120.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'DKK';
        $p->InventColorId = 'Dark Grey Melange';
        $p->InventSizeId = 'one size';
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new stdClass();
        $p->AmountCur = 120.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'DKK';
        $p->InventColorId = 'Violet';
        $p->InventSizeId = 'one size';
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new stdClass();
        $p->AmountCur = 100.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'SalesDK';
        $p->InventColorId = 'Dark Grey Melange';
        $p->InventSizeId = 'one size';
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $p = new stdClass();
        $p->AmountCur = 100.00;
        $p->Currency = 'DKK';
        $p->CustAccount = 'SalesDK';
        $p->InventColorId = 'Violet';
        $p->InventSizeId = 'one size';
        $p->PriceUnit = 1.00;
        $p->Quantity = 1.00;
        $p->UnitId = 'Stk.';
        $c->SalesPrice[] = $p;

        $l = new stdClass();
        $l->priceList = $c;

        \Hanzo\Core\Tools::log($handler->SyncPriceList($l));

        $this->assertEquals('12', '12');
    }
}

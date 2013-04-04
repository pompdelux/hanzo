<?php

namespace Hanzo\Core\Tests;

use PropelCollection;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributes;

class ToolsTest extends \PHPUnit_Framework_TestCase
{
    public function testStripText()
    {
        $text = 'Åse gik en tur, mens Æske læste avis - ikke ?';

        $this->assertEquals('aase-gik-en-tur-mens-aeske-laeste-avis-ikke', Tools::stripText($text));
        $this->assertEquals('AASE-GIK-EN-TUR-MENS-AESKE-LAESTE-AVIS-IKKE', Tools::stripText(mb_strtoupper($text, 'UTF-8'), '-', false));
        $this->assertEquals('aase.gik.en.tur.mens.aeske.laeste.avis.ikke', Tools::stripText($text, '.'));
    }

    public function testStripTags()
    {
        $src = '<div><p class="xxx">test</p>test</div>';

        $this->assertEquals('test test', Tools::stripTags($src));
    }

    public function testOrderAddress()
    {
        $order = new Orders();
        $order
            ->setFirstName('first name')
            ->setLastName('last name')
            ->setBillingAddressLine1('address line 1')
            ->setBillingAddressLine2('address line 2')
            ->setBillingCity('city name')
            ->setBillingPostalCode('postal code')
            ->setBillingCountry('country name')
            ->setBillingStateProvince('some state')
            ->setBillingFirstName('first name')
            ->setBillingLastName('last name')
            ->setDeliveryAddressLine1('address line 1')
            ->setDeliveryAddressLine2('address line 2')
            ->setDeliveryCity('city name')
            ->setDeliveryPostalCode('postal code')
            ->setDeliveryCountry('country name')
            ->setDeliveryStateProvince('some state')
            ->setDeliveryFirstName('first name')
            ->setDeliveryLastName('last name')
        ;

        $address = Tools::orderAddress('payment', $order);
        $this->assertEquals('first name last name
address line 1
address line 2
postal code city name
country name', $address);

        $address = Tools::orderAddress('shipping', $order);
        $this->assertEquals('first name last name
address line 1
address line 2
postal code city name
country name', $address);

        $order
            ->setBillingCompanyName('company name')
            ->setDeliveryCompanyName('company name')
        ;

        $address = Tools::orderAddress('billing', $order);
        $this->assertEquals('company name
Att: first name last name
address line 1
address line 2
postal code city name
country name', $address);

        $address = Tools::orderAddress('delivery', $order);
        $this->assertEquals('company name
Att: first name last name
address line 1
address line 2
postal code city name
country name', $address);


    }


    public function testMoneyFormat()
    {
        $number = 2100.25;

        setlocale(LC_ALL, 'da_DK');
        $this->assertEquals('DKK 2.100,25', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'nl_NL');
        $this->assertEquals('EUR 2 100,25', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'sv_SE');
        $this->assertEquals('2 100,25 SEK', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'en_GB');
        $this->assertEquals('GBP 2,100.25', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'en_US');
        $this->assertEquals('USD 2,100.25', Tools::moneyFormat($number));
    }


    public function testGetBccEmailAddress()
    {
        $order = new Orders();
        $order->setId(1);

        $attr = new OrdersAttributes();
        $attr->setOrdersId($order->getId());
        $attr->setNs('global');
        $attr->setCKey('domain_key');
        $attr->setCValue('DK');

        $c = new PropelCollection();
        $c->prepend($attr);

        $order->setOrdersAttributess($c);

        $this->assertEquals('order@pompdelux.dk', Tools::getBccEmailAddress('order', $order));
        $this->assertEquals('retur@pompdelux.dk', Tools::getBccEmailAddress('retur', $order));

        $order = new Orders();
        $order->setId(2);

        $attr = new OrdersAttributes();
        $attr->setOrdersId($order->getId());
        $attr->setNs('global');
        $attr->setCKey('domain_key');
        $attr->setCValue('SalesFI');

        $c = new PropelCollection();
        $c->prepend($attr);

        $order->setOrdersAttributess($c);

        $this->assertEquals('orderfi@pompdelux.com', Tools::getBccEmailAddress('order', $order));
        $this->assertEquals('returfi@pompdelux.dk', Tools::getBccEmailAddress('retur', $order));
    }
}

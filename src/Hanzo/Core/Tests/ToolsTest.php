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

        $this->assertEquals('testtest', Tools::stripTags($src));
    }


    public function testMoneyFormat()
    {
        $number = 2100.25;

        $l = setlocale(LC_ALL, 'da_DK.utf8', 'da_DK.UTF-8', 'da_DK');

        if (false === $l) {
            $this->markTestIncomplete('Locales could not be changed.');
        }

        $this->assertEquals('DKK 2.100,25', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'nl_NL.utf8', 'nl_NL.UTF-8', 'nl_NL');
        $this->assertEquals('EUR 2 100,25', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'sv_SE.utf8', 'sv_SE.UTF-8', 'sv_SE');
        $this->assertEquals('2 100,25 SEK', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'en_GB.utf8', 'en_GB.UTF-8', 'en_GB');
        $this->assertEquals('GBP 2,100.25', Tools::moneyFormat($number));

        setlocale(LC_ALL, 'en_US.utf8', 'en_US.UTF-8', 'en_US');
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

        $this->assertEquals('orderdk@pompdelux.com', Tools::getBccEmailAddress('order', $order));
        $this->assertEquals('returdk@pompdelux.com', Tools::getBccEmailAddress('retur', $order));

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
        $this->assertEquals('returfi@pompdelux.com', Tools::getBccEmailAddress('retur', $order));
    }
}

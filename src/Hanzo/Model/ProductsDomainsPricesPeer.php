<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseProductsDomainsPricesPeer;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;

use \Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'products_domains_prices' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class ProductsDomainsPricesPeer extends BaseProductsDomainsPricesPeer {


    public static function getProductsPrices(array $products)
    {
        $hanzo     = Hanzo::getInstance();
        $customer  = CustomersPeer::getCurrent();
        $domainId  = $hanzo->get('core.domain_id');
        $domainKey = $hanzo->get('core.domain_key');
        $customer  = CustomersPeer::getCurrent();

        $prices = ProductsDomainsPricesQuery::create()
            ->filterByProductsId($products)
            ->filterByDomainsId($domainId)
            ->filterByFromDate(time(), Criteria::LESS_EQUAL)
            ->filterByToDate(time(), Criteria::GREATER_EQUAL)
            ->_or()
            ->filterByToDate(null, Criteria::ISNULL)
            ->orderByProductsId()
            ->orderByFromDate(Criteria::DESC)
            ->orderByToDate(Criteria::DESC)
            ->find();

        $overrideVat    = false;
        $overrideVatPct = 0;

        if ('COM' == $domainKey) {
            if (!$customer->isNew()) {
                $country = CountriesQuery::create()
                    ->useAddressesQuery()
                        ->filterByType('payment')
                        ->filterByCustomersId($customer->getId())
                    ->endUse()
                    ->findOne()
                ;

                if (($country instanceof Countries) && !$country->getVat()) {
                    $overrideVat    = true;
                    $overrideVatPct = 0;
                }
            }
        }

        $data = [];
        foreach ($prices as $price) {
            $key = $price->getToDate() ? 'sales' : 'normal';
            $vat = $price->getVat();
            $p   = $price->getPrice() + $vat;

            if ($overrideVat) {
                $vat = 0;
                $p   = $price->getPrice();
                if ($overrideVatPct > 0) {
                    $savePrice = $p;
                    $p         = $p * (($overrideVatPct / 100) + 1);
                    $vat       = $price - $savePrice;
                }
            }

            $data[$price->getProductsId()][$key] = [
                'currency'  => $price->getCurrencyId(),
                'raw_price' => $price->getPrice(),
                'price'     => number_format($p, 4, '.', ''),
                'vat'       => $vat,
                'sales_pct' => 0.00,
                'formattet' => Tools::moneyFormat($p)
            ];
        }

        foreach ($data as $productId => $prices) {
            if (isset($prices['sales'])) {
                $data[$productId]['sales']['sales_pct'] = (($prices['normal']['price'] - $prices['sales']['price']) / $prices['normal']['price']) * 100;
            }
        }

        return $data;
    }

} // ProductsDomainsPricesPeer

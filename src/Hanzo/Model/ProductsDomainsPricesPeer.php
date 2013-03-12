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
        $hanzo      = Hanzo::getInstance();
        $customer   = CustomersPeer::getCurrent();
        $domain_id  = $hanzo->get('core.domain_id');
        $domain_key = $hanzo->get('core.domain_key');
        $customer   = CustomersPeer::getCurrent();

        $query = ProductsDomainsPricesQuery::create()
            ->filterByProductsId($products)
            ->filterByDomainsId($domain_id)
            ->filterByFromDate(time(), Criteria::LESS_EQUAL)
            ->filterByToDate(time(), Criteria::GREATER_EQUAL)
            ->_or()
            ->filterByToDate(NULL, Criteria::ISNULL)
            ->orderByProductsId()
            ->orderByFromDate(Criteria::DESC)
            ->orderByToDate(Criteria::DESC)
        ;
        $prices = $query->find();

        $override_vat     = false;
        $override_vat_pct = 0;
        if ('COM' == $domain_key) {
            if (!$customer->isNew()) {
                $country = CountriesQuery::create()
                    ->useAddressesQuery()
                        ->filterByType('payment')
                        ->filterByCustomersId($customer->getId())
                    ->endUse()
                    ->findOne()
                ;

                if (($country instanceof Countries) && !$country->getVat()) {
                    $override_vat     = true;
                    $override_vat_pct = 0;
                }
            }
        }

        $data = array();
        foreach ($prices as $price) {
            $key = $price->getToDate() ? 'sales' : 'normal';
            $vat = $price->getVat();
            $p   = $price->getPrice() + $vat;

            if ($override_vat) {
                $vat = 0;
                $p   = $price->getPrice();
                if ($override_vat_pct > 0) {
                    $save_price = $p;
                    $p          = $p * (($override_vat_pct / 100) + 1);
                    $vat        = $price - $save_price;
                }
            }

            $data[$price->getProductsId()][$key] = array(
                'currency'  => $price->getCurrencyId(),
                'raw_price' => $price->getPrice(),
                'price'     => number_format($p, 4, '.', ''),
                'vat'       => $vat,
                'formattet' => Tools::moneyFormat($p)
            );
        }

        return $data;
    }

} // ProductsDomainsPricesPeer

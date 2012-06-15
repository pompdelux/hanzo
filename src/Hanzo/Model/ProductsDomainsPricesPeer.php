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
        $hanzo = Hanzo::getInstance();
        $domain_id = $hanzo->get('core.domain_id');
        $domain_key = $hanzo->get('core.domain_id');

        $prices = ProductsDomainsPricesQuery::create()
            ->filterByProductsId($products)
            ->filterByDomainsId($domain_id)
            ->orderByProductsId()
            ->orderByFromDate(Criteria::DESC)
            ->orderByToDate(Criteria::DESC)
            ->find()
        ;


        $override_vat_pct = 0;
        $override_vat = false;
        if ('COM' == $domain_key) {
            $override_vat = true;
            $order = OrdersPeer::getCurrent();
            if (!$order->isNew()) {
                $country = CountriesQuery::create()->findOneById($order->getBillingCountriesId());
                if (($country instanceof Countries) && $country->getVat()) {
                    $vat = $country->getVat();
                }
            }
        }

        $data = array();
        foreach ($prices as $price) {
            $key = $price->getToDate() ? 'sales' : 'normal';

            $vat = $price->getVat();
            $p = $price->getPrice() + $vat;
            if ($override_vat) {
                $vat = 0;
                $p = $price->getPrice();
                if ($override_vat_pct > 0) {
                    $save_price = $p;
                    $p = $p * (($override_vat_pct / 100) + 1);
                    $vat = $price - $save_price;
                }
            }

            $data[$price->getProductsId()][$key] = array(
                'currency' => $price->getCurrencyId(),
                'raw_price' => $price->getPrice(),
                'price' => number_format($p, 4, '.', ''),
                'vat' => $vat,
                'formattet' => Tools::moneyFormat($p)
            );
        }

        return $data;
    }

} // ProductsDomainsPricesPeer

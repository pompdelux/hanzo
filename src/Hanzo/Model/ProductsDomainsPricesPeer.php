<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Twig\Extension\HanzoTwigExtension;

use Hanzo\Model\om\BaseProductsDomainsPricesPeer,
    Hanzo\Model\ProductsDomainsPricesQuery
;

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
        $domain_id = Hanzo::getInstance()->get('core.domain_id');

        $prices = ProductsDomainsPricesQuery::create()
            ->filterByProductsId($products)
            ->filterByDomainsId($domain_id)
            ->orderByProductsId()
            ->orderByFromDate(Criteria::DESC)
            ->orderByToDate(Criteria::DESC)
            ->find()
        ;

        $data = array();
        foreach ($prices as $price) {
            $key = $price->getToDate() ? 'sales' : 'normal';
            $data[$price->getProductsId()][$key] = array(
                'currency' => $price->getCurrencyCode(),
                'price' => $price->getPrice() + $price->getVat(),
                'vat' => $price->getVat(),
                'formattet' => Tools::moneyFormat($price->getPrice() + $price->getVat())
            );
        }

        return $data;
    }

} // ProductsDomainsPricesPeer

<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\om\BaseProductsPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'products' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class ProductsPeer extends BaseProductsPeer
{
    public static function findFromRequest($request)
    {
        static $product_cache = array();

        $key = self::mergeRequestParams();
        if (isset($product_cache[$key])) {
            return $product_cache[$key];
        }

        $product_cache[$key] = NULL;
        $product_id = $request->get('product_id');

        if ($product_id) {
            $product_cache[$key] = ProductsQuery::findOneById($product_id);
        }
        else {
            $master = $request->get('master');
            $size = $request->get('size');
            $color = $request->get('color');

            $product_cache[$key] = ProductsQuery::create()
                ->filterByMaster($master)
                ->filterBySize($size)
                ->filterByColor($color)
                ->filterByIsOutOfStock(0)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
                ->endUse()
                ->findOne()
            ;
        }

        return $product_cache[$key];
    }

    protected static function mergeRequestParams()
    {
        $request = array_merge($_GET, $_POST);
        $keys = array('master', 'size', 'color', 'product_id');
        foreach($request as $key => $v) {
            if (!in_array($key, $keys)) {
                unset($request[$key]);
            }
        }

        return implode('.', $request);
    }

} // ProductsPeer

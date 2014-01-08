<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseProductsPeer;
use Symfony\Component\HttpFoundation\Request;

class ProductsPeer extends BaseProductsPeer
{
    public static function findFromRequest(Request $request)
    {
        static $product_cache = array();

        $key = self::mergeRequestParams();

        if (isset($product_cache[$key])) {
            return $product_cache[$key];
        }

        $product_cache[$key] = NULL;
        $product_id = $request->request->get('product_id');

        if ($product_id) {
            $product_cache[$key] = ProductsQuery::findOneById($product_id);
        } else {
            $master = $request->request->get('master');
            $size   = $request->request->get('size');
            $color  = $request->request->get('color');

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

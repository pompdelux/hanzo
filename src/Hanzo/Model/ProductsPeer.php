<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseProductsPeer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProductsPeer
 *
 * @package Hanzo\Model
 */
class ProductsPeer extends BaseProductsPeer
{
    /**
     * @param Request $request
     * @param mixed $isApi
     *
     * @return null|Products
     */
    public static function findFromRequest(Request $request, $isApi = false)
    {
        static $productCache = [];

        $key = self::mergeRequestParams();

        if (isset($productCache[$key])) {
            return $productCache[$key];
        }

        $productCache[$key] = null;
        $productId = $request->request->get('product_id');

        if ($productId) {
            $productCache[$key] = ProductsQuery::findOneById($productId);
        } else {
            $master = $request->request->get('master');
            $size   = $request->request->get('size');
            $color  = $request->request->get('color');

            if (false === $isApi) {
                $domainKey = Hanzo::getInstance()->get('core.domain_id');
            } else {
                $res = DomainsQuery::create()->findOneByDomainKey($isApi);
                $domainKey = $res->getId();
            }

            $productCache[$key] = ProductsQuery::create()
                ->filterByMaster($master)
                ->filterBySize($size)
                ->filterByColor($color)
                ->filterByIsOutOfStock(0)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($domainKey)
                ->endUse()
                ->findOne();
        }

        return $productCache[$key];
    }

    /**
     * @return string
     */
    protected static function mergeRequestParams()
    {
        $request = array_merge($_GET, $_POST);
        $keys = ['master', 'size', 'color', 'product_id'];
        foreach ($request as $key => $v) {
            if (!in_array($key, $keys)) {
                unset($request[$key]);
            }
        }

        return implode('.', $request);
    }

} // ProductsPeer

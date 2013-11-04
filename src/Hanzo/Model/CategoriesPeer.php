<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseCategoriesPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;


/**
 * Skeleton subclass for performing query and update operations on the 'categories' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class CategoriesPeer extends BaseCategoriesPeer
{
    public static function getCategoryProductsByCategoryId($category_id, $pager, $show)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');
        $domain_id = $hanzo->get('core.domain_id');
        $show_by_look = (bool)($show === 'look');

        $result = ProductsImagesCategoriesSortQuery::create()
            ->useProductsQuery()
                ->where('products.MASTER IS NULL')
                ->filterByIsOutOfStock(FALSE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($domain_id)
                ->endUse()
            ->endUse()
            ->joinWithProducts()
            ->useProductsImagesQuery()
                ->filterByType($show_by_look?'set':'overview')
                ->groupByImage()
            ->endUse()
            ->joinWithProductsImages()
            ->orderBySort()
            ->filterByCategoriesId($category_id)
        ;
        if($pager === 'all'){
            $result = $result->paginate(null, null);
        }else{
            $result = $result->paginate($pager, 12);
        }

        $product_route = str_replace('category_', 'product_', $route);

        $records = array();
        $product_ids = array();
        foreach ($result as $record) {
            $product = $record->getProducts();
            $product_ids[] = $product->getId();

            $image_overview = str_replace('_set_', '_overview_', $record->getProductsImages()->getImage());
            $image_set = str_replace('_overview_', '_set_', $record->getProductsImages()->getImage());

            $records[] = array(
                'sku' => $product->getSku(),
                'id' => $product->getId(),
                'title' => $product->getSku(),
                'image' => ($show_by_look)?$image_set:$image_overview,
                'image_flip' => ($show_by_look)?$image_overview:$image_set,
                'url' => $router->generate($product_route, array(
                    'product_id' => $product->getId(),
                    'title' => Tools::stripText($product->getSku()),
                    'focus' => $record->getProductsImages()->getId()
                )),
            );
        }

        // get product prices
        $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

        // attach the prices to the products
        foreach ($records as $i => $data) {
            if (isset($prices[$data['id']])) {
                $records[$i]['prices'] = $prices[$data['id']];
            }
        }

        $data = array(
            'title' => '',
            'products' => $records,
            'paginate' => NULL,
        );

        if ($result->haveToPaginate()) {

            $pages = array();
            foreach ($result->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, array('pager' => $page, 'show' => $show), TRUE);
            }

            $data['paginate'] = array(
                'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage()), TRUE)),
                'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage()), TRUE)),

                'pages' => $pages,
                'index' => $pager,
                'see_all' => array(
                    'total' => $result->getNbResults(),
                    'url' => $router->generate($route, array('pager' => 'all'), TRUE)
                )
            );
        }

        return $data;
    }

    public static function getStylesByCategoryId($category_id, $pager)
    {
        $hanzo     = Hanzo::getInstance();
        $container = $hanzo->container;
        $route     = $container->get('request')->get('_route');
        $router    = $container->get('router');
        $domain_id = $hanzo->get('core.domain_id');

        $result = ProductsImagesProductReferencesQuery::create()
            ->useProductsQuery()
                ->where('products.MASTER IS NULL')
                ->filterByIsOutOfStock(FALSE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($domain_id)
                ->endUse()
            ->endUse()
            ->useProductsImagesQuery()
                ->useProductsImagesCategoriesSortQuery()
                    ->filterByCategoriesId($category_id)
                    ->orderBySort()
                ->endUse()
                ->groupByImage()
            ->endUse()
            ->joinWithProductsImages()
            ->paginate($pager, 12)
        ;

        $product_route = str_replace('category_', 'product_', $route);

        $records = array();
        foreach ($result as $record) {
            $product = $record->getProducts();

            $records[] = array(
                'sku' => $product->getSku(),
                'image' => $record->getProductsImages()->getImage(),
                'url' => $router->generate('product_set', array(
                    'image_id' => $record->getProductsImages()->getId()
                )),
            );
        }

        $data = array(
            'title' => '',
            'products' => $records,
            'paginate' => NULL,
        );

        if ($result->haveToPaginate()) {

            $pages = array();
            foreach ($result->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);
            }

            $data['paginate'] = array(
                'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage()), TRUE)),
                'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage()), TRUE)),

                'pages' => $pages,
                'index' => $pager
            );
        }

        return $data;
    }

} // CategoriesPeer

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
                ->groupByImage()
            ->endUse()
            ->joinWithProductsImages()
            ->orderBySort()
            ->filterByCategoriesId($category_id)
            ->paginate($pager, 12)
        ;

        $product_route = str_replace('category_', 'product_', $route);

        $records = array();
        $product_ids = array();
        foreach ($result as $record) {
            $product = $record->getProducts();
            $product_ids[] = $product->getId();

            $image_overview = str_replace('set', 'overview', $record->getProductsImages()->getImage());
            
            $records[] = array(
                'sku' => $product->getSku(),
                'id' => $product->getId(),
                'title' => $product->getSku(),
                'image' => ($show_by_look)?$image_overview:$record->getProductsImages()->getImage(),
                'image_flip' => ($show_by_look)?$record->getProductsImages()->getImage():$image_overview,
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
                'index' => $pager
            );
        }

        return $data;
    }

} // CategoriesPeer

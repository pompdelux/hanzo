<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Criteria;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\CategoriesI18nQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsImagesPeer;
use Hanzo\Model\ProductsToCategoriesPeer;
use Hanzo\Model\ProductsToCategoriesQuery;

class ByColourController extends CoreController
{
    public function viewAction($id, $show)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');
        $route = $container->get('request')->get('_route');
        $translator = $this->get('translator');

        $router = $container->get('router');

        $cache_id = explode('_', $this->get('request')->get('_route'));
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1], $show);

        // html/normal request
        $cache_id[] = 'html';
        $html = $this->getCache($cache_id);

        if (!$html) {
            $page = CmsPeer::getByPK($id, $locale);
            $settings = $page->getSettings(null, false);

            if (!isset($settings->category_ids)) {
                $settings->category_ids = [];
            }

            if (!isset($settings->ignore)) {
                $settings->ignore = [];
            }

            $includes = explode(',', $settings->category_ids);
            $ignores = explode(',', $settings->ignore);
            $color_map = explode(',', $settings->colors);

            $show_by_look = (bool) ($show === 'look');

            $categories = array();
            $resultset = CategoriesI18nQuery::create()
                ->filterByLocale($locale)
                ->findById($includes)
            ;
            foreach ($resultset as $category) {
                $categories[$category->getId()] = $category->getTitle();
            }
            unset ($resultset);

            $index = 1;
            $products = array();
            $masters = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                    ->filterBySku($ignores, Criteria::NOT_IN)
                    ->filterByMaster(null, Criteria::ISNULL)
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($domain_id)
                    ->endUse()
                ->endUse()
                ->joinWithProducts()
                ->groupByProductsId()
                ->filterByCategoriesId($includes)
                ->addAscendingOrderByColumn(sprintf(
                    "FIELD(%s, %s)",
                    ProductsToCategoriesPeer::CATEGORIES_ID,
                    implode(',', $includes)
                ))
                ->find()
            ;

            $ids = array();
            $products_to_categories = array();
            foreach ($masters as $master) {
                $ids[] = $master->getProducts()->getId();
                $products_to_categories[$master->getProducts()->getId()] = $master->getCategoriesId();
            }

            $variants = ProductsImagesQuery::create()
                ->joinWithProducts()
                ->addAscendingOrderByColumn(sprintf(
                    "FIELD(%s, %s)",
                    ProductsImagesPeer::COLOR,
                    '\''.implode('\',\'', $color_map).'\''
                ))
                ->useProductsQuery()
                    ->joinWithProductsI18n()
                    ->filterByMaster(null, Criteria::ISNULL)
                    ->useProductsToCategoriesQuery()
                        ->addAscendingOrderByColumn(sprintf(
                            "FIELD(%s, %s)",
                            ProductsToCategoriesPeer::CATEGORIES_ID,
                            implode(',', $includes)
                        ))
                        ->filterByCategoriesId($includes)
                    ->endUse()
                    ->useProductsI18nQuery()
                        ->filterByLocale($locale)
                    ->endUse()
                    ->filterById($ids)
                ->endUse()
                ->filterByColor($color_map)
                ->filterByType($show_by_look?'set':'overview')
                ->groupById()
                ->find()
            ;

            $product_route = str_replace('bycolour_', 'product_', $route);

            foreach ($variants as $variant) {
                if (!preg_match("/_01/", $variant->getImage())) {
                    continue;
                }

                $product = $variant->getProducts();
                $product->setLocale($locale);

                // Always use 01.
                $image = preg_replace('/_(\d{2})/', '_01', $variant->getImage());
                $image_overview = str_replace('_set_', '_overview_', $image);
                $image_set = str_replace('_overview_', '_set_', $image);

                $cid = $products_to_categories[$variant->getProducts()->getId()];
                $alt = trim(Tools::stripTags($translator->trans('headers.bycolour-'.$id, [], 'category'))).': '.$product->getSku();

                $products[] = array(
                    'category_id' => $cid,
                    'category' => $categories[$cid],
                    'sku' => $product->getSku(),
                    'id' => $product->getId(),
                    'out_of_stock' => $product->getIsOutOfStock(),
                    'title' => $product->getTitle(),
                    'color' => $variant->getColor(),
                    'image' => ($show_by_look) ? $image_set : $image_overview,
                    'image_flip' => ($show_by_look) ? $image_overview : $image_set,
                    'alt' => $alt,
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getTitle()),
                        'focus' => $variant->getId()
                    )),
                    'index' => $index,
                );

                $index++;
            }

            // get product prices
            $prices = ProductsDomainsPricesPeer::getProductsPrices($ids);

            // attach the prices to the products
            foreach ($products as $i => $data) {
                if (isset($prices[$data['id']])) {
                    $products[$i]['prices'] = $prices[$data['id']];
                }
            }

            $classes = 'bycolour-'.preg_replace('/[^a-z]/', '-', strtolower($page->getTitle()));
            if (preg_match('/(pige|girl|tjej|tytto|jente)/', $container->get('request')->getPathInfo())) {
                $classes .= ' category-girl';
            } elseif (preg_match('/(dreng|boy|kille|poika|gutt)/', $container->get('request')->getPathInfo())) {
                $classes .= ' category-boy';
            }

            $this->get('twig')->addGlobal('page_type', 'bycolour-'.$id);
            $this->get('twig')->addGlobal('body_classes', 'body-bycolour bycolour-'.$id.' body-'.$show.' '.$classes);
            $this->get('twig')->addGlobal('show_new_price_badge', $hanzo->get('webshop.show_new_price_badge'));
            $this->get('twig')->addGlobal('cms_id', $page->getParentId());
            $this->get('twig')->addGlobal('show_by_look', ($show === 'look'));
            $html = $this->renderView('CategoryBundle:ByColour:view.html.twig', array('products' => $products));
            $this->setCache($cache_id, $html, 5);
        }

        return $this->response($html);
    }
}

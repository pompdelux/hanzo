<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Criteria;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\ProductsWashingInstructionsQuery;
use Hanzo\Model\ProductsToCategoriesQuery;

class BundleController extends CoreController
{
    public function viewAction($image_id)
    {
        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');
        $route = $this->get('request')->get('_route');
        $router = $this->get('router');

        $cache_id = array('product.image.bundle', $image_id);
        $products = $this->getCache($cache_id);

        if (empty($products)) {
            $product_ids = array();

            $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
            $locale = strtolower($hanzo->get('core.locale'));

            $product = ProductsQuery::create()
                ->useProductsImagesQuery()
                    ->filterById($image_id)
                ->endUse()
                ->filterByIsActive(TRUE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($hanzo->get('core.domain_id'))
                    ->filterByFromDate(array('max' => 'now'))
                    ->_or()
                    ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                    ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                    ->where(array('c1', 'c2'), 'AND')
                ->endUse()
                ->joinWithProductsImages()
                ->findOne()
            ;

            if (!$product instanceof Products) {
                return $this->redirect($this->generateUrl('_homepage'));
            }

            $product_ids[] = $product->getId();
            $products2category = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                ->filterBySku($product->getSku())
                ->endUse()
                ->findOne()
            ;

            $key = '_' . $locale . '_' . $products2category->getCategoriesId();
            $product_route = $router_keys[$key];

            $image = $product->getProductsImagess()->getFirst();
            $products[$product->getId()] = array(
                'id' => $product->getId(),
                'master' => $product->getSku(),
                'color' => $image->getColor(),
                'image' => $image->getImage(),
                'url' => $router->generate($product_route, array(
                    'product_id' => $product->getId(),
                    'title' => Tools::stripText($product->getSku()),
                )),
                'out_of_stock' => true,
            );

            $result = ProductsQuery::create()
                ->useProductsImagesProductReferencesQuery()
                    ->filterByProductsImagesId($image_id)
                ->endUse()
                ->filterByIsActive(TRUE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($hanzo->get('core.domain_id'))
                    ->filterByFromDate(array('max' => 'now'))
                    ->_or()
                    ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                    ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                    ->where(array('c1', 'c2'), 'AND')
                ->endUse()
                ->useProductsImagesQuery()
                    ->filterByType('overview')
                    ->where('products_images.COLOR = products_images_product_references.COLOR')
                    ->groupByProductsId()
                ->endUse()
                ->joinWithProductsImages()
                ->find()
            ;

            foreach ($result as $product) {

                $products2category = ProductsToCategoriesQuery::create()
                    ->useProductsQuery()
                    ->filterBySku($product->getSku())
                    ->endUse()
                    ->findOne()
                ;

                $key = '_' . $locale . '_' . $products2category->getCategoriesId();
                $product_route = $router_keys[$key];

                $image = $product->getProductsImagess()->getFirst();
                $products[$product->getId()] = array(
                    'id' => $product->getId(),
                    'master' => $product->getSku(),
                    'color' => $image->getColor(),
                    'image' => $image->getImage(),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getSku()),
                    )),
                    'out_of_stock' => true,
                );


                $product_ids[] = $product->getId();
            }

            foreach (ProductsDomainsPricesPeer::getProductsPrices($product_ids) as $i => $price) {
                $products[$i]['prices'] = $price;
            }

            $this->setCache($cache_id, $products);
        }

        foreach ($products as $id => $product) {

            $sizes = $colors = [];
            $colors[$product['color']] = $product['color'];

            $stock = $this->forward('WebServicesBundle:RestStock:check', array(
                'version' => 'v1',
                'master' => $product['master'],
                'id' => '',
            ));
            $stock = json_decode($stock->getContent());

            if (isset($stock->data->products) && count($stock->data->products)) {
                foreach ($stock->data->products as $p) {
                    $sizes[$p->size] = [
                        'value' => $p->size,
                        'in_stock' => true,
                    ];
                }
                $products[$id]['out_of_stock'] = false;
            }

            $products[$id]['sizes'] = $sizes;
            $products[$id]['colors'] = $colors;
        }

        $this->setSharedMaxAge(86400);
        $this->get('twig')->addGlobal('body_classes', 'body-product body-buy-set');
        $responce = $this->render('ProductBundle:Bundle:view.html.twig', array(
            'page_type' => 'bundle',
            'products' => $products,
        ));

        return $responce;
    }


    public function customAction($set)
    {
        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');
        $route = $this->get('request')->get('_route');
        $router = $this->get('router');

        $cache_id = array('product.bundle.custom', str_replace(',', '-', $set));
        $products = $this->getCache($cache_id);

        if (empty($products)) {
            $set = explode(',', $set);

            $where = [];
            $products_ids = [];
            foreach ($set as $product) {
                $pieces = explode('-', $product, 2);

                if (empty($pieces[1])) {
                    Tools::log("Invalid set url: {$_SERVER['REQUEST_URI']}");
                    continue;
                }

                $where[] = array(
                    'ProductsId' => $pieces[0],
                    'Color' => str_replace(['9', '-'], ['/', ' '], $pieces[1]),
                );
                $product_ids[] = $pieces[0];
            }

            $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
            $locale = strtolower($hanzo->get('core.locale'));

            $result = ProductsQuery::create()
                ->filterByIsActive(TRUE)
                ->useProductsImagesQuery()
                    ->filterByType('overview')
            ;
            // Add all sets to conditions seperately.
            $combines = [];
            foreach ($where as $i => $where_clause) {
                $result = $result->condition('id_' . $i, 'products_images.products_id = ?', $where_clause['ProductsId'])
                    ->condition('color_' . $i, 'products_images.color = ?', $where_clause['Color'])
                    ->combine(array('id_' . $i, 'color_' . $i), 'and', 'combine_' . $i)
                ;

                $combines[] = 'combine_' . $i;
            }
            $result = $result->where($combines, 'or')
                ->groupByProductsId()
                ->endUse()
                ->joinWithProductsImages()
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($hanzo->get('core.domain_id'))
                    ->filterByFromDate(array('max' => 'now'))
                    ->_or()
                    ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                    ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                    ->where(array('c1', 'c2'), 'AND')
                ->endUse()
                ->find()
            ;

            $products = [];
            foreach ($result as $product) {
                $products2category = ProductsToCategoriesQuery::create()
                    ->useProductsQuery()
                    ->filterBySku($product->getSku())
                    ->endUse()
                    ->findOne()
                ;

                $key = '_' . $locale . '_' . $products2category->getCategoriesId();
                $product_route = $router_keys[$key];

                $image = $product->getProductsImagess()->getFirst();
                $products[$product->getId()] = array(
                    'id' => $product->getId(),
                    'master' => $product->getSku(),
                    'color' => $image->getColor(),
                    'image' => $image->getImage(),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getSku()),
                    )),
                    'out_of_stock' => true,
                );


                $product_ids[] = $product->getId();
            }

            foreach (ProductsDomainsPricesPeer::getProductsPrices($product_ids) as $i => $price) {
                $products[$i]['prices'] = $price;
            }

            $this->setCache($cache_id, $products);
        }

        foreach ($products as $id => $product) {
            if (empty($product['master'])) {
                continue;
            }

            $sizes = $colors = [];
            $colors[$product['color']] = $product['color'];

            $stock = $this->forward('WebServicesBundle:RestStock:check', array(
                'version' => 'v1',
                'master' => $product['master'],
                'id' => '',
            ));
            $stock = json_decode($stock->getContent());

            if (isset($stock->data->products) && count($stock->data->products)) {
                foreach ($stock->data->products as $p) {
                    $sizes[$p->getSize()] = [
                        'value' => $p->getSize(),
                        'in_stock' => false,
                    ];
                }
                $products[$id]['out_of_stock'] = false;
            }

            $products[$id]['sizes'] = $sizes;
            $products[$id]['colors'] = $colors;
        }

        $this->setSharedMaxAge(86400);
        $responce = $this->render('ProductBundle:Bundle:view.html.twig', array(
            'page_type' => 'bundle',
            'products' => $products,
        ));

        return $responce;
    }
}

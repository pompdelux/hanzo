<?php

namespace Hanzo\Bundle\SearchBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;

class DefaultController extends CoreController
{

    public function indexAction($name)
    {
        return $this->render('HanzoSearchBundle:Default:index.html.twig', array('name' => $name));
    }

    public function categoryAction($id)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $page = CmsPeer::getByPK($id, $locale);

        $settings = json_decode($page->getSettings());
        $categories_string = $settings->category_ids;
        $group = $settings->group;

        $categories  = array_map('trim', explode(',', $categories_string));
        $no_accessories = $categories;
        $accessories = array_shift($no_accessories);

        $cache_key = array('category_search', $id, $domain_id);
        $sizes = $this->getCache($cache_key);

        if (!is_array($sizes)) {


            // find masters
            $products = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($domain_id)
                    ->endUse()
                ->endUse()
                ->groupByProductsId()
                ->findByCategoriesId($no_accessories)
            ;

            $sizes = array();
            foreach ($products as $product) {
                $variants = ProductsQuery::create()
                    // out due to caching issues, maby we have to reinsert...
                    // ->filterByIsOutOfStock(FALSE)
                    ->filterBySize(array('one size', '20-22', '23-26', 'l', 'm', 's'), \Criteria::NOT_IN)
                    ->findByMaster($product->getProducts()->getSku())
                ;
                foreach ($variants as $variant) {
                    $size = $variant->getSize();

                    // we have to re-map some of the sizes
                    switch (trim(strtolower($size)))
                    {
                        case '110':
                        case '116':
                            $size = '110-116';
                            break;
                        case '122':
                        case '128':
                            $size = '122-128';
                            break;
                        case '134':
                        case '140':
                            $size = '134-140';
                            break;
                        case '146':
                        case '152':
                            $size = '146-152';
                            break;
                        default:
                            switch($group) {
                                case 'g':
                                case 'b':
                                    if (in_array($size, array('27-30', '31-34', '35-38', '86', '92', '98', '98-104'))) {
                                        continue 3;
                                    }
                                    break;
                                case 'lg':
                                case 'lb':
                                    if (in_array($size, array('27-30', '31-34', '35-38', '104', '110-116', '122-128'))) {
                                        continue 3;
                                    }
                                    break;
                            }
                            break;
                    }

                    $sizes[$size] = $size;
                }
            }

            $this->setCache($cache_key, $sizes); // cache for an hour
        }

        $result_set = array();
        if ('POST' === $this->getRequest()->getMethod()) {
            $size = $this->getRequest()->get('size');

            $conn = \Propel::getConnection();

            $query = $conn->prepare("
                SELECT
                    products.id,
                    products.size,
                    products_to_categories.categories_id AS category_id,
                    categories_i18n.title
                FROM
                    products
                JOIN
                    products_to_categories
                    ON
                        (products.id = products_to_categories.products_id)
                JOIN
                    categories_i18n
                    ON
                        (products_to_categories.categories_id = categories_i18n.id)
                JOIN
                    products_domains_prices
                    ON
                        (products.id = products_domains_prices.products_id)
                WHERE
                    products.master IS NULL
                    AND
                        products.is_out_of_stock = 0
                    AND
                        products_domains_prices.domains_id = {$domain_id}
                    AND
                        products_to_categories.categories_id IN (".implode(',', $no_accessories).")
                    AND
                        categories_i18n.locale = '{$locale}'
                ORDER BY
                    field(products_to_categories.categories_id, " . implode(',', $no_accessories) . ', ' . $accessories . "),
                    products.sku
            ", array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

            $query->execute();

            $product_ids = array();
            $category_ids = array();
            $category_map = array();

            while ($record = $query->fetch(\PDO::FETCH_ASSOC)) {
                $product_ids[$record['id']] = $record['id'];
                $category_map[$record['title']][$record['id']] = $record['id'];
                $category_ids[$record['title']] = $record['category_id'];
            }

            if (count($product_ids)) {
                $products = ProductsQuery::create()
                    ->useProductsImagesQuery()
                        ->groupByImage()
                    ->endUse()
                    ->joinWithProductsImages()
                    ->findById($product_ids)
                ;

                $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

                $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
                $router = $this->get('router');

                foreach ($products as $product) {
                    if (!$product->getSku()) {
                        continue;
                    }

                    foreach ($category_map as $category => $map) {
                        foreach ($map as $id) {
                            if ($id == $product->getId()) {
                                $product_route = $router_keys['_' . strtolower($locale) . '_' . $category_ids[$category]];

                                $category_map[$category][$id] = array(
                                    'sku' => $product->getSku(),
                                    'id' => $product->getId(),
                                    'title' => $product->getSku(),
                                    'image' => $product->getProductsImagess()->getFirst()->getImage(),
                                    'prices' => $prices[$id],
                                    'url' => $router->generate($product_route, array(
                                        'product_id' => $product->getId(),
                                        'title' => Tools::stripText($product->getSku()),
                                    )),
                                );
                            }
                        }
                    }
                }
            }
            $result_set = $category_map;
        }

        return $this->render('HanzoSearchBundle:Default:category.html.twig', array(
            'page_type' => 'category-search',
            'content'   => $page->getContent(),
            'title'     => $page->getTitle(),
            'result'    => $result_set,
            'sizes'     => (is_array($sizes) ? $sizes : array()),
            'route'     => $this->getRequest()->get('_route'),
            'selected'  => $this->getRequest()->get('size', ''),
        ));
    }

    public function advancedAction($id)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $page = CmsPeer::getByPK($id, $locale);
        //$settings = json_decode($page->getSettings());

        $result = array(
            'products' => array(),
            'pages' => array()
        );

        if (isset($_GET['q'])) {
            $q = $this->getRequest()->get('q', null);
            $q = '%'.$q.'%';

            // search products
            $products = ProductsQuery::create()
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($domain_id)
                ->endUse()
                ->useProductsImagesQuery()
                    ->groupByImage()
                ->endUse()
                ->joinWithProductsImages()
                ->joinWithProductsToCategories()
                ->filterByIsOutOfStock(false)
                ->filterBySku($q)
                ->_or()
                ->filterBySize($q)
                ->_or()
                ->filterByColor($q)
                ->orderBySku()
                ->find()
            ;

            $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
            $router = $this->get('router');

            $product_ids = array();
            foreach ($products as $product) {
                if (!$product->getSku()) {
                    continue;
                }

                $product_ids[$product->getId()] = $product->getId();

                $product_route = '';
                $key = '_' . strtolower($locale) . '_' . $product->getproductsToCategoriess()->getFirst()->getCategoriesId();
                if (isset($router_keys[$key])) {
                    $product_route = $router_keys[$key];
                }

                $image = $product->getProductsImagess()->getFirst();

                $result['products'][] = array(
                    'sku' => $product->getSku(),
                    'id' => $product->getId(),
                    'title' => $product->getSku(),
                    'image' => $image->getImage(),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getSku()),
                        'focus' => $image->getId()
                    )),
                );
            }

            if (count($product_ids)) {
                $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);
                // attach the prices to the products
                foreach ($result['products'] as $i => $data) {
                    if (isset($prices[$data['id']])) {
                        $result['products'][$i]['prices'] = $prices[$data['id']];
                    }
                }
            }

            // search pages
            $pages = CmsI18nQuery::create()
                ->useCmsQuery()
                    ->filterByType('page')
                    ->filterByCmsThreadId(20)
                    ->filterByIsActive(true)
                ->endUse()
                ->filterByLocale($locale)
                ->filterByTitle($q)
                ->_or()
                ->filterByContent($q)
                ->orderByTitle()
                ->find()
            ;

            foreach ($pages as $page) {
                $result['pages'][] = array(
                    'title' => $page->getTitle(),
                    'summery' => mb_substr(Tools::stripTags($page->getContent()), 0, 200),
                    'url' => $router->generate('page_'.$page->getId())
                    #'url' => $router->generate('page_'.$page->getId().'_'.strtolower($locale))
                );
            }
        }

        return $this->render('HanzoSearchBundle:Default:advanced.html.twig', array(
            'page_type' => 'category-search',
            'route'     => $this->getRequest()->get('_route'),
            'content'   => $page->getContent(),
            'title'     => $page->getTitle(),
            'result'    => $result,
        ));
    }
}

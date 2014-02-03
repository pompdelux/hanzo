<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Hanzo\Model\SearchProductsTagsQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesCategoriesSortPeer;
use Hanzo\Model\CategoriesPeer;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsImagesPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;

use Hanzo\Model\SearchProductsTagsPeer;


use Hanzo\Model\CmsPeer;

class DefaultController extends CoreController
{

    /**
     * handle category listings
     *
     * @param Request $request
     * @param integer $cms_id
     * @param integer $category_id
     * @param boolean $show
     * @param integer $pager
     * @return Response
     */
    public function viewAction(Request $request, $cms_id, $category_id, $show, $pager = 1)
    {
        $hanzo      = Hanzo::getInstance();
        $container  = $hanzo->container;
        $locale     = $hanzo->get('core.locale');
        $translator = $this->get('translator');

        $cache_id = explode('_', $request->get('_route'));
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1], $show, $pager);

        $use_filter = false;

        // Extra cache id if its not a json call
        if ($this->getFormat() !== 'json') {
            $cache_id[] = 'html';
        }

        // TODO: should not be set here !!
        $color_mapping = [
            'rose'          => ['rose','khaki'],
            'ice blue'      => ['ice blue','khaki'],
            'curry'         => ['curry','curry/grey','sand'],
            'blue - cerise' => ['blue','cerise','cerise/grey','khaki'],
            'wine'          => ['wine','khaki'],
            'grey'          => ['grey','grey star','grey melange','off white','dark grey','light grey','black'],
            'denim'         => ['blue denim'],
        ];

        $size_filter = [];
        $color_filter = [];
        if ($request->query->has('filter')) {
            foreach ($request->query->get('color', []) as $color) {
                if (isset($color_mapping[$color])) {
                    $color_filter = array_merge($color_filter, $color_mapping[$color]);
                }
            }

            $cache_id = array_merge($cache_id, $color_filter);

            foreach ($request->query->get('size', []) as $size) {
                $size_filter[] = $size;
            }

            $cache_id = array_merge($cache_id, $size_filter);

            $use_filter = true;
        }

        $html = $this->getCache($cache_id); // If there a cached version, html has both the json and html version
        $data = null;
$html= null;
        /*
         *  If html wasn't cached retrieve a fresh set of data
         */
        if (!$html){
            $cms_page = CmsPeer::getByPK($cms_id, $locale);
            $settings = $cms_page->getSettings(null, false);

            if (empty($color_filter)) {
                if(!empty($settings->colors)){
                    $color_filter = explode(',', $settings->colors);
                }
            }

            $route        = $request->get('_route');
            $router       = $container->get('router');
            $domain_id    = $hanzo->get('core.domain_id');
            $show_by_look = (bool) ($show === 'look');

            $result = ProductsImagesCategoriesSortQuery::create()
                ->joinWithProducts()
                ->useProductsQuery()
                    ->joinProductsI18n()
                    ->where('products.MASTER IS NULL')
                    // ->filterByIsOutOfStock(FALSE)
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($domain_id)
                    ->endUse()
                    ->useProductsI18nQuery()
                        ->filterByLocale($locale)
                    ->endUse()
                ->endUse()
                ->useProductsImagesQuery()
                    ->filterByType($show_by_look ? 'set' : 'overview')
                    ->groupByImage()
                ->endUse()
                ->joinWithProductsImages()
                ->filterByCategoriesId($category_id)
            ;

            // If there are any colors in the settings to order from, add the order column here.
            // Else order by normal Sort in db

            if ($use_filter) {
                $sql = '';

                $con = \Propel::getConnection();
                if ($color_filter && $size_filter) {
                    $color_filter_values = implode(', ', array_map(array($con, 'quote'), $color_filter));
                    $size_filter_values = implode(', ', array_map(array($con, 'quote'), $size_filter));

                    $sql = "
                        SELECT
                            C1.master_products_id AS master_products_id
                        FROM
                            search_products_tags AS C1
                        JOIN
                            search_products_tags AS C2
                            ON (C1.products_id = C2.products_id)
                        WHERE
                            C1.token IN ({$color_filter_values})
                            AND
                                C2.token IN ({$size_filter_values})
                        GROUP BY
                            C1.master_products_id
                    ";
                } elseif ($color_filter) {
                    $color_filter_values = implode(', ', array_map(array($con, 'quote'), $color_filter));

                    $sql = "
                        SELECT
                            C1.master_products_id AS master_products_id
                        FROM
                            search_products_tags AS C1
                        WHERE
                            C1.token IN ({$color_filter_values})
                        GROUP BY
                            C1.master_products_id
                    ";
                } elseif ($size_filter) {
                    $size_filter_values = implode(', ', array_map(array($con, 'quote'), $size_filter));

                    $sql = "
                        SELECT
                            C1.master_products_id AS master_products_id
                        FROM
                            search_products_tags AS C1
                        WHERE
                            C1.token IN ({$size_filter_values})
                        GROUP BY
                            C1.master_products_id
                    ";
                }

                if ($sql) {
                    $statement = $con->prepare($sql);
                    $statement->execute();
                    $statement->setFetchMode(\PDO::FETCH_ASSOC);

                    $ids =  [];
                    while ($record = $statement->fetch()) {
                        $ids[] = $record['master_products_id'];
                    }

                    if (!empty($ids)) {
                        $result = $result->useProductsQuery()->filterById($ids)->endUse();
                    }
                }
            }

            if ($color_filter) {
                $result = $result->useProductsImagesQuery()
                    ->addAscendingOrderByColumn(sprintf(
                        "FIELD(%s, %s)",
                        ProductsImagesPeer::COLOR,
                        '\''.implode('\',\'', $color_filter).'\''
                    ))
                ->endUse();
            } else {
                $result = $result->orderBySort();
            }

// un@bellcom.dk 2013.11.28, removed to show all products on the category pages.
//            if ($pager === 'all') {
//                $result = $result->paginate(null, null);
//            } else {
//                $result = $result->paginate($pager, 12);
//            }

//            $result = $result->paginate(null, null);
            $result = $result->find();

            $product_route = str_replace('category_', 'product_', $route);
            $records       = array();
            $product_ids   = array();

            $result = $result->find();
            foreach ($result as $record) {

                $image = $record->getProductsImages()->getImage();

                // Only use 01.
                if (preg_match('/_01.jpg/', $image)) {
                    $product = $record->getProducts();
                    $product->setLocale($locale);

                    $product_ids[] = $product->getId();

                    $image_overview = str_replace('_set_', '_overview_', $image);
                    $image_set      = str_replace('_overview_', '_set_', $image);

                    $alt = trim(Tools::stripTags($translator->trans('headers.category-'.$category_id, [], 'category'))).': '.$product->getSku();

                    $records[] = array(
                        'sku'          => $product->getSku(),
                        'out_of_stock' => $product->getIsOutOfStock(),
                        'id'           => $product->getId(),
                        'title'        => $product->getSku(),
                        'image'        => ($show_by_look) ? $image_set : $image_overview,
                        'image_flip'   => ($show_by_look) ? $image_overview : $image_set,
                        'alt'          => $alt,
                        'url'          => $router->generate($product_route, array(
                            'product_id' => $product->getId(),
                            'title'      => Tools::stripText($product->getSku()),
                            'focus'      => $record->getProductsImages()->getId()
                        )),
                    );
                }
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

            if (method_exists($result, 'haveToPaginate') && $result->haveToPaginate()) {

                $pages = array();
                foreach ($result->getLinks(20) as $page) {
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'show' => $show), TRUE);
                }

                $pages = array();
                foreach ($result->getLinks(20) as $page) {
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'show' => $show), TRUE);
                }

                $data['paginate'] = array(
                    'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage(), 'show' => $show), TRUE)),
                    'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage(), 'show' => $show), TRUE)),

                    'pages' => $pages,
                    'index' => $pager,
                    'see_all' => array(
                        'total' => $result->getNbResults(),
                        'url' => $router->generate($route, array('pager' => 'all', 'show' => $show), TRUE)
                    )
                );
            }

            if ($this->getFormat() == 'json') {

                // for json we need the real image paths
                foreach ($data['products'] as $k => $product) {
                    $data['products'][$k]['image'] = Tools::productImageUrl($product['image'], '234x410');
                    $data['products'][$k]['image_flip'] = Tools::productImageUrl($product['image_flip'], '234x410');
                }
                $this->setCache($cache_id, $data, 5);
                $html = $data; // Use the json data as the html returned to call
            } else {
                $classes = 'category-'.preg_replace('/[^a-z]/', '-', strtolower($cms_page->getTitle()));
                if (preg_match('/(pige|girl|tjej|tytto|jente)/', $container->get('request')->getPathInfo())) {
                    $classes .= ' category-girl';
                } elseif (preg_match('/(dreng|boy|kille|poika|gutt)/', $container->get('request')->getPathInfo())) {
                    $classes .= ' category-boy';
                }

                $twig = $this->get('twig');
                $twig->addGlobal('page_type', 'category-'.$category_id);
                $twig->addGlobal('body_classes', 'body-category category-'.$category_id.' body-'.$show.' '.$classes);
                $twig->addGlobal('show_new_price_badge', $hanzo->get('webshop.show_new_price_badge'));
                $twig->addGlobal('cms_id', $cms_page->getParentId());
                $twig->addGlobal('show_by_look', ($show === 'look'));
                $twig->addGlobal('browser_title', $cms_page->getTitle());

                $html = $this->renderView('CategoryBundle:Default:view.html.twig', $data);
                $this->setCache($cache_id, $html, 5);
            }
        } // End of retrival of fresh data

        // json requests
        if ($this->getFormat() == 'json') {
            return $this->json_response($html);
        }else{
            $this->setSharedMaxAge(1800);
            return $this->response($html);
        }

    }

    public function listProductsAction($view = 'simple', $filter = 'G_')
    {
        $filter_map = array(
            'G_' => 'Girl',
            'LG_' => 'Little Girl',
            'B_' => 'Boy',
            'LB_' => 'Little Boy',
        );

        $hanzo = Hanzo::getInstance();
        $domain_id = $hanzo->get('core.domain_id');

        $products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId($domain_id)
            ->endUse()
            ->useProductsToCategoriesQuery()
                ->useCategoriesQuery()
                    ->filterByContext($filter.'%', \Criteria::LIKE)
                ->endUse()
            ->endUse()
            ->joinWithProductsToCategories()
            ->orderBySku()
            ->groupBySku()
            ->find()
        ;

        $locale = $this->getRequest()->getLocale();
        $records = array();
        foreach ($products as $product) {
            $product->setLocale($locale);

            $records[] = array(
                'sku' => $product->getSku(),
                'id' => $product->getId(),
                'title' => $product->getTitle(),
            );
        }

        $max = ceil(count($records)/3);
        $records = array_chunk($records, $max);

        $this->setSharedMaxAge(86400);
        return $this->render('CategoryBundle:Default:contextList.html.twig', array(
            'page_type' => 'context-list',
            'products' => $records,
            'page_title' => $filter_map[$filter]
        ));
    }
}

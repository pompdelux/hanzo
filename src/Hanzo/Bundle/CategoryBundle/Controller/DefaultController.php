<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\DependencyInjection\ContainerAware;
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

use Hanzo\Model\CmsPeer;

class DefaultController extends CoreController
{

    /**
     * handle category listings
     *
     * @param integer $cms_id
     * @param integer $category_id
     * @param boolean $show
     * @param integer $pager
     *
     * @return Response
     */
    public function viewAction($cms_id, $category_id, $show, $pager = 1)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $hanzo->get('core.locale');
        $translator = $this->get('translator');

        $cache_id = explode('_', $this->get('request')->get('_route'));
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1], $show, $pager);

        if($this->getFormat() !== 'json') $cache_id[] = 'html'; // Extra cache id if its not a json call

        $html = $this->getCache($cache_id); // If there a cached version, html has both the json and html version
        $data = null;

        /*
         *  If html wasn't cached retrieve a fresh set of data
         */
        if(!$html){
            $cms_page = CmsPeer::getByPK($cms_id, $locale);
            $settings = $cms_page->getSettings(null, false);

            $color_map = null;
            if(!empty($settings->colors)){
                $color_map = explode(',', $settings->colors);
            }

            $route = $container->get('request')->get('_route');
            $router = $container->get('router');
            $domain_id = $hanzo->get('core.domain_id');
            $show_by_look = (bool)($show === 'look');

            $result = ProductsImagesCategoriesSortQuery::create()
                ->useProductsQuery()
                    ->where('products.MASTER IS NULL')
                    // ->filterByIsOutOfStock(FALSE)
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
                ->filterByCategoriesId($category_id)
            ;

            // If there are any colors in the settings to order from, add the order column here.
            // Else order by normal Sort in db
            if ($color_map) {
                $result = $result->useProductsImagesQuery()
                    ->addAscendingOrderByColumn(sprintf(
                        "FIELD(%s, %s)",
                        ProductsImagesPeer::COLOR,
                        '\''.implode('\',\'', $color_map).'\''
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

            $result = $result->paginate(null, null);

            $product_route = str_replace('category_', 'product_', $route);

            $records = array();
            $product_ids = array();
            foreach ($result as $record) {

                $image = $record->getProductsImages()->getImage();

                // Only use 01.
                if (preg_match('/_01.jpg/', $image)) {
                    $product = $record->getProducts();
                    $product_ids[] = $product->getId();

                    $image_overview = str_replace('_set_', '_overview_', $image);
                    $image_set = str_replace('_overview_', '_set_', $image);

                    $alt = trim(Tools::stripTags($translator->trans('headers.category-'.$category_id, [], 'category'))).': '.$product->getSku();

                    $records[] = array(
                        'sku' => $product->getSku(),
                        'out_of_stock' => $product->getIsOutOfStock(),
                        'id' => $product->getId(),
                        'title' => $product->getTitle(),
                        'image' => ($show_by_look) ? $image_set : $image_overview,
                        'image_flip' => ($show_by_look) ? $image_overview : $image_set,
                        'alt' => $alt,
                        'url' => $router->generate($product_route, array(
                            'product_id' => $product->getId(),
                            'title' => Tools::stripText($product->getTitle()),
                            'focus' => $record->getProductsImages()->getId()
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

            if ($result->haveToPaginate()) {

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

                $this->get('twig')->addGlobal('page_type', 'category-'.$category_id);
                $this->get('twig')->addGlobal('body_classes', 'body-category category-'.$category_id.' body-'.$show.' '.$classes);
                $this->get('twig')->addGlobal('show_new_price_badge', $hanzo->get('webshop.show_new_price_badge'));
                $this->get('twig')->addGlobal('cms_id', $cms_page->getParentId());
                $this->get('twig')->addGlobal('show_by_look', ($show === 'look'));
                $this->get('twig')->addGlobal('browser_title', $cms_page->getTitle());
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

        $records = array();
        foreach ($products as $product) {
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

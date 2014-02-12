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
     * @param boolean $show
     * @param integer $pager
     *
     * @return Response
     */
    public function viewAction($cms_id, $show, $pager = 1)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $hanzo->get('core.locale');
        $translator = $this->get('translator');

        $cacheId = explode('_', $this->get('request')->get('_route'));
        $cacheId = array($cacheId[0], $cacheId[2], $cacheId[1], $show, $pager);

        if ($this->getFormat() !== 'json') $cacheId[] = 'html'; // Extra cache id if its not a json call

        $html = $this->getCache($cacheId); // If there a cached version, html has both the json and html version
        $data = null;

        /*
         *  If html wasn't cached retrieve a fresh set of data
         */
        if (!$html) {

            $data = $this->getCategoryProducts($cms_id, $show, $pager);

            if ($this->getFormat() == 'json') {
                $this->setCache($cacheId, $data, 5);
                $html = $data;
            } else {

                $cmsPage = CmsPeer::getByPK($cms_id, $locale);
                $settings = $cmsPage->getSettings(null, false);

                // Define classes to the body, dependently on the context of the category.
                $classes = 'category-'.preg_replace('/[^a-z]/', '-', strtolower($cmsPage->getTitle()));
                if (preg_match('/(pige|girl|tjej|tytto|jente)/', $container->get('request')->getPathInfo())) {
                    $classes .= ' category-girl';
                } elseif (preg_match('/(dreng|boy|kille|poika|gutt)/', $container->get('request')->getPathInfo())) {
                    $classes .= ' category-boy';
                }

                $this->get('twig')->addGlobal('route', $container->get('request')->get('_route'));
                $this->get('twig')->addGlobal('page_type', 'category-'.$settings->category_id);
                $this->get('twig')->addGlobal('body_classes', 'body-category category-'.$settings->category_id.' body-'.$show.' '.$classes);
                $this->get('twig')->addGlobal('show_new_price_badge', $hanzo->get('webshop.show_new_price_badge'));
                $this->get('twig')->addGlobal('cms_id', $cmsPage->getParentId());
                $this->get('twig')->addGlobal('show_by_look', ($show === 'look'));
                $this->get('twig')->addGlobal('browser_title', $cmsPage->getTitle());
                $html = $this->renderView('CategoryBundle:Default:view.html.twig', $data);
                $this->setCache($cacheId, $html, 5);
            }
        } // End of retrival of fresh data

        // json requests
        if ($this->getFormat() == 'json') {

            return $this->json_response($html);
        } else {
            $this->setSharedMaxAge(1800);

            return $this->response($html);
        }

    }

    public function listProductsAction($view = 'simple', $filter = 'G_')
    {
        $filterMap = array(
            'G_' => 'Girl',
            'LG_' => 'Little Girl',
            'B_' => 'Boy',
            'LB_' => 'Little Boy',
        );

        $hanzo = Hanzo::getInstance();
        $domainId = $hanzo->get('core.domain_id');

        $products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId($domainId)
            ->endUse()
            ->useProductsToCategoriesQuery()
                ->useCategoriesQuery()
                    ->filterByContext($filter.'%', \Criteria::LIKE)
                ->endUse()
            ->endUse()
            ->joinWithProductsToCategories()
            ->orderBySku()
            ->groupBySku()
            ->find();

        $locale = $this->getRequest()->getLocale();
        $records = array();
        $locale = $this->getRequest()->getLocale();
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
            'page_title' => $filterMap[$filter]
        ));
    }


    public function listCategoryProductsAction($cms_id, $show, $pager = 1)
    {

        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $hanzo->get('core.locale');
        $translator = $this->get('translator');

        $cacheId = array(__FUNCTION__, $cms_id, $show, $pager);

        if ($this->getFormat() !== 'json') $cacheId[] = 'html'; // Extra cache id if its not a json call

        $html = $this->getCache($cacheId); // If there a cached version, html has both the json and html version
        $data = null;
        /*
         *  If html wasnt cached retrieve a fresh set of data
         */
        if (!$html) {
            $data = $this->getCategoryProducts($cms_id, $show, $pager);

            if ($this->getFormat() == 'json') {
                $this->setCache($cacheId, $data, 5);
                $html = $data;
            } else {
                $route = $container->get('request')->get('_route');

                if (!$route) {
                    // Maybe this is an subrequest. Genereate the route from cmsPage
                    $route = strtolower('category_' . $cms_id . '_' . $locale);
                }

                $this->get('twig')->addGlobal('route', $route);
                $this->get('twig')->addGlobal('show_new_price_badge', $hanzo->get('webshop.show_new_price_badge'));

                $html = $this->renderView('CategoryBundle:Default:listCategoryProducts.html.twig', $data);
                $this->setCache($cacheId, $html, 5);
            }
        }

        // json requests
        if ($this->getFormat() == 'json') {
            return $this->json_response($html);
        } else {
            $this->setSharedMaxAge(1800);

            return $this->response($html);
        }
    }
    /**
     * get category products
     *
     * @param integer $cms_id
     * @param boolean $show
     * @param integer $pager
     *
     * @return array
     */
    public function getCategoryProducts($cms_id, $show, $pager = 1)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $hanzo->get('core.locale');
        $translator = $this->get('translator');
        $cmsPage = CmsPeer::getByPK($cms_id, $locale);
        if (!$cmsPage) {
            return [];
        }

        $settings = $cmsPage->getSettings(null, false);

        $colorMap = null;
        if (!empty($settings->colors)) {
            $colorMap = explode(',', $settings->colors);
        }

        $route = $container->get('request')->get('_route');
        if (!$route) {
            // Maybe this is an subrequest. Genereate the route from cmsPage
            $route = strtolower('category_' . $cms_id . '_' . $locale);
        }

        $router = $container->get('router');
        $domainId = $hanzo->get('core.domain_id');
        $showByLook = (bool) ($show === 'look');

        $result = ProductsImagesCategoriesSortQuery::create()
            ->joinWithProducts()
            ->useProductsQuery()
                ->joinProductsI18n()
                ->where('products.MASTER IS NULL')
                // ->filterByIsOutOfStock(FALSE)
                ->useProductsDomainsPricesQuery()
                    ->filterByDomainsId($domainId)
                ->endUse()
                ->useProductsI18nQuery()
                    ->filterByLocale($locale)
                ->endUse()
            ->endUse()
            ->useProductsImagesQuery()
                ->filterByType($showByLook?'set':'overview')
                ->groupByImage()
            ->endUse()
            ->joinWithProductsImages()
            ->filterByCategoriesId($settings->category_id);

        // If there are any colors in the settings to order from, add the order column here.
        // Else order by normal Sort in db
        if ($colorMap) {
            $result = $result->useProductsImagesQuery()
                ->addAscendingOrderByColumn(sprintf(
                    "FIELD(%s, %s)",
                    ProductsImagesPeer::COLOR,
                    '\''.implode('\',\'', $colorMap).'\''
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

        // $result = $result->paginate(null, null);
        $result = $result->find();
        $productRoute = str_replace('category_', 'product_', $route);

        $records = array();
        $productIds = array();
        foreach ($result as $record) {

            $image = $record->getProductsImages()->getImage();

            // Only use 01.
            if (preg_match('/_01.jpg/', $image)) {
                $product = $record->getProducts();
                $productIds[] = $product->getId();
                $product->setLocale($locale);

                $imageOverview = str_replace('_set_', '_overview_', $image);
                $imageSet = str_replace('_overview_', '_set_', $image);

                $alt = trim(Tools::stripTags($translator->trans('headers.category-'.$settings->category_id, [], 'category'))).': '.$product->getSku();

                $records[] = array(
                    'sku' => $product->getSku(),
                    'out_of_stock' => $product->getIsOutOfStock(),
                    'id' => $product->getId(),
                    'title' => $product->getTitle(),
                    'image' => ($showByLook) ? $imageSet : $imageOverview,
                    'image_flip' => ($showByLook) ? $imageOverview : $imageSet,
                    'alt' => $alt,
                    'url' => $router->generate($productRoute, array(
                        'product_id' => $product->getId(),
                        'title' => Tools::stripText($product->getTitle()),
                        'focus' => $record->getProductsImages()->getId()
                    )),
                );
            }
        }

        // get product prices
        $prices = ProductsDomainsPricesPeer::getProductsPrices($productIds);

        // attach the prices to the products
        foreach ($records as $i => $data) {
            if (isset($prices[$data['id']])) {
                $records[$i]['prices'] = $prices[$data['id']];
            }
        }

        $data = [
            'title' => '',
            'products' => $records,
            'paginate' => null,
        ];

        if (method_exists($result, 'haveToPaginate') && $result->haveToPaginate()) {

            $pages = array();
            foreach ($result->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, array('pager' => $page, 'show' => $show), true);
            }

            $data['paginate'] = array(
                'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage(), 'show' => $show), true)),
                'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage(), 'show' => $show), true)),

                'pages' => $pages,
                'index' => $pager,
                'see_all' => array(
                    'total' => $result->getNbResults(),
                    'url' => $router->generate($route, array('pager' => 'all', 'show' => $show), true)
                )
            );
        }

        if ($this->getFormat() == 'json') {

            // for json we need the real image paths
            foreach ($data['products'] as $k => $product) {
                $data['products'][$k]['image'] = Tools::productImageUrl($product['image'], '234x410');
                $data['products'][$k]['image_flip'] = Tools::productImageUrl($product['image_flip'], '234x410');
            }
        }

        return $data;
    }
}

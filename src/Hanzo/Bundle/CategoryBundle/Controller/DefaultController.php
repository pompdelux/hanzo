<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\CmsQuery;
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
     * @param boolean $show
     * @param integer $pager
     * @return Response
     */
    public function viewAction(Request $request, $cms_id, $show, $pager = 1)
    {
        $hanzo     = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale = $this->getRequest()->getLocale();

        $cache_id = explode('_', $this->get('request')->get('_route'));
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1], $show, $pager);

        if ($this->getFormat() !== 'json') {
            $cache_id[] = 'html';
        }

        // TODO: should not be set here !!
        $cms_page = CmsPeer::getByPK($cms_id, $locale);

        $topLevel = $this->getTopLevelCMSPage($cms_page);

        $colorMapping = $this->getSettings($locale, $topLevel->getId(), 'colormap');

        $size_filter  = [];
        $color_filter = [];
        $eco_filter   = [];

        // we need this "hack" to prevent url pollution..
        $escapes = [
            ' - ' => ' & ',
        ];

        if ($request->query->has('filter')) {
            // A color, e.g. "Blue", can be mapped to many colors like "Dusty Blue" or "Navy"
            // So this maps the color to all it's aliases
            foreach ($request->query->get('color', []) as $color) {
                $color = strtr($color, $escapes);
                if (isset($colorMapping[$color])) {
                    $color_filter = array_merge($color_filter, $colorMapping[$color]);
                }
            }

            $cache_id = array_merge($cache_id, $color_filter);

            foreach ($request->query->get('size', []) as $size) {
                $size_filter[] = $size;
            }

            $cache_id = array_merge($cache_id, $size_filter);

            foreach ($request->query->get('eco', []) as $eco) {
                $eco_filter[] = $eco;
            }

            $cache_id = array_merge($cache_id, $eco_filter);
        }

        $html = $this->getCache($cache_id); // If there a cached version, html has both the json and html version
        // FIXME: testing
        $html = false;

        /**
         *  If html wasn't cached retrieve a fresh set of data
         */
        $data = null;
        if (!$html) {
            $data = $this->getCategoryProducts($cms_id, $show, $pager);

            if ($this->getFormat() == 'json') {
                $this->setCache($cache_id, $data, 5);
                $html = $data;
            } else {
                $cms_page = CmsPeer::getByPK($cms_id, $locale);
                $settings = $cms_page->getSettings(null, false);

                // Define classes to the body, dependently on the context of the category.
                $classes = 'category-'.preg_replace('/[^a-z]/', '-', strtolower($cms_page->getTitle()));
                if (preg_match('/(pige|girl|tjej|tytto|jente)/', $container->get('request')->getPathInfo())) {
                    $classes .= ' category-girl';
                } elseif (preg_match('/(dreng|boy|kille|poika|gutt|junge|jongen)/', $container->get('request')->getPathInfo())) {
                    $classes .= ' category-boy';
                }

                $this->get('twig')->addGlobal('route', $container->get('request')->get('_route'));

                $twig = $this->get('twig');
                $twig->addGlobal('route', $container->get('request')->get('_route'));
                $twig->addGlobal('page_type', 'category-'.$settings->category_id);
                $twig->addGlobal('body_classes', 'body-category category-'.$settings->category_id.' body-'.$show.' '.$classes);
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
        }

        $this->setSharedMaxAge(1800);
        return $this->response($html);

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

    public function listCategoryProductsAction($cms_id, $show, $pager = 1, $route = null)
    {
        $hanzo     = Hanzo::getInstance();
        $container = $hanzo->container;
        $locale    = $hanzo->get('core.locale');

        $cache_id = array(__FUNCTION__, $cms_id, $show, $pager);

        if ($this->getFormat() !== 'json') {
            $cache_id[] = 'html';
        }

        $html = $this->getCache($cache_id); // If there a cached version, html has both the json and html version

        /**
         *  If html wasnt cached retrieve a fresh set of data
         */
        $data = null;
        if (!$html) {
            $data = $this->getCategoryProducts($cms_id, $show, $pager);
            $data['route'] = $route;

            if ($this->getFormat() == 'json') {
                $this->setCache($cache_id, $data, 5);
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
                $this->setCache($cache_id, $html, 5);
            }
        }

        // json requests
        if ($this->getFormat() == 'json') {
            return $this->json_response($html);
        }

        $this->setSharedMaxAge(1800);
        return $this->response($html);
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
        $hanzo      = Hanzo::getInstance();
        $container  = $hanzo->container;
        $locale     = $hanzo->get('core.locale');
        $translator = $this->get('translator');
        $cms_page   = CmsPeer::getByPK($cms_id, $locale);
        $request    = $container->get('request');

        if (!$cms_page) {
            return [];
        }

        $topLevel = $this->getTopLevelCMSPage($cms_page);

        // One color or size might cover many others, e.g. Blue => Navy
        // The tokens are not mapped, just extracted the same way and passed to the tpl
        $mappings           = [];
        $mappings['color']  = $this->getSettings($locale, $topLevel->getId(), 'colormap');
        $mappings['size']   = $this->getSettings($locale, $topLevel->getId(), 'sizes', TRUE);
        $mappings['tokens'] = $this->getSettings($locale, $topLevel->getId(), 'tokens');

        $use_filter = false;
        $filters    = [];
        $filters    = $this->getFilters($mappings);

        $settings = $cms_page->getSettings(null, false);
        // hf@bellcom.dk: still needed? 13-may-2015
        if (empty($filters['color'])) {
            if (!empty($settings->colors)) {
                $filters['color'] = explode(',', $settings->colors);
            }
        }

        if (!empty($filters)) {
            $use_filters = true;
        }

        $route = $request->get('_route');

        if (!$route) {
            // Maybe this is an sub-request. Genereate the route from cmsPage
            $route = strtolower('category_' . $cms_id . '_' . $locale);
        }

        $router = $container->get('router');

        $domain_id = $hanzo->get('core.domain_id');
        $show_by_look = (bool) ($show === 'look');
        $product_range = $this->container->get('hanzo_product.range')->getCurrentRange();

        // Use embedded_category_id if exists, else fallback to category_id. This way we can support multiple categories on the same page
        if (isset($settings->embedded_category_id))
        {
            $category_ids_for_filter = $settings->embedded_category_id;
        }
        else
        {
            $category_ids_for_filter = $settings->category_id;
        }

        $result = ProductsImagesCategoriesSortQuery::create()
            ->joinWithProducts()
            ->useProductsQuery()
                ->filterByRange($product_range)
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
            ->filterByCategoriesId($category_ids_for_filter);

        // If there are any colors in the settings to order from, add the order column here.
        // Else order by normal Sort in db

        if ($use_filter) {
            $con     = \Propel::getConnection();

            $sql = "
                SELECT
                    C1.master_products_id AS master_products_id
                FROM
                    products AS p,
                    search_products_tags AS C1\n";

            $sql .= $this->searchProductsFilterBuilder($filters);
            $sql .= "\nAND p.is_out_of_stock = 0 AND p.id = C1.products_id";

            $sql .= "\nGROUP BY
                C1.master_products_id";

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

        if ($request->query->get('show_all')) {
            $result = $result->useProductsQuery()
                ->filterByIsOutOfStock(false)
                ->_or()
                ->filterByIsOutOfStock(true)
                ->endUse()
                ;
        } else {
            $result = $result->useProductsQuery()
                ->filterByIsOutOfStock(false)
                ->endUse()
                ;
        }

        if ($filters['color']) {
            if ($use_filter) {
                // when using filters we need descending order not ascending.
                $result = $result->useProductsImagesQuery()
                    ->addDescendingOrderByColumn(sprintf(
                        "FIELD(%s, %s)",
                        ProductsImagesPeer::COLOR,
                        "'".implode("','", $filters['color'])."'"

                    ))
                    // We already to this?
                    ->filterByColor($filters['color'])
                    ->endUse();
            } else {
                $result = $result->useProductsImagesQuery()
                    ->addAscendingOrderByColumn(sprintf(
                        "FIELD(%s, %s)",
                        ProductsImagesPeer::COLOR,
                        "'".implode("','", $filters['color'])."'"

                    ))
                    ->endUse();
            }
        } else {
            $result = $result->orderBySort();
        }

        $result = $result->find();

        $product_route = str_replace('category_', 'product_', $route);

        $records = [];
        $product_ids = [];

        foreach ($result as $record) {

            $image = $record->getProductsImages()->getImage();

            // Only use 01.
            if (preg_match('/_01.[jpg|png]/', $image)) {
                $product = $record->getProducts();

                $product_ids[] = $product->getId();
                $product->setLocale($locale);

                $image_overview = str_replace('_set_', '_overview_', $image);
                $image_set = str_replace('_overview_', '_set_', $image);

                $alt = trim(Tools::stripTags($translator->trans('headers.category-'.$settings->category_id, [], 'category'))).': '.$product->getTitle($request->getLocale());

                $records[] = array(

                    'sku'          => $product->getSku(),
                    'out_of_stock' => $product->getIsOutOfStock(),
                    'id'           => $product->getId(),
                    'title'        => $product->getTitle(),
                    'image'        => (($show_by_look) ? $image_set      : $image_overview),
                    'image_flip'   => (($show_by_look) ? $image_overview : $image_set),
                    'alt'          => $alt,
                    'url'          => $router->generate($product_route, [
                        'product_id' => $product->getId(),
                        'title'      => Tools::stripText($product->getTitle()),
                        'focus'      => $record->getProductsImages()->getId()
                    ]),
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

        $data = [
            'title' => '',
            'products' => $records,
            'paginate' => null,
        ];

        $data['color_mapping'] = array_keys($mappings['color']);
        $data['size_mapping']  = array_keys($mappings['size']);

        // Workaround random text in token
        $escapedTokens = [];
        foreach (array_keys($mappings['tokens']) as $rawToken)
        {
            $escapedTokens[] = ['name' => $rawToken, 'value' => Tools::stripText($rawToken)];
        }
        $data['token_mapping'] = $escapedTokens;

        if ($this->getFormat() == 'json') {
            // for json we need the real image paths
            foreach ($data['products'] as $k => $product) {
                $data['products'][$k]['image'] = Tools::productImageUrl($product['image'], '234x410');
                $data['products'][$k]['image_flip'] = Tools::productImageUrl($product['image_flip'], '234x410');
            }
        }

        return $data;
    }

    /**
     * searchProductsFilterBuilder
     *
     * @param array $filters
     * @return string
     *
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    protected function searchProductsFilterBuilder($filters)
    {
        $sql     = '';
        $joins   = [];
        $wheres  = [];
        $counter = 2;
        $con     = \Propel::getConnection();

        if (count($filters) > 1) {
            foreach ($filters as $filter)
            {
                if (empty($filter))
                {
                    continue;
                }
                $filter_values = implode(', ', array_map(array($con, 'quote'), $filter));
                $joins[] = "JOIN
                    search_products_tags AS C{$counter}
                    ON (C1.products_id = C{$counter}.products_id)";

            $wheres[] = "\nC{$counter}.token IN ({$filter_values})";
            $counter++;
            }
        }
        else {
            $filter_values = implode(', ', array_map(array($con, 'quote'), array_shift($filters)));
            $wheres[] = "\nC1.token IN ({$filter_values})";
        }

        $sql = implode("\n", $joins)."\nWHERE".implode("\nAND", $wheres);
        return $sql;
    }

    /**
     * @param mixed $cms_page
     * Ugly hack to traverse to top item
     * - Needed because an extra level was introduced: Pige > Overtøj > Jakker
     */
    public function getTopLevelCMSPage($cms_page)
    {
        $locale   = $this->getRequest()->getLocale();
        $topLevel = CmsPeer::getByPK($cms_page->getParentId(), $locale);

        if (is_null($topLevel))
        {
            $topLevel = $cms_page;
        }
        else
        {
            while ($topLevel->getParentId() != NULL)
            {
                $topLevel = CmsPeer::getByPK($topLevel->getParentId(), $locale);
            }
        }

        return $topLevel;
    }

    /**
     * Extracts the json from the CMS page (which again is loaded from the xliff files)
     *
     * @param string $locale
     * @param int $id
     * @param string $settingsName
     * @param bool $fixNumeric Loop over array to fix key/value
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function getSettings($locale, $cmsId, $settingsName, $fixNumeric = false)
    {
        $settings = [];

        static $cache = [];

        if (!isset($cache[$cmsId.'-'.$locale])) {
            $parentSettings = CmsI18nQuery::create()
                ->filterByLocale($locale)
                ->filterById($cmsId)
                ->findOne()->getSettings(false);

            $cache[$cmsId.'-'.$locale] = $parentSettings;
        } else {
            $parentSettings = $cache[$cmsId.'-'.$locale];
        }

        if ($parentSettings && isset($parentSettings->{$settingsName})) {
            $settings = (array) $parentSettings->{$settingsName};
        }

        if ($fixNumeric === true) {
            // When casting objects to arrays with numeric attributes, everything goes belly up
            // The fix should be to pass true to json_decode http://php.net/json_decode
            // but that breaks other stuff :), so there for the extra foreach after this
            $tmp      = $settings;
            $settings = [];
            foreach ($tmp as $key => $value) {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    /**
     * Extracts filter values from url and performs mapping for color/size
     *
     * @param array $mappings Contains the mappings for color and size
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function getFilters(Array $mappings)
    {
        $request    = $this->container->get('request');

        $filterTypes = ['color', 'size', 'eco', 'discount'];

        $filters = [];

        // we need this "hack" to prevent url pollution..
        $escapes = [
            ' - ' => ' & ',
        ];

        if ($request->query->has('filter')) {
            foreach ($filterTypes as $filterName) {
                foreach ($request->query->get($filterName, []) as $value) {
                    $value = strtr($value, $escapes);
                    if (!isset($filters[$filterName])) {
                        $filters[$filterName] = [];
                    }
                    if (isset($mappings[$filterName])) {
                        if (isset($mappings[$filterName][$value])) {
                            $filters[$filterName] = array_merge($filters[$filterName], $mappings[$filterName][$value]);
                        }
                    } else {
                        $filters[$filterName][] = $value;
                    }
                }
            }
        }

        return $filters;
    }
}

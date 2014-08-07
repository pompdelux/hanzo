<?php

namespace Hanzo\Bundle\SearchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;

class DefaultController extends CoreController
{
    /**
     * category / size product search
     * @param  Request  $request
     * @param  int      $id      cms node id
     * @return Response
     */
    public function categoryAction(Request $request, $id)
    {
        $hanzo = Hanzo::getInstance();

        // if webshop is closed, disable search
        if (1 == $hanzo->get('webshop.closed')) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $page = CmsPeer::getByPK($id, $locale);

        $settings = json_decode($page->getSettings());
        $group = $settings->group;

        $categories  = array_map('trim', explode(',', $settings->category_ids));

        $no_accessories = $categories;
        $accessories = array_shift($no_accessories);

        $category_sort = implode(',', $no_accessories);

        // TODO: figure out a way to avoid this..
        // setup size grouping
        switch ($group) {
            case 'g':
            case 'b':
                $sizes = [
                    110 => [110, '110-116'],
                    116 => [116, '110-116'],
                    122 => [122, '122-128'],
                    128 => [128, '122-128'],
                    134 => [134, '134-140'],
                    140 => [140, '134-140'],
                    146 => [146, '146-152'],
                    152 => [152, '146-152'],
                ];
                break;
            case 'lb':
            case 'lg':
                $sizes = [
                    80  => [80, '80-86'],
                    86  => [86, '80-86', '86-92'],
                    92  => [92, '86-92'],
                    98  => [98, '98-104'],
                    104 => [104, '98-104'],
                ];
                break;
        }

        $result_set = [];
        if ('POST' === $this->getRequest()->getMethod()) {
            $size = $this->getRequest()->get('size');

            if (empty($size) || empty($sizes[$size])) {
                return $this->redirect($request->headers->get('referer'));
            }

            $product_ids  = array();
            $category_ids = array();
            $category_map = array();

            $conn = \Propel::getConnection();

            // not "accessories"
            $sql = "
                SELECT DISTINCT
                    p.id AS vid,
                    p.master,
                    p.size,
                    ci.id as category_id,
                    ci.title,
                    (SELECT p1.id FROM products AS p1 WHERE SKU = p.master) AS id
                FROM
                    products AS p
                JOIN
                    products_to_categories AS p2c
                    ON (
                        p2c.products_id = (SELECT p1.id FROM products AS p1 WHERE SKU = p.master)
                    )
                JOIN
                    categories_i18n AS ci
                    ON (
                        p2c.categories_id = ci.id
                    )
                JOIN
                    products_domains_prices AS pdp
                    ON (
                        p.id = pdp.products_id
                    )
                WHERE
                    p.size IN ('".implode("','", $sizes[$size])."')
                AND
                    p.is_out_of_stock = 0
                AND
                    pdp.domains_id = {$domain_id}
                AND
                    ci.locale = '{$locale}'
                AND
                    p2c.categories_id IN ({$category_sort})
                ORDER BY
                    field(p2c.categories_id, {$category_sort}),
                    p.sku
            ";

            $query = $conn->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute();

            while ($record = $query->fetch(\PDO::FETCH_ASSOC)) {
                $product_ids[$record['id']] = $record['id'];
                $category_map[$record['title']][$record['id']] = $record['id'];
                $category_ids[$record['title']] = $record['category_id'];
            }

            // "accessories" only
            $sql = "
                SELECT DISTINCT
                    p.id AS vid,
                    p.master,
                    p.size,
                    ci.id as category_id,
                    ci.title,
                    (SELECT p1.id FROM products AS p1 WHERE SKU = p.master) AS id
                FROM
                    products AS p
                JOIN
                    products_to_categories AS p2c
                    ON (
                        p2c.products_id = (SELECT p1.id FROM products AS p1 WHERE SKU = p.master)
                    )
                JOIN
                    categories_i18n AS ci
                    ON (
                        p2c.categories_id = ci.id
                    )
                JOIN
                    products_domains_prices AS pdp
                    ON (
                        p.id = pdp.products_id
                    )
                WHERE
                    pdp.domains_id = {$domain_id}
                AND
                    p.is_out_of_stock = 0
                AND
                    ci.locale = '{$locale}'
                AND
                    p2c.categories_id IN ({$accessories})
                ORDER BY
                    field(p2c.categories_id, {$accessories}),
                    p.sku
            ";

            $query = $conn->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute();

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

                $router_keys = include $this->container->getParameter('kernel.cache_dir').'/category_map.php';
                $router = $this->get('router');

                foreach ($products as $product) {
                    if (!$product->getSku() || $product->getIsOutOfStock()) {
                        continue;
                    }

                    foreach ($category_map as $category => $map) {
                        foreach ($map as $id) {
                            if ($id == $product->getId()) {
                                $product_route = $router_keys['_' . strtolower($locale) . '_' . $category_ids[$category]];

                                $image_overview = str_replace('_set_', '_overview_', $product->getProductsImagess()->getFirst()->getImage());
                                $image_set = str_replace('_overview_', '_set_', $product->getProductsImagess()->getFirst()->getImage());

                                $result_set[$category][$id] = array(
                                    'sku' => $product->getSku(),
                                    'id' => $product->getId(),
                                    'out_of_stock' => $product->getIsOutOfStock(),
                                    'title' => $product->getSku(),
                                    'image' => $image_set,
                                    'image_flip' => $image_overview,
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
        }

        $this->setSharedMaxAge(300);
        return $this->render('SearchBundle:Default:category.html.twig', array(
            'page_type' => 'category-search',
            'content'   => $page->getContent(),
            'title'     => $page->getTitle(),
            'result'    => $result_set,
            'sizes'     => (is_array($sizes) ? $sizes : array()),
            'route'     => $this->getRequest()->get('_route'),
            'selected'  => $this->getRequest()->get('size', ''),
            'cms_id'    => $page->getParentId()
        ));
    }


    /**
     * combined product and cms search
     *
     * @param Request   $request
     * @param  int      $id  cms page to display at the top of the page
     * @return Response
     */
    public function advancedAction(Request $request, $id = null)
    {
        $hanzo     = Hanzo::getInstance();
        $locale    = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $result = array(
            'products' => array(),
            'pages' => array()
        );

        if (isset($_GET['q'])) {
            $q = $request->get('q', null);

            // if webshop is closed, disable product search
            if (0 == $hanzo->get('webshop.closed')) {
                $result['products'] = $this->productSearch($q, $locale, $domain_id);
            }

            $result['pages'] = $this->pageSearch($q, $locale);
        }

        $this->setSharedMaxAge(300);
        return $this->render('SearchBundle:Default:advanced.html.twig', array(
            'page_type' => 'category-search',
            'route'     => $request->get('_route'),
            'result'    => $result,
        ));
    }


    /**
     * search through produces
     *
     * @todo   implement product description search
     *
     * @param  string $q         search criteria
     * @param  string $locale    locale
     * @param  int    $domain_id domain id to search
     * @return array
     */
    protected function productSearch($q, $locale, $domain_id)
    {
        $q = explode(' ', trim($q));
        $where = '';
        foreach ($q as $string) {
            if ($string !== reset($q)) {
                $where .= 'OR ';
            }
            $where .= "p.size LIKE '%{$string}%'
                    OR
                        p.color LIKE '%{$string}%'
                    OR
                        p18.title LIKE '%{$string}%'
                    OR
                        p18.content LIKE '%{$string}%'
                    ";
        }
        $sql = "
            SELECT
                p.id,
                p18.title,
                p.is_out_of_stock,
                p2c.categories_id,
                pi.id AS image_id,
                pi.image
            FROM
                products AS p
            JOIN
                products_i18n p18
                ON
                    (p.id = p18.id AND p18.locale = '{$locale}')
            JOIN
                products_domains_prices AS pdp
                ON
                    (p.id = pdp.products_id AND pdp.domains_id = {$domain_id})
            JOIN
                products_images AS pi
                ON
                    (p.id = pi.products_id AND pi.type = 'set')
            JOIN
                products_to_categories AS p2c
                ON
                    (p.id = p2c.products_id)
            WHERE (
                {$where}
                )
                AND p.master IS NULL
            GROUP BY
                pi.image
            ORDER BY
                p.sku
        ";

        $conn = \Propel::getConnection();
        $query = $conn->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute();

        $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
        $router = $this->get('router');

        $result = [];
        $product_ids = [];
        while ($record = $query->fetch(\PDO::FETCH_ASSOC)) {
            if (isset($product_ids[$record['id']])) {
                continue;
            }

            $product_ids[$record['id']] = $record['id'];

            $product_route = '';
            $key = '_' . strtolower($locale) . '_' . $record['categories_id'];

            if (isset($router_keys[$key])) {
                $product_route = $router_keys[$key];
            }
            if (!empty($product_route)) {
                $result[] = array(
                    'sku' => $record['title'],
                    'id' => $record['id'],
                    'out_of_stock' => $record['is_out_of_stock'],
                    'title' => $record['title'],
                    'image' => $record['image'],
                    'url' => $router->generate($product_route, array(
                            'product_id' => $record['id'],
                            'title' => Tools::stripText($record['title']),
                            'focus' => $record['image_id']
                        )),
                );
            }
        }


        if (count($product_ids)) {
            $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);
            // attach the prices to the products
            foreach ($result as $i => $data) {
                if (isset($prices[$data['id']])) {
                    $result[$i]['prices'] = $prices[$data['id']];
                }
            }
        }

        return $result;
    }


    /**
     * search cms pages
     *
     * @param  string $q      query string
     * @param  string $locale
     * @return array
     */
    protected function pageSearch($q, $locale)
    {
        $q = explode(' ', trim($q));
        // search pages
        $pages = CmsI18nQuery::create()
            ->useCmsQuery()
                ->filterByType(['page', 'category'])
                ->filterByCmsThreadId([20, 23])
            ->endUse()
            ->joinWithCms()
            ->filterByIsActive(true)
            ->filterByLocale($locale)
            ->orderByTitle();
        foreach ($q as $string) {
            if ($string !== reset($q)) {
                $pages->_or();
            }
            $string = '%'.$string.'%';
            $pages->filterByTitle($string)
                ->_or()
                ->filterByContent($string);
        }
        $pages = $pages->find();

        $result = [];
        foreach ($pages as $page) {
            $result[] = array(
                'title' => $page->getTitle(),
                'summery' => mb_substr(Tools::stripTags($page->getContent()), 0, 200),
                'url' => '/'.$locale.'/'.$page->getPath(),
            );
        }

        return $result;
    }
}

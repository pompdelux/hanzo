<?php

namespace Hanzo\Bundle\MannequinBundle\Controller;

use Symfony\Component\Yaml\Yaml;

use Criteria;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsPeer;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\CategoriesI18nQuery;
use Hanzo\Model\MannequinImagesQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsToCategoriesPeer;
use Hanzo\Model\ProductsToCategoriesQuery;

class DefaultController extends CoreController
{
    public function viewAction($id)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $page = CmsPeer::getByPK($id, $locale);

        $cache_key = array('mannequin', $id, $locale);
        $data = $this->getCache($cache_key);

        if (!$data) {
            $settings = json_decode($page->getSettings());

            // TODO: move to db, teach hd format.
            $color_map = Yaml::parse(__DIR__ . '/../../../../../app/config/mannequin_color_scheme.yaml');

            $settings->dress_form = $settings->image;
            $color_scheme = $color_map[str_replace('Little', '', $settings->dress_form)][$settings->colorscheme];

            $includes = explode(',', $settings->category_ids);
            $ignores = explode(',', $settings->ignore);

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

            $skus = array();
            $ids = array();
            $products_to_categories = array();
            foreach ($masters as $master) {
                $skus[$master->getProducts()->getSku()] = $categories[$master->getCategoriesId()];
                $ids[] = $master->getProducts()->getId();
                $products_to_categories[$master->getProducts()->getId()] = $master->getCategoriesId();
            }

            $variants = MannequinImagesQuery::create()
                ->joinWithProducts()
                ->useProductsQuery()
                    ->filterByMaster(null, Criteria::ISNULL)
                    ->useProductsToCategoriesQuery()
                        ->addDescendingOrderByColumn(sprintf(
                            "FIELD(%s, %s)",
                            ProductsToCategoriesPeer::CATEGORIES_ID,
                            implode(',', $includes)
                        ))
                    ->endUse()
                ->endUse()
                ->filterByColor($color_scheme)
                ->filterByMaster(array_keys($skus))
                ->find()
            ;

            $prices = ProductsDomainsPricesPeer::getProductsPrices($ids);

            foreach ($variants as $variant) {
                $category = $skus[$variant->getMaster()];

                $price = $prices[$variant->getProducts()->getId()];
                $price = array_shift($price);
                $key = $variant->getMaster().' '.$variant->getColor();

                $products[$category][$key] = array(
                    'category' => $category,
                    'category_id' => $products_to_categories[$variant->getProducts()->getId()],
                    'master' => $variant->getMaster(),
                    'color' => $variant->getColor(),
                    'icon' => $variant->getIcon(),
                    'image' => $variant->getImage(),
                    'layer' => $variant->getLayer(),
                    'price' => $price['price'],
                    'index' => $index,
                );

                $index++;
            }

            $products = array_reverse($products, true);

            foreach ($products as $k => $p) {
                asort($products[$k]);
            }

            $this->setCache($cache_key, array(
                'settings' => $settings,
                'products' => $products,
            ));
        } else {
            $settings = $data['settings'];
            $products = $data['products'];
        }

        return $this->render('MannequinBundle:Default:view.html.twig', array(
            'title' => $settings->title . ' - ' . $settings->colorscheme,
            'products' => $products,
            'dress_form' => $settings->dress_form,
            'page_type' => 'mannequin',
            'localeconv' => localeconv(),
        ));
    }

    public function dressFormAddAction()
    {
        $hanzo = Hanzo::getInstance();
        $request = $this->getRequest();
        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $master = $request->request->get('master');
        $color = $request->request->get('color');
        $layer = $request->request->get('layer');
        $price = $request->request->get('price');
        $key = $request->request->get('key');
        $category_id = $request->request->get('category_id');

        $product = ProductsQuery::create()
            ->findOneBySku($master)
        ;

        $order = OrdersPeer::getCurrent();
        $in_cart = '';
        if ($order->hasProduct($master)) {
            $in_cart = ' in-cart';
        }

        $router_keys = include $this->container->getParameter('kernel.cache_dir').'/category_map.php';
        $router = $this->get('router');
        $product_route = $router_keys['_' . strtolower($locale) . '_' . $category_id];
        $url = $router->generate($product_route, array(
            'product_id' => $product->getId(),
            'title' => Tools::stripText($product->getSku()),
        ));

        $html = '
            <tr class="' . $key . ' mannequin-layer-' . $layer . $in_cart .'">
              <td class="name"><a href="' . $url . '">' . $master . '</a></td>
              <td>' . Tools::moneyFormat($price) . '</td>
              <td>' . $color . '</td>
              <td class="size"></td>
              <td class="quantity"></td>
              <td class="actions">
                <a href="" class="edit" title="">add</a>
                <a href="" class="remove" title="">delete</a>
              </td>
            </tr>
        ';


        return $this->json_response(array(
            'status' => true,
            'message' => '',
            'data' => array('html' => $html),
        ));
    }

    public function cartFormAction($step)
    {
        $hanzo = Hanzo::getInstance();
        $stock = $this->get('stock');
        $translator = $this->get('translator');
        $request = $this->getRequest();

        $locale = $hanzo->get('core.locale');
        $domain_id = $hanzo->get('core.domain_id');

        $response = array(
            'status' => false,
            'message' => 'out.of.stock'
        );

        switch($step) {
            case 'size':
                $sizes = ProductsQuery::create()
                    ->filterByIsOutOfStock(false)
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($domain_id)
                    ->endUse()
                    ->groupById()
                    ->filterByMaster($request->request->get('master'))
                    ->filterByColor($request->request->get('color'))
                    ->find()
                ;
                if ($sizes->count()) {
                    $stock->prime($sizes);

                    $options = array();
                    foreach ($sizes as $record) {
                        if ($dato = $stock->check($record)) {
                            $options[] = '<option value="'.$record->getSize().':'.$record->getId().'">'.$record->getSize().'</option>';
                        }
                    }

                    if (count($options)) {
                        array_unshift($options,
                            '<select name="quantity">',
                            '<option value="">' . $translator->trans('choose') . '</option>'
                        );
                        $response = array(
                            'status' => true,
                            'message' => '',
                            'data' => array('html' => implode('', $options))
                        );
                    }
                }

                break;

            case 'quantity':
                $product = ProductsQuery::create()
                    ->filterByIsOutOfStock(false)
                    ->useProductsDomainsPricesQuery()
                        ->filterByDomainsId($domain_id)
                    ->endUse()
                    ->groupById()
                    ->filterByMaster($request->request->get('master'))
                    ->filterByColor($request->request->get('color'))
                    ->filterBySize($request->request->get('size'))
                    ->findOne()
                ;

                if ($product instanceof Products) {
                    if ($level = $stock->get($product)) {
                        $max = $level > 10 ? 10 : $level;
                        $select = '<select name="quantity">';
                        for ($i=1; $i<=$max; $i++) {
                            $select .= '<option value="' . $i . '">' . $i . '</option>';
                        }
                        $select .= '</select>';

                        $response = array(
                            'status' => true,
                            'message' => '',
                            'data' => array('html' => $select)
                        );
                    }
                }
                break;
        }

        return $this->json_response($response);
    }
}

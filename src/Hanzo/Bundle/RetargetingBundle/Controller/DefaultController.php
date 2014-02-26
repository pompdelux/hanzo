<?php

namespace Hanzo\Bundle\RetargetingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hanzo\Core\Tools;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;

class DefaultController extends Controller
{
    protected $data = [
        'da_DK' => [
            'cid' => 'P0OKH',
            'tid' => '7908',
        ],
        'de_DE' => [
            'cid' => 'Q7JS3',
            'tid' => '7898',
        ],
        'fi_FI' => [
            'cid' => 'PM46A',
            'tid' => '7906',
        ],
        'nl_NL' => [
            'cid' => 'QSZDW',
            'tid' => '7904',
        ],
        'nb_NO' => [
            'cid' => 'REEZP',
            'tid' => '7902',
        ],
        'sv_SE' => [
            'cid' => 'RZULI',
            'tid' => '7900',
        ],
    ];

    protected $default_locale = 'da_DK';


    /**
     * @Template()
     * @param integer $order_id
     * @return array
     */
    public function successBlockAction($order_id)
    {
        $order = OrdersPeer::retrieveByPK($order_id);

        return [
            'cid'      => $this->getTrackingId('cid'),
            'tid'      => $this->getTrackingId('tid'),
            'total'    => number_format($order->getTotalPrice(), 2, '.', ''),
            'order_id' => $order->getId(),
            'currency' => $order->getCurrencyCode(),
        ];
    }


    /**
     * @Template()
     */
    public function jsBlockAction()
    {
        return [
            'cid' => $this->getTrackingId('cid')
        ];
    }


    /**
     * @Cache(smaxage="3600")
     * @Route("/retarteging/feed", defaults={"_format"="xml"})
     * @param Request $request
     * @return Response
     */
    public function productFeedAction(Request $request)
    {
        $router = $this->get('router');
        $routes = [];

        foreach ($router->getRouteCollection()->all() as $route => $data) {
            if ('product_' === substr($route, 0, 8)) {
                $category_id = $data->getDefault('category_id');
                if (is_numeric($category_id)) {
                    $routes[$route][] = $category_id;
                }
            }
        }


        $exclude = [
            'Hayward kneesocks',
            'Arlington socks',
            'Oregon socks',
            'POMP bag',
            'POMP big bag',
        ];

        $items = [];
        $product_ids = [];
        foreach ($routes as $route => $category_id) {
            $products = ProductsQuery::create()
                ->filterByMaster(null, \Criteria::ISNULL)
                ->filterBySku($exclude, \Criteria::NOT_IN)
                ->joinWithProductsImages()
                ->useProductsToCategoriesQuery()
                    ->filterByCategoriesId($category_id)
                ->endUse()
                ->joinWithProductsI18n()
                ->useProductsI18nQuery()
                    ->filterByLocale($request->getLocale())
                ->endUse()
                ->find()
            ;

            foreach ($products as $product) {
                $product_id = $product->getId();
                $product_sku = $product->getTitle();
                $product_sku_stripped = Tools::stripText($product_sku);

                $product_ids[] = $product_id;
                $images = ProductsImagesQuery::create()
                    ->filterByProductsId($product_id)
                    ->find()
                ;

                $items[] = [
                    'product_id' => $product_id,
                    'url'   => $router->generate($route, [
                        'product_id' => $product_id,
                        'title'      => $product_sku_stripped,
                    ], true),
                    'name'  => $product_sku,
                    'price' => 0,
                    'image' => Tools::productImageUrl($product->getProductsImagess()->getFirst()->getImage(), '0x0'),
                ];

                foreach ($images as $image) {
                    $items[] = [
                        'product_id' => $product_id,
                        'url'   => $router->generate($route, [
                            'product_id' => $product_id,
                            'title'      => $product_sku_stripped,
                            'focus'      => $image->getId()
                        ], true),
                        'name'  => $product_sku,
                        'price' => 0,
                        'image' => Tools::productImageUrl($image->getImage(), '0x0'),
                    ];
                }
            }
        }

        foreach (ProductsDomainsPricesPeer::getProductsPrices($product_ids) as $id => $prices) {
            if (isset($prices['sales'])) {
                $price = number_format($prices['sales']['price'], 2, '.', '');
            } else {
                $price = number_format($prices['normal']['price'], 2, '.', '');
            }

            foreach ($items as $i => $item) {
                if ($item['product_id'] == $id) {
                    $items[$i]['price'] = $price;
                }
            }
        }

        return $this->render('RetargetingBundle:Default:feed.xml.twig', ['items' => $items]);
    }


    /**
     * Finds the right cid or tid to use
     *
     * @param  string $type Either "cid" or "tid"
     * @return string
     */
    protected function getTrackingId($type = 'cid')
    {
        $locale = $this->getRequest()->getLocale();
        if (isset($this->data[$locale][$type])) {
            return $this->data[$locale][$type];
        }

        return $this->data[$this->default_locale][$type];
    }
}

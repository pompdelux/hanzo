<?php

namespace Hanzo\Bundle\GoogleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsImagesQuery;


class ProductFeedController extends Controller
{
    /**
     * @Route("/google/productfeed.xml")
     * @Template()
     */
    public function productFeedAction(Request $request)
    {
        // See definition here: https://support.google.com/merchants/answer/188494

        $hanzo = Hanzo::getInstance();
        $translator = $this->get('translator');
        $router = $this->get('router');

        $exclude = [
            'Hayward kneesocks',
            'Arlington socks',
            'Oregon socks',
            'POMP bag',
            'POMP big bag',
        ];

        $items = [];
        $product_ids = [];

        $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';

        $products = ProductsQuery::create()
            ->filterByMaster(null, \Criteria::ISNULL)
            ->filterBySku($exclude, \Criteria::NOT_IN)
            ->joinWithProductsImages()
            ->joinWithProductsI18n()
            ->useProductsI18nQuery()
                ->filterByLocale($request->getLocale())
            ->endUse()
            ->find();
        foreach ($products as $product) {
            $product_id = $product->getId();
            $product_sku = $product->getTitle();
            $product_sku_stripped = Tools::stripText($product_sku);

            $product_ids[] = $product_id;


            $products2category = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                    ->filterBySku($product->getSku())
                ->endUse()
                ->findOne();

            $key = '_' . strtolower($request->getLocale()) . '_' . $products2category->getCategoriesId();
            $product_route = $router_keys[$key];

            $images = ProductsImagesQuery::create()
                ->filterByProductsId($product_id)
                ->find();
            $images_array = [];
            foreach ($images as $image) {
                $images_array[] = Tools::productImageUrl($image->getImage(), '0x0');
            }
            $translation_key = 'description.' . Tools::stripText($product->getSku(), '_', false);

            $find = '~(background|src)="(../|/)~';
            $replace = '$1="' . $hanzo->get('core.cdn');

            $description = $translator->trans($translation_key, array('%cdn%' => $hanzo->get('core.cdn')), 'products');
            $description = preg_replace($find, $replace, $description);

            $is_in_stock = $this->get('stock')->check($product);
            $items[] = [
                'product_id' => $product_id,
                'url'   => $router->generate($product_route, [
                    'product_id' => $product_id,
                    'title'      => $product_sku_stripped,
                ], true),
                'name'  => $product_sku,
                'description'  => preg_replace('/\s+/', ' ', Tools::stripTags($description)),
                'price' => 0,
                'availability' => $translator->trans(($is_in_stock) ? 'google.feed.availability.in_stock' : 'google.feed.availability.sold_out'),
                'image' => Tools::productImageUrl($product->getProductsImagess()->getFirst()->getImage(), '0x0'),
                'additional_images' => $images_array,
            ];
        }
        foreach (ProductsDomainsPricesPeer::getProductsPrices($product_ids) as $id => $prices) {
            foreach ($items as $i => $item) {
                if ($item['product_id'] == $id) {
                    $items[$i]['price'] = $prices['normal']['formattet'];

                    if (isset($prices['sales']['price'])) {
                        $items[$i]['sale_price'] = $prices['sales']['formattet'];
                    }
                }
            }
        }
        $response = new Response($this->renderView('GoogleBundle:ProductFeed:feed.xml.twig', ['items' => $items]));
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}

<?php

namespace Hanzo\Bundle\QuickOrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsDomainsPricesQuery,
    Hanzo\Model\ProductsToCategoriesQuery,
    Hanzo\Model\OrdersPeer
;
use \PropelCollection;
use Hanzo\Core\CoreController;

class DefaultController extends CoreController
{

    public function indexAction()
    {
        // All logic is a copy of BasketBundle.Default.viewAction

        $order = OrdersPeer::getCurrent();

        $router = $this->get('router');
        $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
        $locale = strtolower(Hanzo::getInstance()->get('core.locale'));

        $products = array();
        $delivery_date = 0;

        // product lines- if any
        foreach ($order->getOrdersLiness() as $line) {
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            if ($line['type'] != 'product') {
                continue;
            }

            // find first products2category match
            $products2category = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                ->filterBySku($line['products_name'])
                ->endUse()
                ->findOne()
            ;

            if (!$products2category) {
                Tools::log($locale.' -> '.$line['products_name']);
            }

            $line['expected_at'] = new \DateTime($line['expected_at']);

            $t = $line['expected_at']->getTimestamp();
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
                if ($delivery_date < $line['expected_at']) {
                    $delivery_date = $line['expected_at'];
                }
            }
            else {
                $line['expected_at'] = NULL;
            }

            $line['basket_image'] =
                preg_replace('/[^a-z0-9]/i', '-', $line['products_name']) .
                '_' .
                preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $line['products_color'])) .
                '_overview_01.jpg'
            ;

            $line['url'] = '#';
            $master = ProductsQuery::create()->findOneBySku($line['products_name']);
            if ($master) {
                $line['url'] = $router->generate('product_info', array('product_id' => $master->getId()));
            }

            $products[] = $line;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        return $this->render('QuickOrderBundle:Default:index.html.twig',
            array(
                'embedded' => false,
                'page_type' => 'basket',
                'products' => $products,
                'total' => $order->getTotalPrice(true),
                'delivery_date' => $delivery_date
            )
        );
    }

    public function getSkuAction()
    {
        $request = $this->get('request');
        $max_rows = $request->query->get('max_rows', 12);
        $name = $request->query->get('name');
        $callback = $request->query->get('callback');

    	$products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->filterBySku($name.'%')
            ->filterByIsOutOfStock(FALSE)
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
            ->endUse()
            ->groupBySku()
            ->orderBySku()
            ->limit($max_rows)
            ->find()
        ;

        $result = array();
        if ($products->count()) {
            foreach ($products as $product) {
                $result[] = $product->getSku();
            }
            if(count($result)){
                return $this->json_response(array(
                    'status' => true,
                    'message' => $this->get('translator')->trans('quickorder.products.found', array(), 'quickorder'),
                    'data' => $result
                ));
            }
        }

        return $this->json_response(array(
            'status' => false,
            'message' => $this->get('translator')->trans('quickorder.no.products.found', array(), 'quickorder'),
            'data' => []
        ));
    }
}

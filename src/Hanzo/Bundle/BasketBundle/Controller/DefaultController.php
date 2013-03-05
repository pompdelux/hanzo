<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\Stock;
use Hanzo\Core\CoreController;

use Hanzo\Model\Products;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesPeer;
use Hanzo\Model\CategoriesQuery;

class DefaultController extends CoreController
{
    public function addAction()
    {
        $this->get('twig')->addGlobal('page_type', 'basket');
        $translator = $this->get('translator');

        // product_id,master,size,color,quantity
        $request = $this->get('request');
        $quantity = $request->get('quantity', 1);
        $product = ProductsPeer::findFromRequest($request);

        // could not find matching product, throw 404 ?
        if (!$product instanceof Products) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => ''
                ));
            }

            $this->redirect($request->headers->get('referer'));
        }

        $stock = $this->get('stock')->check($product, $quantity);

        if ($stock) {
            $date = $this->get('stock')->decrease($product, $quantity);
            $order = OrdersPeer::getCurrent();

            // hf@bellcom.dk, 23-jun-2012: check order state -->>
            if ( $order->getState() >= Orders::STATE_PRE_PAYMENT )
            {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('order.state_pre_payment.locked', array(), 'checkout')
                ));
            }
            // <<-- hf@bellcom.dk, 23-jun-2012: check order state

            if ($order->isNew()) {
                $order->setLanguagesId(Hanzo::getInstance()->get('core.language_id'));
            }

            $order->setOrderLineQty($product, $quantity, false, $date);

            if ($order->validate()) {
                $order->setUpdatedAt(time());
                $order->save();

                $price = ProductsDomainsPricesPeer::getProductsPrices(array($product->getId()));

                $price = array_shift($price);
                $original_price = $price['normal'];
                $price = array_shift($price);

                $latest = array(
                    'id' => $product->getId(),
                    'single_price' => Tools::moneyFormat($price['price']),
                    'price' => Tools::moneyFormat($price['price'] * $quantity),
                    'expected_at' => ''
                );

                $t = new \DateTime($date);
                $t = $t->getTimestamp();
                if (($t > 0) && ($t > time())) {
                    $latest['expected_at'] = $date;
                }

                Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => TRUE,
                        'message' => $translator->trans('product.added.to.cart', array('%product%' => $product)),
                        'data' => $this->miniBasketAction(TRUE),
                        'latest' => $latest,
                        'total' => $order->getTotalQuantity(true),
                    ));
                }
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('product.out.of.stock', array('%product%' => $product)),
            ));
        }


        return $this->forward('BasketBundle:Default:view');
    }


    public function miniBasketAction($return = FALSE)
    {
        $order = OrdersPeer::getCurrent();
        $total = '('.$order->getTotalQuantity(true).') ' . Tools::moneyFormat( $order->getTotalPrice(true) );

        if ($return) {
            return $total;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $warning = '';
        if ($this->getFormat() == 'json') {
            if (OrdersPeer::inEdit()) {
                $warning = Tools::getInEditWarning();
            }

            return $this->json_response(array(
                'status' => TRUE,
                'message' => '',
                'data' => array(
                    'total' => $total,
                    'warning' => $warning,
                ),
            ));
        }

        return $this->response($total);
    }


    public function setAction($product_id, $quantity)
    {
        return $this->response('set basket entry');
    }

    public function removeAction($product_id, $quantity)
    {
        $order = OrdersPeer::getCurrent();
        $order_lines = $order->getOrdersLiness();
        $product_found = FALSE;

        foreach ($order_lines as $k => $line) {
            if ($line->getProductsId() == $product_id) {
                $product_found = TRUE;

                if ($quantity == 'all') {
                    unset($order_lines[$k]);
                }
                else {
                    $line->setQuantity($line->getQuantity() - $quantity);
                    $order_lines[$k] = $line;
                }

                break;
            }
        }

        if ($product_found) {
            $order->setOrdersLiness($order_lines);
            $order->setUpdatedAt(time());
            $order->save();

            $data = array(
                'total' => Tools::moneyFormat($order->getTotalPrice(true)),
                'quantity' => $order->getTotalQuantity(true)
            );

            Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => '',
                    'data' => $data,
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('no.such.product.in.cart'),
            ));
        }

        return $this->response('remove from basket');
    }


    public function replaceItemAction()
    {
        // 0. sanetize request
        // 1. figure out whether or not the product is in the basket
        // 2. find out whether or not the new product is in stock
        // 2.1 if not kick request with a reply about it
        // 2.2. if there is delivery time on the product, get confirmation
        // 2.3. or just add the goddam product.
        // 3. remove the old product
        // 4. add the new

        $request = $this->get('request');
        $product_to_replace = $request->get('product_to_replace');

        $request_data = array(
            'quantity' => $request->get('quantity'),
            'master' => $request->get('master'),
            'size' => $request->get('size'),
            'color' => $request->get('color'),
        );

        $response = $this->forward('WebServicesBundle:RestStock:check', $request_data);
        $response = json_decode($response->getContent(), TRUE);

        if ($response['status'] && isset($response['data']['products']) && count($response['data']['products']) == 1) {
            $product = $response['data']['products'][0];

            // if the product is backordered, require a confirmation to continue
            if ($product['date'] && (FALSE === $request->get('confirmed', FALSE))) {
                return $this->json_response($response);
            }

            // ok, we proceed
            // first, nuke original product
            $this->forward('BasketBundle:Default:remove', array('product_id' => $product_to_replace, 'quantity' => 'all'));

            // then add new product to cart
            $response = $this->forward('BasketBundle:Default:add', $request_data);
            $response = json_decode($response->getContent(), TRUE);

            // we need the product!
            if ($response['status']) {
                $response['data'] = array();

                $product = ProductsPeer::findFromRequest($request);
                $prices = ProductsDomainsPricesPeer::getProductsPrices(array($product->getId()));
                $prices = array_shift($prices);

                foreach ($prices as $key => $price) {
                    $response['data'][$key.'_total'] = Tools::moneyFormat($price['price'] * $request->get('quantity'));
                    $response['data'][$key] = Tools::moneyFormat($price['price']);
                }
                $response['data']['basket'] = $this->miniBasketAction(TRUE);
                $response['data']['product_id'] = $product->getId();
            }
        }

        return $this->json_response($response);
    }


    public function updateAction()
    {
        return $this->response('update basket');
    }


    public function viewAction($embed = false, $orders_id = null)
    {
        if ($orders_id) {
            $order = OrdersQuery::create()->findOneById($orders_id);
        } else {
            $order = OrdersPeer::getCurrent();
        }

        $router = $this->get('router');
        $router_keys = include $this->container->parameters['kernel.cache_dir'] . '/category_map.php';
        $locale = strtolower(Hanzo::getInstance()->get('core.locale'));

        $mode = $this->get('kernel')->getStoreMode();

        $cid = array('category2group');
        $category2group = $this->getCache($cid);
        if (empty($category2group)) {
            $result = CategoriesQuery::create()
                ->filterByContext(null, \Criteria::ISNOTNULL)
                ->orderByContext()
                ->find()
            ;
            foreach ($result as $category) {
                list($group, ) = explode('_', $category->getContext());
                $category2group[$category->getId()] = 'product.group.'.strtolower($group);
            }
            $this->setCache($cid, $category2group);
        }


        $products = array();
        $delivery_date = 0;

        // product lines- if any
        $c = new \Criteria();
        $c->addAscendingOrderByColumn(OrdersLinesPeer::PRODUCTS_NAME);
        foreach ($order->getOrdersLiness($c) as $line) {
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            if ($line['type'] != 'product') {
                continue;
            }

            $t = strtotime($line['expected_at']);
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
                if ($delivery_date < $line['expected_at']) {
                    $delivery_date = $line['expected_at'];
                }
            }
            else {
                $line['expected_at'] = NULL;
            }

            // find first products2category match
            $products2category = ProductsToCategoriesQuery::create()
                ->useProductsQuery()
                ->filterBySku($line['products_name'])
                ->endUse()
                ->findOne()
            ;

            if ($products2category) {
                $line['basket_image'] =
                    preg_replace('/[^a-z0-9]/i', '-', $line['products_name']) .
                    '_' .
                    preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $line['products_color'])) .
                    '_overview_01.jpg'
                ;

                // find matching router
                $key = '_' . $locale . '_' . $products2category->getCategoriesId();
                $group = $category2group[$products2category->getCategoriesId()];

                $master = ProductsQuery::create()->findOneBySku($line['products_name']);

                if ('consultant' == $mode) {
                    $line['url'] = $router->generate('product_info', array('product_id' => $master->getId()));
                } else {
                    if (isset($router_keys[$key])) {
                        $line['url'] = $router->generate($router_keys[$key], array(
                            'product_id' => $master->getId(),
                            'title' => Tools::stripText($line['products_name']),
                        ));
                    }
                }
            } else {
                $line['url'] = '';
                $line['basket_image'] = '';
                $group = 0;
            }

            $products[$group][] = $line;
        }

        ksort($products);

        $template = 'BasketBundle:Default:view.html.twig';
        if ($embed) {
            $template = 'BasketBundle:Default:block.html.twig';
        }

        // hf@bellcom.dk, 21-aug-2012: link continue shopping to quickorder on consultant site -->>
        $continue_shopping = 'javascript:history.go(-1)';

        $hanzo = Hanzo::getInstance();
        $domain_key = $hanzo->get('core.domain_key');
        if (strpos($domain_key, 'Sales') !== false) {
            $continue_shopping = $router->generate('QuickOrderBundle_homepage');
        }
        // <<-- hf@bellcom.dk, 21-aug-2012: link continue shopping to quickorder on consultant site

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        return $this->render($template, array(
            'embedded' => $embed,
            'page_type' => 'basket',
            'products' => $products,
            'total' => $order->getTotalPrice(true),
            'delivery_date' => $delivery_date,
            'continue_shopping' => $continue_shopping,
        ));
    }
}

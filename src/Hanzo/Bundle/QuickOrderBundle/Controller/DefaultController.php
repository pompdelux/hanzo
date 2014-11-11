<?php

namespace Hanzo\Bundle\QuickOrderBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;

use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\OrdersPeer;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\QuickOrderBundle
 */
class DefaultController extends CoreController
{

    /**
     * All logic is a copy of BasketBundle.Default.viewAction
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $order        = OrdersPeer::getCurrent();
        $translator   = $this->container->get('translator');
        $router       = $this->get('router');
        $products     = [];
        $deliveryDate = 0;

        // product lines- if any
        foreach ($order->getOrdersLiness() as $line) {
            $line->setProductsSize($line->getPostfixedSize($translator));
            $line = $line->toArray(\BasePeer::TYPE_FIELDNAME);

            if ($line['type'] != 'product') {
                continue;
            }

            $line['expected_at'] = new \DateTime($line['expected_at']);

            $t = $line['expected_at']->getTimestamp();
            if (($t > 0) && ($t > time())) {
                $line['expected_at'] = $t;
                if ($deliveryDate < $line['expected_at']) {
                    $deliveryDate = $line['expected_at'];
                }
            } else {
                $line['expected_at'] = null;
            }

            // we need the id and sku from the master record to generate image and url to product.
            $sql = "
                SELECT p.id, p.sku FROM products AS p
                WHERE p.sku = (
                    SELECT pp.master FROM products AS pp
                    WHERE pp.id = ".$line['products_id']."
                )
            ";
            $master = \Propel::getConnection()
                ->query($sql)
                ->fetch(\PDO::FETCH_OBJ)
            ;

            $line['basket_image'] =
                preg_replace('/[^a-z0-9]/i', '-', $master->sku) .
                '_' .
                preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $line['products_color'])) .
                '_overview_01.jpg'
            ;

            $line['url'] = '#';
            if ($master) {
                $line['url']    = $router->generate('product_info', ['product_id' => $master->id]);
                $line['master'] = $master->sku;
            }

            $products[] = $line;
        }

        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);

        $outOfStock = [];
        if ($request->getSession()->has('missing_wishlist_products')) {
            $outOfStock = $request->getSession()->get('missing_wishlist_products');
            $request->getSession()->remove('missing_wishlist_products');
        }

        return $this->render('QuickOrderBundle:Default:index.html.twig', [
            'embedded'        => false,
            'page_type'       => 'basket',
            'products'        => $products,
            'total'           => $order->getTotalPrice(true),
            'delivery_date'   => $deliveryDate,
            'wishlist_misses' => $outOfStock,
        ]);
    }

    /**
     * Fetch sku based on product title.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getSkuAction(Request $request)
    {
        $max_rows = $request->query->get('max_rows', 12);
        $name     = $request->query->get('name');

        $products = ProductsQuery::create()
            ->where('products.MASTER IS NULL')
            ->filterByIsOutOfStock(false)
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId(Hanzo::getInstance()->get('core.domain_id'))
            ->endUse()
            ->useProductsI18nQuery()
                ->filterByTitle($name.'%')
                ->filterByLocale($request->getLocale())
            ->endUse()
            ->groupBySku()
            ->orderBySku()
            ->limit($max_rows)
            ->find()
        ;

        $result = [];
        if ($products->count()) {
            foreach ($products as $product) {
                $product->setLocale($request->getLocale());

                $result[] = [
                    'name'   => $product->getSku(),
                    'tokens' => [$product->getTitle()],
                    'value'  => $product->getTitle(),
                ];
            }

            if (count($result)) {
                return $this->json_response($result);
            }
        }

        return $this->json_response([$this->get('translator')->trans('quickorder.no.products.found', [], 'quickorder')]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loadWishListAction(Request $request)
    {
        $query = "
            SELECT
                w.customers_id,
                wl.*,
                p.size,
                p.color,
                p.master,
                p.sku, (
                SELECT
                    products_i18n.title
                FROM
                    products_i18n
                JOIN
                    products ON (
                        products_i18n.id = products.id
                    )
                WHERE
                    products_i18n.locale = :locale
                    AND
                      products.sku = p.master
            ) as title
            FROM
                wishlists_lines as wl
            JOIN
                products as p ON (
                    p.id = wl.products_id
                )
            JOIN
                wishlists as w ON (
                  w.id = wl.wishlists_id
                )
            WHERE
                wl.wishlists_id = :wishlists_id
        ";

        $con  = \Propel::getConnection();
        $stmt = $con->prepare($query);
        $stmt->execute([
            ':locale'       => $request->getLocale(),
            ':wishlists_id' => $request->request->get('q'),
        ]);

        $lines = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (0 === count($lines)) {
            $request->getSession()->set('notice', 'wishlist.not.found');

            return $this->redirect($this->generateUrl('QuickOrderBundle_homepage'));
        }

        $outOfStock = [];
        $wishlistId = '';

        /** @var \Hanzo\Model\WishlistsLines $line */
        foreach ($lines as $line) {
            $product = ProductsQuery::create()
                ->findOneById($line['products_id']);

            $product->setLocale($request->getLocale());
            $product->setTitle($line['title']);

            if (!$this->addToOrder($product, $line['quantity'])) {
                $outOfStock[] = $line;
            }

            $wishlistId = $line['wishlists_id'];
        }

        if (count($outOfStock)) {
            $request->getSession()->set('missing_wishlist_products', $outOfStock);
        }

        if ($wishlistId) {
            $order = OrdersPeer::getCurrent();
            $order->setAttribute('id', 'wishlist', $wishlistId);
            $order->save();
        }

        return $this->redirect($this->generateUrl('QuickOrderBundle_homepage'));
    }

    /**
     * @param Products $products
     * @param int      $quantity
     *
     * @return bool
     */
    private function addToOrder(Products $products, $quantity = 1)
    {
        static $basket;

        if (empty($basket)) {
            $basket = $this->container->get('hanzo.basket');
            $basket->setOrder(OrdersPeer::getCurrent());
            $basket->flush();
        }

        try {
            $basket->addProduct($products, $quantity);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}

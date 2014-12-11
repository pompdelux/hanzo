<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\Wishlists;
use Hanzo\Model\WishlistsLines;
use Hanzo\Model\WishlistsLinesQuery;
use Hanzo\Model\WishlistsQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class WishlistController
 *
 * @package Hanzo\Bundle\AccountBundle
 */
class WishlistController extends CoreController
{
    /**
     * Create a new wish list.
     *
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     * @throws \PropelException
     *
     * @Template
     */
    public function addAction(Request $request)
    {
        $query = "
            SELECT
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
                    products_i18n.locale = '".$request->getLocale()."'
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
                    wl.wishlists_id = w.id
                )
            WHERE
                w.customers_id = ".(int) CustomersPeer::getCurrent()->getId()."
        ";

        $con      = \Propel::getConnection();
        $result   = $con->query($query);
        $locale   = $request->getLocale();
        $products = [];

        /** @var \Hanzo\Model\WishlistsLines $item */
        foreach ($result as $item) {
            $item = $this->getItemViewData($item, $locale);
            $products[$item['sku']] = $item;
        }

        ksort($products);

        return [
            'wishlist_id' => $this->getWishlist()->getId(),
            'page_type'   => 'wishlist',
            'products'    => $products
        ];
    }

    /**
     * Add a product line to the wish list
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function addItemAction(Request $request)
    {
        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json_response([
                'status'  => false,
                'message' => 'requires.login'
            ]);
        }

        $list = $this->getWishlist();
        $type = 'add';

        $productId = $request->request->get('product_id');
        if (empty($productId)) {
            $product = ProductsPeer::findFromRequest($request);
            $productId = $product->getId();
        }

        $item = WishlistsLinesQuery::create()
            ->filterByProductsId($productId)
            ->filterByWishlists($list)
            ->findOne();

        if ($item instanceof WishlistsLines) {
            $type = 'update';
            $item->setQuantity($request->request->get('quantity', 1));
        } else {
            $item = new WishlistsLines();
            $item->setQuantity($request->request->get('quantity', 1));
            $item->setWishlists($list);
            $item->setProductsId($productId);
        }

        $item->save();

        return $this->json_response([
            'data'    => $this->getItemViewData($item, $request->getLocale()),
            'message' => '',
            'status'  => true,
            'type'    => $type,
        ]);
    }

    /**
     * Update a line by removing the old and inserting a new line
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateItemAction(Request $request)
    {
        $post = $request->request->all();

        if (empty($post['old_products_id']) || empty($post['new_products_id'])) {
            return $this->json_response([
                'status'  => false,
                'message' => 'missing.update_line.parameters'
            ]);
        }

        $request->request->set('products_id', $post['old_products_id']);

        $this->deleteItemAction($request);

        return $this->addItemAction($request);
    }

    /**
     * Remove a line from a wishlist
     *
     * @param Request $request
     * @param int     $product_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function deleteItemAction(Request $request, $product_id = null)
    {
        WishlistsLinesQuery::create()
            ->filterByWishlistsId($this->getWishlist()->getId())
            ->filterByProductsId($product_id)
            ->delete();

        return $this->json_response([
            'status'  => true,
            'message' => 'wishlist.line.removed'
        ]);
    }

    /**
     * Delete a whole wishlist and all it's lines
     *
     * @param string $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function deleteAction($id)
    {
        WishlistsQuery::create()
            ->filterById($id)
            ->delete();

        if ('json' === $this->getFormat()) {
            return $this->json_response([
                'status'  => true,
                'message' => 'wishlist.deleted'
            ]);
        }

        $this->container->get('session')->getFlashBag()->add('notice', 'wishlist.deleted');

        return $this->redirect($this->generateUrl('_account'));
    }

    /**
     * Delete all items in a users wishlist
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function removeAllItemsAction()
    {
        WishlistsLinesQuery::create()
            ->filterByWishlists($this->getWishlist())
            ->delete();

        if ('json' === $this->getFormat()) {
            return $this->json_response([
                'status'  => true,
                'message' => 'wishlist.all.items.deleted'
            ]);
        }

        $this->container->get('session')->getFlashBag()->add('notice', 'wishlist.all.items.deleted');

        return $this->redirect($this->generateUrl('_account'));
    }


    /**
     * Generate unique random key string.
     * Unique in the way that it's not already in use.
     *
     * @param int $length
     *
     * @return string
     */
    private function random($length = 5)
    {
        while (true) {
            $string = implode('', array_rand(array_flip(str_split('ABCDEFGHJKLMNPQRSTUVWXYZ98765432')), $length));

            $found = WishlistsQuery::create()
                ->filterById($string)
                ->count();

            if (0 === $found) {
                return $string;
            }
        }
    }

    /**
     * Retrive customers list
     *
     * @param int $customerId
     *
     * @return Wishlists
     * @throws \Exception
     * @throws \PropelException
     */
    private function getWishlist($customerId = null)
    {
        if (is_null($customerId)) {
            $customerId = CustomersPeer::getCurrent()->getId();
        }

        $list = WishlistsQuery::create()
            ->filterByCustomersId($customerId)
            ->findOne();

        if (!$list instanceof Wishlists) {
            $list = new Wishlists();
            $list->setCustomersId($customerId);
            $list->setId($this->random(5));
            $list->save();

            $this->container->get('hanzo.statsd')->increment('shoppinglists.'.$this->container->get('kernel')->getAttribute('domain_key'));
        }

        return $list;
    }

    /**
     * Get view data for a wishlist item
     *
     * @param WishlistsLines|array $item
     * @param string               $locale
     *
     * @return array
     */
    private function getItemViewData($item, $locale)
    {
        $sizeLabel = $this->container->get('translator')->trans('size.label.postfix');
        if ('size.label.postfix' === $sizeLabel) {
            $sizeLabel = '';
        }

        if (is_array($item)) {
            $image = preg_replace('/[^a-z0-9]/i', '-', explode(' ', $item['sku'])[0]) .
                '_' .
                preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $item['color'])) .
                '_overview_01.jpg';

            return [
                'color'    => $item['color'],
                'id'       => $item['products_id'],
                'image'    => Tools::productImageUrl($image, '57x100'),
                'master'   => $item['master'],
                'quantity' => $item['quantity'],
                'size'     => $item['size'].$sizeLabel,
                'sku'      => $item['sku'],
                'title'    => $item['title'],
            ];
        }

        $product = $item->getProducts();
        $image   = preg_replace('/[^a-z0-9]/i', '-', explode(' ', $product->getSku())[0]) .
            '_' .
            preg_replace('/[^a-z0-9]/i', '-', str_replace('/', '9', $product->getColor())) .
            '_overview_01.jpg';

        $title = ProductsI18nQuery::create()
            ->select('Title')
            ->filterByLocale($locale)
            ->useProductsQuery()
                ->filterBySku($product->getMaster())
            ->endUse()
            ->findOne();

        return [
            'color'    => $product->getColor(),
            'id'       => $item->getProductsId(),
            'image'    => Tools::productImageUrl($image, '57x100'),
            'master'   => $product->getMaster(),
            'quantity' => $item->getQuantity(),
            'size'     => $product->getPostfixedSize($this->container->get('translator')),
            'sku'      => $product->getSku(),
            'title'    => $title,
        ];
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendWishlistAction(Request $request)
    {
        $email = $request->request->get('to_address');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json_response([
                'status'  => false,
                'message' => $this->container->get('translator')->trans('wishlist.invalid_to_address', [], 'account')
            ]);
        }

        $this->container->get('hanzo.send_wishlist_handler')->send($email, $this->getWishlist()->getId());

        return $this->json_response([
            'status'  => true,
            'message' => $this->container->get('translator')->trans('wishlist.send', [], 'account')
        ]);
    }

    /**
     * @param Request     $request
     * @param string|null $listId
     *
     * @throws \Exception
     * @throws \PropelException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function wishListToBasketAction(Request $request, $listId = null)
    {
        $targetRoute = 'basket_view';
        $wishlistId  = $listId;

        // this dirty hack handles list loads via the consultants/quickorder page
        if ('POST' === $request->getMethod()) {
            $targetRoute = 'QuickOrderBundle_homepage';
            $wishlistId  = $request->request->get('q');
        }

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
                ':wishlists_id' => $wishlistId,
            ]);

        $lines = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (0 === count($lines)) {
            $request->getSession()->set('notice', 'wishlist.not.found');

            return $this->redirect($this->generateUrl($targetRoute));
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

        return $this->redirect($this->generateUrl($targetRoute));
    }

    /**
     * @param Session $session
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMissingProductsAction(Session $session)
    {
        $misses = [];
        if ($session->has('missing_wishlist_products')) {
            $misses = $session->get('missing_wishlist_products');
            $session->remove('missing_wishlist_products');
        }

        return $this->render('AccountBundle:Wishlist:missing.html.twig', ['misses' => $misses]);
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

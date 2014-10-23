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
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\Wishlists;
use Hanzo\Model\WishlistsLines;
use Hanzo\Model\WishlistsLinesQuery;
use Hanzo\Model\WishlistsQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

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
        $list = $this->getWishlist();
        $type = 'add';

        $productId = $request->request->get('product_id');

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
     * @return Wishlists
     * @throws \Exception
     * @throws \PropelException
     */
    private function getWishlist()
    {
        $customerId = CustomersPeer::getCurrent()->getId();

        $list = WishlistsQuery::create()
            ->filterByCustomersId($customerId)
            ->findOne();

        if (!$list instanceof Wishlists) {
            $list = new Wishlists();
            $list->setCustomersId($customerId);
            $list->setId($this->random(5));
            $list->save();
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
}

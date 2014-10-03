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
use Hanzo\Model\CustomersPeer;
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
     * @return array
     * @throws \Exception
     * @throws \PropelException
     *
     * @Template
     */
    public function addAction()
    {
        return ['page_type' => 'wishlist'];
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
        $list = WishlistsQuery::create()
            ->filterByCustomersId(CustomersPeer::getCurrent()->getId())
            ->findOne();

        if (!$list instanceof Wishlists) {
            $list = $this->addAction()['list'];
        }


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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function deleteItemAction(Request $request)
    {
        $post = $request->request->all();

        WishlistsLinesQuery::create()
            ->filterByWishlistsId($post['list_id'])
            ->filterByProductsId($post['products_id'])
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
            $string = implode('', array_rand(str_split('ABCDEFGHJKLMNPQRSTUVWXYZ98765432'), $length));

            $found = WishlistsQuery::create()
                ->filterById($string)
                ->count();

            if (0 === $found) {
                return $string;
            }
        }
    }
}

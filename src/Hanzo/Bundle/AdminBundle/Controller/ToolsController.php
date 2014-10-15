<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Model\OrdersQuery;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ToolsController
 *
 * @package Hanzo\Bundle\AdminBundle
 */
class ToolsController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('AdminBundle:Tools:index.html.twig', [
            'database' => $request->getSession()->get('database')
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function syncCategoriesAction(Request $request)
    {
        $this->get('replication_manager')->syncCategories();

        $request->getSession()->getFlashBag()->add('notice', 'Kategori synkronisering færdig..');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function syncImagesAction(Request $request)
    {
        $this->get('replication_manager')->syncProductsImages();

        $request->getSession()->getFlashBag()->add('notice', 'Billede synkronisering færdig..');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function syncImagesStyleguideAction(Request $request)
    {
        $this->get('replication_manager')->syncStyleGuide();

        $request->getSession()->getFlashBag()->add('notice', 'Styleguide synkronisering færdig..');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function syncImagesSortingAction(Request $request)
    {
        $this->get('replication_manager')->syncImageSorting();

        $request->getSession()->getFlashBag()->add('notice', 'Billedesorterings synkronisering færdig..');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \VarnishException
     */
    public function clearVarnishCacheAction(Request $request)
    {
        try {
            $this->get('varnish.controle')->banUrl('^/*');
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }

        $request->getSession()->getFlashBag()->add('notice', 'Varnish cache tømt.');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * Build product search index
     *
     * @param Request $request
     *
     * @return array
     *
     * @Template("AdminBundle:Tools:productSearchIndexer.html.twig")
     */
    public function buildProductSearchIndexAction(Request $request)
    {
        if ($request->query->get('run')) {
            $builder = $this->get('hanzo_search.product.index_builder');
            // $builder->setConnection($this->getDbConnection());
            $builder->build();

            $request->getSession()->getFlashBag()->add('notice', 'Søgeindekset er nu opdateret.');

            return $this->redirect($this->generateUrl($request->get('_route')));
        }

        return ['database' => $request->getSession()->get('database')];
    }

    /**
     * [dibsToolsAction description]
     *
     * @param Request $request
     * @param string  $action
     *
     * @Template("AdminBundle:Tools:dibsTools.html.twig")
     * @return array
     */
    public function dibsToolsAction(Request $request, $action = '')
    {
        $return = [
            'action'   => $action,
            'database' => $request->getSession()->get('database'),
            'message'  => '',
            'data'     => [
                'fixed'   => '',
                'missing' => '',
            ],
        ];

        switch ($action) {
            case 'transinfo':

                if ('POST' === $request->getMethod()) {
                    $ids = explode('-', $request->request->get('ids'));

                    if ('' == $ids[0]) {
                        unset($ids[0]);
                    }

                    switch (count($ids)) {
                        case 0: // alle

                            $con = \Propel::getConnection();
                            $query = "SELECT id, payment_gateway_id, customers_id FROM orders WHERE state > 30 AND billing_method = 'dibs' AND finished_at > '2012-08-20 00:00:01'";
                            $result = $con->query($query);

                            $ids = [];
                            foreach ($result as $record) {
                                $ids[$record['id']] = $record;
                            }

                            if (empty($ids)) {
                                $return['message'] = 'Ingen ordre er i stykker pt.';
                                break;
                            }

                            $query = "SELECT orders_id, c_value FROM orders_attributes WHERE orders_id IN (".implode(',', array_keys($ids)).") AND ns = 'payment' AND c_key = 'transact'";

                            $result = $con->query($query);
                            foreach ($result as $record) {
                                if (isset($ids[$record['orders_id']])) {
                                    unset($ids[$record['orders_id']]);
                                }
                            }

                            if (empty($ids)) {
                                $return['message'] = 'Ingen ordre er i stykker pt.';
                                break;
                            }

                            $return = $this->fixTransactionId($return, $ids);
                            break;
                        case 1:
                            $return = $this->fixTransactionId($return, [$ids[0] => $ids[0]]);
                            break;

                        case 2:
                            $con = \Propel::getConnection();
                            $query = "SELECT id FROM orders WHERE state > 30 AND billing_method = 'dibs' AND id >= ".trim($ids[0]).' AND id <= '.trim($ids[1]);
                            $result = $con->query($query);

                            $ids = [];
                            foreach ($result as $record) {
                                $ids[$record['id']] = $record;
                            }

                            $return = $this->fixTransactionId($return, $ids);
                            break;
                    }
                }

                break;

            default:
                break;
        }

        return $return;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateSearchIndexAction(Request $request)
    {
        $this->container->get('hanzo_search.product_and_category_indexer')->build();

        $request->getSession()->getFlashBag()->add('notice', 'Søgeindexer opdateret for produkter og kategorier.');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * @param array $return
     * @param array $ids
     *
     * @return mixed
     * @throws \Exception
     * @throws \PropelException
     */
    protected function fixTransactionId($return, $ids)
    {
        $api = $this->get('payment.dibsapi');

        foreach ($ids as $id => $item) {
            $order = OrdersQuery::create()->findOneById($id);

            if (!$order) {
                $return['message'] = 'Ordren #'.$id.' findes altså ikke...';
                continue;
            }

            $result = $api->call()->transinfo($order);

            if (isset($result->data['transact'])) {
                $order->setAttribute('transact', 'payment', $result->data['transact']);
                $order->save();
                $return['data']['fixed_message'] = 'Fiksede ordre:';
                $return['data']['fixed'][] = $id.' -> '.$result->data['transact'];
            } else {
                $missing[$id] = $result->data;
            }
        }

        if (isset($missing) && count($missing)) {
            $return['data']['missing_message'] = 'Gah, der var nogen ordre der ikke kunne fikses:';
            $return['data']['missing'] = $missing;
        }

        return $return;
    }
}

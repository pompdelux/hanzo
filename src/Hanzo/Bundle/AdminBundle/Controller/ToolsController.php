<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\WishlistsQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateSearchIndexAction(Request $request)
    {
        $this->container->get('hanzo_search.product_and_category_indexer')->build();

        $request->getSession()->getFlashBag()->add('notice', 'Søgeindexer opdateret for produkter og kategorier.');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function wishlistsFlushAllAction(Request $request)
    {
        WishlistsQuery::create()->deleteAll($this->getDbConnection());

        $request->getSession()->getFlashBag()->add('notice', 'Alle shoppinglister er nu tømt.');

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

    /**
     * Delete a whole product collection. Requires confirmation.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function purgeProductRangeAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            if ('' == $request->request->get('range')) {
                $this->container->get('session')->getFlashBag()->set('notice', 'Liiige vælge en kollektion tak.');

                return $this->redirect($this->generateUrl('admin_tools_purge_product_range'));
            }

            if (false === $request->request->get('confirm', false)) {
                return $this->render('AdminBundle:Tools:confirm.html.twig', [
                    'action'   => 'admin_tools_purge_product_range',
                    'data'     => ['range' => $request->request->get('range')],
                    'database' => $request->getSession()->get('database'),
                    'message'  => 'Er du sikker på du vil slette kollektionen "'.$request->request->get('range').'" ?<br><br> - alle produkt, billeder, sorteringer og andre produktknytninger bliver slettet og kan <em>ikke</em> genskabes!',
                ]);
            }

            $range = $request->request->get('range');

            ProductsQuery::create()
                ->filterByRange($range)
                ->delete($this->getDbConnection());

            $this->container->get('session')->getFlashBag()->set('notice', 'Kollektionen "'.$range.'" er nu slettet, dvs - alle produkter, billeder, sorteringer og andre databaseknytninger er væk og kan ikke genskabes!');

            return $this->redirect($this->generateUrl('admin_tools_purge_product_range'));
        }

        $ranges = [];
        $result = ProductsQuery::create()
            ->select('Range')
            ->distinct()
            ->find($this->getDbConnection());

        foreach ($result as $range) {
            $ranges[$range] = $range;
        }

        return $this->render('AdminBundle:Tools:purgeProductRange.html.twig', [
            'database' => $request->getSession()->get('database'),
            'ranges'   => $ranges,
        ]);
    }

    /**
     * Generate product mapping used in cross-database product imports.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \PropelException
     */
    public function generateProductMappingAction()
    {
        $file = $this->container->getParameter('kernel.root_dir').'/config/products_id_map.php';

        $products = ProductsQuery::create()
            ->select(['Id', 'Sku'])
            ->orderBySku()
            ->find();

        $data = [];
        foreach ($products as $product) {
            $data[strtolower($product['Sku'])] = $product['Id'];
        }

        $data = "<?php /* generated: " . date('Y-m-d H:i:s') . " */\n\$products_id_map = " . var_export($data, 1) .";\n";
        file_put_contents($file, $data);

        $this->container->get('session')->getFlashBag()->set('notice', 'Produkt mapping filen er nu blevet opdateret.');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * eventCloseAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function eventsCloseAction(Request $request)
    {
        if ($request->query->get('run')) {
            $timeRange = '';

            if ($request->query->get('start'))
            {
                $start = date('Y-m-d', strtotime($request->query->get('start')));
                $end   = date('Y-m-d', strtotime($request->query->get('end')));
                $timeRange = sprintf(" where event_date >= '%s 00:00:00' AND event_date <= '%s 23:59:59'", $start, $end);
            }

            $con    = \Propel::getConnection();
            $query  = "UPDATE events SET is_open = 0".$timeRange;
            $con->query($query);
            error_log(__LINE__.':'.__FILE__.' '.$query); // hf@bellcom.dk debugging

            $data = ['msg' => 'ok'];
            return $this->json_response($data);
        }

        return $this->render('AdminBundle:Tools:eventsClose.html.twig',[
            'start' => date('d-m-Y'),
            'end'   => date('d-m-Y', strtotime("+1 Year")),
        ]);
    }
}

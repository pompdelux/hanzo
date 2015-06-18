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
            'database' => $request->getSession()->get('database'),
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
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchIndexAction(Request $request)
    {
        return $this->render('AdminBundle:Tools:productSearchIndexer.html.twig', [
            'database' => $request->getSession()->get('database'),
        ]);
    }

    /**
     * Performs action on search product tag index
     * - Adds job to Beanstalk
     *
     * @param Request $request
     * @return array
     */
    public function searchIndexPerformAction(Request $request)
    {
        $queueId = $this->queueBeanstalkIndexJob($request);

        $request->getSession()->getFlashBag()->add('notice', 'Job til opdatering af indeks er nu lagt i kø med id "'.$queueId.'".');

        return $this->redirect($this->generateUrl('admin_tools_search_index_overview'));
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

        $data = "<?php /* generated: ".date('Y-m-d H:i:s')." */\n\$products_id_map = ".var_export($data, 1).";\n";
        file_put_contents($file, $data);

        $this->container->get('session')->getFlashBag()->set('notice', 'Produkt mapping filen er nu blevet opdateret.');

        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * Queues a job in beanstalk for the search-index
     *
     * @param Request $request
     *
     * @return int Job id
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function queueBeanstalkIndexJob(Request $request)
    {
        $pheanstalkQueue = $this->get('leezy.pheanstalk');

        $options = [
            'action' => $request->query->get('action'),
            'indexes' => [],
        ];

        if ($request->query->has('index')) {
            $index = $request->query->get('index');

            $options['indexes'][] = $index;
        }

        $data = json_encode($options);

        $priority = \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY;
        $delay    = \Pheanstalk_PheanstalkInterface::DEFAULT_DELAY;

        return $pheanstalkQueue->putInTube('search-index', $data, $priority, $delay);
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

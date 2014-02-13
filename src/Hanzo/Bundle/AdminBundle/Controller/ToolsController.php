<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\OrdersQuery;

class ToolsController extends CoreController
{

    public function indexAction()
    {
        return $this->render('AdminBundle:Tools:index.html.twig', [
            'database' => $this->getRequest()->getSession()->get('database')
        ]);
    }

    public function syncCategoriesAction()
    {
        $this->get('replication_manager')->syncCategories();

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Kategori synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    public function syncImagesAction()
    {
        $this->get('replication_manager')->syncProductsImages();

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Billede synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    public function syncImagesStyleguideAction()
    {
        $this->get('replication_manager')->syncStyleGuide();

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Styleguide synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    public function syncImagesSortingAction()
    {
        $this->get('replication_manager')->syncImageSorting();

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Billedesorterings synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    public function clearVarnishCacheAction()
    {
        try {
            $this->get('varnish.controle')->banUrl('^/*');
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Varnish cache tømt.');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    /**
     * [dibsToolsAction description]
     *
     * @Template("AdminBundle:Tools:dibsTools.html.twig")
     * @param  Request $request
     * @param  string  $action
     * @return array
     */
    public function dibsToolsAction(Request $request, $action = '')
    {
        $return = [
            'action'   => $action,
            'database' => $this->getRequest()->getSession()->get('database'),
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

    public function updateSearchIndexAction()
    {
        $this->container->get('hanzo_search.product_and_category_indexer')->build();

        $this->getRequest()->getSession()->getFlashBag()->add('notice', 'Søgeindexer opdateret for produkter og kategorier.');
        return $this->redirect($this->generateUrl('admin_tools'));
    }


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

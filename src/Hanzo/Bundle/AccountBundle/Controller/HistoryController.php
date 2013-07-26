<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

use \Criteria;
use Exception;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class HistoryController extends CoreController
{
    public function indexAction()
    {
        return $this->render('AccountBundle:History:index.html.twig', array(
            'page_type' => 'account-history',
        ));
    }

    public function viewAction(Request $request, $order_id)
    {
        $order = OrdersQuery::create()
            ->joinWithOrdersLines()
            ->findPk($order_id)
        ;

        if (!$order instanceof Orders) {
           return $this->redirect($request->headers->get('referer'));
        }

        $order_lines = $order->getOrdersLiness();
        $order_attributes = $order->getOrdersAttributess();

        $addresses = array();
        foreach ($order->toArray() as $key => $value) {
            if (substr($key, 0, 7) == 'Billing') {
                $key = strtolower(substr($key, 7));
                $addresses['billing'][$key] = $value;
                $addresses['billing']['type'] = 'billing';
            } elseif (substr($key, 0, 8) == 'Delivery') {
                $key = strtolower(substr($key, 8));
                $addresses['delivery'][$key] = $value;
                $addresses['delivery']['type'] = 'delivery';
            }
        }

        return $this->render('AccountBundle:History:view.html.twig', array(
            'page_type' => 'account-history-view',
            'order' => $order,
            'order_lines' => $order_lines,
            'addresses' => $addresses,
        ));
    }


    public function editAction($order_id)
    {
        $order = OrdersQuery::create()
            ->filterByCustomersId(CustomersPeer::getCurrent()->getId())
            ->findOneById($order_id)
        ;

        if (!$order instanceof Orders || $order->isNew()) {
            return $this->redirect($this->generateUrl('_account'));
        }

        $event = new FilterOrderEvent($order);
        $this->get('event_dispatcher')->dispatch('order.edit.start', $event);

        $status = $event->getStatus();
        if (false === $status->code) {
            $this->get('session')->getFlashBag()->add('notice', $this->get('translator')->trans($status->message, ['%order_id%' => $order_id], 'account'));
            return $this->redirect($this->generateUrl('_account'));
        }

        // update/set basket cookie
        Tools::setCookie('basket', '('.$order->getTotalQuantity(true).') '.Tools::moneyFormat($order->getTotalPrice(true)), 0, false);
        return $this->redirect($this->generateUrl('basket_view'));
    }


    public function blockAction($limit = 6, $link = TRUE, $route = FALSE)
    {
        $hanzo = Hanzo::getInstance();
        $customer = CustomersPeer::getCurrent();

        if (empty($route)) {
            $route = $this->get('request')->get('_route');
        }

        $router = $this->get('router');
        $pager = $this->get('request')->get('pager', 1);


        $offset = 6;
        if (($limit > 6) || ($limit == 0)) {
            $offset = 20;
        }

        $result = OrdersQuery::create()
            ->filterByState(Orders::STATE_PENDING, Criteria::GREATER_EQUAL)
            ->_or()
            ->filterByInEdit(true)
            ->orderByCreatedAt(Criteria::DESC)
            ->limit($limit)
            ->filterByCustomersId($customer->getId())
            ->paginate($pager, $offset)
        ;

        $paginate = FALSE;

        if (!$link) {
            $pages = array();
            if ($result->haveToPaginate()) {
                foreach ($result->getLinks(20) as $page) {
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);
                }

                $paginate = array(
                    'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage()), TRUE)),
                    'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage()), TRUE)),
                    'pages' => $pages,
                    'index' => $pager
                );
            }
        }

        $orders = array();
        foreach ($result as $record) {
            $folder = $this->mapLanguageToPdfDir($record->getLanguagesId()).'_'.$record->getCreatedAt('Y');

            $attachments = array();
            foreach ($record->getAttachments() as $key => $attachment) {
                $attachments[] = $hanzo->get('core.cdn') . 'pdf.php?' . http_build_query(array(
                    'folder' => $folder,
                    'file' => $attachment,
                    'key' => $this->get('session')->getId()
                ));
            }

            $orders[] = array(
                'id' => $record->getId(),
                'in_edit' => $record->getInEdit(),
                'can_modify' => (($record->getState() <= Orders::STATE_PENDING) ? true : false),
                'status' => str_replace('-', 'neg.', $record->getState()),
                'created_at' => $record->getCreatedAt(),
                'total' => $record->getTotalPrice(),
                'attachments' => $attachments,
            );
        }

        return $this->render('AccountBundle:History:block.html.twig', array(
            'page_type' => 'account-history',
            'orders' => (count($orders) ? $orders : NULL),
            'link' => $link,
            'paginate' => $paginate
        ));
    }


    /**
     * delete an order, but only if in a allowed state
     *
     * @param  int $order_id
     * @return Response
     */
    public function deleteAction($order_id)
    {
        $order = OrdersQuery::create()
            ->filterByCustomersId(CustomersPeer::getCurrent()->getId())
            ->filterByState(Orders::STATE_PENDING, Criteria::LESS_EQUAL)
            ->findOneById($order_id)
        ;

        if (!$order instanceof Orders) {
            $this->get('session')->getFlashBag()->add('notice', 'unable.to.delete.order.in.current.state');
        } else {
            $msg = $this->get('translator')->trans('order.deleted', array( '%id%' => $order_id ));

            // NICETO: not hardcoded
            $attributes = $order->getAttributes();
            $sw = isset($attributes->global->domain_key) ? $attributes->global->domain_key : '';
            switch ($sw) {
                case 'SalesFI':
                case 'FI':
                    $bcc = 'orderfi@pompdelux.com';
                    break;
                case 'SalesNL':
                case 'NL':
                    $bcc = 'ordernl@pompdelux.com';
                    break;
                case 'SalesSE':
                case 'SE':
                    $bcc = 'order@pompdelux.se';
                    break;
                case 'SalesNO':
                case 'NO':
                    $bcc = 'order@pompdelux.no';
                    break;
                default:
                    $bcc = 'order@pompdelux.dk';
                    break;
            }

            // nuke order
            try
            {
                $firstName = $order->getFirstName();
                $lastName  = $order->getLastName();
                $id        = $order->getId();
                $email     = $order->getEmail();

                $order->delete();

                // send delete notification
                $mailer = $this->get('mail_manager');
                $mailer->setMessage('order.deleted', array(
                    'name'     => $firstName,
                    'order_id' => $id,
                    'date' => date('d-m-Y'),
                    'time' => date('H:i'),
                ));

                $mailer->setBcc($bcc);
                $mailer->setTo($email, $firstName.' '.$lastName);
                $mailer->send();
            }
            catch ( Exception $e )
            {
                $msg = $e->getMessage();
            }

            $this->get('session')->getFlashBag()->add('notice', $msg);
        }

        return $this->redirect($this->generateUrl('_account'));
    }
}

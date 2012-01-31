<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AccountBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

use \Criteria;

class HistoryController extends CoreController
{
    public function indexAction()
    {
        return $this->render('AccountBundle:History:index.html.twig', array(
            'page_type' => 'account-history',
        ));
    }

    public function viewAction($order_id)
    {
        return $this->render('AccountBundle:History:index.html.twig', array(
            'page_type' => 'account-history-view',
            'order' => OrdersQuery::create()->findPk($order_id)
        ));
    }


    public function blockAction($limit = 10, $link = true)
    {
        $hanzo = Hanzo::getInstance();
        $customer = CustomersPeer::getCurrent();

        $result = OrdersQuery::create()
            ->filterByState(Orders::STATE_PENDING, Criteria::GREATER_THAN)
            ->orderByCreatedAt(Criteria::DESC)
            ->limit($limit)
            ->findByCustomersId($customer->getId())
        ;


        $orders = array();
        foreach ($result as $record) {

            $attachments = array();
            foreach ($record->getAttachments() as $key => $attachment) {
                $attachments[] = $hanzo->get('core.cdn') . 'dl.php?' . http_build_query(array(
                    'file' => $attachment,
                    'key' => $this->get('session')->getId()
                ));
            }


            $orders[] = array(
                'id' => $record->getId(),
                'status' => str_replace('-', 'neg.', $record->getState()),
                'created_at' => $record->getCreatedAt(),
                'total' => $record->getTotalPrice(),
                'attachments' => $attachments,
            );
        }

        return $this->render('AccountBundle:History:block.html.twig', array(
            'page_type' => 'account-history',
            'orders' => (count($orders) ? $orders : NULL),
            'link' => $link
        ));
    }
}

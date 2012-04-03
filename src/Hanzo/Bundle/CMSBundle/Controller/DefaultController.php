<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

use Hanzo\Model\CustomersPeer;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $page = CmsPeer::getFrontpage(Hanzo::getInstance()->get('core.locale'));
        return $this->forward('HanzoCMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

    public function viewAction($id, $page = NULL)
    {


$mailer = $this->get('mail_manager');
$order = \Hanzo\Model\OrdersQuery::create()->findPk(572830);
//Tools::log(get_class_methods($order));

$email = $order->getEmail();
$name  = trim($order->getFirstName() . ' ' . $order->getLastName());
$shippinng = $this->get('translator')->trans('shipping_method_name_' . $order->getDeliveryMethod(), array(), 'shipping');

$params = array(
    'order' => $order,
    'payment_address' => Tools::orderAddress('payment', $order),
    'company_address' => '',
    'delivery_address' => Tools::orderAddress('shipping', $order),
    'customer_id' => $order->getCustomersId(),
    'order_date' => $order->getCreatedAt('Y-m-d'),
    'payment_method' => $order->getBillingMethod(),
    'shipping_title' => $shippinng,
    'shipping_cost' => 0.00, // TODO
    'payment_fee' => 0.00, // expected_at
    'TODO' => '', // TODO
    'username' => $order->getCustomers()->getEmail(),
    'password' => $order->getCustomers()->getPasswordClear(),
    'conditions' => '', // TODO
    'expected_at' => '',
    'card_type' => '',
    'transaction_id' => '',
);

// TODO: only set if not null
if(0){
    $params['event_id'] = '';
    $params['payment_gateway_id'] = '';
    $params['coupon_amount'] = 0.00;
    $params['coupon_name'] = '';
    $params['hostess_discount'] = '';
    $params['hostess_discount_title'] = '';
    $params['gothia_fee'] = 0.00;
    $params['gothia_fee_title'] = '';
}

$mailer->setMessage('order.confirmation', $params);
$mailer->setTo($email, $name);
// TODO: send confirmation mail to customer and bcc to store owner
$mailer->send();





        $locale = Hanzo::getInstance()->get('core.locale');

        if ($page instanceof Cms) {
            $type = $page->getType();
        }
        else {
            $page = CmsPeer::getByPK($id, $locale);
            $type = 'pages';
            if (is_null($page)) {
                $page = 'implement 404 !';
            }
        }

        $this->get('twig')->addGlobal('page_type', $type);
        return $this->render('HanzoCMSBundle:Default:view.html.twig', array('page' => $page));
    }
}

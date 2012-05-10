<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\Customers,
    Hanzo\Model\CustomersPeer,
    Hanzo\Core\CoreController
    ;

class DefaultController extends CoreController
{
    /**
     * blockAction
     *
     * @return object Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        return $this->render('PaymentBundle:Default:block.html.twig');
    }

    public function testAction()
    {
        $order = OrdersPeer::retrieveByPK(572846);
        $order->cancelPayment();

        return new Response('Test');
        //return $this->render('PaymentBundle:Default:block.html.twig');
    }

    /**
     * successAction
     *
     * @return object Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function successAction()
    {
        return $this->redirect($this->generateUrl('_checkout_success'));
        // $customer = CustomersPeer::getCurrent();
        // $order    = OrdersPeer::getCurrent();
        // return $this->render('PaymentBundle:Default:success.html.twig', array('page_type' => 'checkout-success'));
    }
}

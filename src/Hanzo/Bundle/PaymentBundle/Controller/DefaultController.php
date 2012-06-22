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

    /**
     * processAction
     *
     * This shows the customer a page that checks the state of the ordre until it is correct (<= payment ok ) or fails
     *
     * @return object Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function processAction()
    {
        return $this->render('PaymentBundle:Default:process.html.twig');

        /*$order = OrdersPeer::getCurrent();

        if ( $order->getState() <= Orders::STATE_PAYMENT_OK ) // State should be pending by now (set if event order.payment.collected is triggered)
        {
            return $this->redirect($this->generateUrl('_checkout_failed'));
        }
        else
        {
            return $this->redirect($this->generateUrl('_checkout_success'));
        }*/
    }
}

<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Bundle\PaymentBundle\PayByBill\PayByBillApi;

class PayByBillController extends CoreController
{
    /**
     * callbackAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function callbackAction()
    {
        $api = $this->get('payment.paybybillapi');
        $request = $this->get('request');
        $order = OrdersPeer::getCurrent();

        if ( !($order instanceof Orders) ) {
            throw new Exception( 'PayByBill callback found no valid order to proccess.' );
        }

        try {
            $api->updateOrderSuccess( $request, $order );
        } catch (Exception $e) {
            Tools::log($e->getMessage());
        }

        return $this->redirect($this->generateUrl('_checkout_success'));
    }

    /**
     * cancelAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function cancelAction()
    {
        return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
    }


    /**
     * blockAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $api = $this->get('payment.paybybillapi');

        if ( !$api->isActive() )
        {
            return new Response( '', 200, array('Content-Type' => 'text/html'));
        }

        #$api = $this->get('payment.paybybillapi');
        #$order = OrdersPeer::getCurrent();

        return $this->render('PaymentBundle:PayByBill:block.html.twig');
    }
}

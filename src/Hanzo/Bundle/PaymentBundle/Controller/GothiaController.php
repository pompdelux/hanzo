<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\GothiaAccounts;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApi;
use Hanzo\Bundle\PaymentBundle\Methods\Gothia\GothiaApiCallException;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

class GothiaController extends CoreController
{
    /**
     * blockAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $api = $this->get('payment.gothiaapi');

        if (!$api->isActive()) {
            return new Response( '', 200, array('Content-Type' => 'text/html'));
        }

        return $this->render('PaymentBundle:Gothia:block.html.twig',array());
    }

    /**
     * paymentAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function paymentAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->isNew() )
        {
            return $this->redirect($this->generateUrl('_checkout'));
        }

        // hf@bellcom.dk, 18-sep-2012: maybe a fix for orders contaning valid dibs info and then is overriden with gothia billingmethod -->>
        if ( $order->getState() > Orders::STATE_PRE_PAYMENT )
        {
            $this->get('session')->setFlash('notice', 'order.state_pre_payment.locked');
            return $this->redirect($this->generateUrl('basket_view'));
        }
        // <<-- hf@bellcom.dk, 18-sep-2012: maybe a fix for orders contaning valid dibs info and then is overriden with gothia billingmethod
        //
        $gothiaAccount = $order->getCustomers()->getGothiaAccounts();

        // No gothia account has been created and associated with the customer, so lets do that
        $step = 2;
        if (is_null($gothiaAccount)) {
            $step = 1;
            $gothiaAccount = new GothiaAccounts();
        }

        // Build the form where the customer can enter his/hers information
        $form = $this->createFormBuilder( $gothiaAccount )
            ->add( 'social_security_num', 'text', array(
                'label' => 'social_security_num',
                'required' => true,
                'translation_domain' => 'gothia' ) )
            ->getForm();

        return $this->render('PaymentBundle:Gothia:payment.html.twig',array('page_type' => 'gothia','step' => $step, 'form' => $form->createView()));
    }

    /**
     * checkCustomerAction
     * The form has been submitted via ajax -> process it
     * @param Request $request
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function checkCustomerAction(Request $request)
    {
        $form       = $request->request->get('form');
        $SSN        = $form['social_security_num'];
        $translator = $this->get('translator');

        $hanzo = Hanzo::getInstance();
        $domainKey = $hanzo->get('core.domain_key');

        // Use form validation?

        if('FI' == str_replace('Sales', '', $domainKey)){
            if(!strpos($SSN, '-')){ // FI has to have dash. If it isnt there, add it. Could be made better?
                $SSN = substr($SSN, 0, 6).'-'.substr($SSN, 6);
            }

            //Finland cases
            if (strlen($SSN) < 11) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                ));
            }

            if (strlen($SSN) > 11) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                ));
            }
        }elseif('NO' == str_replace('Sales', '', $domainKey)){
            $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

            //Norway cases
            if (strlen($SSN) < 11) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                ));
            }

            if (strlen($SSN) > 11) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                ));
            }
        }elseif('DK' == str_replace('Sales', '', $domainKey)){
            $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

            //Denmark cases
            if (strlen($SSN) < 8) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                ));
            }

            if (strlen($SSN) > 8) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                ));
            }
        }else{

            $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

            //Every other domain
            if (!is_numeric($SSN)) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.not_numeric', array(), 'gothia'),
                ));
            }
            if (strlen($SSN) < 10) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
                ));
            }

            if (strlen($SSN) > 10) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.ssn.to_long', array(), 'gothia')
                ));
            }
        }

        $order         = OrdersPeer::getCurrent();
        $customer      = $order->getCustomers();
        $gothiaAccount = $customer->getGothiaAccounts();

        if (is_null($gothiaAccount)) {
            $gothiaAccount = new GothiaAccounts();
        }

        $gothiaAccount->setDistributionBy( 'NotSet' )
            ->setDistributionType( 'NotSet' )
            ->setSocialSecurityNum( $SSN );

        $customer->setGothiaAccounts( $gothiaAccount );

        try
        {
            // Validate information @ gothia
            $api = $this->get('payment.gothiaapi');
            $response = $api->call()->checkCustomer( $customer );
        }
        catch( GothiaApiCallException $g )
        {
            #Tools::debug( $g->getMessage(), __METHOD__);
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.checkcustomer.failed', array('%msg%' => $g->getMessage()), 'gothia'),
            ));
        }

        if ( !$response->isError() )
        {
            $gothiaAccount = $customer->getGothiaAccounts();
            $gothiaAccount->setDistributionBy( $response->data['DistributionBy'] )
                ->setDistributionType( $response->data['DistributionType'] );

            $customer->setGothiaAccounts( $gothiaAccount );
            $customer->save();

            return $this->json_response(array(
                'status' => true,
                'message' => '',
            ));
        }
        else
        {
            if ( $response->data['PurchaseStop'] === 'true')
            {
                #Tools::debug( 'PurchaseStop', __METHOD__, array( 'Transaction id' => $response->transactionId ));

                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.checkcustomer.purchasestop', array(), 'gothia'),
                ));
            }

            #Tools::debug( 'Check customer error', __METHOD__, array( 'Transaction id' => $response->transactionId, 'Data' => $response->data ));

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.checkcustomer.error', array(), 'gothia'),
            ));
        }
    }

    /**
     * confirmAction
     * @param Request $request
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function confirmAction(Request $request)
    {
        $order      = OrdersPeer::getCurrent();
        $customer   = $order->getCustomers();
        $api        = $this->get('payment.gothiaapi');
        $translator = $this->get('translator');

        if ( $order->getState() > Orders::STATE_PRE_PAYMENT )
        {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.order.state_pre_payment.locked', array(), 'gothia'),
            ));
        }

        // Handle reservations in Gothia when editing the order
        // A customer can max reserve 7.000 SEK currently, so if they edit an order to 3.500+ SEK
        // it will fail because we have not removed the old reservation first, this should fix it

        if ( $order->getInEdit() )
        {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if ( !( $currentVersion < 2 ) )
            {
                $oldOrderVersion = ( $currentVersion - 1);
                $oldOrder = $order->getOrderAtVersion($oldOrderVersion);

                $paytype = strtolower( $oldOrder->getBillingMethod() );

                // The new order amount is different from the old order amount
                // We will remove the old reservation, and create a new one
                // but only if the old paytype was gothia
                if ( $paytype == 'gothia' && $order->getTotalPrice() != $oldOrder->getTotalPrice() )
                {
                    try
                    {
                        $response = $api->call()->cancelReservation( $customer, $oldOrder );
                    }
                    catch( GothiaApiCallException $g )
                    {
                        #Tools::debug( $g->getMessage(), __METHOD__);

                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.cancelreservation.failed', array('%msg%' => $g->getMessage()), 'gothia'),
                        ));
                    }

                    if ( $response->isError() )
                    {
                        #Tools::debug( 'Cancel reservation error', __METHOD__, array( 'Transaction id' => $response->transactionId, 'Data' => $response->data ));

                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $translator->trans('json.cancelreservation.error', array(), 'gothia'),
                        ));
                    }
                }
            }
        }

        try
        {
            $response = $api->call()->placeReservation( $customer, $order );
        }
        catch( GothiaApiCallException $g )
        {
            #Tools::debug( $g->getMessage(), __METHOD__);

            $api->updateOrderFailed( $request, $order );
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.failed', array('%msg%' => $g->getMessage()), 'gothia'),
            ));
        }

        if ( $response->isError() )
        {
            #Tools::debug( 'Confirm action error', __METHOD__, array( 'Transaction id' => $response->transactionId, 'Data' => $response->data ));

            $api->updateOrderFailed( $request, $order );
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.error', array(), 'gothia'),
            ));
        }

        // NICETO: priority: low, refacture gothia to look more like DibsController

        try
        {
            $api->updateOrderSuccess( $request, $order );
            $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));

            return $this->json_response(array(
                'status' => TRUE,
                'message' => '',
            ));
        }
        catch (Exception $e)
        {
            #Tools::debug( $e->getMessage(), __METHOD__);
            $api->updateOrderFailed( $request, $order );

            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.error', array(), 'gothia'),
            ));
        }
    }

    /**
     * cancelAction
     * Cancels a Gothia Payment, and restores the order in good state
     * @return void
     * @author Anders Bryrup <anders@bellcom.dk>
     **/
    public function cancelAction()
    {
        $translator = $this->get('translator');

        $order = OrdersPeer::getCurrent();
        $order->setState( Orders::STATE_BUILDING );
        $order->save();

        $this->get('session')->setFlash('notice', $translator->trans( 'payment.canceled', array(), 'checkout' ));

        return $this->redirect($this->generateUrl('_checkout'));
    }

    /**
     * testAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function testAction()
    {
        $customer  = CustomersPeer::getCurrent();

        $api = $this->get('payment.gothiaapi');
        $response = $api->call()->checkCustomer( $customer );

        error_log(__LINE__.':'.__FILE__.' '.print_r($response,1)); // hf@bellcom.dk debugging

        return new Response( 'Test completed', 200, array('Content-Type' => 'text/html'));
    }

    /**
     * processAction
     *
     * @return object Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function processAction()
    {
        $order = OrdersPeer::getCurrent();

        if ( $order->getState() < Orders::STATE_PAYMENT_OK )
        {
            return $this->redirect($this->generateUrl('_checkout_failed'));
        }
        else
        {
            return $this->redirect($this->generateUrl('_checkout_success'));
        }
    }
}

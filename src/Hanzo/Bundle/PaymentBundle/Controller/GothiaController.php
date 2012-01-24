<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersPeer,
    Hanzo\Model\Customers,
    Hanzo\Model\CustomersPeer,
    Hanzo\Model\GothiaAccounts,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Bundle\PaymentBundle\Gothia\GothiaApi;

class GothiaController extends CoreController
{
    /**
     * blockAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        return new Response('Gothia payment block', 200, array('Content-Type' => 'text/html'));
    }

    /**
     * paymentAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function paymentAction()
    {
        $customer = CustomersPeer::getCurrent();
        $order    = OrdersPeer::getCurrent();
        $gothiaAccount = $customer->getGothiaAccounts();

        // No gothia account has been created and associated with the customer, so lets do that
        if ( is_null($gothiaAccount) )
        {
            $gothiaAccount = new GothiaAccounts();

            // TODO: form is created twice
            // Build the form where the customer can enter his/hers information
            $form = $this->createFormBuilder( $gothiaAccount )
                ->add( 'social_security_num', 'text' )
                ->getForm();

            return $this->render('PaymentBundle:Gothia:payment.html.twig',array('page_type' => 'gothia','step' => 1, 'form' => $form->createView()));
        }
        else
        {
            // TODO: form is created twice
            // Build the form where the customer can enter his/hers information
            $form = $this->createFormBuilder( $gothiaAccount )
                ->add( 'social_security_num', 'text' )
                ->getForm();

            // A existing gothia account exists, ask the user for confirmation and get on with it
            return $this->render('PaymentBundle:Gothia:payment.html.twig',array('page_type' => 'gothia','step' => 2, 'form' => $form->createView()));
        }

        return new Response( 'You should not be here', 500, array('Content-Type' => 'text/html'));
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
        $form = $request->request->get('form');
        $SSN  = $form['social_security_num'];
        $translator = $this->get('translator');

        // Use form validation?
        if ( !is_numeric( $SSN ) )
        {
            return $this->json_response(array( 
                'status' => FALSE,
                'message' => $translator->trans('json.ssn.not_numeric', array(), 'gothia'),
            ));
        }

        if ( strlen( $SSN ) < 10 )
        {
            // FIXME: define
            return $this->json_response( GOTHIA_ERROR_ORGNOSSN_IS_TO_SHORT );
        }

        $SSN = strtr( $SSN, array( '-' => '', ' ' => '' ) );

        $customer      = CustomersPeer::getCurrent();
        $gothiaAccount = $customer->getGothiaAccounts();
        $order         = OrdersPeer::getCurrent();

        if ( is_null($gothiaAccount) )
        {
            $gothiaAccount = new GothiaAccounts();
        }

        $gothiaAccount->setDistributionBy( 'NotSet' )
            ->setDistributionType( 'NotSet' )
            ->setSocialSecurityNum( $SSN );

        $customer->setGothiaAccounts( $gothiaAccount );

        // Validate information @ gothia
        $api = new GothiaApi();

        try
        {
            $response = $api->call()->checkCustomer( $customer );
        }
        catch( GothiaApiCallException $g )
        {
            // FIXME: better response
            return $this->json_response( 'error' );
        }

        if ( !$response->isError() ) 
        {
            $gothiaAccount = $customer->getGothiaAccounts();
            $gothiaAccount->setDistributionBy( $response->data['DistributionBy'] )
                ->setDistributionType( $response->data['DistributionType'] );

            $customer->setGothiaAccounts( $gothiaAccount );
            $customer->save();
            // FIXME: better response
            return $this->json_response( 'ok' );
        }
        else
        {
            if ( $response->data['PurchaseStop'] === 'true')
            {
                // FIXME: better response
                return $this->json_response( 'error: purchase denied' );
            }

            // FIXME: better response
            return $this->json_response( 'error' );
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
        $customer      = CustomersPeer::getCurrent();
        $order         = OrdersPeer::getCurrent();
        $api           = new GothiaApi();

        // Handle reservations in Gothia when editing the order
        // A customer can max reserve 7.000 SEK currently, so if they edit an order to 3.500+ SEK 
        // it will fail because we have not removed the old reservation first, this should fix it

        if ( $order->getState() == Orders::STATE_EDITING )
        {
            // FIXME:
            $oldOrder = OrdersPeer::retrieveByPK($_SESSION['editing_order']['edit_order_id']);

            // The new order amount is different from the old order amount
            // We will remove the old reservation, and create a new one
            if ( $order->getTotalPrice() != $oldOrder->getTotalPrice() )
            {
                try
                {
                    $response = $api->call()->cancelReservation( $customer, $oldOrder );
                }
                catch( GothiaApiCallException $g )
                {
                    // FIXME: better response
                    return $this->json_response( 'error' );
                }

                if ( $response->isError() )
                {
                    // FIXME: better response
                    return $this->json_response( 'error' );
                }
            }
        }

        try
        {
            $response = $api->call()->placeReservation( $customer, $order );
        }
        catch( GothiaApiCallException $g )
        {
            // FIXME: better response
            return $this->json_response( 'error' );
        }

        if ( $response->isError() )
        {
            // FIXME: better response
            return $this->json_response( 'error' );
        }

        // FIXME: better response
        return $this->json_response( 'ok' );
    }

    /**
     * testAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function testAction()
    {
        $customer  = CustomersPeer::getCurrent();
        $addresses = $customer->getAddresses();

        error_log(__LINE__.':'.__FILE__.' '.print_r($addresses[0],1)); // hf@bellcom.dk debugging

        return new Response( 'Test completed', 200, array('Content-Type' => 'text/html'));
    }

}

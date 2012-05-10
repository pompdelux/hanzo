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
use Hanzo\Bundle\PaymentBundle\Gothia\GothiaApi;
use Hanzo\Bundle\PaymentBundle\Gothia\GothiaApiCallException;

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

        if ( !$api->isActive() )
        {
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
        $customer = CustomersPeer::getCurrent();
        $order    = OrdersPeer::getCurrent();
        $gothiaAccount = $customer->getGothiaAccounts();

        // No gothia account has been created and associated with the customer, so lets do that
        $step = 2;
        if ( is_null($gothiaAccount) )
        {
            $step = 1;
            $gothiaAccount = new GothiaAccounts();
        }

        // Build the form where the customer can enter his/hers information
        $form = $this->createFormBuilder( $gothiaAccount )
            ->add( 'social_security_num', 'text' )
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
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.ssn.to_short', array(), 'gothia'),
            ));
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

        try
        {
            // Validate information @ gothia
            $api = $this->get('payment.gothiaapi');
            $response = $api->call()->checkCustomer( $customer );
        }
        catch( GothiaApiCallException $g )
        {
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
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $translator->trans('json.checkcustomer.purchasestop', array(), 'gothia'),
                ));
            }

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
        $customer   = CustomersPeer::getCurrent();
        $order      = OrdersPeer::getCurrent();
        $api        = $this->get('payment.gothiaapi');
        $translator = $this->get('translator');

        // Handle reservations in Gothia when editing the order
        // A customer can max reserve 7.000 SEK currently, so if they edit an order to 3.500+ SEK
        // it will fail because we have not removed the old reservation first, this should fix it

        if ( $order->getInEdit() )
        {
            $currentVersion = $order->getVersionId();

            if ( !( $currentVersion < 2 ) ) // If the version number is less than 2 there is no previous version
            {
              $oldOrderVersion = ( $currentVersion - 1);
              $oldOrder = $order->getOrderAtVersion($oldOrderVersion);

              $attributes = $oldOrder->getOrdersAttributess()->toArray();

              $paytype = false;

              foreach ($attributes as $attribute) 
              {
                  if ( $attribute['Ns'] == 'payment' && $attribute['CKey'] == 'paytype' )
                  {
                      $paytype = $attribute['CValue'];
                  }
              }

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
                      return $this->json_response(array(
                          'status' => FALSE,
                          'message' => $translator->trans('json.cancelreservation.failed', array('%msg%' => $g->getMessage()), 'gothia'),
                      ));
                  }

                  if ( $response->isError() )
                  {
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
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.failed', array('%msg%' => $g->getMessage()), 'gothia'),
            ));
        }

        if ( $response->isError() )
        {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $translator->trans('json.placereservation.error', array(), 'gothia'),
            ));
        }

        $this->get('event_dispatcher')->dispatch('order.payment.collected', new FilterOrderEvent($order));

        return $this->json_response(array(
            'status' => TRUE,
            'message' => '',
        ));
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

        return new Response( 'Test completed', 200, array('Content-Type' => 'text/html'));
    }
}

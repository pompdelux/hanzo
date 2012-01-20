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
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        return new Response('Gothia payment block', 200, array('Content-Type' => 'text/html'));
    }

    /**
     * paymentAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function paymentAction()
    {
    /*
    if !gothia account
      ask user to fill form

    if user is creating account
      verify account with gothia
      if error
        show error
      else
        pre fill form

    if user submits request
      verify payment with gothia
      if error
        show error
      else
        go to payment success
     */

        $api = new GothiaApi();

        // FIXME:
        //$customer = CustomersPeer::getCurrent();
        $customer = CustomersPeer::retrieveByPK(4);
        $order    = OrdersPeer::getCurrent();
        $gothiaAccount = $customer->getGothiaAccounts();

        // No gothia account has been created and associated with the customer, so lets do that
        if ( is_null($gothiaAccount) )
        {
            $gothiaAccount = new GothiaAccounts();

            // Prefill object with information from the customer object
            // TODO: has this is the same information as on the customer, why have it?
            $gothiaAccount->setFirstName( $customer->getFirstName() )
                ->setLastName( $customer->getLastName() )
                ->setAddress( 'Dalagatan' )
                ->setPostalCode( '28020' )
                ->setPostalPlace( 'BJÄRNUM' )
                ->setEmail( 'hf-gothia-28020@bellcom.dk' )
                ->setPhone( '00000000' )
                ->setCountryCode( 'SE' )
                ->setDistributionBy( 'NotSet' )
                ->setDistributionType( 'NotSet' );

            // Build the form where the customer can enter his/hers information
            // SSN can be used for testing: 4409291111
            $form = $this->createFormBuilder( $gothiaAccount )
                ->add( 'social_security_num', 'text' )
                ->getForm();

            // The form has been submitted via ajax -> process it
            if ( $this->get('request')->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() ) 
            {
                $form->bindRequest($this->get('request'));

                // Validate information @ gothia
                $response = $api->call()->checkCustomer( $gothiaAccount );

                // TODO: The data in the gothia account must be validated before it is created, e.g. spaces and dashed stripped from social security num
                if ( !$response->isError() && $form->isValid()) 
                {
                    $customer->setGothiaAccounts( $gothiaAccount );
                    $customer->save();

                    // TODO: use corecontrollers json_response
                    return new Response( json_encode( array('ok') ), 200, array('Content-Type' => 'application/json; charset=utf-8'));
                }
                else
                {
                    if ( !$form->isValid() )
                    {
                        // TODO: use corecontrollers json_response
                        return new Response( json_encode( array('error') ), 200, array('Content-Type' => 'application/json; charset=utf-8'));
                    }

                    if ( $response->isError() )
                    {
                        // TODO: use corecontrollers json_response
                        return new Response( json_encode( array('error') ), 200, array('Content-Type' => 'application/json; charset=utf-8'));
                    }
                }
            }
            else
            {
                return $this->render('PaymentBundle:Gothia:create_account.html.twig',array('page_type' => 'gothia','title' => 'Gothia opret konto', 'form' => $form->createView(), 'customer' => $customer));
            }
        }
        else
        {
            // A existing gothia account exists, ask the user for confirmation and get on with it
            return $this->render('PaymentBundle:Gothia:confirm.html.twig',array('page_type' => 'gothia','title' => 'Gothia bekræft'));
        }

        return new Response( 'You should not be here', 500, array('Content-Type' => 'text/html'));

        /* Test data:
        $gothiaAccount->setFirstName( 'Sven Anders' )
            ->setLastName( 'Ström' )
            ->setAddress( 'Dalagatan' )
            ->setPostalCode( '28020' )
            ->setPostalPlace( 'BJÄRNUM' )
            ->setEmail( 'hf-gothia-28020@bellcom.dk' )
            ->setPhone( '00000000' )
            ->setCountryCode( 'SE' )
            ->setDistributionBy( 'NotSet' )
            ->setDistributionType( 'NotSet' )
            ->setSocialSecurityNum( '4409291111' );
         */

        // TODO: if editing order... see line 149-> in oscom gothiaApi.php
        $api->call()->placeReservation( $gothiaAccount, $order );


    }
}

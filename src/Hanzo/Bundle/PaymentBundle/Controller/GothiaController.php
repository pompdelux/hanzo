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
     * if !gothia account
     *   ask user enter SSN
     *
     * if user is creating account
     *   verify account with gothia
     *   if error
     *     show error
     *
     * if user submits request
     *   verify payment with gothia
     *   if error
     *     show error
     *   else
     *     go to payment success
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function paymentAction()
    {
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
            $gothiaAccount->setDistributionBy( 'NotSet' )
                ->setDistributionType( 'NotSet' );

            // Build the form where the customer can enter his/hers information
            $form = $this->createFormBuilder( $gothiaAccount )
                ->add( 'social_security_num', 'text' )
                ->getForm();

            // The form has been submitted via ajax -> process it
            if ( $this->get('request')->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() ) 
            {
                // TODO: The data in the gothia account must be validated before it is created, e.g. spaces and dashed stripped from social security num
                $form->bindRequest($this->get('request'));

                // Validate information @ gothia
                $response = $api->call()->checkCustomer( $customer );

                if ( !$response->isError() && $form->isValid()) 
                {
                    $customer->setGothiaAccounts( $gothiaAccount );
                    $customer->save();

                    // TODO: HANDLE THIS!: should maybe be moved to own route?
                    // Handle reservations in Gothia when editing the order
                    // A customer can max reserve 7.000 SEK currently, so if they edit an order to 3.500+ SEK 
                    // it will fail because we have not removed the old reservation first, this should fix it

                    if ( $order->getState() == OrdersPeer::STATE_EDITING )
                    {
                        // FIXME:
                        $oldOrder = OrdersPeer::retrieveByPK($_SESSION['editing_order']['edit_order_id']);

                        // The new order amount is different from the old order amount
                        // We will remove the old reservation, and create a new one
                        // FIXME:
                        if ( $order->getTotalPrice() != $oldOrder->getTotalPrice() )
                        {
                            $api->call()->cancelReservation( $gothiaAccount, $oldOrder );
                        }
                    }

                    $response = $api->call()->placeReservation( $gothiaAccount, $order );

                    return new $this->json_response( array('ok') );
                }
                else
                {
                    if ( !$form->isValid() )
                    {
                        return new $this->json_response( array('error') );
                    }

                    if ( $response->isError() )
                    {
                        return new $this->json_response( array('error') );
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
    }
}
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

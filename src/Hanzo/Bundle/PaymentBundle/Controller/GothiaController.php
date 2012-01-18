<?php

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
   * testAction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function testAction()
  {
    $api = new GothiaApi();

    //$customer = CustomersPeer::getCurrent();
    $customer = CustomersPeer::retrieveByPK(4);
    $order    = OrdersPeer::getCurrent();
    $gothiaAccount = $customer->getGothiaAccounts();
    
    // TODO: The data in the gothia account must be validated before it is created, e.g. spaces and dashed stripped from social security num
    if ( is_null($gothiaAccount) )
    {
      $gothiaAccount = new GothiaAccounts();
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

      $customer->setGothiaAccounts($gothiaAccount);
      $customer->save();
    }

    $api->call()->checkCustomer( $gothiaAccount );
    // TODO: if editing order... see line 149-> in oscom gothiaApi.php
    $api->call()->placeReservation( $gothiaAccount, $order );

    return new Response('Ok', 200, array('Content-Type' => 'text/plain'));
  }

  /**
   * paymentAction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function paymentAction()
  {


    /*

    if gothia account
      pre fill form
    else
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

  }
}

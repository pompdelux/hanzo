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
    $gothiaAccount = $customer->getGothiaAccounts();

    if ( is_null($gothiaAccount) )
    {
      $gothiaAccount = new GothiaAccounts();

       

      $customer->setGothiaAccounts($gothiaAccount);
      $customer->save();
    }

    $api->call()->checkCustomer( $gothiaAccount );

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

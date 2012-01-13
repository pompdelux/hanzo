<?php

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Bundle\PaymentBundle\Dibs\DibsApi;


class DibsController extends CoreController
{
  /**
   * callbackAction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function callbackAction()
  {
    return new Response('Hello', 200, array('Content-Type' => 'text/plain'));
    error_log(__LINE__.':'.__FILE__.' '); // hf@bellcom.dk debugging
  }

  /**
   * apiTestAction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function apiTestAction($method)
  {
    $api = new DibsApi();
    //$apiResponse = $api->call()->acquirersStatus();
    $apiResponse = $api->call()->payinfo( 527221861 );

    return new Response(print_r($apiResponse->debug(),1), 200, array('Content-Type' => 'text/plain'));
  }

  /**
   * formTestAdction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function formTestAction()
  {
    return new Response('Hest', 200, array('Content-Type' => 'text/html'));
  }

  public function indexAction($name)
  {
    return $this->render('PaymentBundle:Default:index.html.twig', array('name' => $name));
  }
}

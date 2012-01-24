<?php

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
  Hanzo\Core\Tools,
  Hanzo\Core\CoreController;

class DefaultController extends CoreController
{
  /**
   * blockAction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function blockAction()
  {
    return $this->render('PaymentBundle:Default:block.html.twig');
  }

  public function indexAction($name)
  {
    return $this->render('PaymentBundle:Default:index.html.twig', array('name' => $name));
  }

  /**
   * successAction
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function successAction()
  {
    return new Response( 'Success', 200, array('Content-Type' => 'text/html'));
  }
}

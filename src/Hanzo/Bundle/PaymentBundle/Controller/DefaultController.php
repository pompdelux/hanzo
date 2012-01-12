<?php

namespace Hanzo\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
  Hanzo\Core\Tools,
  Hanzo\Core\CoreController;

class DefaultController extends CoreController
{

  public function indexAction($name)
  {
    return $this->render('PaymentBundle:Default:index.html.twig', array('name' => $name));
  }
}

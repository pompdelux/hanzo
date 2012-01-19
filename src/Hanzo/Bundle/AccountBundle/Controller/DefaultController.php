<?php

namespace Hanzo\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('AccountBundle:Default:index.html.twig', array(
            'page_type' => 'account'
        ));
    }
}

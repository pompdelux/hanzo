<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Session;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig', array(
            'database' => $this->getRequest()->getSession()->get('database'))
        );
    }

    public function setDatabaseConnectionAction($name)
    {
    	//$session = new Session();
        // $session->start();
        // $session->set('database', $name);
    	$this->getRequest()->getSession()->set('database', $name);
    	return $this->redirect($this->generateUrl('admin'));
    }
}

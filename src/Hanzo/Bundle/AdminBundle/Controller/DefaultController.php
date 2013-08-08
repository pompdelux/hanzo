<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Session;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        // redirect hack
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            if ($this->get('security.context')->isGranted('ROLE_CUSTOMERS_SERVICE')) {
                return $this->redirect($this->generateUrl('admin_customers'));
            }

            $username = $this->get('security.context')->getToken()->getUser()->getUsername();
            if (in_array($username, ['pd@pompdelux.dk', 'mh@pompdelux.dk'])) {
                return $this->redirect($this->generateUrl('admin_statistics'));
            }
        }

        return $this->render('AdminBundle:Default:index.html.twig', array(
            'database' => $this->getRequest()->getSession()->get('database'))
        );
    }

    public function setDatabaseConnectionAction($name)
    {
    	$this->getRequest()->getSession()->set('database', $name);
    	return $this->redirect($this->generateUrl('admin'));
    }
}

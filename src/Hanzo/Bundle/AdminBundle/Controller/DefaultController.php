<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends CoreController
{
    public function indexAction(Request $request)
    {
        // redirect hack
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            if ($this->get('security.context')->isGranted('ROLE_CUSTOMERS_SERVICE') && !$this->get('security.context')->isGranted('ROLE_CUSTOMERS_SERVICE_EXTRA') && !$this->get('security.context')->isGranted('ROLE_SUPPORT')) {
                return $this->redirect($this->generateUrl('admin_customers'));
            }

            $username = $this->get('security.context')->getToken()->getUser()->getUsername();

            if (in_array($username, ['pd@pompdelux.dk', 'mh@pompdelux.dk'])) {
                return $this->redirect($this->generateUrl('admin_statistics'));
            }

            if (in_array($username, ['tj@pompdelux.dk'])) {
                return $this->redirect($this->generateUrl('admin_cms'));
            }
        }

        return $this->render('AdminBundle:Default:index.html.twig', array(
            'database' => $request->getSession()->get('database'))
        );
    }

    public function setDatabaseConnectionAction(Request $request, $name)
    {
    	$this->getRequest()->getSession()->set('database', $name);

        if ($request->query->has('goto')) {
            $url = $request->query->get('goto');
        } else {
            $url = $this->generateUrl('admin');
        }

    	return $this->redirect($url);
    }
}

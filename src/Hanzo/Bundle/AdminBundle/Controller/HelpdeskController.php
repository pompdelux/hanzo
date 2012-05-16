<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\CoreController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use \PropelCollection;
use Hanzo\Model\HelpdeskDataLogQuery;
use Hanzo\Model\HelpdeskDataLog;

class HelpdeskController extends CoreController
{
    public function indexAction($pager)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }
        

    	$helpdesk_data = HelpdeskDataLogQuery::create()
    		->orderByCreatedAt('DESC')
            ->paginate($pager, 50)
    	;

    	$paginate = null;
        if ($helpdesk_data->haveToPaginate()) {

            $pages = array();
            foreach ($helpdesk_data->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }
            
            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($helpdesk_data->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $helpdesk_data->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($helpdesk_data->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $helpdesk_data->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($helpdesk_data->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $helpdesk_data->getNextPage()), TRUE)),
                    'prew' => ($helpdesk_data->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $helpdesk_data->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        return $this->render('AdminBundle:Helpdesk:index.html.twig', array(
            'helpdesk_data'  => $helpdesk_data,
            'paginate'      => $paginate
        ));
    }

    public function deleteAction($key)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }
        
    	$helpdesk_data = null;
    	if('ALL' == $key){
			$helpdesk_data = HelpdeskDataLogQuery::create()
				->filterByCreatedAt(array('max' => strtotime('-2 days', time())))
				->find()
			;
    	}else{
			$helpdesk_data = HelpdeskDataLogQuery::create()
				->findOneByKey($key)
			;	
    	}

        if($helpdesk_data instanceof HelpdeskDataLog || $helpdesk_data instanceof PropelCollection){
            $helpdesk_data->delete();


	        if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	                'status' => TRUE,
	                'message' => $this->get('translator')->trans('delete.helpdesk.success', array(), 'admin'),
	            ));
	        }

	        $this->get('session')->setFlash('notice', 'delete.helpdesk.success');
        
        	return $this->redirect($this->generateUrl('admin_helpdesk'));

        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.helpdesk.failed', array(), 'admin'),
            ));
        }

        return $this->redirect($this->generateUrl('admin_helpdesk'));
    }
}

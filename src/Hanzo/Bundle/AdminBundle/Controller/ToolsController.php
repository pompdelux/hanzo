<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;


class ToolsController extends CoreController
{

    public function indexAction()
    {
        return $this->render('AdminBundle:Tools:index.html.twig');
    }

    public function syncImagesAction()
    {
        $this->get('replication_manager')->syncProductsImages();

        $this->getRequest()->getSession()->setFlash('notice', 'Billede synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    public function syncImagesStyleguideAction()
    {
        $this->get('replication_manager')->syncStyleGuide();

        $this->getRequest()->getSession()->setFlash('notice', 'Styleguide synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }

    public function syncImagesSortingAction()
    {
        $this->get('replication_manager')->syncImageSorting();

        $this->getRequest()->getSession()->setFlash('notice', 'Billedesorterings synkronisering færdig..');
        return $this->redirect($this->generateUrl('admin_tools'));
    }
}

<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

use Hanzo\Model\CustomersPeer;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $page = CmsPeer::getFrontpage(Hanzo::getInstance()->get('core.locale'));

        return $this->forward('HanzoCMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

    public function viewAction($id, $page = NULL)
    {
        $locale = Hanzo::getInstance()->get('core.locale');

        if ($page instanceof Cms) {
            $type = $page->getType();
        } else {
            $page = CmsPeer::getByPK($id, $locale);
            $type = 'pages';

            if (is_null($page)) {
                throw $this->createNotFoundException('The page does not exist (id: '.$id.' )');
            }
        }

        $this->get('twig')->addGlobal('page_type', $type);
        return $this->render('HanzoCMSBundle:Default:view.html.twig', array('page' => $page));
    }

    public function testAction()
    {
        return $this->response('1.2.3 ... test');
    }

}

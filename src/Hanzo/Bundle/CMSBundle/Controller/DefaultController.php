<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $hanzo = Hanzo::getInstance();
        $page = CmsPeer::getFrontpage($hanzo->get('core.locale'));

        $this->setSharedMaxAge(86400);
        return $this->forward('CMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

    public function viewAction($id, $page = NULL)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');

        if ($page instanceof Cms) {
            $type = $page->getType();
        } else {
            $page = CmsPeer::getByPK($id, $locale);
            $type = 'pages';

            if (is_null($page)) {
                throw $this->createNotFoundException('The page does not exist (id: '.$id.' )');
            }
        }

        // access check - should be done better tho...
        if ((10 == $page->getCmsThreadId()) && !$this->get('security.context')->isGranted('ROLE_CONSULTANT') && !$this->get('security.context')->isGranted('ROLE_EMPLOYEE') ) {
            return $this->redirect($this->generateUrl('_homepage', ['_locale' => $locale]));
        }

        // TODO: figure out wether this still is an issue or ....
        $html = $page->getContent();
        $find = '~(background|src)="(../|/)~';
        $replace = '$1="' . $hanzo->get('core.cdn');
        $html = preg_replace($find, $replace, $html);
        $page->setContent($html);

        $this->setSharedMaxAge(86400);
        return $this->render('CMSBundle:Default:view.html.twig', array(
            'page_type' => $type,
            'body_classes' => 'body-'.$type,
            'page' => $page,
            'parent_id' => $page->getParentId(),
            'browser_title' => $page->getTitle()
        ));
    }

    public function blockAction($page = NULL)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');
        $route = $this->get('request')->get('_route');
        die(print_r($route));
        if(!$page instanceof Cms){
            $page = CmsPeer::getByPK(1);
        }
        return $this->render('CMSBundle:Default:view.html.twig', array('page_type' => $type, 'page' => $page), $response);
    }
}

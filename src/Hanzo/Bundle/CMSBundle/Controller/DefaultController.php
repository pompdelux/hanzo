<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

class DefaultController extends CoreController
{
    public function indexAction(Request $request)
    {
        $hanzo = Hanzo::getInstance();
        $page = CmsPeer::getFrontpage($hanzo->get('core.locale'));

        // Be able to preview an revision. Only for admins!!!
        if ($request->query->get('revision') && in_array($this->getRequest()->getHost(), array('admin.pompdelux.com', 'www.testpompdelux.com', 'pdl.ab'))) {
            $revision_service = $this->get('cms_revision');
            $page = $revision_service->getRevision($page, $request->query->get('revision'));
            $page->setLocale($hanzo->get('core.locale'));
            $page->is_revision = true;
        }

        if (!isset($page->is_revision)) {
            $this->setSharedMaxAge(86400);
        }
        return $this->forward('CMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

    public function viewAction(Request $request, $id, $page = NULL)
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

        // Be able to preview an revision. Only for admins!!!
        if ($request->query->get('revision') && in_array($this->getRequest()->getHost(), array('admin.pompdelux.com', 'testpompdelux.com', 'pdl.ab'))) {
            $revision_service = $this->get('cms_revision');
            $page = $revision_service->getRevision($page, $request->query->get('revision'));
            $page->setLocale($hanzo->get('core.locale'));
            $page->is_revision = true;
        }

        // access check - should be done better tho...
        if ((10 == $page->getCmsThreadId()) && !$this->get('security.context')->isGranted('ROLE_CONSULTANT') && !$this->get('security.context')->isGranted('ROLE_EMPLOYEE') ) {
            return $this->redirect($this->generateUrl('_homepage', ['_locale' => $locale]));
        }

        // Add any extra classes from the cms settings.
        $settings = $page->getSettings(null, false);
        $class = '';
        if (isset($settings->class)) {
            $class = $settings->class;
        }

        // TODO: figure out wether this still is an issue or ....
        $html = $page->getContent();
        $find = '~(background|src)="(../|/)~';
        $replace = '$1="' . $hanzo->get('core.cdn');
        $html = preg_replace($find, $replace, $html);
        $page->setContent($html);

        if (!isset($page->is_revision)) {
            $this->setSharedMaxAge(86400);
        }

        return $this->render('CMSBundle:Default:view.html.twig', array(
            'page_type' => $type,
            'body_classes' => 'body-'.$type . ' body-page-' . $id . ' ' . $class,
            'page' => $page,
            'embedded_content' => $this->getEmbeddedContent($page),
            'parent_id' => ($page->getParentId()) ? $page->getParentId() : $id,
            'browser_title' => $page->getTitle(),
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

    protected function getEmbeddedContent(Cms $page)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');

        $html = '';
        // Get any embedded cms/categories.
        $settings = $page->getSettings(null, false);

        if (isset($settings->embedded_page_id) && is_numeric($settings->embedded_page_id)) {

            $category = $this->forward('CategoryBundle:Default:listCategoryProducts', array('cms_id' => $settings->embedded_page_id, 'show' => 'look'));

            $html = $category->getContent();
        }

        return $html;
    }
}

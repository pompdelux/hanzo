<?php

namespace Hanzo\Bundle\CMSBundle\Controller;


use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\CMSBundle
 */
class DefaultController extends CoreController
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $hanzo = Hanzo::getInstance();
        $page = CmsPeer::getFrontpage($hanzo->get('core.locale'));

        // Be able to preview an revision. Only for admins!!!
        if ($request->query->get('revision') && in_array($request->getHost(), ['admin.pompdelux.com', 'www.testpompdelux.com', 'pdl.ab'])) {
            $revisionService = $this->get('cms_revision');
            $page = $revisionService->getRevision($page, $request->query->get('revision'));
            $page->setLocale($hanzo->get('core.locale'));
            $page->is_revision = true;
        }

        if (!isset($page->is_revision)) {
            $this->setSharedMaxAge(86400);
        }
        return $this->forward('CMSBundle:Default:view', [
            'id'  => null,
            'page' => $page
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     * @param null    $page
     *
     * @return Response
     * @throws \Exception
     */
    public function viewAction(Request $request, $id, $page = null)
    {
        if ($page instanceof Cms) {
            $type = $page->getType();
        } else {
            $page = CmsPeer::getByPK($id, $request->getLocale());
            $type = 'pages';

            if (is_null($page)) {
                throw $this->createNotFoundException('The page does not exist (id: '.$id.' )');
            }
        }

        // Be able to preview an revision. Only for admins!!!
        if ($request->query->get('revision') && in_array($request->getHost(), ['admin.pompdelux.com', 'www.testpompdelux.com', 'pdl.ab'])) {
            $revisionService = $this->get('cms_revision');
            $page            = $revisionService->getRevision($page, $request->query->get('revision'));
            $page->setLocale($request->getLocale());
            $page->is_revision = true;
        }

        // access check - should be done better tho...
        if ((10 == $page->getCmsThreadId()) && !$this->get('security.context')->isGranted('ROLE_CONSULTANT') && !$this->get('security.context')->isGranted('ROLE_EMPLOYEE') ) {
            return $this->redirect($this->generateUrl('_homepage', ['_locale' => $request->getLocale()]));
        }

        // Add any extra classes from the cms settings.
        $settings = $page->getSettings(null, false);
        $class    = '';

        if (isset($settings->class)) {
            $class = $settings->class;
        }

        // TODO: figure out wether this still is an issue or ....
        $html    = $page->getContent();
        $find    = '~(background|src)="(../|/)~';
        $replace = '$1="' . Hanzo::getInstance()->get('core.cdn');
        $html    = preg_replace($find, $replace, $html);

        $page->setContent($html);

        if (!isset($page->is_revision)) {
            $this->setSharedMaxAge(86400);
        }

        return $this->render('CMSBundle:Default:view.html.twig', [
            'page_type'        => $type,
            'body_classes'     => 'body-' . $type . ' body-page-' . $id . ' ' . $class,
            'page'             => $page,
            'embedded_content' => $this->getEmbeddedContent($page),
            'parent_id'        => ($page->getParentId()) ? $page->getParentId() : $id,
            'browser_title'    => $page->getTitle(),
        ]);
    }

    /**
     * @param null $page
     *
     * @return mixed
     * @throws \Exception
     */
    public function blockAction($page = null)
    {
        $hanzo = Hanzo::getInstance();
        $route = $this->get('request')->get('_route');
        die(print_r($route));

        if (!$page instanceof Cms){
            $page = CmsPeer::getByPK(1);
        }

        return $this->render('CMSBundle:Default:view.html.twig', ['page_type' => $type, 'page' => $page], $response);
    }

    /**
     * @param Cms $page
     *
     * @return string
     * @throws \Exception
     */
    protected function getEmbeddedContent(Cms $page)
    {
        $html = '';
        // Get any embedded cms/categories.
        $settings = $page->getSettings(null, false);

        if (isset($settings->embedded_page_id) && is_numeric($settings->embedded_page_id)) {
            $category = $this->forward('CategoryBundle:Default:listCategoryProducts', ['cms_id' => $settings->embedded_page_id, 'show' => 'look']);

            $html = $category->getContent();
        }
        // Support for multiple arguments
        // Note that the template just is displayed multiple times
        elseif (isset($settings->embedded_page_id) && strpos($settings->embedded_page_id,',') !== false)
        {
            $ids = explode(',', $settings->embedded_page_id);
            if (is_array($ids))
            {
                $ids = array_map('trim', $ids);

                foreach ($ids as $id)
                {
                    $category = $this->forward('CategoryBundle:Default:listCategoryProducts', ['cms_id' => $id, 'show' => 'look']);

                    $html .= $category->getContent();
                }
            }
        }

        return $html;
    }
}

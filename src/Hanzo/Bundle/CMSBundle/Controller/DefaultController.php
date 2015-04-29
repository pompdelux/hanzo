<?php

namespace Hanzo\Bundle\CMSBundle\Controller;


use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Symfony\Component\HttpFoundation\RequestStack;
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
            'embedded_content' => $this->getEmbeddedContent($page, $request),
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
        //die(print_r($route));

        if (!$page instanceof Cms){
            $page = CmsPeer::getByPK(1);
        }

        return $this->render('CMSBundle:Default:view.html.twig', ['page_type' => $type, 'page' => $page], $response);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function jobApplicationCallbackAction(Request $request)
    {
        $json       = json_decode($request->getContent());
        $response   = ['error' => false, 'error_msg' => '', 'msg' => ''];
        $translator = $this->get('translator');
        // Errors
        if (isset($json->errors) && !empty($json->errors)) {
            $response['error'] = true;
            foreach ($json->errors as $error) {
                $response['error_msg'][] = $translator->trans('job_application.error.'.$error->type, [], 'cms');
            }
        }

        // Checks if upload has returned the correct data
        $fields = ['files', 'data'];
        foreach ($fields as $field){
            if (!isset($json->{$field})) {
                $response['error'] = true;
                $response['error_msg'][] = $translator->trans('job_application.error.missing_data', [], 'cms');
            }
        }

        if (true === $response['error']) {
            return $this->json_response($response);
        }

        switch (strtolower($request->getLocale())) {
            case 'da_dk':
                $to = 'mn@bellcom.dk';
                break;
            case 'de_de':
                $to = 'jobde@pompdelux.com';
                break;
            case 'de_ch':
                $to = 'jobch@pompdelux.com';
                break;
            case 'de_at':
                $to = 'jobat@pompdelux.com';
                break;
            case 'fi_fi':
                $to = 'jobfi@pompdelux.com';
                break;
            case 'nl_nl':
                $to = 'jobnl@pompdelux.com';
                break;
            case 'nb_no':
                $to = 'jobno@pompdelux.com';
                break;
            case 'sv_se':
                $to = 'jobse@pompdelux.com';
                break;
        }

        try {
            $mail = $this->container->get('mail_manager');
            $mail->setTo($to, 'Job application');

            if ((isset($json->data->email)) &&
                (filter_var($json->data->email, FILTER_VALIDATE_EMAIL))
            ) {
                $sender = $json->data->email;
                $name   = $json->data->name;

                $mail->setReplyTo($sender, $name)->setSender($sender, $name);
            }

            $mail->setMessage('cms.job_application', [
                'data'  => $json->data,
                'files' => $json->files,
            ]);

            $mail->send();

            $response['msg'] = $translator->trans('job_application.success', [], 'cms');
        } catch (\Exception $e) {

            error_log($e->getMessage());
            $response['error'] = true;
            $response['error_msg'][] = 'Failed sending claim';
        }

        return $this->json_response($response);
    }


    /**
     * @param Cms $page
     *
     * @return string
     * @throws \Exception
     */
    protected function getEmbeddedContent(Cms $page, Request $request)
    {
        $html = '';
        // Get any embedded cms/categories.
        $settings = $page->getSettings(null, false);

        if (isset($settings->embedded_page_id) && is_numeric($settings->embedded_page_id)) {
            $category = $this->forward('CategoryBundle:Default:listCategoryProducts', [
                'cms_id' => $settings->embedded_page_id,
                'show'   => 'look',
                'route'  => $request->get('_route'),
            ]);

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

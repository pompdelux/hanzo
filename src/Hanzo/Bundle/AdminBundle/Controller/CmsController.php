<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Locale\Locale;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bridge\Propel1\Form\Type\TranslationCollectionType;
use Symfony\Bridge\Propel1\Form\Type\TranslationType;
use Symfony\Bridge\Propel1\Form\PropelExtension;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsThreadQuery;
use Hanzo\Model\CmsThreadI18n;
use Hanzo\Model\CmsThreadI18nPeer;
use Hanzo\Model\CmsThreadI18nQuery;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\Redirects;
use Hanzo\Model\RedirectsQuery;
use Hanzo\Model\DomainsQuery;

use Hanzo\Bundle\AdminBundle\Form\Type\CmsType;
use Hanzo\Bundle\AdminBundle\Entity\CmsNode;
use Hanzo\Bundle\AdminBundle\Event\FilterCMSEvent;

use Symfony\Component\HttpFoundation\Request;

class CmsController extends CoreController
{

    public function indexAction($locale)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$locale) {
            $locale = LanguagesQuery::create()->orderById()->findOne($this->getDbConnection())->getLocale();
        }

        $inactive_nodes = CmsQuery::create()
            ->useCmsI18nQuery()
                ->filterByIsActive(false)
            ->endUse()
            ->joinWithI18n($locale)
            ->groupById()
            ->orderById()
            ->find($this->getDbConnection())
        ;

        $languages_availible = LanguagesQuery::Create()
            ->find($this->getDbConnection());

        return $this->render('AdminBundle:Cms:menu.html.twig',
            array(
                'tree'=>$this->getCmsTree(null,null,$locale),
                'inactive_nodes' => $inactive_nodes,
                'languages' => $languages_availible,
                'current_language' => $locale,
                'database' => $this->getRequest()->getSession()->get('database')
            )
        );
    }

    public function deleteAction($id, $locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $cache = $this->get('cache_manager');

        $node = CmsI18nQuery::create()
            ->filterByLocale($locale)
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        if($node instanceof CmsI18n) {
            $node->delete($this->getDbConnection());

            $this->get('event_dispatcher')->dispatch('cms.node.deleted', new FilterCMSEvent($node, $locale));
        }


        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.node.success', array(), 'admin'),
            ));
        }
    }

    public function addAction($locale)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$locale){
            $locale = LanguagesQuery::create()->orderById()->findOne($this->getDbConnection())->getLocale();
        }

        $cms_threads = CmsThreadQuery::create()
            ->joinWithI18n($locale)
            ->find($this->getDbConnection())
        ;

        $cms_thread_choices = array();
        $parent_choices = array();

        foreach ($cms_threads as $cms_thread) {
            $cms_thread_choices[$cms_thread->getId()] = $cms_thread->getTitle();
            $parent_choices[$cms_thread->getId(). ' ' .$cms_thread->getTitle()] = $this->getSelectCms($cms_thread->getId(), $locale);

        }

        $node = new Cms();
        $form = $this->createFormBuilder($node)
            ->add('type', 'choice', array(
                    'label'     => 'cms.edit.label.settings',
                    'choices'   => array(
                        'frontpage'  => 'cms.edit.type.frontpage',
                        'page'  => 'cms.edit.type.page',
                        'url'  => 'cms.edit.type.url',
                        'category'  => 'cms.edit.type.category',
                        'category_search'  => 'cms.edit.type.category_search',
                        'newsletter'  => 'cms.edit.type.newsletter',
                        'advanced_search'  => 'cms.edit.type.advanced_search',
                        'mannequin'  => 'cms.edit.type.mannequin',
                        'bycolour'  => 'cms.edit.type.bycolour',
                        'look'  => 'cms.edit.type.look',
                        'heading'  => 'cms.edit.type.heading'
                    ),
                    'empty_value' => 'Vælg en type',
                    'required'  => TRUE,
                    'translation_domain' => 'admin'
                ))
            ->add('cms_thread_id', 'choice', array(
                    'label' => 'cms.edit.label.cms_thread',
                    'choices' => $cms_thread_choices,
                    'empty_value' => 'Vælg en Thread',
                    'required' => TRUE,
                    'translation_domain' => 'admin'
                ))
            ->add('parent_id', 'choice', array(
                    'label' => 'cms.edit.label.parent_id',
                    'choices' => $parent_choices,
                    'empty_value' => 'Vælg evt. en forældre',
                    'required' => false,
                    'translation_domain' => 'admin'
                ))
            ->getForm();

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                // Validate threadid on choosen parent.
                if ($node->getParentId()) {
                    $parent = CmsQuery::create()
                        ->findPK($node->getParentId());
                    if ($parent) {
                        $node->setCmsThreadId($parent->getCmsThreadId());
                    }
                }

                $settings = array();
                switch ($node->getType()) {
                    case 'category':
                        $node->setType('category');
                        $settings['category_id'] = 'x';
                        // Noget med category_id
                        break;
                    case 'look':
                        $node->setType('look');
                        $settings['category_id'] = 'x';
                        break;
                    case 'category_search':
                        $node->setType('search');
                        $settings['category_ids'] = 'x,y,z';
                        $settings['group'] = '';
                        break;
                    case 'newsletter':
                        $node->setType('newsletter');
                        break;
                    case 'advanced_search':
                        $node->setType('search');
                        $settings['type'] = 'advanced';
                        break;
                    case 'mannequin':
                        $node->setType('mannequin');
                        $settings['category_ids'] = '';
                        $settings['image'] = '';
                        $settings['title'] = '';
                        $settings['colorsheme'] = '';
                        $settings['ignore'] = '';
                        break;
                    case 'bycolour':
                        $node->setType('bycolour');
                        $settings['category_ids'] = 'x,y,z';
                        $settings['colorsheme'] = '';
                        $settings['colors'] = 'x,z';
                        $settings['ignore'] = '';
                        break;
                    case 'frontpage':
                        $node->setType('frontpage');
                        $settings['is_frontpage'] = true;
                        break;
                }

                $node->setUpdatedBy($this->get('security.context')->getToken()->getUser()->getUsername());
                $node->save($this->getDbConnection());

                try {
                    $trans = new CmsI18n();
                    if($node->getType() === 'heading'){
                        $trans->setPath('#');
                    }
                    $trans->setCms($node);
                    $trans->setIsActive(false);
                    $trans->setLocale($locale);
                    if (!empty($settings)) {
                        $trans->setSettings(json_encode($settings));
                    }
                    $trans->save($this->getDbConnection());
                } catch (\Exception $e) {}

                $this->get('session')->getFlashBag()->add('notice', 'cms.added');
                return $this->redirect($this->generateUrl('admin_cms_edit',
                    array(
                        'id' => $node->getId(),
                        'locale' => $locale
                    )
                ));
            }
        }
        return $this->render('AdminBundle:Cms:addcms.html.twig', array(
            'form' => $form->createView(),
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }


    public function editAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $revision_service = $this->get('cms_revision')->setCon($this->getDbConnection());


        $languages_availible = LanguagesQuery::Create()
            ->select('locale')
            ->find($this->getDbConnection());

        $node = CmsQuery::create()
            ->joinWithCmsI18n()
            ->findPK($id, $this->getDbConnection());
        $revision_date = null;
        if ($request->query->get('revision')) {
            $revision = $revision_service->getRevision($node, $request->query->get('revision'));
            if ($revision instanceof Cms) {
                $revision_date = $request->query->get('revision');
                $node = $revision;
            }
        }

        $translations = CmsI18nQuery::create()
            ->filterById($node->getPrimaryKey())
            ->findOneOrCreate($this->getDbConnection());

        $form = $this->createFormBuilder($node, ['data_class' => 'Hanzo\Model\Cms'])
            ->add('cmsI18ns', new \Symfony\Bridge\Propel1\Form\Type\TranslationCollectionType(), array(
                'languages' => array_values($languages_availible->toArray()),
                'label' => 'Oversættelser',
                'label_attr' => ['class' => 'translations-label'],
                'required' => false,
                'type' => new \Symfony\Bridge\Propel1\Form\Type\TranslationType(),
                'options' => array(
                    'columns' => array(
                        'title' => array(
                            'label'              => 'Titel -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                'required' => true,
                                'attr' => ['class' => 'form-title'],
                            ),
                        ),
                        'path' => array(
                            'label'              => 'Sti (URL) -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                'required'           => true,
                                'attr' => ['class' => 'form-path'],
                            ),
                        ),
                        'is_active' => array(
                            'label'              => 'Online -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                // 'label_attr'         => ['class' => 'col-sm-3'],
                            ),
                            'type'               => 'checkbox'
                        ),
                        'is_restricted' => array(
                            'label'              => 'Kræver godkendt IP i offline mode -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                // 'label_attr'         => ['class' => 'col-sm-3'],
                            ),
                            'type'               => 'checkbox'
                        ),
                        'on_mobile' => array(
                            'label'              => 'Vises på mobilsitet -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                // 'label_attr'         => ['class' => 'col-sm-3'],
                            ),
                            'type'               => 'checkbox'
                        ),
                        'content' => array(
                            'label'              => 'Indhold -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                'attr'               => ['rows' => 10],
                            ),
                            'type'               => 'textarea'
                        ),
                        'settings' => array(
                            'label'              => 'Indstillinger -',
                            'options'            => array(
                                'translation_domain' => 'admin',
                                'attr'               => ['rows' => 10, 'class' => 'form-settings'],
                            ),
                            'type'               => 'textarea'
                        ),
                    ),
                    'data_class' => 'Hanzo\Model\CmsI18n',
                )
            ))
            ->add('type', 'choice', array(
                    'label'     => 'Type',
                    'choices'   => array(
                        'frontpage'  => 'cms.edit.type.frontpage',
                        'page'  => 'cms.edit.type.page',
                        'url'  => 'cms.edit.type.url',
                        'category'  => 'cms.edit.type.category',
                        'category_search'  => 'cms.edit.type.category_search',
                        'newsletter'  => 'cms.edit.type.newsletter',
                        'advanced_search'  => 'cms.edit.type.advanced_search',
                        'mannequin'  => 'cms.edit.type.mannequin',
                        'bycolour'  => 'cms.edit.type.bycolour',
                        'look'  => 'cms.edit.type.look',
                        'heading'  => 'cms.edit.type.heading'
                    ),
                    'empty_value' => 'Vælg en type',
                    'required'  => TRUE,
                    'translation_domain' => 'admin'
                ))->getForm();

        // Get parents, to find some good URL of the nodes. If no url on parent,
        // use title.
        $parents = CmsI18nQuery::create()
            ->filterById($node->getParentId())
            ->find($this->getDbConnection())
        ;
        $parent_paths = [];
        foreach ($parents as $parent) {
            if ($parent instanceof CmsI18n) {
                if ($parent->getPath() !== '#') {
                    $parent_paths[$parent->getLocale()] = $parent->getPath();
                } else {
                    $parent_paths[$parent->getLocale()] = $parent->getTitle();
                }
            }
        }

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {

            $is_changed = false;

            $form->handleRequest($request);

            $data = $form->getData();

            $is_active = false;

            foreach ($node->getCmsI18ns() as $translation) {
                if (!$is_changed && $translation->isModified()) {
                    $is_changed = true;
                }
                $path = trim($translation->getPath(),'/');
                // Find dublicate URL'er hvis der er angivet en URL
                $urls = null;
                if($path !== '#' AND !empty($path)){
                    $urls = CmsQuery::create()
                        ->useCmsI18nQuery()
                            ->filterByIsActive(TRUE)
                            ->filterByPath($path)
                            ->filterByLocale($translation->getLocale())
                        ->endUse()
                        ->joinCmsI18n(NULL, 'INNER JOIN')
                        ->where('cms.id <> ?', $node->getId())
                        ->findOne($this->getDbConnection())
                    ;
                    // Findes der ikke nogle med samme url-path _eller_ er node IKKE aktiv
                    if(($urls instanceof Cms) && $translation->getIsActive()) {
                        $form->addError(new FormError($this->get('translator')->trans('cms.update.failed.dublicate.path', ['%url%' => $path], 'admin')));
                    } else {
                        $translation->setPath($path); // Trimmed version
                    }
                    if(!$is_active && $translation->getIsActive()){
                        $is_active = true;
                    }
                }
            }

            if (($is_changed || $node->isModified()) && $form->isValid()) {

                $node->setUpdatedBy($this->get('security.context')->getToken()->getUser()->getUsername());

                // Be sure to change the time. If only the i18n fields are changed
                // it doesnt resolve in an updated time.
                $node->setUpdatedAt(time());
                if ($request->request->get('publish_on_date') && $publish_on_date = \DateTime::createFromFormat('d-m-Y H:i', $request->request->get('publish_on_date'))) {
                    // This should be saved as an revision with a publish date.
                    $new_revision = $revision_service->saveRevision($node, isset($revision_date) ? $revision_date : null, $publish_on_date);

                    $this->get('session')->getFlashBag()->add('notice', 'cms.updated');
                    if (empty($revision_date)) {
                        return $this->redirect($this->generateUrl('admin_cms_edit', array('id' => $node->getId(), 'revision' => $new_revision->getCreatedAt())));
                    }
                } else {
                    $node->save($this->getDbConnection());
                    $revision_service->saveRevision($node);

                    if($is_active){
                        $cache = $this->get('cache_manager');
                        $cache->clearRedisCache();
                    }

                    $this->get('session')->getFlashBag()->add('notice', 'cms.updated');
                    foreach ($node->getCmsI18ns() as $translation) {
                        $this->get('event_dispatcher')->dispatch('cms.node.updated', new FilterCMSEvent($node, $translation->getLocale(), $this->getDbConnection()));
                    }
                }

            }
        }

        $settings = json_decode($node->getSettings(false));

        return $this->render('AdminBundle:Cms:editcmsi18n.html.twig', array(
            'form'      => $form->createView(),
            'node'      => $node,
            'revision' => isset($revision) ? $revision : null,
            'revision_date' => isset($revision_date) ? $revision_date : null,
            'revisions' => $revision_service->getRevisions($node),
            'publish_revisions' => $revision_service->getRevisions($node, true),
            'languages' => $languages_availible,
            'paths'      => json_encode($parent_paths),
            'database' => $this->getRequest()->getSession()->get('database'),
            'is_frontpage' => isset($settings->is_frontpage) ? (bool) $settings->is_frontpage : false
        ));

    }

    public function updateCmsTreeAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $requests = $this->get('request');
        $nodes = $requests->get('data');

        // $sort: Array to keep track on the sort number associated with the parent id.
        // NestedSortable jQuery Plugin doesnt have a sort number, but the array are sorted.
        $sort = array();
        $cms_thread = null;
        foreach ($nodes as $node) {
            if ("null" == $node['item_id']) {
                continue; // Root item from nestedSortable is not a page
            }

            if ("null" == $node['parent_id']) { // Its a cms_thread
                $cms_thread = substr($node['item_id'],1);
                continue;
            }

            if (empty($sort[$cms_thread])) {
                // Init the sort number to 1 if its not already is set
                $sort[$cms_thread] = 1;
            } else {
                // If sort number are set, increment it
                $sort[$cms_thread]++;
            }

            $cmsNode = CmsQuery::create()->findOneById($node['item_id'], $this->getDbConnection());

            if (substr($node['parent_id'],0,1) == 't') {
                // Its a top level cms page. It has no parent_id. This parent_id is the id of which cms_thread
                $cmsNode->setParentId(null);
            } else {
                // Its a normal page with a parent
                $cmsNode->setParentId($node['parent_id']);
            }

            $cmsNode->setSort($sort[$cms_thread]);
            $cmsNode->setCmsThreadId($cms_thread);
            $cmsNode->save($this->getDbConnection());
        }

        $this->get('event_dispatcher')->dispatch('cms.node.moved', new FilterCMSEvent($cmsNode, null, $this->getDbConnection()));

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin'),
            ));
        }
    }

    public function redirectsIndexAction($domain_key)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $redirects = RedirectsQuery::create();

        if($domain_key){
            $redirects = $redirects->filterByDomainKey($domain_key);
        }

        $redirects = $redirects->orderByDomainKey()
            ->orderBySource()
            ->orderByTarget()
            ->find($this->getDbConnection())
        ;

        $domains_availible = DomainsQuery::Create()
            ->find($this->getDbConnection())
        ;
        return $this->render('AdminBundle:Cms:redirectsIndex.html.twig', array(
            'redirects' => $redirects,
            'domains_availible' => $domains_availible,
            'domain_key' => $domain_key,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function redirectEditAction($id = null)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $redirect = null;

        if($id)
            $redirect = RedirectsQuery::create()
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
        else{
            $redirect = new Redirects();
        }

        $domains_availible = DomainsQuery::Create()
            ->find($this->getDbConnection())
        ;
        $domains = array();
        foreach ($domains_availible as $domain) {
            $domains[$domain->getDomainKey()] = $domain->getDomainKey();
        }
        $form = $this->createFormBuilder($redirect)
            ->add('domain_key', 'choice',
                array(
                    'choices' => $domains,
                    'label' => 'admin.cms.redirects.domain_key',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('source', 'text',
                array(
                    'label' => 'admin.cms.redirects.source',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('target', 'text',
                array(
                    'label' => 'admin.cms.redirects.target',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $redirect->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.cms.redirects.inserted');
                return $this->redirect($this->generateUrl('admin_cms_redirects'));
            }
        }

        return $this->render('AdminBundle:Cms:redirectEdit.html.twig', array(
            'form' => $form->createView(),
            'redirect' => $redirect,
            'domains_availible' => $domains_availible,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function redirectDeleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $redirect = RedirectsQuery::create()
            ->filtereById($id)
            ->findOne($this->getDbConnection())
        ;

        if($redirect instanceof Redirects){
            $redirect->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.cms.redirects.success', array(), 'admin'),
            ));
        }
    }


    public function adminMenuAction()
    {
        $pages = [
            'admin' => [
                'access' => ['ROLE_ADMIN', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_DESIGN', 'ROLE_EMPLOYEE', 'ROLE_SALES', 'ROLE_STATS'],
                'title' => 'Forside',
            ],
            'admin_statistics' => [
                'access' => ['ROLE_ADMIN', 'ROLE_STATS', 'ROLE_SALES'],
                'title' => 'Salgs statistik',
            ],
            'admin_settings' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Indstillinger',
            ],
            'admin_cms' => [
                'access' => ['ROLE_ADMIN', 'ROLE_MARKETING'],
                'title' => 'CMS',
            ],
            'admin_customers' => [
                'access' => ['ROLE_ADMIN', 'ROLE_CUSTOMERS_SERVICE'],
                'title' => 'Kunder',
            ],
            'admin_consultants' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES'],
                'title' => 'Shopping Advisor',
            ],
            'admin_employees' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES'],
                'title' => 'Medarbejdere',
            ],
            'admin_orders' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES'],
                'title' => 'Ordrer',
            ],
            'admin_products' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_DESIGN'],
                'title' => 'Katalog',
            ],
            'admin_shipping_index' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Fragt',
            ],
            'admin_settings_washing_instructions' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Vaskeanvisninger',
            ],
            'admin_gift_cards' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Gavekort',
            ],
            'admin_coupons' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Rabatkoder',
            ],
            'admin_postalcode' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Postnumre',
            ],
            'admin_helpdesk' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Helpdesk',
            ],
            'admin_tools' => [
                'access' => ['ROLE_ADMIN'],
                'title' => 'Tools',
            ],
        ];

        $roles = $this->get('security.context')->getToken()->getUser()->getRoles();

        $links = '';
        $router = $this->get('router');

        foreach ($pages as $key => $content) {
            foreach ($content['access'] as $value) {
                if (in_array($value, $roles)) {
                    $links .= '<li><a href="'.$router->generate($key).'">'.$content['title'].'</a></li>'."\n";
                    break;
                }
            }
        }

        return $this->response('
              <ul class="nav nav-sidebar">
              '.$links.'
              </ul>
        ');
    }

    public function deleteRevisionAction($id, $timestamp)
    {

        $node = CmsQuery::create()->findPK($id, $this->getDbConnection());

        $revision_service = $this->get('cms_revision')->setCon($this->getDbConnection());

        $revision_service->deleteRevisionFromTimestamp($node, $timestamp);

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => true,
                'message' => 'Revision er nu slettet.',
            ));
        }

        $this->get('session')->getFlashBag()->add('notice', 'Revision er blevet slettet.');

        return $this->redirect($this->generateUrl('admin_cms_edit', array('id' => $id)));
    }


    /**
     * Creates the html for a System Tree of the CMS. Works recursivly.
     *
     * @todo no-recursive: This could be done better, with an left join. How? Too many Propel Calls.
     * @todo remove html from controller and make an array instead.
     *
     * @param int $cms_thread
     * @param int $parent_id The parents ID
     * @param string $locale
     * @return html ordered list
     */
    protected function getCmsTree($cms_thread, $parent_id, $locale)
    {
        $t = $this->get('translator');
        $menu = '';

        if (empty($cms_thread)) { // First level is the CMS_THREAD, next are CMS
            $query = CmsThreadQuery::create()
                ->joinWithI18n($locale)
                ->orderById()
            ;
            $result = $query->find($this->getDbConnection());

            if ($result->count()) {

                $menu .= '<ul id="sortable-list">';
                foreach($result as $record) {

                    $menu .= '<li id="item-t' . $record->getId(). '" class="sortable-item ui-state-disabled top">';
                    $menu .= '<div class="sort-handle record">';
                    $menu .= '<span class="record-id">'.$record->getId().'</span>';
                    $menu .= '<span class="record-title">' . $record->getTitle() . '</span>';
                    $menu .= '</div>';


                    // Retrieve all this nodes leafs/childrens
                    $menu .= $this->getCmsTree($record->getId(), null, $locale);

                    $menu .= '</li>';
                }

                $menu .= '</ul>';

            }
        } else {
            $query = CmsQuery::create()
                ->filterByCmsThreadId($cms_thread)
                ->joinWithI18n($locale, \Criteria::RIGHT_JOIN)
                ->groupById()
                ->orderBySort()
            ;

            if (empty($parent_id)) {
                $query->filterByParentId(NULL, \Criteria::ISNULL);
            }
            else {
                $query->filterByParentId($parent_id);
            }
            $result = $query->find($this->getDbConnection());

            if ($result->count()) {

                $menu .= '<ul>';
                foreach($result as $record) {
                    $inactive = $record->getIsActive()==true ? 'ui-state-enable' :'ui-state-disabled';
                    $menu .= '<li id="item-' . $record->getId(). '" class="sortable-item ' . $record->getType() . ' '.$inactive.'">';
                    $menu .= '<div class="sort-handle record ">';
                    $menu .= '<span class="record-id">'.$record->getId().'</span>';
                    $menu .= '<span class="record-title">' . $record->getTitle() . '</span>';
                    $menu .= '<span class="record-type">' . $record->getType() . '</span>';
                    $menu .= '<div class="actions">';
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_edit', array('id' => $record->getId())) .'" title="' . $t->trans('page.edit', array(), 'admin') . '" class="edit glyphicon glyphicon-edit" title="' . $t->trans('page.edit', [], 'admin') . '"></a>';
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_delete', array('id' => $record->getId(), 'locale' => $record->getLocale())) .'" title="' . $t->trans('page.delete', array(), 'admin') . '" class="delete glyphicon glyphicon-remove-circle" title="' . $t->trans('page.delete', [], 'admin') . '"></a>';
                    $menu .= '</div>';
                    $menu .= '</div>';


                    // Retrieve all this nodes leafs/childrens
                    $menu .= $this->getCmsTree($cms_thread, $record->getId(), $locale);

                    $menu .= '</li>';
                }

                $menu .= '</ul>';

            }
        }
        return $menu;
    }

    /**
     * Creates an single dimension array og cms pages. Used as the options in a select.
     * @param  int    $from_thread the thread
     * @param  string $locale
     * @param  int    $parent      The parent, initial null
     * @param  int    $indention
     * @return array               the array
     */
    protected function getSelectCms($from_thread, $locale, $parent = null, $indention = 0)
    {
        $menu = array();

        $query = CmsQuery::create()
            ->filterByCmsThreadId($from_thread)
            ->joinWithI18n($locale, \Criteria::RIGHT_JOIN)
            ->groupById()
            ->orderBySort()
        ;

        if (empty($parent)) {
            $query->filterByParentId(NULL, \Criteria::ISNULL);
        }
        else {
            $query->filterByParentId($parent);
        }
        $result = $query->find($this->getDbConnection());

        foreach ($result as $cms) {
            $menu[$cms->getId()] = str_repeat('- ', $indention) . $cms->getId(). ' - ' .$cms->getTitle();
            $menu = $menu + $this->getSelectCms($from_thread, $locale, $cms->getId(), $indention + 1);
        }

        return $menu;
    }
}

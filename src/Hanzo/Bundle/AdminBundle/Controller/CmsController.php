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
            $form->bind($request);

            if ($form->isValid()) {
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
                    $trans->setSettings(json_encode($settings));
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


    public function editAction($id, $locale)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$locale) {
            $locale = LanguagesQuery::create()
                ->orderById()
                ->findOne($this->getDbConnection())
                ->getLocale();
        }

        $cache = $this->get('cache_manager');

        $languages_availible = LanguagesQuery::Create()
            ->find($this->getDbConnection());

        $node = CmsQuery::create()
            ->joinWithI18n($locale, 'INNER JOIN')
            ->findPK($id, $this->getDbConnection());

        $is_new = false;
        if ( !($node instanceof Cms)) { // Oversættelsen findes ikke for det givne ID
            $is_new = true;

            // Vi laver en ny Oversættelse. Hent Settings fra en anden og brug dette.
            $settings = CmsI18nQuery::create()
                ->where('cms_i18n.settings IS NOT NULL')
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;

            $node = CmsQuery::create()
                ->findPk($id, $this->getDbConnection());


            if ($node instanceof Cms) {
                $node->setLocale($locale);

                if($settings instanceof CmsI18n){
                    $node->setSettings($settings->getSettings(null, true));
                }
            }
        }

        // Vi skal bruge titel på Thread til Path
        $cms_thread = CmsThreadQuery::create()
            ->joinWithI18n($locale)
            ->filterById($node->getCmsThreadId())
            ->findOne($this->getDbConnection())
        ;
        $parent = CmsQuery::create()
            ->joinWithI18n($locale)
            ->filterById($node->getParentId())
            ->findOne($this->getDbConnection())
        ;

        if ($parent) {
            $parent_path = $parent->getPath();
        }

        if ($parent && (empty($parent_path) || $parent_path === '#')){
            $parent = CmsQuery::create()
                ->joinWithI18n($locale)
                ->filterById($parent->getParentId())
                ->findOne($this->getDbConnection())
            ;
        }

        $form = $this->createFormBuilder($node)
            ->add('locale', 'hidden')
            ->add('is_active', 'checkbox', array(
                'label'     => 'cms.edit.label.is_active',
                'translation_domain' => 'admin',
                'required'  => false
            ))
            ->add('is_restricted', 'checkbox', array(
                'label'     => 'cms.edit.label.is_restricted',
                'translation_domain' => 'admin',
                'required'  => false
            ))
            ->add('on_mobile', 'checkbox', array(
                'label'     => 'cms.edit.label.on_mobile',
                'translation_domain' => 'admin',
                'required'  => false
            ))
            ->add('title', null, array(
                'label'     => 'cms.edit.label.title',
                'required' => TRUE,
                'translation_domain' => 'admin'
            ))
            ->add('path', null, array(
                'label'     => 'cms.edit.label.path',
                'required' => TRUE,
                'translation_domain' => 'admin'
            ))
            ->add('content', 'textarea', array(
                'label'     => 'cms.edit.label.content',
                'required' => FALSE,
                'translation_domain' => 'admin'
            ))
            ->add('settings', 'textarea', array(
                'label'     => 'cms.edit.label.settings',
                'required' => FALSE,
                'translation_domain' => 'admin'
            ))->getForm();

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            $node->setUpdatedBy($this->get('security.context')->getToken()->getUser()->getUsername());

            $data = $form->getData();

            // validate settings, must be json encodable data
            if ($s = $data->getSettings()) {
                $s = json_decode($s);
                if (!$s) {
                    $form->addError(new FormError('Formatet på Indstillinger er ikke korrekt'));
                }
            }

            if ($form->isValid()) {
                $path = trim($node->getPath(),'/');
                // Find dublicate URL'er hvis der er angivet en URL
                $urls = null;
                if($path !== '#' AND !empty($path)){
                    $urls = CmsQuery::create()
                        ->useCmsI18nQuery()
                            ->filterByIsActive(TRUE)
                            ->filterByPath($path)
                        ->endUse()
                        ->joinCmsI18n(NULL, 'INNER JOIN')
                        ->where('cms.id <> ?', $node->getId())
                        ->findOne($this->getDbConnection())
                    ;
                }

                // Findes der ikke nogle med samme url-path _eller_ er node IKKE aktiv
                if( !($urls instanceof Cms) || !$node->getIsActive())
                {
                    $node->setPath($path); // Trimmed version
                    $node->save($this->getDbConnection());

                    if($node->getIsActive()){
                        $cache->clearRedisCache();
                    }

                    $this->get('session')->getFlashBag()->add('notice', 'cms.updated');
                    $this->get('event_dispatcher')->dispatch('cms.node.updated', new FilterCMSEvent($node, $locale, $this->getDbConnection()));
                }
                else // Dublicate url-path
                {
                    $this->get('session')->getFlashBag()->add('notice', 'cms.update.failed.dublicate.path');
                }
            }
        }
        return $this->render('AdminBundle:Cms:editcmsi18n.html.twig', array(
            'form'      => $form->createView(),
            'node'      => $node,
            'languages' => $languages_availible,
            'path'      => ($parent)?$parent->getPath():'',
            'database' => $this->getRequest()->getSession()->get('database')
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
            if("root" == $node['item_id'])
                continue; // Root item from nestedSortable is not a page

            if("root" == $node['parent_id']) { // Its a cms_thread
                $cms_thread = substr($node['item_id'],1);
                continue;
            }
            if (empty($sort[$cms_thread])) // Init the sort number to 1 if its not already is set
                $sort[$cms_thread] = 1;
            else // If sort number are set, increment it
                $sort[$cms_thread]++;

            $cmsNode = CmsQuery::create()->findOneById($node['item_id'], $this->getDbConnection());
            if (substr($node['parent_id'],0,1) == 't') // Its a top level cms page. It has no parent_id. This parent_id is the id of which cms_thread
                $cmsNode->setParentId(null);
            else
                $cmsNode->setParentId($node['parent_id']); // Its a normal page with a parent

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
            $form->bind($request);

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
                'access' => ['ROLE_ADMIN', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE', 'ROLE_SALES', 'ROLE_STATS'],
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
                'title' => 'Konsulenter',
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
                'access' => ['ROLE_ADMIN'],
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
            <nav class="main">
              <ul>
              '.$links.'
              </ul>
            </nav>
        ');
    }


    /**
     * Creates the html for a System Tree of the CMS. Works recursivly.
     * @todo no-recursive: This could be done better, with an left join.
     * How? Too many Propel Calls.
     * @todo revove html from controller and make an array instead.
     * @param $int parent_id The parents ID
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

                    $menu .= '<li id="item-t' . $record->getId(). '" class="sortable-item ui-state-disabled">';
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
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_edit', array('id' => $record->getId(), 'locale' => $record->getLocale())) .'" title="' . $t->trans('page.edit', array(), 'admin') . '" class="edit">' . $t->trans('page.edit', array(), 'admin') . '</a>';
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_delete', array('id' => $record->getId(), 'locale' => $record->getLocale())) .'" title="' . $t->trans('page.delete', array(), 'admin') . '" class="delete">' . $t->trans('page.delete', array(), 'admin') . '</a>';
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
     * @param  int    $parent      The parent, initial null
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

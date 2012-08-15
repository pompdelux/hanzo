<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Locale\Locale;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\Cms,
    Hanzo\Model\CmsPeer,
    Hanzo\Model\CmsQuery,
    Hanzo\Model\CmsThreadQuery,
    Hanzo\Model\CmsThreadI18n,
    Hanzo\Model\CmsThreadI18nPeer,
    Hanzo\Model\CmsThreadI18nQuery,
    Hanzo\Model\CmsI18n,
    Hanzo\Model\CmsI18nQuery,
    Hanzo\Model\LanguagesQuery,
    Hanzo\Model\Redirects,
    Hanzo\Model\RedirectsQuery,
    Hanzo\Model\DomainsQuery;

use Hanzo\Bundle\AdminBundle\Form\Type\CmsType;
use Hanzo\Bundle\AdminBundle\Entity\CmsNode;

class CmsController extends CoreController
{

    public function indexAction($locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$locale)
            $locale = LanguagesQuery::create()->orderById()->findOne($this->getDbConnection())->getLocale();

        $inactive_nodes = CmsQuery::create()
            ->filterByIsActive(FALSE)
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

            // Do a check if all translations are deleted, then delete the Cms
            $numberOfTranslations = CmsI18nQuery::create()
                ->filterById($id)
                ->count($this->getDbConnection());

            if($numberOfTranslations == 0){
                $master = CmsQuery::create()
                    ->findPK($id, $this->getDbConnection());

                if($master instanceof Cms) {
                    $master->delete();
                }
            }
        }

        $cache->clearRedisCache();

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.node.success', array(), 'admin'),
            ));
        }
    }

    public function addAction($locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$locale)
            $locale = LanguagesQuery::create()->orderById()->findOne($this->getDbConnection())->getLocale();

        $cms_node = new CmsNode();

        $cms_threads = CmsThreadQuery::create()
            ->joinWithI18n($locale)
            ->find($this->getDbConnection())
        ;

        $cms_thread_choices = array();

        foreach ($cms_threads as $cms_thread) {
            $cms_thread_choices[$cms_thread->getId()] = $cms_thread->getTitle();
        }
        $form = $this->createFormBuilder($cms_node)
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
                        'mannequin'  => 'cms.edit.type.mannequin'
                    ),
                    'required'  => TRUE,
                    'translation_domain' => 'admin'
                ))
            ->add('cms_thread_id', 'choice', array(
                    'label' => 'cms.edit.label.cms_thread',
                    'choices' => $cms_thread_choices,
                    'required' => TRUE,
                    'translation_domain' => 'admin'
                ))
            ->getForm();

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $node = new Cms();
                $settings = array();
                switch ($cms_node->getType()) {
                    case 'category':
                        $node->setType('category');
                        $settings['category_id'] = 'x';
                        // Noget med category_id
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
                    case 'frontpage':
                        $node->setType('frontpage');
                        $settings['is_frontpage'] = true;
                        break;
                    default:
                        $node->setType($cms_node->getType());
                        break;
                }
                // Vi skal bruge titel på Thread til Path
                $cms_thread = CmsThreadQuery::create()
                    ->joinWithI18n()
                    ->filterById($cms_node->getCmsThreadId())
                    ->findOne($this->getDbConnection())
                ;

                $node->setCmsThreadId($cms_node->getCmsThreadId());
                $node->setPath(Tools::stripText($cms_thread->getTitle()) . '/');

                $node->setIsActive(FALSE);
                $node->setSettings(json_encode($settings));
                $node->save($this->getDbConnection());

                $this->get('session')->setFlash('notice', 'cms.added');
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
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

        if ( !($node instanceof Cms)) { // Oversættelsen findes ikke for det givne ID

            // Vi laver en ny Oversættelse. Hent Settings fra en anden og brug dette.
            $settings = CmsI18nQuery::create()
                ->where('cms_i18n.settings IS NOT NULL')
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;

            $node = CmsQuery::create()
                ->findPk($id, $this->getDbConnection());

            // Vi skal bruge titel på Thread til Path
            $cms_thread = CmsThreadQuery::create()
                ->joinWithI18n($locale)
                ->filterById($node->getCmsThreadId())
                ->findOne($this->getDbConnection())
            ;

            if ($node instanceof Cms) {
                $node->setLocale($locale);
                $node->setPath(Tools::stripText($cms_thread->getTitle()) . '/');

                if($settings instanceof CmsI18n)
                    $node->setSettings($settings->getSettings(null, true));
            }
        }

        $form = $this->createForm(new CmsType(), $node);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

                // Find dublicate URL'er
                $urls = CmsQuery::create()
                    ->useCmsI18nQuery()
                        ->filterByPath($node->getPath())
                    ->endUse()
                    ->joinCmsI18n(NULL, 'INNER JOIN')
                    ->filterByIsActive(TRUE)
                    ->where('cms.id <> ?', $node->getId())
                    //->filterById($node->getId(), Criteria::NOT_EQUAL)
                    ->findOne($this->getDbConnection())
                ;

                // Findes der ikke nogle med samme url-path _eller_ er node IKKE aktiv
                if( !($urls instanceof Cms) || !$node->getIsActive())
                {
                    $node->save($this->getDbConnection());

                    if($node->getIsActive()){
                        $cache->clearRedisCache();
                    }

                    $this->get('session')->setFlash('notice', 'cms.updated');
                }
                else // Dublicate url-path
                {
                    $this->get('session')->setFlash('notice', 'cms.update.failed.dublicate.path');
                }
            }
        }
        return $this->render('AdminBundle:Cms:editcmsi18n.html.twig', array(
            'form'      => $form->createView(),
            'node'      => $node,
            'languages' => $languages_availible,
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
            if (empty($sort[$node['parent_id']])) // Init the sort number to 1 if its not already is set
                $sort[$node['parent_id']] = 1;
            else // If sort number are set, increment it
                $sort[$node['parent_id']]++;

            $cmsNode = CmsQuery::create()->findOneById($node['item_id'], $this->getDbConnection());
            if (substr($node['parent_id'],0,1) == 't') // Its a top level cms page. It has no parent_id. This parent_id is the id of which cms_thread
                $cmsNode->setParentId(null);
            else
                $cmsNode->setParentId($node['parent_id']); // Its a normal page with a parent

            $cmsNode->setSort($sort[$node['parent_id']]);
            $cmsNode->setCmsThreadId($cms_thread);
            $cmsNode->save($this->getDbConnection());
        }

        $cache = $this->get('cache_manager');
        $cache->clearRedisCache();

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin'),
            ));
        }
    }

    public function redirectsIndexAction($domain_key)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
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
            $form->bindRequest($request);

            if ($form->isValid()) {

                $redirect->save($this->getDbConnection());

                $this->get('session')->setFlash('notice', 'admin.cms.redirects.inserted');
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
    /*
     * Alternative method under construction
    protected function getFlatCmsTree()
    {
        // Get all nodes in Cms sorted by SORT and PARENTID
        $query = CmsQuery::create()
            ->filterByIsActive(TRUE)
            ->orderByParentId()
            ->orderBySort()
            ->joinCmsRelatedByParentId('sub')
        ;

        $result = $query->find();
        $menu = array();
        foreach ($result as $record) {
            //$menu[] = getChildren($record);
        }

        return $result;
    }*/

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
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_edit', array('id' => $record->getId())) .'" title="' . $t->trans('page.edit', array(), 'admin') . '" class="edit">' . $t->trans('page.edit', array(), 'admin') . '</a>';
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

}

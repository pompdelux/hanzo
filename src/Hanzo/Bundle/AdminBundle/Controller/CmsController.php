<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use Hanzo\Bundle\AdminBundle\Event\FilterCMSEvent;
use Hanzo\Core\CoreController;
use Hanzo\Model\Cms;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsThreadQuery;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\Redirects;
use Hanzo\Model\RedirectsQuery;
use Hanzo\Model\DomainsQuery;
use Symfony\Bridge\Propel1\Form\Type\TranslationCollectionType;
use Symfony\Bridge\Propel1\Form\Type\TranslationType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CmsController
 *
 * @package Hanzo\Bundle\AdminBundle
 */
class CmsController extends CoreController
{
    /**
     * @param string $locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($locale)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if (!$locale) {
            $locale = LanguagesQuery::create()->orderById()->findOne($this->getDbConnection())->getLocale();
        }

        $inactiveNodes = CmsQuery::create()
            ->useCmsI18nQuery()
                ->filterByIsActive(false)
            ->endUse()
            ->joinWithI18n($locale)
            ->groupById()
            ->orderById()
            ->find($this->getDbConnection());

        $languagesAvailible = LanguagesQuery::Create()
            ->find($this->getDbConnection());

        return $this->render('AdminBundle:Cms:menu.html.twig', [
            'tree'             => $this->getCmsTree(null, null, $locale),
            'inactive_nodes'   => $inactiveNodes,
            'languages'        => $languagesAvailible,
            'current_language' => $locale,
            'database'         => $this->getRequest()->getSession()->get('database')
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     * @param string  $locale
     *
     * @throws \Exception
     * @throws \PropelException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id, $locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $translations = CmsI18nQuery::create()
            ->filterById($id)
            ->find($this->getDbConnection());

        $deleteCms = true;
        foreach ($translations as $translation) {
            if ($translation->getLocale() == $locale) {
                $translation->delete($this->getDbConnection());
                $this->get('event_dispatcher')->dispatch('cms.node.deleted', new FilterCMSEvent($translation, $locale));
            } else {
                // There are other translations. Dont delete the master CMS.
                $deleteCms = false;
            }
        }

        if ($deleteCms) {
            CmsQuery::create()->filterById($id)->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->get('translator')->trans('delete.node.success', [], 'admin'),
            ]);
        }

        $request->getSession()->getFlashBag()->add('notice', $this->get('translator')->trans('delete.node.success', [], 'admin'));

        return $this->redirect($this->generateUrl('admin_cms'));
    }

    /**
     * @param Request $request
     * @param string  $locale
     *
     * @throws \Exception
     * @throws \PropelException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, $locale)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if (!$locale) {
            $locale = LanguagesQuery::create()->orderById()->findOne($this->getDbConnection())->getLocale();
        }

        $cmsThreads = CmsThreadQuery::create()
            ->joinWithI18n($locale)
            ->find($this->getDbConnection());

        $cmsThreadChoices = [];
        $parentChoices = [];

        foreach ($cmsThreads as $cmsThread) {
            $cmsThreadChoices[$cmsThread->getId()] = $cmsThread->getTitle();
            $parentChoices[$cmsThread->getId(). ' ' .$cmsThread->getTitle()] = $this->getSelectCms($cmsThread->getId(), $locale);

        }

        $node = new Cms();
        $form = $this->createFormBuilder($node)
            ->add('type', 'choice', [
                'label'              => 'cms.edit.label.settings',
                'choices'            => $this->getCmsNodeTypes(),
                'empty_value'        => 'Vælg en type',
                'required'           => true,
                'translation_domain' => 'admin'
            ])->add('cms_thread_id', 'choice', [
                'label'              => 'cms.edit.label.cms_thread',
                'choices'            => $cmsThreadChoices,
                'empty_value'        => 'Vælg en Thread',
                'required'           => true,
                'translation_domain' => 'admin'
            ])->add('parent_id', 'choice', [
                'label'              => 'cms.edit.label.parent_id',
                'choices'            => $parentChoices,
                'empty_value'        => 'Vælg evt. en forældre',
                'required'           => false,
                'translation_domain' => 'admin'
            ])->getForm();

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

                $settings = [];
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
                    case 'advisor_map':
                        $settings['country'] = 'Denmark';
                        break;
                }

                $node->setUpdatedBy($this->get('security.context')->getToken()->getUser()->getUsername());
                $node->save($this->getDbConnection());

                try {
                    $trans = new CmsI18n();
                    if ($node->getType() === 'heading') {
                        $trans->setPath('#');
                    }
                    $trans->setCms($node);
                    $trans->setIsActive(false);
                    $trans->setLocale($locale);
                    if (!empty($settings)) {
                        $trans->setSettings(json_encode($settings));
                    }
                    $trans->save($this->getDbConnection());
                } catch (\Exception $e) {
                }

                $this->get('session')->getFlashBag()->add('notice', 'cms.added');

                return $this->redirect($this->generateUrl('admin_cms_edit', [
                    'id'     => $node->getId(),
                    'locale' => $locale
                ]));
            }
        }

        return $this->render('AdminBundle:Cms:addcms.html.twig', [
            'form'     => $form->createView(),
            'database' => $request->getSession()->get('database')
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function editAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $revisionService = $this->get('cms_revision')->setCon($this->getDbConnection());

        $languagesAvailible = LanguagesQuery::Create()
            ->select('locale')
            ->find($this->getDbConnection());

        $node = CmsQuery::create()
            ->joinWithCmsI18n()
            ->findPK($id, $this->getDbConnection());

        $revisionDate = null;
        if ($request->query->get('revision')) {
            $revision = $revisionService->getRevision($node, $request->query->get('revision'));
            if ($revision instanceof Cms) {
                $revisionDate = $request->query->get('revision');
                $node = $revision;
            }
        }

        $form = $this->createFormBuilder($node, ['data_class' => 'Hanzo\Model\Cms'])
            ->add('cmsI18ns', new TranslationCollectionType(), [
                'languages'  => array_values($languagesAvailible->toArray()),
                'label'      => 'Oversættelser',
                'label_attr' => ['class' => 'translations-label'],
                'required'   => false,
                'type'       => new TranslationType(),
                'options'    => [
                    'columns' => [
                        'title' => [
                            'label'              => 'Titel -',
                            'options'            => [
                                'translation_domain' => 'admin',
                                'required'           => true,
                                'attr'               => ['class' => 'form-title'],
                            ],
                        ],
                        'path' => [
                            'label'              => 'Sti (URL) -',
                            'options'            => [
                                'translation_domain' => 'admin',
                                'required'           => true,
                                'attr'               => ['class' => 'form-path'],
                            ],
                        ],
                        'is_active' => [
                            'label'              => 'Online -',
                            'options'            => [
                                'translation_domain' => 'admin',
                            ],
                            'type'               => 'checkbox'
                        ],
                        'is_restricted' => [
                            'label'              => 'Vis på admin...com -',
                            'options'            => [
                                'translation_domain' => 'admin',
                            ],
                            'type'               => 'checkbox'
                        ],
                        'on_mobile' => [
                            'label'              => 'Vises på mobilsitet -',
                            'options'            => [
                                'translation_domain' => 'admin',
                            ],
                            'type'               => 'checkbox'
                        ],
                        'content' => [
                            'label'              => 'Indhold -',
                            'options'            => [
                                'translation_domain' => 'admin',
                                'attr'               => ['rows' => 10],
                            ],
                            'type'               => 'textarea'
                        ],
                        'settings' => [
                            'label'              => 'Indstillinger -',
                            'options'            => [
                                'translation_domain' => 'admin',
                                'attr'               => ['rows' => 10, 'class' => 'form-settings'],
                            ],
                            'type'               => 'textarea'
                        ],
                    ],
                    'data_class' => 'Hanzo\Model\CmsI18n',
                ]
            ])->add('type', 'choice', [
                'label'              => 'Type',
                'choices'            => $this->getCmsNodeTypes(),
                'empty_value'        => 'Vælg en type',
                'required'           => true,
                'translation_domain' => 'admin'
            ])->getForm();

        // Get parents, to find some good URL of the nodes. If no url on parent,
        // use title.
        $parents = CmsI18nQuery::create()
            ->filterById($node->getParentId())
            ->find($this->getDbConnection());

        $parentPaths = [];
        foreach ($parents as $parent) {
            if ($parent instanceof CmsI18n) {
                if ($parent->getPath() !== '#') {
                    $parentPaths[$parent->getLocale()] = $parent->getPath();
                } else {
                    $parentPaths[$parent->getLocale()] = $parent->getTitle();
                }
            }
        }

        if ('POST' === $request->getMethod()) {

            $isActive  = false;
            $isChanged = false;

            $form->handleRequest($request);

            foreach ($node->getCmsI18ns() as $translation) {
                if (!$isChanged && $translation->isModified()) {
                    $isChanged = true;
                }

                $path = trim($translation->getPath(), '/');
                // Find dublicate URL'er hvis der er angivet en URL
                $urls = null;
                if ($path !== '#' and !empty($path)) {
                    $urls = CmsQuery::create()
                        ->useCmsI18nQuery()
                            ->filterByIsActive(true)
                            ->filterByPath($path)
                            ->filterByLocale($translation->getLocale())
                        ->endUse()
                        ->joinCmsI18n(null, 'INNER JOIN')
                        ->where('cms.id <> ?', $node->getId())
                        ->findOne($this->getDbConnection());

                    // Findes der ikke nogle med samme url-path _eller_ er node IKKE aktiv
                    if (($urls instanceof Cms) && $translation->getIsActive()) {
                        $form->addError(new FormError($this->get('translator')->trans('cms.update.failed.dublicate.path', ['%url%' => $path], 'admin')));
                    } else {
                        $translation->setPath($path); // Trimmed version
                    }
                    if (!$isActive && $translation->getIsActive()) {
                        $isActive = true;
                    }
                }
            }

            if (($isChanged || $node->isModified()) && $form->isValid()) {

                $node->setUpdatedBy($this->get('security.context')->getToken()->getUser()->getUsername());

                // Be sure to change the time. If only the i18n fields are changed
                // it doesnt resolve in an updated time.
                $node->setUpdatedAt(time());
                if ($request->request->get('publish_on_date') && ($publishOnDate = \DateTime::createFromFormat('d-m-Y H:i', $request->request->get('publish_on_date')))) {
                    // This should be saved as an revision with a publish date.
                    $newRevision = $revisionService->saveRevision($node, isset($revisionDate) ? $revisionDate : null, $publishOnDate);

                    $this->get('session')->getFlashBag()->add('notice', 'cms.updated');

                    if (empty($revisionDate)) {
                        return $this->redirect($this->generateUrl('admin_cms_edit', ['id' => $node->getId(), 'revision' => $newRevision->getCreatedAt()]));
                    }
                } else {
                    $node->save($this->getDbConnection());
                    $revisionService->saveRevision($node);

                    if ($isActive) {
                        $cache = $this->get('cache_manager');
                        $cache->clearRedisCache();
                    }

                    $this->get('session')->getFlashBag()->add('notice', 'cms.updated');
                    foreach ($node->getCmsI18ns() as $translation) {
                        $this->get('event_dispatcher')->dispatch('cms.node.updated', new FilterCMSEvent($node, $translation->getLocale(), $this->getDbConnection()));
                    }

                    // If this is an old revision. Redirect to the live cms.
                    if (!empty($revisionDate)) {
                        return $this->redirect($this->generateUrl('admin_cms_edit', ['id' => $node->getId()]));
                    }
                }

            }
        }

        $settings = json_decode($node->getSettings(false));

        return $this->render('AdminBundle:Cms:editcmsi18n.html.twig', [
            'form'              => $form->createView(),
            'node'              => $node,
            'revision'          => isset($revision) ? $revision : null,
            'revision_date'     => isset($revisionDate) ? $revisionDate : null,
            'revisions'         => $revisionService->getRevisions($node),
            'publish_revisions' => $revisionService->getRevisions($node, true),
            'languages'         => $languagesAvailible,
            'paths'             => json_encode($parentPaths),
            'database'          => $request->getSession()->get('database'),
            'is_frontpage'      => isset($settings->is_frontpage) ? (bool)$settings->is_frontpage : false
        ]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function updateCmsTreeAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $requests = $this->get('request');
        $nodes    = $requests->get('data');

        // $sort: Array to keep track on the sort number associated with the parent id.
        // NestedSortable jQuery Plugin doesnt have a sort number, but the array are sorted.
        $sort = [];
        $cmsThread = null;
        foreach ($nodes as $node) {
            if ("null" == $node['item_id']) {
                continue; // Root item from nestedSortable is not a page
            }

            // Its a cms_thread
            if ("null" == $node['parent_id']) {
                $cmsThread = substr($node['item_id'], 1);
                continue;
            }

            if (empty($sort[$cmsThread])) {
                // Init the sort number to 1 if its not already is set
                $sort[$cmsThread] = 1;
            } else {
                // If sort number are set, increment it
                $sort[$cmsThread]++;
            }

            $cmsNode = CmsQuery::create()->findOneById($node['item_id'], $this->getDbConnection());

            if (substr($node['parent_id'], 0, 1) == 't') {
                // Its a top level cms page. It has no parent_id. This parent_id is the id of which cms_thread
                $cmsNode->setParentId(null);
            } else {
                // Its a normal page with a parent
                $cmsNode->setParentId($node['parent_id']);
            }

            $cmsNode->setSort($sort[$cmsThread]);
            $cmsNode->setCmsThreadId($cmsThread);
            $cmsNode->save($this->getDbConnection());
        }

        $this->get('event_dispatcher')->dispatch('cms.node.moved', new FilterCMSEvent($cmsNode, null, $this->getDbConnection()));

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->get('translator')->trans('save.changes.success', [], 'admin'),
            ]);
        }
    }

    /**
     * @param Request $request
     * @param string  $domain_key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectsIndexAction(Request $request, $domain_key)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $redirects = RedirectsQuery::create();

        if ($domain_key) {
            $redirects = $redirects->filterByDomainKey($domain_key);
        }

        $redirects = $redirects->orderByDomainKey()
            ->orderBySource()
            ->orderByTarget()
            ->find($this->getDbConnection());

        $domainsAvailible = DomainsQuery::Create()
            ->find($this->getDbConnection());

        return $this->render('AdminBundle:Cms:redirectsIndex.html.twig', [
            'redirects'         => $redirects,
            'domains_availible' => $domainsAvailible,
            'domain_key'        => $domain_key,
            'database'          => $request->getSession()->get('database')
        ]);
    }

    /**
     * @param Request  $request
     * @param int|null $id
     *
     * @throws \Exception
     * @throws \PropelException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function redirectEditAction(Request $request, $id = null)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_MARKETING") or hasRole("ROLE_ADMIN")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $redirect = null;

        if ($id) {
            $redirect = RedirectsQuery::create()
                ->filterById($id)
                ->findOne($this->getDbConnection());
        } else {
            $redirect = new Redirects();
        }

        $domainsAvailible = DomainsQuery::Create()
            ->find($this->getDbConnection());

        $domains = [];
        foreach ($domainsAvailible as $domain) {
            $domains[$domain->getDomainKey()] = $domain->getDomainKey();
        }

        $form = $this->createFormBuilder($redirect)
            ->add('domain_key', 'choice', [
                'choices'            => $domains,
                'label'              => 'admin.cms.redirects.domain_key',
                'translation_domain' => 'admin',
                'required'           => true
            ])->add('source', 'text', [
                'label'              => 'admin.cms.redirects.source',
                'translation_domain' => 'admin',
                'required'           => true
            ])->add('target', 'text', [
                'label'              => 'admin.cms.redirects.target',
                'translation_domain' => 'admin',
                'required'           => true
            ])->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $redirect->save($this->getDbConnection());
                $this->get('session')->getFlashBag()->add('notice', 'admin.cms.redirects.inserted');

                return $this->redirect($this->generateUrl('admin_cms_redirects'));
            }
        }

        return $this->render('AdminBundle:Cms:redirectEdit.html.twig', [
            'form'              => $form->createView(),
            'redirect'          => $redirect,
            'domains_availible' => $domainsAvailible,
            'database'          => $request->getSession()->get('database')
        ]);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function redirectDeleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $redirect = RedirectsQuery::create()
            ->filtereById($id)
            ->findOne($this->getDbConnection());

        if ($redirect instanceof Redirects) {
            $redirect->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->get('translator')->trans('delete.cms.redirects.success', [], 'admin'),
            ]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminMenuAction()
    {
        $pages = [
            'admin' => [
                'access' => [
                    'ROLE_ADMIN',
                    'ROLE_CUSTOMERS_SERVICE',
                    'ROLE_DESIGN',
                    'ROLE_EMPLOYEE',
                    'ROLE_SALES',
                    'ROLE_STATS'
                ],
                'title'  => 'Forside',
            ],
            'admin_statistics' => [
                'access' => ['ROLE_ADMIN', 'ROLE_STATS', 'ROLE_SALES'],
                'title'  => 'Salgs statistik',
            ],
            'admin_settings' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Indstillinger',
            ],
            'admin_cms' => [
                'access' => ['ROLE_ADMIN', 'ROLE_MARKETING'],
                'title'  => 'CMS',
            ],
            'admin_customers' => [
                'access' => ['ROLE_ADMIN', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_LOGISTICS'],
                'title'  => 'Kunder',
            ],
            'admin_consultants' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES'],
                'title'  => 'Shopping Advisor',
            ],
            'admin_employees' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES'],
                'title'  => 'Medarbejdere',
            ],
            'admin_orders' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_LOGISTICS'],
                'title'  => 'Ordrer',
            ],
            'admin_products' => [
                'access' => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_DESIGN'],
                'title'  => 'Katalog',
            ],
            'admin_shipping_index' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Fragt',
            ],
            'admin_settings_washing_instructions' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Vaskeanvisninger',
            ],
            'admin_gift_cards' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Gavekort',
            ],
            'admin_coupons' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Rabatkoder',
            ],
            'admin_postalcode' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Postnumre',
            ],
            'admin_helpdesk' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Helpdesk',
            ],
            'admin_tools' => [
                'access' => ['ROLE_ADMIN'],
                'title'  => 'Tools',
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

    /**
     * @param int   $id
     * @param mixed $timestamp
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteRevisionAction($id, $timestamp)
    {
        $node = CmsQuery::create()->findPK($id, $this->getDbConnection());
        $revisionService = $this->get('cms_revision')->setCon($this->getDbConnection());
        $revisionService->deleteRevisionFromTimestamp($node, $timestamp);

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status' => true,
                'message' => 'Revision er nu slettet.',
            ]);
        }

        $this->get('session')->getFlashBag()->add('notice', 'Revision er blevet slettet.');

        return $this->redirect($this->generateUrl('admin_cms_edit', array('id' => $id)));
    }

    /**
     * Creates the html for a System Tree of the CMS. Works recursivly.
     *
     * @param int    $cms_thread
     * @param int    $parent_id
     * @param string $locale
     *
     * @return string ordered list
     *
     * @todo no-recursive: This could be done better, with an left join. How? Too many Propel Calls.
     * @todo remove html from controller and make an array instead.
     */
    protected function getCmsTree($cms_thread, $parent_id, $locale)
    {
        $t = $this->get('translator');
        $menu = '';

        // First level is the CMS_THREAD, next are CMS
        if (empty($cms_thread)) {
            $query = CmsThreadQuery::create()
                ->joinWithI18n($locale)
                ->orderById();

            $result = $query->find($this->getDbConnection());

            if ($result->count()) {
                $menu .= '<ul id="sortable-list">';
                foreach ($result as $record) {

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
                ->orderBySort();

            if (empty($parent_id)) {
                $query->filterByParentId(null, \Criteria::ISNULL);
            } else {
                $query->filterByParentId($parent_id);
            }
            $result = $query->find($this->getDbConnection());

            if ($result->count()) {
                $menu .= '<ul>';
                foreach ($result as $record) {
                    $inactive = $record->getIsActive()==true ? 'ui-state-enable' : 'ui-state-disabled';
                    $menu .= '<li id="item-' . $record->getId(). '" class="sortable-item ' . $record->getType() . ' '.$inactive.'">';
                    $menu .= '<div class="sort-handle record ">';
                    $menu .= '<span class="record-id">'.$record->getId().'</span>';
                    $menu .= '<span class="record-title">' . $record->getTitle() . '</span>';
                    $menu .= '<span class="record-type">' . $record->getType() . '</span>';
                    $menu .= '<div class="actions">';
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_edit', array('id' => $record->getId())) .'" title="' . $t->trans('page.edit', [], 'admin') . '" class="edit glyphicon glyphicon-edit" title="' . $t->trans('page.edit', [], 'admin') . '"></a>';
                    $menu .= '<a href="'. $this->get('router')->generate('admin_cms_delete', array('id' => $record->getId(), 'locale' => $record->getLocale())) .'" title="' . $t->trans('page.delete', [], 'admin') . '" class="delete glyphicon glyphicon-remove-circle" title="' . $t->trans('page.delete', [], 'admin') . '"></a>';
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
     *
     * @param int    $from_thread the thread
     * @param string $locale      locale
     * @param int    $parent      The parent, initial null
     * @param int    $indention   level of indention
     *
     * @return array  the array
     */
    protected function getSelectCms($from_thread, $locale, $parent = null, $indention = 0)
    {
        $menu = [];

        $query = CmsQuery::create()
            ->filterByCmsThreadId($from_thread)
            ->joinWithI18n($locale, \Criteria::RIGHT_JOIN)
            ->groupById()
            ->orderBySort();

        if (empty($parent)) {
            $query->filterByParentId(null, \Criteria::ISNULL);
        } else {
            $query->filterByParentId($parent);
        }
        $result = $query->find($this->getDbConnection());

        foreach ($result as $cms) {
            $menu[$cms->getId()] = str_repeat('- ', $indention) . $cms->getId(). ' - ' .$cms->getTitle();
            $menu = $menu + $this->getSelectCms($from_thread, $locale, $cms->getId(), $indention + 1);
        }

        return $menu;
    }

    /**
     * @return array
     */
    private function getCmsNodeTypes()
    {
        return [
            'frontpage'          => 'cms.edit.type.frontpage',
            'page'               => 'cms.edit.type.page',
            'url'                => 'cms.edit.type.url',
            'newsletter'         => 'cms.edit.type.newsletter',
            'category'           => 'cms.edit.type.category',
            'category_search'    => 'cms.edit.type.category_search',
            'advanced_search'    => 'cms.edit.type.advanced_search',
            'search'             => 'Størrelse/kategori søgning',
            'mannequin'          => 'cms.edit.type.mannequin',
            'bycolour'           => 'cms.edit.type.bycolour',
            'look'               => 'cms.edit.type.look',
            'heading'            => 'cms.edit.type.heading',
            'advisor_finder'     => 'Find konsulent',
            'advisor_map'        => 'Konsulenter på kort',
            'advisor_open_house' => 'Åbenthus arrangementer',
        ];
    }
}

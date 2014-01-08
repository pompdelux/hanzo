<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesI18nQuery;
use Hanzo\Model\CategoriesI18n;
use Hanzo\Model\LanguagesQuery;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Bundle\AdminBundle\Event\FilterCategoryEvent;

class CategoryController extends CoreController
{

    public function indexAction()
    {
        return $this->render('AdminBundle:Default:default.html.twig');
    }

    public function addCategoryAction($id, $locale = null)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $languages_availible = array();
        $languages = LanguagesQuery::create()->find($this->getDbConnection());
        foreach ($languages as $language) {
            $languages_availible[$language->getLocale()] = $language->getLocale();
        }

        $categories_i18n = null;

        if($locale)
            $categories_i18n = CategoriesI18nQuery::create()
                ->filterByLocale($locale)
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
        else
            $categories_i18n = new CategoriesI18n();

        $form_add = $this->createFormBuilder($categories_i18n)
            ->add('locale', 'choice',
                array(
                    'choices' => $languages_availible,
                    'label' => 'admin.category.locale.label',
                    'translation_domain' => 'admin'
                )
            )->add('title', 'text',
                array(
                    'label' => 'admin.category.title.label',
                    'translation_domain' => 'admin'
                )
            )->add('content', 'textarea',
                array(
                    'label' => 'admin.category.content.label',
                    'translation_domain' => 'admin',
                    'required' => false,
                )
            )->getForm()
        ;
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form_add->bind($request);

            if ($form_add->isValid()) {

                $categories_i18n->setId($id);
                $categories_i18n->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'category.updated');
            }
        }
        return $this->redirect($this->generateUrl('admin_category_edit', array('id' => $id)));
    }


    /**
     * @param locale the locale to change the translation of the category
     * @param id the id of the category
     */
    public function editCategoryAction($id = null, $locale = null)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $languages_availible = array();
        $languages = LanguagesQuery::create()->find($this->getDbConnection());
        foreach ($languages as $language) {
            $languages_availible[$language->getLocale()] = $language->getLocale();
        }

        $category = null;
        if($id)
            $category = CategoriesQuery::create()->findOneById($id, $this->getDbConnection());
        else
            $category = new Categories();

        $parent_categories = CategoriesQuery::create()
            ->where('categories.PARENT_ID IS NULL')
            ->joinWithCategoriesI18n('da_DK')
            ->find($this->getDbConnection())
        ;

        $parent_categories_data = array('null' => '--');
        foreach ($parent_categories as $parent_category) {
            $parent_categories_data[$parent_category->getId()] = $parent_category->getTitle();
        }

        $form = $this->createFormBuilder($category)
            ->add('parent_id', 'choice',
                array(
                    'choices' => $parent_categories_data,
                    'label' => 'admin.category.parent_id.label',
                    'translation_domain' => 'admin'
                )
            )->add('context', 'text',
                array(
                    'label' => 'admin.category.context.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('is_active', 'checkbox',
                array(
                    'label' => 'admin.category.is_active.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        $categories_i18n = CategoriesI18nQuery::create()
            ->filterById($id)
            ->find($this->getDbConnection())
        ;

        $categories_i18n_to_change = null;

        if($locale) {
            $categories_i18n_to_change = CategoriesI18nQuery::create()
                ->filterByLocale($locale)
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
        } else {
            $categories_i18n_to_change = new CategoriesI18n();
        }

        $form_add = $this->createFormBuilder($categories_i18n_to_change)
            ->add('locale', 'choice',
                array(
                    'choices' => $languages_availible,
                    'label' => 'admin.category.locale.label',
                    'translation_domain' => 'admin'
                )
            )->add('title', 'text',
                array(
                    'label' => 'admin.category.title.label',
                    'translation_domain' => 'admin'
                )
            )->add('content', 'textarea',
                array(
                    'label' => 'admin.category.content.label',
                    'translation_domain' => 'admin',
                    'required' => false,
                )
            )->getForm()
        ;

        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $category->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'category.updated');
                $this->get('event_dispatcher')->dispatch('category.node.updated', new FilterCategoryEvent($category, $locale, $this->getDbConnection()));

                if(!$id) {
                    return $this->redirect($this->generateUrl('admin_category_edit', array('id' => $category->getId())));
                }
            }
        }

        return $this->render('AdminBundle:Categories:view.html.twig', array(
            'form'      => $form->createView(),
            'form_add'  => $form_add->createView(),
            'translations' => $categories_i18n,
            'locale'    => $locale,
            'id'        => $id,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function deleteTranslationAction($id, $locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $categories_i18n = CategoriesI18nQuery::create()
            ->filterByLocale($locale)
            ->filterById($id)
            ->delete($this->getDbConnection())
        ;

        // need a dummy category to handle cache expiration
        $node = new Categories();
        $node->setId($id);

        $this->get('event_dispatcher')->dispatch('category.node.deleted', new FilterCategoryEvent($node, $locale, $this->getDbConnection()));

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.translation.success', array(), 'admin'),
            ));
        }
    }

    public function deleteCategoryAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $categories = CategoriesQuery::create()
            ->filterById($id)
            ->delete($this->getDbConnection())
        ;

        // need a dummy category to handle cache expiration
        $node = new Categories();
        $node->setId($id);

        $this->get('event_dispatcher')->dispatch('category.node.deleted', new FilterCategoryEvent($categories, null, $this->getDbConnection()));

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.category.success', array(), 'admin'),
            ));
        }
    }
}

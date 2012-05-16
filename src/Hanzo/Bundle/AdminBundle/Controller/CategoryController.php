<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Model\CategoriesQuery,
    Hanzo\Model\Categories,
    Hanzo\Model\CategoriesI18nQuery,
    Hanzo\Model\CategoriesI18n,
    Hanzo\Model\LanguagesQuery;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

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
        $languages = LanguagesQuery::create()->find();
        foreach ($languages as $language) {
            $languages_availible[$language->getLocale()] = $language->getLocale();
        }

        $categories_i18n = null;

        if($locale)
            $categories_i18n = CategoriesI18nQuery::create()
                ->filterByLocale($locale)
                ->findOneById($id)
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
            $form_add->bindRequest($request);

            if ($form_add->isValid()) {

                $categories_i18n->setId($id);
                $categories_i18n->save();

                $this->get('session')->setFlash('notice', 'category.updated');
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
        $languages = LanguagesQuery::create()->find();
        foreach ($languages as $language) {
            $languages_availible[$language->getLocale()] = $language->getLocale();
        }

        $category = null;
        if($id)
            $category = CategoriesQuery::create()->findOneById($id);
        else
            $category = new Categories();

        $parent_categories = CategoriesQuery::create()
            ->where('categories.PARENT_ID IS NULL')
            ->joinWithCategoriesI18n('da_DK')
            ->find()
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
            ->find()
        ;
        $categories_i18n_to_change = null;
        if($locale)
            $categories_i18n_to_change = CategoriesI18nQuery::create()
                ->filterByLocale($locale)
                ->findOneById($id)
            ;
        else
            $categories_i18n_to_change = new CategoriesI18n();

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
            $form->bindRequest($request);

            if ($form->isValid()) {

                $category->save();

                $this->get('session')->setFlash('notice', 'category.updated');

                if(!$id)
                    return $this->redirect($this->generateUrl('admin_category_edit', array('id' => $category->getId())));
            }
        }

        return $this->render('AdminBundle:Categories:view.html.twig', array(
            'form'      => $form->createView(),
            'form_add'  => $form_add->createView(),
            'translations' => $categories_i18n,
            'locale'    => $locale,
            'id'        => $id
        ));
    }

    public function deleteTranslationAction($id, $locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $categories_i18n = CategoriesI18nQuery::create()
            ->filterByLocale($locale)
            ->findOneById($id)
        ;

        if($categories_i18n instanceof CategoriesI18n)
            $categories_i18n->delete();

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
            ->findOneById($id)
        ;

        if($categories instanceof Categories)
            $categories->delete();

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.category.success', array(), 'admin'),
            ));
        }
    }
}

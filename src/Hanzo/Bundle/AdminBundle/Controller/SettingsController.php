<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\Settings,
Hanzo\Model\DomainsSettings,
Hanzo\Model\SettingsQuery,
Hanzo\Model\DomainsSettingsQuery,
Hanzo\Model\DomainsQuery,
Hanzo\Model\ProductsWashingInstructions,
Hanzo\Model\ProductsWashingInstructionsQuery,
Hanzo\Model\LanguagesQuery,
Hanzo\Model\Languages,
Hanzo\Model\MessagesQuery,
Hanzo\Model\Messages,
Hanzo\Model\MessagesI18nQuery,
Hanzo\Model\MessagesI18n;

use Hanzo\Bundle\AdminBundle\Form\Type\SettingsType;

class SettingsController extends CoreController
{
    /**
     * Shows all globale settings.
     */
    public function indexAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if ($request->getMethod() == 'POST') {

            $data = $request->request->get('form');
            foreach ($data as $key_ns => $c_value) {

                if('_token' == $key_ns) continue;

                $keys = explode('__',$key_ns);
                $c_key = $keys[0];
                $ns = $keys[1];

                try{
                    $setting = SettingsQuery::create()
                        ->filterByNs($ns)
                        ->filterByCKey($c_key)
                        ->findOne($this->getDbConnection())
                    ;

                    if ($setting && '' === $c_value) {

                        $setting->delete($this->getDbConnection());

                    }else{

                        $setting->setCValue($c_value);
                        $setting->save($this->getDbConnection());

                    }
                }catch(PropelException $e){
                    $this->get('session')->getFlashBag()->add('notice', 'settings.updated.failed.'.$e);
                }
            }
            $this->get('session')->getFlashBag()->add('notice', 'settings.updated');
        }

        $form_add_global_setting = $this->createFormBuilder(new Settings())
            ->add('c_key', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('ns', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('title', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('c_value', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->getForm()
        ;

        // Generate the Form for the global settings excluding some namespaces used other places
        $exclude_ns = array('consultant', 'shipping', 'payment');
        $global_settings = SettingsQuery::create()
            ->orderByNs()
            ->orderByCKey()
            ->where('settings.ns NOT IN ?', $exclude_ns)
            ->find($this->getDbConnection())
        ;

        //Fields names: CKEY__NS << Double underscored
        $global_settings_list = array();
        foreach ($global_settings as $setting) {
            $global_settings_list[$setting->getCKey() . '__' . $setting->getNs()] = $setting->getCValue();
        }

        $form = $this->createFormBuilder($global_settings_list);
        foreach ($global_settings as $setting) {
            $form->add($setting->getCKey() . '__' . $setting->getNs(), 'text', array(
                'label' => $setting->getTitle() . ' (' . $setting->getCKey() . ' - ' . $setting->getNs() . ')',
                'required' => false,
                'label_attr' => ['class' => 'col-sm-2']
            ));
        }

        // End of global settings Form

        $domains_availible = DomainsQuery::Create()
            ->find($this->getDbConnection())
        ;

        return $this->render('AdminBundle:Settings:global.html.twig', array(
            'form'      => $form->getForm()->createView(),
            'add_global_setting_form' => $form_add_global_setting->createView(),
            'domains_availible' => $domains_availible,
            'database' => $request->getSession()->get('database')
        ));
    }

    /**
     * Shows the settings for the chosed domain.
     *
     * @param Request $request
     * @param string  $domain_key The domain key
     * @return Response
     */
    public function domainAction(Request $request, $domain_key)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$domain_key)
            $domain_key = DomainsQuery::create()->orderById()->findOne($this->getDbConnection())->getDomainKey();

        if ($request->getMethod() == 'POST') {

            $data = $request->request->get('form');
            foreach ($data as $key_ns => $c_value) {

                if('_token' == $key_ns) continue;

                $keys = explode('_',$key_ns);

                try{
                    $setting = DomainsSettingsQuery::create()
                        ->filterById($keys[1])
                        ->findOne($this->getDbConnection())
                    ;

                    if ($setting && '' === $c_value) {
                        $setting->delete($this->getDbConnection());
                    } else {
                        $setting->setCValue($c_value);
                        $setting->save($this->getDbConnection());
                    }

                }catch(PropelException $e){
                    $this->get('session')->getFlashBag()->add('notice', 'settings.updated.failed.'.$e);
                }
            }
            $this->get('session')->getFlashBag()->add('notice', 'settings.updated');
        }

        $domains_settings = new DomainsSettings();
        $domains_settings->setDomainKey($domain_key);

        $form_add_domain_setting = $this->createFormBuilder($domains_settings)
            ->add('domain_key', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('c_key', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('ns', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('c_value', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->getForm();

        // Generate the Form for the domain settings
        $exclude_ns = array('consultant', 'shipping', 'payment');

        $domain_settings = DomainsSettingsQuery::create()
            ->where('domains_settings.ns NOT IN ?', $exclude_ns)
            ->filterByDomainKey($domain_key)
            ->orderByNs()
            ->find($this->getDbConnection())
        ;

        $domain_settings_list = array();
        foreach ($domain_settings as $setting) {
            $domain_settings_list['key_'. $setting->getId()] = $setting->getCValue();
        }

        $form = $this->createFormBuilder($domain_settings_list);
        foreach ($domain_settings as $setting) {
            $form->add('key_'. $setting->getId(), 'text',
                array(
                    'required' => false,
                    'label' => $setting->getCKey() . ' - ' . $setting->getNs(),
                    'label_attr' => ['class' => 'col-sm-2']
                )
            );
        }

        // End of domain settings Form

        $domains_availible = DomainsQuery::Create()->find($this->getDbConnection());

        return $this->render('AdminBundle:Settings:domain.html.twig', array(
            'form'      => $form->getForm()->createView(),
            'add_domain_setting_form' => $form_add_domain_setting->createView(),
            'domains_availible' => $domains_availible,
            'domain' => $domain_key,
            'database' => $request->getSession()->get('database')
        ));
    }
    /**
     * Shows the payment and delivery settings for the chosed domain.
     *
     * @param domain_key The domain key
     */
    public function paymentdeliveryAction(Request $request, $domain_key)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        if(!$domain_key)
            $domain_key = DomainsQuery::create()
                ->orderById()
                ->findOne($this->getDbConnection())
                ->getDomainKey()
            ;

        if ($request->getMethod() == 'POST') {

            $data = $request->request->get('form');
            foreach ($data as $key_ns => $c_value) {

                if('_token' == $key_ns) continue;

                $keys = explode('_',$key_ns);

                try{
                    $setting = DomainsSettingsQuery::create()
                        ->filterById($keys[1])
                        ->findOne($this->getDbConnection())
                    ;

                    if ($setting && '' === $c_value) {

                        $setting->delete($this->getDbConnection());

                    }else{

                        $setting->setCValue($c_value);
                        $setting->save($this->getDbConnection());

                    }
                }catch(PropelException $e){
                    $this->get('session')->getFlashBag()->add('notice', 'settings.updated.failed.'.$e);
                }
            }
            $this->get('session')->getFlashBag()->add('notice', 'settings.updated');
        }

        $domains_settings = new DomainsSettings();
        $domains_settings->setDomainKey($domain_key);

        $form_add_domain_setting = $this->createFormBuilder($domains_settings)
            ->add('domain_key', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('c_key', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->add('ns', 'choice', array(
                    'choices' => array('payment' => 'Betaling', 'shipping' => 'Fragt'),
                    'label_attr' => ['class' => 'col-sm-2']
            ))
            ->add('c_value', 'text', [
                'label_attr' => ['class' => 'col-sm-2']
            ])
            ->getForm();

        // Generate the Form for the domain settings
        $include_ns = array('shipping', 'payment');

        $domain_settings = DomainsSettingsQuery::create()
            ->where('domains_settings.Ns IN ?', $include_ns)
            ->filterByDomainKey($domain_key)
            ->orderByNs()
            ->find($this->getDbConnection())
        ;

        $domain_settings_list = array();
        foreach ($domain_settings as $setting) {
            $domain_settings_list['key_'. $setting->getId()] = $setting->getCValue();
        }

        $form = $this->createFormBuilder($domain_settings_list);
        foreach ($domain_settings as $setting) {
            $form->add('key_'. $setting->getId(), 'text',
                array(
                    'required' => true,
                    'label' => $setting->getCKey() . ' - ' . $setting->getNs(),
                    'label_attr' => ['class' => 'col-sm-3']
                )
            );
        }

        // End of domain settings Form

        $domains_availible = DomainsQuery::Create()->find($this->getDbConnection());

        return $this->render('AdminBundle:Settings:paymentdelivery.html.twig', array(
            'form'      => $form->getForm()->createView(),
            'add_domain_setting_form' => $form_add_domain_setting->createView(),
            'domains_availible' => $domains_availible,
            'domain' => $domain_key,
            'database' => $request->getSession()->get('database')
        ));
    }

    /**
     * Function to add new setting to the settings tables. Either a domain specific or a global setting
     *
     * @param domain_setting If the setting are a domain specific setting othervise it will be added to globale settings
     */
    public function addSettingAction(Request $request, $domain_setting = false)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $referer = $request->headers->get('referer');
        $data = $request->request->get('form');
        $c_key = $data['c_key'];
        $ns = $data['ns'];
        $c_value = $data['c_value'];

        if($domain_setting){

            $domain_key = $data['domain_key'];

            $setting = new DomainsSettings();
            $setting->setDomainKey($domain_key);
            $setting->setCKey($c_key);
            $setting->setNs($ns);
            $setting->setCValue($c_value);

            try {
                $setting->save($this->getDbConnection());
            } catch (PropelException $e) {
                $this->get('session')->getFlashBag()->add('notice', 'settings.update.failed');
            }
            return $this->redirect($referer);
            // return $this->redirect($this->generateUrl('admin_settings_domain',
            //     array('domain_key' => $domain_key)
            // ));

        }else{

            $title = $data['title'];

            $setting = new Settings();
            $setting->setCKey($c_key);
            $setting->setNs($ns);
            $setting->setCValue($c_value);
            $setting->setTitle($title);

            try {
                $setting->save($this->getDbConnection());
            } catch (PropelException $e) {
                $this->get('session')->getFlashBag()->add('notice', 'settings.update.failed');
            }

            return $this->redirect($referer);
        }
    }

    public function washingInstructionsIndexAction($code = null, $locale = null)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $washing_instructions = ProductsWashingInstructionsQuery::create();

        if($code)
            $washing_instructions = $washing_instructions->filterByCode($code);

        if($locale)
            $washing_instructions = $washing_instructions->filterByLocale($locale);

        $washing_instructions = $washing_instructions->orderByCode()
            ->find($this->getDbConnection())
        ;

        $codes_availible = ProductsWashingInstructionsQuery::create()
            ->groupByCode()
            ->find($this->getDbConnection());
        $languages_availible = LanguagesQuery::Create()
            ->find($this->getDbConnection());

        return $this->render('AdminBundle:Settings:washing_instructions.html.twig', array(
            'washing_instructions'  => $washing_instructions,
            'languages_availible' => $languages_availible,
            'codes_availible' => $codes_availible,
            'locale' => $locale,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    /**
     * Funktion to edit an instruction.
     * @todo Slet en anvisning
     */
    public function washingInstructionsEditAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $washing_instruction = null;
        if ($id){
            $washing_instruction = ProductsWashingInstructionsQuery::create()
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
        } else {
            $washing_instruction = new ProductsWashingInstructions();
        }

        $languages_availible = LanguagesQuery::Create()->find($this->getDbConnection());

        $languages = array();
        foreach ($languages_availible as $language) {
            $languages[$language->getLocale()] = $language->getName();
        }

        $form = $this->createFormBuilder($washing_instruction)
            ->add('code', 'integer',
                array(
                    'label' => 'admin.washing.code',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('locale', 'choice',
                array(
                    'choices' => $languages,
                    'label' => 'admin.washing.locale',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('description', 'textarea',
                array(
                    'label' => 'admin.washing.description',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'attr' => ['rows' => 10]
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $washing_instruction->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.washing.inserted');
            }
        }

        return $this->render('AdminBundle:Settings:washing_instructionsEdit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function washingInstructionsDeleteAction($id, $locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $washing_instruction = ProductsWashingInstructionsQuery::create()
            ->filterByLocale($locale)
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        if($washing_instruction instanceof ProductsWashingInstructions)
            $washing_instruction->delete($this->getDbConnection());

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.washing_instruction.success', array(), 'admin'),
            ));
        }
    }

    public function messagesIndexAction($ns = null, $locale = null)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $messages = MessagesI18nQuery::create();
        if($locale){
            $messages = $messages
                ->filterByLocale($locale)
            ;
        }

        if($ns)
            $messages = $messages->filterByNs($ns);

        $messages = $messages
            ->useMessagesQuery()
                ->orderByNs()
                ->orderByKey()
            ->endUse()
            ->joinWithMessages()
            ->find($this->getDbConnection())
        ;

        $message_ns_availible = MessagesQuery::create()->find($this->getDbConnection());
        $languages_availible = LanguagesQuery::Create()->find($this->getDbConnection());

        $languages = array();
        foreach ($languages_availible as $language) {
            $languages[$language->getLocale()] = $language->getName();
        }

        return $this->render('AdminBundle:Settings:messagesList.html.twig', array(
            'messages' => $messages,
            'languages_availible' => $languages_availible,
            'message_ns_availible' => $message_ns_availible,
            'locale' => $locale,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function messagesI18nViewAction($id, $locale = null)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $message = null;

        if($locale)
            $message = MessagesI18nQuery::create()
                ->filterByLocale($locale)
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
        if( !($message instanceof MessagesI18n) ) {
            $message = new MessagesI18n();
            $message->setId($id);

            if($locale)
                $message->setLocale($locale);
        }

        $languages_availible = LanguagesQuery::Create()->find($this->getDbConnection());

        $languages = array();
        foreach ($languages_availible as $language) {
            $languages[$language->getLocale()] = $language->getName();
        }

        $form = $this->createFormBuilder($message)
            ->add('locale', 'choice',
                array(
                    'choices' => $languages,
                    'label' => 'admin.messages.locale',
                    'translation_domain' => 'admin',
                    'required' => true,
                )
            )->add('subject', 'text',
                array(
                    'label' => 'admin.messages.subject',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('body', 'textarea',
                array(
                    'label' => 'admin.messages.body',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $message->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.message.inserted');
            }
        }

        return $this->render('AdminBundle:Settings:messageEdit.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function messagesViewNsAction($id = null)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $message = null;

        if ($id) {
            $message = MessagesQuery::create()
                ->findOneById($id, $this->getDbConnection())
            ;
        } else {
            $message = new Messages();
        }

        $form = $this->createFormBuilder($message)
            ->add('ns', 'choice',
                array(
                    'choices' => array('email' => 'E-mail skabelon', 'sms' => 'SMS Skabelon'),
                    'label' => 'admin.messages.ns',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->add('key', 'text',
                array(
                    'label' => 'admin.messages.key',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $message->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.message.ns.inserted');
                return $this->redirect($this->generateUrl('admin_settings_messages_edit', array('id' => $message->getId())));
            }
        }

        return $this->render('AdminBundle:Settings:messageNsEdit.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function messagesDeleteAction($id, $locale)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $message = MessagesI18nQuery::create();

        if($locale){
            $message = $message->filterByLocale($locale);
        }

        $message = $message->filterById($id)->findOne($this->getDbConnection());

        if($message instanceof MessagesI18n){
            $message->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.messages.success', array(), 'admin'),
            ));
        }
    }

    public function languagesAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $language = null;
        if ($id) {
            $language = LanguagesQuery::create()->filterById($id)->findOne($this->getDbConnection());
        }else{
            $language = new Languages();
        }

        $form = $this->createFormBuilder($language)
            ->add('name', 'text',
                array(
                    'label' => 'admin.languages.name',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->add('local_name', 'text',
                array(
                    'label' => 'admin.languages.local_name',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->add('locale', 'text',
                array(
                    'label' => 'admin.languages.locale',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->add('iso2', 'text',
                array(
                    'label' => 'admin.languages.iso2',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->add('direction', 'choice',
                array(
                    'choices' => array('ltr' => 'Left to Right', 'rtl' => 'Right to Left'),
                    'label' => 'admin.languages.direction',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'label_attr' => ['class' => 'col-sm-2']
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $language->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.languages.inserted');
            }
        }

        $languages = LanguagesQuery::create()->find($this->getDbConnection());

        return $this->render('AdminBundle:Settings:languages.html.twig', array(
            'form' => $form->createView(),
            'languages' => $languages,
            'language_id' => $id,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function languagesDeleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $language = LanguagesQuery::create()->filterById($id)->findOne($this->getDbConnection());

        if($language instanceof Languages){
            $language->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.languages.success', array(), 'admin'),
            ));
        }
    }
}

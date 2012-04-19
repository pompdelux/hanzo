<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Model\Settings,
Hanzo\Model\DomainsSettings,
Hanzo\Model\SettingsQuery,
Hanzo\Model\DomainsSettingsQuery,
Hanzo\Model\DomainsQuery,
Hanzo\Model\ProductsWashingInstructions,
Hanzo\Model\ProductsWashingInstructionsQuery,
Hanzo\Model\LanguagesQuery;

use Hanzo\Bundle\AdminBundle\Form\Type\SettingsType;

class SettingsController extends Controller
{
    /**
     * Shows all globale settings.
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {

            $data = $request->get('form');
            foreach ($data as $key_ns => $c_value) {

                if('_token' == $key_ns) continue;

                $keys = explode('__',$key_ns);
                $c_key = $keys[0];
                $ns = $keys[1];

                try{
                    $setting = SettingsQuery::create()
                    ->filterByNs($ns)
                    ->findOneByCKey($c_key);

                    if ($setting && '' === $c_value) {

                        $setting->delete();

                    }else{

                        $setting->setCValue($c_value);
                        $setting->save();

                    }
                }catch(PropelException $e){
                    $this->get('session')->setFlash('notice', 'settings.updated.failed.'.$e);
                }
            }
            $this->get('session')->setFlash('notice', 'settings.updated');
        }

        $form_add_global_setting = $this->createFormBuilder(new Settings())
            ->add('c_key', 'text')
            ->add('ns', 'text')
            ->add('title', 'text')
            ->add('c_value', 'text')
            ->getForm();

        // Generate the Form for the global settings

        $global_settings = SettingsQuery::create()
            ->orderByNs()
            ->find()
        ;

        //Fields names: CKEY__NS << Double underscored
        $global_settings_list = array();
        foreach ($global_settings as $setting) {
            $global_settings_list[$setting->getCKey() . '__' . $setting->getNs()] = $setting->getCValue();
        }

        $form = $this->createFormBuilder($global_settings_list);
        foreach ($global_settings as $setting) {
            $form->add($setting->getCKey() . '__' . $setting->getNs(), 'text',
                array(
                    'label' => $setting->getTitle() . ' (' . $setting->getCKey() . ' - ' . $setting->getNs() . ')',
                    'required' => false
                    )
                );
        }

        // End of global settings Form

        $domains_availible = DomainsQuery::Create()
            ->find()
        ;

        return $this->render('AdminBundle:Settings:global.html.twig', array(
            'form'      => $form->getForm()->createView(),
            'add_global_setting_form' => $form_add_global_setting->createView(),
            'domains_availible' => $domains_availible
        ));
    }

    /**
     * Shows the settings for the chosed domain.
     *
     * @param domain_key The domain key to show example:'DA', default=COM
     */
    public function domainAction($domain_key = 'COM')
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {

            $data = $request->get('form');
            foreach ($data as $key_ns => $c_value) {

                if('_token' == $key_ns) continue;

                $keys = explode('_',$key_ns);
                // $keys = explode('__',$key_ns);
                // $c_key = $keys[0];
                // $ns = $keys[1];
                // $domain_key = $keys[2];

                try{
                    $setting = DomainsSettingsQuery::create()
                        // ->filterByNs($ns)
                        // ->filterByDomainKey($domain_key)
                        // ->findOneByCKey($c_key);
                        ->findOneById($keys[1]);

                    if ($setting && '' === $c_value) {

                        $setting->delete();

                    }else{

                        $setting->setCValue($c_value);
                        $setting->save();

                    }
                }catch(PropelException $e){
                    $this->get('session')->setFlash('notice', 'settings.updated.failed.'.$e);
                }
            }
            $this->get('session')->setFlash('notice', 'settings.updated');
        }

        $domains_settings = new DomainsSettings();
        $domains_settings->setDomainKey($domain_key);

        $form_add_domain_setting = $this->createFormBuilder($domains_settings)
            ->add('domain_key', 'text')
            ->add('c_key', 'text')
            ->add('ns', 'text')
            ->add('c_value', 'text')
            ->getForm();

        // Generate the Form for the domain settings

        $domain_settings = DomainsSettingsQuery::create()
            ->filterByDomainKey($domain_key)
            ->orderByNs()
            ->find()
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
                    'label' => $setting->getCKey() . ' - ' . $setting->getNs()
                )
            );
        }

        // End of domain settings Form

        $domains_availible = DomainsQuery::Create()->find();

        return $this->render('AdminBundle:Settings:domain.html.twig', array(
            'form'      => $form->getForm()->createView(),
            'add_domain_setting_form' => $form_add_domain_setting->createView(),
            'domains_availible' => $domains_availible,
            'domain' => $domain_key
        ));
    }

    /**
     * Function to add new setting to the settings tables. Either a domain specific or a global setting
     *
     * @param domain_setting If the setting are a domain specific setting othervise it will be added to globale settings
     */
    public function addSettingAction($domain_setting = false)
    {
        $request = $this->getRequest();
        $data = $request->get('form');
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
                $setting->save();
            } catch (PropelException $e) {
                $this->get('session')->setFlash('notice', 'settings.update.failed');
            }
            return $this->redirect($this->generateUrl('admin_settings_domain',
                array('domain_key' => $domain_key)
            ));

        }else{

            $title = $data['title'];

            $setting = new Settings();
            $setting->setCKey($c_key);
            $setting->setNs($ns);
            $setting->setCValue($c_value);
            $setting->setTitle($title);

            try {
                $setting->save();
            } catch (PropelException $e) {
                $this->get('session')->setFlash('notice', 'settings.update.failed');
            }

            return $this->redirect($this->generateUrl('admin_settings'));
        }
    }

    public function washingInstructionsIndexAction($code = null, $locale = null)
    {
        $washing_instructions = ProductsWashingInstructionsQuery::create();

        if($code)
            $washing_instructions = $washing_instructions->filterByCode($code);

        if($locale)
            $washing_instructions = $washing_instructions->filterByLocale($locale);

        $washing_instructions = $washing_instructions->orderByCode()
            ->find()
        ;

        $codes_availible = ProductsWashingInstructionsQuery::create()
            ->groupByCode()
            ->find();
        $languages_availible = LanguagesQuery::Create()
            ->find();

        return $this->render('AdminBundle:Settings:washing_instructions.html.twig', array(
            'washing_instructions'  => $washing_instructions,
            'languages_availible' => $languages_availible,
            'codes_availible' => $codes_availible
        ));
    }

    public function washingInstructionsEditAction($id)
    {
        $washing_instruction = null;
        if ($id)
            $washing_instruction = ProductsWashingInstructionsQuery::create()->findOneById($id);
        else
            $washing_instruction = new ProductsWashingInstructions();

        $languages_availible = LanguagesQuery::Create()->find();

        $languages = array();
        foreach ($languages_availible as $language) {
            $languages[$language->getLocale()] = $language->getName();
        }

        $form = $this->createFormBuilder($washing_instruction)
            ->add('code', 'integer', 
                array(
                    'label' => 'admin.washing.instructions.code',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('locale', 'choice', 
                array(
                    'choices' => $languages,
                    'label' => 'admin.washing.instructions.locale',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('description', 'textarea', 
                array(
                    'label' => 'admin.washing.instructions.description',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $washing_instruction->save();

                $this->get('session')->setFlash('notice', 'admin.washing.instruction.inserted');
            }
        }

        return $this->render('AdminBundle:Settings:washing_instructionsEdit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
        ));
    }
}

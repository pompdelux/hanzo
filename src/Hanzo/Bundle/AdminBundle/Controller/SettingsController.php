<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Model\SettingsQuery;

use Hanzo\Bundle\AdminBundle\Form\Type\SettingsType;

class SettingsController extends Controller
{
    
    public function indexAction()
    {
    	$general_settings = SettingsQuery::create()
    		->orderByNs()
    		->find()
    	;

    	$general_settings_list = array();
    	foreach ($general_settings as $setting) {
    		$general_settings_list[$setting->getCKey() . '__' . $setting->getNs()] = $setting->getCValue();
    	}

    	$form = $this->createFormBuilder($general_settings_list)
	        ->add('api_user__dibsapi', 'text')
	        ->add('api_pass__dibsapi', 'text')
	        ->add('merchant__dibsapi', 'text')
	        ->add('md5key1__dibsapi', 'text')
	        ->add('md5key2__dibsapi', 'text')
	        ->add('test__dibsapi', 'text')
	        ->add('from_name__email', 'text')
	        ->add('from_email__email', 'text')
	        ->add('clientID__gothiaapi', 'text')
	        ->add('password__gothiaapi', 'text')
	        ->add('username__gothiaapi', 'text')
	        ->getForm()
	    ;

		$request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            // data is an array with "api_user__dibsapi", "api_pass__dibsapi" keys
            $data = $form->getData();
            foreach ($data as $key_ns => $c_value) {
            	$keys = explode('__',$key_ns);
            	$c_key = $keys[0];
            	$ns = $keys[1];

            	try{
	            	$setting = SettingsQuery::create()
						->filterByNs($ns)
						->findOneByCKey($c_key)
						->setCValue($c_value)
						->save()
					;
				}catch(PropelException $e){
            		$this->get('session')->setFlash('notice', 'settings.updated.failed.'.$e);
				}
            }
            $this->get('session')->setFlash('notice', 'settings.updated');
        }
		return $this->render('AdminBundle:Settings:view.html.twig', array(
			'form'      => $form->createView()
		));
    }

    public function domainAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }
}

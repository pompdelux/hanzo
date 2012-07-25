<?php

namespace Hanzo\Bundle\ConsultantNewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CustomersQuery;

use Hanzo\Bundle\ConsultantNewsletterBundle\ConsultantNewsletterApi;

use Symfony\Component\Finder\Finder;

class DefaultController extends CoreController
{
    
    public function indexAction()
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());
        return $this->render('ConsultantNewsletterBundle:Default:index.html.twig',
        	array(
        		'page_type' => 'consultant-newsletter',
        		'test_receiver' => $consultant->getEmail()
        	)
        );
    }

    public function saveDraftAction()
    {
        $request = $this->getRequest();
        $subject = $request->get('subject');
        $newsletter = $request->get('newsletter');
    	
    }

    public function deleteDraftAction()
    {
        $request = $this->getRequest();
    	
    }

    public function createAdminAction()
    {
    	$api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());
    	if(!$api->doesAdminUserExist($consultant->getEmail())){
    		$admin = array(
    			'loginname' => ,
    			'email' => ,
    			'password' => ,
    			'id' => 
    		);
    	}
    }

    public function sendNewsletterAction()
    {
    	$api = $this->get('consultantnewsletterapi');
    	
        $consultant = CustomersQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());
    	
    	$request = $this->getRequest();
        $test = $request->get('actionSendTest');
        $test_reciever = $request->get('test_reciever');
		$status = $request->get('status');
		$subject  = htmlentities( utf8_decode( $request->get('subject') ) );
		$message  = stripslashes( utf8_decode( $request->get('mail') ) );
		$from     = $this->get('translator')->trans('consultant.newsletter.from.field.%name%.%email%', array('name' => $consultant->getName(), 'email' => $consultant->getEmail()), 'consultant');
		$to       = $consultant->getEmail();
		$replyto  = $consultant->getEmail();
		$template = $request->get('template');
		$lists    = $api->getAdminUserByEmail($consultant->getEmail())->id;
		$status   = ( isset( $status ) ? $status : ConsultantNewsletterApi::STATUS_DRAFT );

    	
    	$admin_user = $api->getAdminUserByEmail( $consultant_email );

    	if(!empty($test)){
    		if(!empty($test_reciever)){
    			$response = $api->sendTestMail(
    				$from,
    				$to,
    				$replyto,
    				$subject,
    				$message,
    				null,
    				$lists,
    				$template,
    				$status,
    				$test_reciever
    			);

    			if($response){
			        if ($this->getFormat() == 'json') {
			            return $this->json_response(array(
			                'status' => TRUE,
			                'message' => $this->get('translator')->trans('consultant.newsletter.test.mail.send.success', array(), 'consultant'),
			            ));
			        }
    			}else{
			        if ($this->getFormat() == 'json') {
			            return $this->json_response(array(
			                'status' => FALSE,
			                'message' => $this->get('translator')->trans('consultant.newsletter.test.mail.send.failed', array(), 'consultant'),
			            ));
			        }
    			}
    		}else{
    			// No test mail receiver
		        if ($this->getFormat() == 'json') {
		            return $this->json_response(array(
		                'status' => FALSE,
		                'message' => $this->get('translator')->trans('consultant.newsletter.test.mail.no.test.mail', array(), 'consultant'),
		            ));
		        }
    		}
    	}else{
		    $response = $api->sendTestMail(
				$from,
				$to,
				$replyto,
				$subject,
				$message,
				null,
				$lists,
				$template,
				$status,
				null,
				null,
				$admin_user->id
			);

			if($response){
		        if ($this->getFormat() == 'json') {
		            return $this->json_response(array(
		                'status' => TRUE,
		                'message' => $this->get('translator')->trans('consultant.newsletter.schedule.newsletter.send.success', array(), 'consultant'),
		            ));
		        }
			}else{
		        if ($this->getFormat() == 'json') {
		            return $this->json_response(array(
		                'status' => FALSE,
		                'message' => $this->get('translator')->trans('consultant.newsletter.schedule.newsletter.send.failed', array(), 'consultant'),
		            ));
		        }
			}
    	}
    }

    public function importUsersAction()
    {
    	return $this->render('ConsultantNewsletterBundle:Default:importUsers.html.twig',
        	array(
        		'page_type' => 'consultant-newsletter'
        	)
        );
    }

    public function doImportUsersAction()
    {
    	$api = $this->get('consultantnewsletterapi');

        $consultant = CustomersQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());
    	
    	$request = $this->getRequest();

        $users = explode("\n", $request->get('users') );
        $list = array( (int) $api->getAdminUserByEmail($consultant->getEmail())->id );

        if(empty($users)){
	        if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	                'status' => FALSE,
	                'message' => $this->get('translator')->trans('consultant.newsletter.import.no.users', array(), 'consultant'),
	            ));
	        }
        }else{
	        foreach ($users as $user) {
	        	if(empty($user))
	        		continue;
	        	$userData = array(
	        		'email_address' => $user
	        	);

		        try{
		          $api->subscribeUser($userData,$list, true );
		        }catch (Exception $e){
			        if ($this->getFormat() == 'json') {
			            return $this->json_response(array(
			                'status' => FALSE,
			                'message' => $e.getMessage(),
			            ));
			        }
		        }
	        }
        	
	        if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	                'status' => TRUE,
	                'message' => $this->get('translator')->trans('consultant.newsletter.import.users.imported', array(), 'consultant'),
	            ));
	        }
        }

    }

    public function fileManagerAction()
    {
    	$finder = new Finder();
    	$finder->files()->in(__DIR__.'/../../../../../web/fx/images');
    	$finder->files()->name('*.jpg');
    	$finder->files()->name('*.png');
    	$finder->files()->name('*.gif');
    	$finder->sortByName();
    	$images = array();

		foreach ($finder as $file) {
		    $images[] = array(
		    	'absolute' 	=> $this->container->get('router')->getContext()->getBaseUrl().'/fx/images/'.$file->getRelativePathname(),
		    	'relative' 	=> '/fx/images/'.$file->getRelativePathname(),
		    	'name'		=> $file->getFilename()
		    );
		}
        return $this->render('ConsultantNewsletterBundle:Default:filemanager.html.twig',
        	array(
        		'images' => $images
        	)
        );
    }
}

<?php

namespace Hanzo\Bundle\ConsultantNewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CustomersQuery;
use Hanzo\Model\ConsultantNewsletterDraftsQuery;
use Hanzo\Model\ConsultantNewsletterDrafts;

use Hanzo\Bundle\ConsultantNewsletterBundle\ConsultantNewsletterApi;

class DefaultController extends CoreController
{
    public function indexAction($draft_id)
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()
            ->findPK($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            )
        ;

        $drafts = ConsultantNewsletterDraftsQuery::create()
            ->filterByConsultantsId($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            )
            ->find()
        ;

        $draft   = null;
        $subject = '';
        $message = '';

        if ($draft_id) {
            $draft = ConsultantNewsletterDraftsQuery::create()
                ->filterByConsultantsId($this->get('security.context')
                    ->getToken()
                    ->getUser()
                    ->getPrimaryKey()
                )
                ->findOneById($draft_id)
            ;
            $subject = $draft->getSubject();
            $message = $draft->getContent();
        }

        $domain = 'pompdelux.com';
        $serverName = $_SERVER['SERVER_NAME'];

        $env = substr( $this->container->getParameter('kernel.environment'), 0, 3 );

        switch ($env) {
          case 'dev':
          case 'tes':
            if (substr($serverName, 0, 1 ) == 'c') {
              $domain = str_replace( 'c.', '', $serverName );
            } else {
              $domain = $serverName;
            }
            break;
        }

        return $this->render('ConsultantNewsletterBundle:Default:index.html.twig', array(
            'page_type'       => 'consultant-newsletter',
            'test_receiver'   => $consultant->getEmail(),
            'drafts'          => $drafts,
            'draft_id'        => $draft_id,
            'subject'         => $subject,
            'message'         => $message,
            'document_domain' => $domain,
        ));
    }

    public function saveDraftAction()
    {
        $request  = $this->getRequest();
        $subject  = $request->request->get('subject');
        $content  = $request->request->get('message');
        $draft_id = $request->request->get('draft_id');

        $draft = null;

        if (!empty($draft_id)) {
            $draft = ConsultantNewsletterDraftsQuery::create()
                ->findPK($draft_id)
            ;
        } else {
            $draft = new ConsultantNewsletterDrafts();
            $draft->setConsultantsId($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            );
        }

        $draft->setSubject($subject);
        $draft->setContent($content);

        if ($draft instanceof ConsultantNewsletterDrafts) {
            $draft->save();
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('consultant.newsletter.draft.saved', array(), 'consultant'),
                ));
            }
        }

    }

    public function deleteDraftAction($draft_id)
    {

        $draft = ConsultantNewsletterDraftsQuery::create()
            ->filterByConsultantsId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
            ->findOneById($draft_id)
        ;

        if ($draft instanceof ConsultantNewsletterDrafts) {
            $draft->delete();
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('consultant.newsletter.draft.deleted', array(), 'consultant'),
            ));
        }
    }

    public function sendNewsletterAction()
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()
            ->findPK($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            )
        ;

        if (!$api->doesAdminUserExist($consultant->getEmail())) {
            $admin = new \stdClass();
            $admin->loginname = $consultant->getName();
            $admin->email = $consultant->getEmail();
            $admin->password = $consultant->getPasswordClear();
            $admin->id = $consultant->getId();

            $access = array();
            $api->addAdminUser( $admin , (object) $access);
        }

        $admin_user = $api->getAdminUserByEmail($consultant->getEmail());
        $lists      = $api->getListsByOwner($admin_user->id);

        if (empty($lists)) {
            $list = new \stdClass();
            $list->name = 'Konsulent '.$consultant->getName();
            $list->description = 'Oprettet til '.$consultant->getName();
            $list->owner = $admin_user->id;
            $list->active = true;

            try {
                $api->createList($list);
            } catch(Exception $e) {
                error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
            }

            $lists = $api->getListsByOwner($admin_user->id);
        }

        $listIds = array();
        foreach ($lists as $list) {
            $listIds[] = $list->id;
        }

        $request       = $this->getRequest();
        $test          = $request->request->get('actionSendTest');
        $test_reciever = $request->request->get('test_reciever');
        $status        = $request->request->get('status');
        $subject       = htmlentities($request->request->get('subject'),ENT_QUOTES,'UTF-8') ;
        $message       = stripslashes( utf8_decode( $request->request->get('message') ) );
        $from          = $consultant->getEmail();
        $to            = $consultant->getEmail();
        $replyto       = $consultant->getEmail();
        $template      = $request->request->get('template');
        $lists         = $listIds;
        $status        = ($status ?: ConsultantNewsletterApi::STATUS_DRAFT);

        if (!empty($test)) {
            if (!empty($test_reciever)) {
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

                if ($response) {
                    if ($this->getFormat() == 'json') {
                        return $this->json_response(array(
                            'status' => TRUE,
                            'message' => $this->get('translator')->trans('consultant.newsletter.test.mail.send.success', array(), 'consultant'),
                        ));
                    }
                } else {
                    if ($this->getFormat() == 'json') {
                        return $this->json_response(array(
                            'status' => FALSE,
                            'message' => $this->get('translator')->trans('consultant.newsletter.test.mail.send.failed', array(), 'consultant'),
                        ));
                    }
                }
            } else {
                // No test mail receiver
                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => FALSE,
                        'message' => $this->get('translator')->trans('consultant.newsletter.test.mail.no.test.mail', array(), 'consultant'),
                    ));
                }
            }
        } else {
            $response = $api->scheduleNewsletter(
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

            if ($response) {
                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => TRUE,
                        'message' => $this->get('translator')->trans('consultant.newsletter.schedule.newsletter.send.success', array(), 'consultant'),
                    ));
                }
            } else {
                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => FALSE,
                        'message' => $this->get('translator')->trans('consultant.newsletter.schedule.newsletter.send.failed', array(), 'consultant'),
                        'data' => $response
                    ));
                }
            }
        }
    }

    public function editUsersAction()
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()
            ->findPK($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            )
        ;

        if (!$api->doesAdminUserExist($consultant->getEmail())) {
            $admin = new \stdClass();
            $admin->loginname = $consultant->getName();
            $admin->email = $consultant->getEmail();
            $admin->password = $consultant->getPasswordClear();
            $admin->id = $consultant->getId();

            $access = array();
            $api->addAdminUser( $admin , (object) $access);
        }

        $admin_user = $api->getAdminUserByEmail($consultant->getEmail());
        $lists      = $api->getListsByOwner($admin_user->id);

        if (empty($lists)) {
            $list = new \stdClass();
            $list->name = 'Konsulent '.$consultant->getName();
            $list->description = 'Oprettet til '.$consultant->getName();
            $list->owner = $admin_user->id;
            $list->active = true;

            try {
                $api->createList($list);
            } catch (Exception $e) {
                error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
            }

            $lists = $api->getListsByOwner($admin_user->id);
        }

        $subscribed_users = $api->getAllUsersSubscribedToList($lists[0]->id);

        return $this->render('ConsultantNewsletterBundle:Default:editUsers.html.twig', array(
            'page_type'        => 'consultant-newsletter',
            'subscribed_users' => $subscribed_users
        ));
    }

    public function unsubscribeUserAction($userId)
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()->findPK($this->get('security.context')->getToken()->getUser()->getPrimaryKey());

        if (!$api->doesAdminUserExist($consultant->getEmail())) {
            $admin            = new \stdClass();
            $admin->loginname = $consultant->getName();
            $admin->email     = $consultant->getEmail();
            $admin->password  = $consultant->getPasswordClear();
            $admin->id        = $consultant->getId();

            $access = array();
            $api->addAdminUser( $admin , (object) $access);
        }

        $admin_user = $api->getAdminUserByEmail( $consultant->getEmail() );
        $lists      = $api->getListsByOwner($admin_user->id);

        if (empty($lists)) {
            $list = new \stdClass();
            $list->name = 'Konsulent '.$consultant->getName();
            $list->description = 'Oprettet til '.$consultant->getName();
            $list->owner = $admin_user->id;
            $list->active = true;
            try{
                $api->createList($list);
            }catch(Exception $e){
                error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
            }
            $lists = $api->getListsByOwner($admin_user->id);
        }

        $api->unSubscribeUser($userId, $lists[0]->id);

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('consultant.newsletter.unsubscribe.user.success', array(), 'consultant'),
            ));
        }

    }

    public function importUsersAction()
    {
        return $this->render('ConsultantNewsletterBundle:Default:importUsers.html.twig', array(
            'page_type' => 'consultant-newsletter'
        ));
    }

    public function doImportUsersAction()
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()
            ->findPK($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            )
        ;

        if (!$api->doesAdminUserExist($consultant->getEmail())) {
            $admin            = new \stdClass();
            $admin->loginname = $consultant->getName();
            $admin->email     = $consultant->getEmail();
            $admin->password  = $consultant->getPasswordClear();
            $admin->id        = $consultant->getId();

            $access = array();
            $api->addAdminUser( $admin , (object) $access);
        }

        $admin_user = $api->getAdminUserByEmail( $consultant->getEmail() );
        $lists      = $api->getListsByOwner($admin_user->id);

        if (empty($lists)) {
            $list              = new \stdClass();
            $list->name        = 'Konsulent '.$consultant->getName();
            $list->description = 'Oprettet til '.$consultant->getName();
            $list->owner       = $admin_user->id;
            $list->active      = true;

            try{
                $api->createList($list);
            }catch(Exception $e){
                error_log(__LINE__.':'.__FILE__.' '.$e->getMessage());
            }

            $lists = $api->getListsByOwner($admin_user->id);
        }

        $listIds = array();
        foreach ($lists as $list) {
            $listIds[] = $list->id;
        }

        $emails = explode("\n", $this->getRequest()->get('users') );

        if (empty($emails)) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $this->get('translator')->trans('consultant.newsletter.import.no.users', array(), 'consultant'),
                ));
            }
        } else {
            foreach ($emails as $email) {
                if (empty($email)) {
                    continue;
                }

                $userData = array(
                    'email_address' => trim($email),
                    'attributes'    => array()
                );

                $api->subscribeUser($userData, $listIds, true );
            }

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('consultant.newsletter.import.users.imported', array(), 'consultant'),
                ));
            }
        }
    }

    public function historyAction()
    {
        $api = $this->get('consultantnewsletterapi');
        $consultant = CustomersQuery::create()
            ->findPK($this->get('security.context')
                ->getToken()
                ->getUser()
                ->getPrimaryKey()
            )
        ;

        if (!$api->doesAdminUserExist($consultant->getEmail())) {
            $admin            = new \stdClass();
            $admin->loginname = $consultant->getName();
            $admin->email     = $consultant->getEmail();
            $admin->password  = $consultant->getPasswordClear();
            $admin->id        = $consultant->getId();

            $access = array();
            $api->addAdminUser($admin , (object) $access);
        }

        $admin_user = $api->getAdminUserByEmail($consultant->getEmail());
        $history    = $api->getNewsletterHistory($admin_user->id);

        // Workaround. Crap content receivet from phplist :-(
        $histSize = count($history);
        for ($i=0; $i < $histSize; $i++) {
            $history[$i]['message'] = htmlspecialchars_decode($history[$i]['message']);
        }

        krsort($history);

        return $this->render('ConsultantNewsletterBundle:Default:history.html.twig', array(
            'page_type' => 'consultant-newsletter',
            'history'   => $history
        ));
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
                'absolute'  => $this->container->get('router')->getContext()->getBaseUrl().'/fx/images/'.$file->getRelativePathname(),
                'relative'  => '/fx/images/'.$file->getRelativePathname(),
                'name'      => $file->getFilename()
            );
        }

        return $this->render('ConsultantNewsletterBundle:Default:filemanager.html.twig', array(
            'images' => $images
        ));
    }
}

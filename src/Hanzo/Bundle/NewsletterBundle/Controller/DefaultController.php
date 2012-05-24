<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Bundle\NewsletterBundle\TestEvents;
use Hanzo\Bundle\NewsletterBundle\FilterTestEvent;

class DefaultController extends CoreController
{
    /**
     * handleAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function handleAction( $action )
    {
        $name     = $this->get('request')->get('name');
        $email    = $this->get('request')->get('email');
        $api      = $this->get('newsletterapi');

        $api->sendNotificationEmail( $action, $email, $name );
        return $this->json_response( array('error' => false) );
    }

    /**
     * jsAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function jsAction()
    {
        $customer = CustomersPeer::getCurrent();
        $api      = $this->get('newsletterapi');
        $listId   = $api->getListIdAvaliableForDomain();

        return $this->render('NewsletterBundle:Default:js.html.twig', array( 
            'customer' => $customer, 
            'listid' => $listId, 
            'newsletter_jsonp_url' => 'http://phplist.pompdelux.dk/integration/json.php?callback=?'
        )
    );
    }

    /**
     * blockAction 
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $customer = CustomersPeer::getCurrent();
        $api      = $this->get('newsletterapi');
        $listId   = $api->getListIdAvaliableForDomain();

        return $this->render('NewsletterBundle:Default:block.html.twig', array( 
            'customer' => $customer, 
            'listid' => $listId, 
            )
        );
    }

    /**
     * viewAction 
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function viewAction($id)
    {
        $page = CmsPeer::getByPK($id, Hanzo::getInstance()->get('core.locale'));

        if (is_null($page)) {
            throw $this->createNotFoundException('The page does not exist (id: '.$id.' )'); // FIXME: translation
        }

        return $this->render('NewsletterBundle:Default:view.html.twig', array( 'page' => $page, 'page_type' => 'newsletter'));
    }
}

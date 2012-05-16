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

    public function indexAction()
    {
        // $event = new FilterTestEvent('hest');
        // $dispatcher = $this->get('event_dispatcher');

        // $dispatcher->dispatch(TestEvents::onHanzoTest, $event);
        return new Response( 'Ok', 200, array('Content-Type' => 'text/html'));
    }

    public function blockAction()
    {
        $api = $this->get('newsletterapi');
        $customer = CustomersPeer::getCurrent();
        return $this->render('NewsletterBundle:Default:block.html.twig', array( 'customer' => $customer, 'listid' => $api->getListIdAvaliableForDomain() ));
    }

    /**
     * jsAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function jsAction()
    {
        $api = $this->get('newsletterapi');
        $customer = CustomersPeer::getCurrent();
        return $this->render('NewsletterBundle:Default:js.html.twig', array( 'customer' => $customer, 'listid' => $api->getListIdAvaliableForDomain() ));
    }

    public function viewAction($id)
    {
        $page = CmsPeer::getByPK($id, Hanzo::getInstance()->get('core.locale'));

        if (is_null($page)) {
            $page = 'implement 404 !';
        }

        return $this->render('NewsletterBundle:Default:view.html.twig', array('page' => $page, 'page_type' => 'newsletter'));
    }
}

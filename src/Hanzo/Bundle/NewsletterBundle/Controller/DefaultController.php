<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventDispatcher
    ;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Model\Customers,
    Hanzo\Model\CustomersPeer,
    Hanzo\Bundle\NewsletterBundle\TestEvents,
    Hanzo\Bundle\NewsletterBundle\FilterTestEvent
    ;

class DefaultController extends CoreController
{

    public function indexAction()
    {
        $event = new FilterTestEvent('hest');
        $dispatcher = $this->get('event_dispatcher');

        /*$dispatcher->addListener('hanzo.test', function (Event $event) {
            error_log(__LINE__.':'.__FILE__.' '.$event->getData()); // hf@bellcom.dk debugging
        });*/

        //$listener = $this->get('hanzo.test_listener');
        //$dispatcher->addListener('hanzo.test', array($listener, 'onTest'));

        $dispatcher->dispatch(TestEvents::onHanzoTest, $event);

        //$api = $this->get('newsletterapi');
        //$api->subscribe( 'hf@bellcom.dk', 1 );

        return new Response( 'Ok', 200, array('Content-Type' => 'text/html'));

        /*return $this->render('NewsletterBundle:Default:index.html.twig', array(
            'page_type' => 'newsletter'
        ));*/
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
}

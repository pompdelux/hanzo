<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\EventDispatcher\Event,
    Symfony\Component\EventDispatcher\EventDispatcher
    ;  

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Bundle\DataIOBundle\Events,
    Hanzo\Bundle\DataIOBundle\FilterUpdateEvent
    ;

class DefaultController extends CoreController
{
    public function indexAction($name)
    {
        return $this->render('DataIOBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * updateSystemAction
     *
     * Recives webhook call from github
     *
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function updateSystemAction()
    {
        $json = json_decode( $this->get('request')->get('payload'));

        if ( is_object($json) && isset($json->commits) )
        {
            foreach ($json->commits as $commit) 
            {
              error_log('[Github Webhook]: commit by '. $commit->author->email.' url: '.$commit->url); // hf@bellcom.dk debugging
            }

            $event = new FilterUpdateEvent( 'translations' );
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(Events::updateTranslations, $event);
            return new Response( 'Ok', 200, array('Content-Type' => 'text/plain') );
        }
        else
        {
            return new Response( 'Could not verify request', 500, array('Content-Type' => 'text/plain') );
        }
    }

    /**
     * testMigrateAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function testMigrateAction( $step )
    {
        $hanzo = Hanzo::getInstance();
        $session = $hanzo->getSession();

        switch ($step) 
        {
          case 1:
              $session->set('order_id');
            break;
          
          case 2:
              $session->remove('order_id');
              $session->migrate();
            break;

          case 3:
              $session->remove('order_id');
              $session->save();
              $session->migrate();
              break;
        }

        $orderId = $session->get('order_id');

        if ( $orderId && $step > 1 )
        {
            return new Response( 'Order id is:'.$session->get('order_id'), 500, array('Content-Type' => 'text/plain') );
        }

        return new Response( 'Order id is:'.$session->get('order_id'), 200, array('Content-Type' => 'text/plain') );
    }

    /**
     * testOrderAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function testOrderAction( $state )
    {
        $order = OrdersPeer::getCurrent();

        switch ($state) 
        {
          case 'empty':
              if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) 
              {
                  return new Response( 'Empty', 200, array('Content-Type' => 'text/plain') );
              }
              else
              {
                  return new Response( 'Not empty', 500, array('Content-Type' => 'text/plain') );
              }
              break;

          case 'full':
              if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) 
              {
                  return new Response( 'Empty', 500, array('Content-Type' => 'text/plain') );
              }
              else
              {
                  return new Response( 'Not empty', 200, array('Content-Type' => 'text/plain') );
              }
              break;
        }
    }
}

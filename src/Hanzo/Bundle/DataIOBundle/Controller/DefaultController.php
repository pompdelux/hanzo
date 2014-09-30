<?php

namespace Hanzo\Bundle\DataIOBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;

use Hanzo\Model\OrdersPeer;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\HelpdeskDataLog;

use Hanzo\Bundle\DataIOBundle\Events;
use Hanzo\Bundle\DataIOBundle\FilterUpdateEvent;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\DataIOBundle
 */
class DefaultController extends CoreController
{
    /**
     * Check stuff
     *
     * @param Request $request Request object
     *
     * @return Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function checkAction(Request $request)
    {
        $uniqid = '';
        if ('POST' === $request->getMethod()) {
            $log = new HelpdeskDataLog();
            $log->setKey($_POST['uniqid']);
            $log->setCreatedAt(time());

            unset($_POST['uniqid']);

            $session_vars = [];
            foreach ($this->get('session')->all() as $key => $value) {
                if ('_' === substr($key, 0, 1)) {
                    continue;
                }

                $session_data[$key] = $value;
            }

            $log->setData(json_encode([
                'browser_data'     => $_POST,
                'cookie_data'      => $_COOKIE,
                'session_data'     => $session_data,
                'current_order_id' => OrdersPeer::getCurrent()->getId(),
                'current_user_id'  => CustomersPeer::getCurrent()->getId(),
            ]));

            $log->save();

            return new Response('', 200);
        } else {
            $aZ09 = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
            shuffle($aZ09);
            foreach (array_rand($aZ09, 2) as $k) {
                $uniqid .= $aZ09[$k];
            }
            $uniqid .= date('is');
        }

        return $this->render('DataIOBundle:Default:check.html.twig', [
           'uniqid' => $uniqid
        ]);
    }


    /**
     * used by nagios/varnish to check uptime
     *
     * @return Response
     */
    public function pingAction()
    {
        return new Response('', 200);
    }


    /**
     * used for testing HTTP 500 error reporting
     *
     * @param  integer $method error method, currently 1 and 2 exists
     */
    public function error500Action($method = 1)
    {
        // method 1, trigger some invalid php code
        if (1 == $method){
            throw new UnknownException('This should trigger a HTTP 500 error.');
            return new Response('', 200);
        }

        // method 2, trigger symfony 500 by not returning a Response object
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

        switch ($step) {
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

        if ( $orderId && $step > 1 ) {
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

        switch ($state) {
          case 'empty':
              if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) {
                  return new Response( 'Empty', 200, array('Content-Type' => 'text/plain') );
              } else {
                  return new Response( 'Not empty', 500, array('Content-Type' => 'text/plain') );
              }
              break;

          case 'full':
              if ( ($order->isNew() === true) || ($order->getTotalQuantity(true) == 0)) {
                  return new Response( 'Empty', 500, array('Content-Type' => 'text/plain') );
              } else {
                  return new Response( 'Not empty', 200, array('Content-Type' => 'text/plain') );
              }
              break;
        }
    }
}

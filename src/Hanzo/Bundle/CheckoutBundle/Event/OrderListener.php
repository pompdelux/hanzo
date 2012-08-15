<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Bundle\ServiceBundle\Services\AxService;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


class OrderListener
{
    protected $session;
    protected $ax;
    protected $cookie_path;

    public function __construct(Session $session, AxService $ax)
    {
        $this->session = $session;
        $this->ax = $ax;

        $this->cookie_path = $_SERVER['SCRIPT_NAME'];
        if ('/app.php' == $this->cookie_path) {
            $this->cookie_path = '';
        }
        $this->cookie_path .= '/'.$this->session->getLocale().'';
    }

    public function onEditStart(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        if ('Sales' == substr($order->getAttributes()->global->domain_key, 0, 5)) {
            setcookie('__ice', uniqid(), 0, $this->cookie_path, $_SERVER['HTTP_HOST'], false, true);
        }

        // first we create the edit version.
        $order->createNewVersion();

        $order->setSessionId(session_id());
        $order->setState( Orders::STATE_BUILDING ); // Old order state is probably payment ok
        $order->clearPaymentAttributes();
        $order->setInEdit(true);
        $order->setPaymentGatewayId(Tools::getPaymentGatewayId());
        $order->save();

        $this->session->set('in_edit', true);
        $this->session->set('order_id', $order->getId());

        $this->ax->lockUnlockSalesOrder($order, true);
    }

    public function onEditCancel(FilterOrderEvent $event)
    {
        if (isset($_COOKIE['__ice'])) {
            setcookie('__ice', '', -3600, $this->cookie_path, $_SERVER['HTTP_HOST'], false, true);
        }

        $order = $event->getOrder();
        // reset order object
        $order->toPreviousVersion();

        // unset session vars.
        $this->session->remove('in_edit');
        $this->session->remove('order_id');
        $this->session->migrate();

        $this->ax->lockUnlockSalesOrder($order, false);
    }

    public function onEditDone(FilterOrderEvent $event)
    {
        if (isset($_COOKIE['__ice'])) {
            setcookie('__ice', '', -3600, $this->cookie_path, $_SERVER['HTTP_HOST'], false, true);
        }

        $order = $event->getOrder();
        $order->setSessionId($order->getId());

        // unset session vars.
        $this->session->remove('in_edit');
        $this->session->remove('order_id');
        // only place this function is called is in CheckoutBundle > DefaultController > successAction
        // and there migrate is called after this function
        //$this->session->migrate();

        $this->ax->lockUnlockSalesOrder($order, false);
    }
}

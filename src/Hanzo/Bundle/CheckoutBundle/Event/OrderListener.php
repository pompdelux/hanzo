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

        $this->setEditCookie(true, substr($order->getAttributes()->global->domain_key, 0, 5));

        // first we create the edit version.
        $order->createNewVersion();

        $order->setSessionId(session_id());
        $order->setState( Orders::STATE_BUILDING ); // Old order state is probably payment ok
        $order->clearFees();
        $order->clearPaymentAttributes();
        $order->setInEdit(true);
        $order->setPaymentGatewayId(Tools::getPaymentGatewayId());
        $order->save();

        $this->session->set('in_edit', true);
        $this->session->set('order_id', $order->getId());
        $this->session->save();

        $this->ax->lockUnlockSalesOrder($order, true);
    }

    public function onEditCancel(FilterOrderEvent $event)
    {
        $this->setEditCookie(false);

        $order = $event->getOrder();
        // reset order object
        $order->toPreviousVersion();

        // unset session vars.
        $this->session->remove('in_edit');
        $this->session->remove('order_id');
        $this->session->save();
        $this->session->migrate();

        $this->ax->lockUnlockSalesOrder($order, false);
    }

    public function onEditDone(FilterOrderEvent $event)
    {
        $this->setEditCookie(false);

        $order = $event->getOrder();
        $order->setSessionId($order->getId());

        // unset session vars.
        $this->session->remove('in_edit');
        $this->session->remove('order_id');

        $this->ax->lockUnlockSalesOrder($order, false);
    }

    protected function setEditCookie($set = true, $domain = 'not_sales')
    {
        if ((false == $set) && empty($_COOKIE['__ice'])) {
            return;
        }

        $content = $set ? $domain : '';
        $lifetime = $set ? 0 : -3600;
        setcookie('__ice', $content, $lifetime, $this->cookie_path, $_SERVER['HTTP_HOST'], false, true);
    }
}

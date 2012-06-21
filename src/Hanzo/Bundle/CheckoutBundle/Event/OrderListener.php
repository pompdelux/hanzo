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

    public function __construct(Session $session, AxService $ax)
    {
        $this->session = $session;
        $this->ax = $ax;
    }

    public function onEditStart(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

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

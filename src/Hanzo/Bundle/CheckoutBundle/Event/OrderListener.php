<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Core\Tools;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


class OrderListener
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function onEditStart(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        // first we create the edit version.
        $order->createNewVersion();
        // then we update the new version with new states
        $order->setState(Orders::STATE_BUILDING);

        $order->setSessionId(session_id());
        $order->setInEdit(true);
        $order->save();

        $this->session->set('in_edit', true);
        $this->session->set('order_id', $order->getId());
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
    }
}

<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Bundle\AxBundle\Actions\Out\AxServiceWrapper;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersStateLog;
use Propel;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class OrderListener
 * @package Hanzo\Bundle\CheckoutBundle
 */
class OrderListener
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AxServiceWrapper
     */
    protected $axServiceWrapper;

    /**
     * OrderListener constructor
     *
     * @param Session          $session
     * @param AxServiceWrapper $axServiceWrapper
     */
    public function __construct(Session $session, AxServiceWrapper $axServiceWrapper)
    {
        $this->session = $session;
        $this->axServiceWrapper = $axServiceWrapper;
    }


    /**
     * onEditStart event handeling
     *
     * @param  FilterOrderEvent $event [description]
     */
    public function onEditStart(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        // if we are unable to lock the order, we should not allow edits to start.
        if (!$this->axServiceWrapper->SalesOrderLockUnlock($order, true)) {
            $event->setStatus(false, 'unable.to.lock.order');
            return;
        }

        // ensure that we do not have session clashes.
        $o = OrdersQuery::create()->findOneBySessionId(
            $this->session->getId(),
            Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE)
        );

        if ($o instanceof Orders) {
            $this->session->migrate();
        }

        unset($o);

        // first we create the edit version.
        $order->createNewVersion();

        // then we set edit stuff on the order.
        $order->setSessionId($this->session->getId());
        $order->setState(Orders::STATE_BUILDING);
        $order->clearFees();
        $order->clearPaymentAttributes();
        $order->setInEdit(true);
        $order->setBillingMethod(null);
        $order->setPaymentGatewayId(Tools::getPaymentGatewayId());
        $order->setUpdatedAt(time());
        $order->save();

        $log = new OrdersStateLog();
        $log->setOrdersId($order->getId());
        $log->setState(0);
        $log->setMessage(Orders::INFO_STATE_EDIT_STARTED);
        $log->setCreatedAt(time());
        $log->save();

        $this->session->set('in_edit', true);
        $this->session->set('order_id', $order->getId());
        $this->session->save();

        // note, cookies must be set after session stuff is done
        $this->setEditCookie(true, substr($order->getAttributes()->global->domain_key, 0, 5));

        $event->setStatus(true);
    }


    /**
     * onEditCancel event handling
     *
     * @param  FilterOrderEvent $event
     */
    public function onEditCancel(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        // reset order object
        $order->toPreviousVersion();

        $log = new OrdersStateLog();
        $log->setOrdersId($order->getId());
        $log->setState(0);
        $log->setMessage(Orders::INFO_STATE_EDIT_CANCLED_BY_USER);
        $log->setCreatedAt(time());
        $log->save();

        // unset session vars.
        $this->session->remove('in_edit');
        $this->session->remove('order_id');
        $this->session->save();
        $this->session->migrate();

        // note, cookies must be set after session stuff is done
        $this->setEditCookie(false);

        $this->axServiceWrapper->SalesOrderLockUnlock($order, false);
    }


    /**
     * onEditDone event handler
     *
     * @param  FilterOrderEvent $event
     */
    public function onEditDone(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $order->setSessionId($order->getId());

        $log = new OrdersStateLog();
        $log->setOrdersId($order->getId());
        $log->setState(0);
        $log->setMessage(Orders::INFO_STATE_EDIT_DONE);
        $log->setCreatedAt(time());
        $log->save();

        // unset session vars.
        $this->session->remove('in_edit');
        $this->session->remove('order_id');

        // note, cookies must be set after session stuff is done
        $this->setEditCookie(false);

        $this->axServiceWrapper->SalesOrderLockUnlock($order, false);
    }


    /**
     * cookie helper - it could be stored in a session, but ....
     *
     * @param boolean $set    to set or delete
     * @param mixed   $domain domain string
     */
    protected function setEditCookie($set = true, $domain = null)
    {
        if ((false == $set) && empty($_COOKIE['__ice'])) {
            return;
        }

        $content  = $set ? $domain : '';
        $notice   = $set ? Tools::getInEditWarning(true) : '';
        $lifetime = $set ? 0 : -3600;

        Tools::setCookie('__ice', $content, $lifetime, true);
        Tools::setCookie('__ice_n', $notice, $lifetime, false);
    }
}

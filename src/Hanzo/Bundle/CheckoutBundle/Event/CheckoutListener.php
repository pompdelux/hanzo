<?php

namespace Hanzo\Bundle\CheckoutBundle\Event;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLinesQuery;
use Symfony\Component\HttpFoundation\Session\Session;

class CheckoutListener
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * onPaymentFailed
     *
     * @param FilterOrderEvent $event
     **/
    public function onPaymentFailed(FilterOrderEvent $event)
    {
        if ($this->session->has('failed_order_mail_sent')) {
            return;
        }

        $order     = $event->getOrder();
        $host      = gethostname();
        $hanzo     = Hanzo::getInstance();
        $session   = $hanzo->getSession();
        $domainKey = $hanzo->get('core.domain_key');

        $message =
            '  Order id..........: '.$order->getID()."\n".
            '  Order id i session: '.$session->get('order_id')."\n".
            '  Kunde navn........: '.$order->getFirstName() .' '. $order->getLastName()."\n".
            '  Kunde email.......: '.$order->getEmail()."\n".
            '  Host name.........: '.$host."\n".
            '  Domain key........: '.$domainKey."\n".
            '  Order state.......: '.$order->getState()."\n".
            '  Billing method....: '.$order->getBillingMethod()."\n".
            '  In edit...........: '.$order->getInEdit()."\n".
            ' - - - - - - - - - - - - - - - - '
        ;

        Tools::log("Payment failed:\n".$message);

        $this->session->set('failed_order_mail_sent', true);
    }


    /**
     * Closing order
     * Note, this MUST be the first event triggered!
     *
     * If the order is in a wrong state, propagation is halted.
     *
     * @param  FilterOrderEvent $event
     * @return void
     */
    public function onPaymentCollectedFirst(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->getState() < Orders::STATE_PAYMENT_OK ) {
            $this->logger->addError('Order #'.$order->getId().' was in state "'.$order->getState().'" and was stopped in flow!');
            $event->stopPropagation();
            return;
        }

        // need copy for later
        $event->setInEdit($order->getInEdit());

        $order->setState(Orders::STATE_PENDING);
        $order->setInEdit(false);
        $order->setSessionId($order->getId());
        $order->setUpdatedAt(time());
        $order->save();
    }


    /**
     * Build and send order confirmation to customer
     * Event is triggered last.
     *
     * @param  FilterOrderEvent $event
     */
    public function onPaymentCollected(FilterOrderEvent $event)
    {
        /** @var \Hanzo\Model\Orders $order */
        $order      = $event->getOrder();
        $in_edit    = $event->getInEdit();
        $attributes = $order->getAttributes();

        // close event if this is the hostess purchase.
        if ($order->getEventsId() && isset($attributes->event->is_hostess_order)) {
            $event = $order->getEvents();
            $event->setIsOpen(false);
            $event->save();
        }

        // Handle payment canceling of old order
        if ($in_edit && !in_array($order->getBillingMethod(), ['gothia', 'gothiade'])) {
            $currentVersion = $order->getVersionId();

            // If the version number is less than 2 there is no previous version
            if (!($currentVersion < 2)) {
                $oldOrderVersion = ( $currentVersion - 1);
                $oldOrder = $order->getOrderAtVersion($oldOrderVersion);

                try {
                    $oldOrder->cancelPayment();
                } catch (\Exception $e) {
                    Tools::log( 'Could not cancel payment for old order, id: '. $oldOrder->getId() .' error was: '. $e->getMessage());
                }
            }
        }
    }


    /**
     * @param  FilterOrderEvent $event
     * @throws \Exception
     */
    public function onFinalize(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $hanzo = Hanzo::getInstance();

        // if for some reason a shipping method without data is set, cleanup.
        if ($order->getDeliveryMethod() && ('' == $order->getDeliveryFirstName())) {
            OrdersLinesQuery::create()
                ->filterByType('shipping')
                ->_or()
                ->filterByType('shipping.fee')
                ->filterByOrdersId($order->getId())
                ->delete()
            ;
            $order->setDeliveryMethod(null);

            // ??? maybe this is not so safe after all..
            $order->setBillingMethod(null);
            $order->clearPaymentAttributes();
        }

        // set once, newer touch again
        $domain_key = $hanzo->get('core.domain_key');
        if (!$order->getInEdit() && (false === strpos($domain_key, 'Sales'))) {
            $order->setAttribute('HomePartyId', 'global', 'WEB ' . $domain_key);
            $order->setAttribute('SalesResponsible', 'global', 'WEB ' . $domain_key);
        }
    }
}

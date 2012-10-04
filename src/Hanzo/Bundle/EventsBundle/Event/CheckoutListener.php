<?php

namespace Hanzo\Bundle\EventsBundle\Event;

use Criteria;
use Propel;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Bundle\ServiceBundle\Services\AxService;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Session;

class CheckoutListener
{
    protected $mailer;
    protected $ax;
    protected $translator;
    protected $session;

    public function __construct(MailService $mailer, AxService $ax, Translator $translator, Session $session)
    {
        $this->mailer     = $mailer;
        $this->ax         = $ax;
        $this->translator = $translator;
        $this->session    = $session;
    }

    public function onFinalize(FilterOrderEvent $event)
    {
        $order = $event->getOrder();

        // we do not change event discounts for edits
        if ($order->getInEdit()) {
            return;
        }

        $attributes = $order->getAttributes();

        // calculate hostess discount if nessesary
        if ($order->getEventsId()) {
            $customer = $order->getCustomers();
            $hanzo = Hanzo::getInstance();


            if (isset($attributes->event->is_hostess_order) && !$order->getInEdit()) {
                $discount = 0;
                $add_discount = true;

                // make sure all orders are ok
                define('ACTION_TRIGGER', __METHOD__);
                $cleanup_service = $hanzo->container->get('deadorder_manager');
                $con = Propel::getConnection(null, Propel::CONNECTION_WRITE);

                $orders = OrdersQuery::create()
                    ->filterByEventsId($order->getEventsId())
                    ->filterById($order->getId(), Criteria::NOT_EQUAL)
                    ->filterByBillingMethod('dibs')
                    ->filterByState(array( 'max' => Orders::STATE_PAYMENT_OK) )
                    ->find($con)
                ;

                foreach ($orders as $order_item) {
                    $status = $cleanup_service->checkOrderForErrors($order_item);

                    if (isset($status['is_error'])) {
                        if ($status['is_error'] === true) {
                            Tools::log($status);
                            $order_item->delete();
                        }
                    }
                }

                $orders = OrdersQuery::create()
                    ->filterByEventsId($order->getEventsId())
                    ->filterByState(Orders::STATE_PENDING, Criteria::GREATER_EQUAL)
                    ->_or()
                    ->filterById($order->getId())
                    ->find($con)
                ;

                foreach ($orders as $o) {
                    $total = $o->getTotalProductPrice();

                    // TODO: not hardcoded !
                    $discount += (($total / 100) * -5.00);
                }

                $order->setDiscountLine('discount.hostess', $discount, -5.00);
            }

            $event = $order->getEvents();
            $order->setAttribute('HomePartyId', 'global', $event->getCode());
            $order->setAttribute('SalesResponsible', 'global', $event->getCustomersRelatedByConsultantsId()->getConsultants()->getInitials());

        } elseif (isset($attributes->purchase->type)) {
            $initials = CustomersPeer::getCurrent()->getConsultants()->getInitials();
            $discount = 0;
            $label = '';

            // TODO: not hardcoded
            switch ($attributes->purchase->type) {
                case 'friend':
                    $discount = -15.00;
                    $label = 'Veninde køb';
                    break;
                case 'gift':
                    $discount = -20.00;
                    $label = 'Gave køb';
                    break;
                case 'other':
                    $label = 'Udenfor arrangement';
                    break;
                case 'private':
                    $label = 'Privat køb';
                    break;
            }

            if ($discount) {
                $total = $order->getTotalProductPrice();
                $discount_amount = (($total / 100) * $discount);
                $order->setDiscountLine('discount.'.$attributes->purchase->type, $discount_amount, $discount);

                if ($attributes->purchase->type != 'private') {
                    $order->removeDiscountLine('discount.private');
                }
            }

            $order->setAttribute('HomePartyId', 'global', $label);
            $order->setAttribute('SalesResponsible', 'global', $initials);
        }
    }
}

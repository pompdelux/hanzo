<?php

namespace Hanzo\Bundle\EventsBundle\Event;

use Criteria;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
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

        // add hostess discount if nessesary
        if ($order->getEventsId()) {
            $customer = $order->getCustomers();
            $discount_lines = $order->getOrderLineDiscount();
            $hanzo = Hanzo::getInstance();


            if (isset($attributes->event->is_hostess_order) && empty($attributes->event->is_hostess_order_calculated)) {
                $add_discount = true;

                $discount = 0;

                $c = new Criteria;
                $c->add(OrdersPeer::STATE, Orders::STATE_PENDING, Criteria::GREATER_EQUAL);

                foreach ($order->getEvents()->getOrderss($c) as $o) {
                    $total = $o->getTotalProductPrice();

                    // TODO: not hardcoded !
                    $discount += (($total / 100) * -5);
                }

                $order->setDiscountLine('discount.hostess', $discount);
                $order->setAttribute('is_hostess_order_calculated', 'event', true);
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
                    $discount = -15;
                    $label = 'Veninde køb';
                    break;
                case 'gift':
                    $discount = -20;
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
                $discount = (($total / 100) * $discount);
                $order->setDiscountLine('discount.'.$attributes->purchase->type, $discount);
            }

            $order->setAttribute('HomePartyId', 'global', $label);
            $order->setAttribute('SalesResponsible', 'global', $initials);
        }
    }
}

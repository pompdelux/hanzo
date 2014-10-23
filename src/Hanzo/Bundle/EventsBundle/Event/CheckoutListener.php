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
use Hanzo\Bundle\AxBundle\Actions\Out\AxService;
use Hanzo\Bundle\ServiceBundle\Services\MailService;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CheckoutListener
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class CheckoutListener
{
    /**
     * @var MailService
     */
    protected $mailer;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param MailService $mailer
     * @param Translator  $translator
     */
    public function __construct(MailService $mailer, Translator $translator)
    {
        $this->mailer     = $mailer;
        $this->translator = $translator;
    }

    /**
     * @param FilterOrderEvent $event
     *
     * @throws \Exception
     */
    public function onFinalize(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $attributes = $order->getAttributes();

        // calculate hostess discount if nessesary
        // we do not change event discounts for edits
        if ($order->getEventsId() && (false === $order->getInEdit())) {
            $hanzo = Hanzo::getInstance();


            if (isset($attributes->event->is_hostess_order) && !$order->getInEdit()) {
                $discount = 0;

                // make sure all orders are ok
                define('ACTION_TRIGGER', __METHOD__);
                $con = Propel::getConnection(null, Propel::CONNECTION_WRITE);

                try {
                    $cleanupService = $hanzo->container->get('deadorder_manager');

                    $orders = OrdersQuery::create()
                        ->filterByEventsId($order->getEventsId())
                        ->filterById($order->getId(), Criteria::NOT_EQUAL)
                        ->filterByBillingMethod('dibs')
                        ->filterByState(['max' => Orders::STATE_PAYMENT_OK])
                        ->find($con);

                    foreach ($orders as $orderItem) {
                        $status = $cleanupService->checkOrderForErrors($orderItem);

                        if (isset($status['is_error'])) {
                            if ($status['is_error'] === true) {
                                Tools::log($status);
                                $orderItem->delete();
                            }
                        }
                    }
                } catch (\Exception $e) {
                }

                $orders = OrdersQuery::create()
                    ->filterByEventsId($order->getEventsId())
                    ->filterByState(Orders::STATE_PENDING, Criteria::GREATER_EQUAL)
                    ->_or()
                    ->filterById($order->getId())
                    ->find($con);

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
            $label = '';
            $discount = 0;

            // TODO: not hardcoded
            switch ($attributes->purchase->type) {
                case 'friend':
                    $label = 'Veninde køb';
                    $discount = -15.00;
                    break;
                case 'gift':
                    $label = 'Gave køb';
                    $discount = -20.00;
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
                $discountAmount = (($total / 100) * $discount);
                $order->setDiscountLine('discount.'.$attributes->purchase->type, $discountAmount, $discount);

                if ($attributes->purchase->type != 'private') {
                    $order->removeDiscountLine('discount.private');
                }
            }

            if (false === $order->getInEdit()) {
                $initials = CustomersPeer::getCurrent()->getConsultants()->getInitials();
                $order->setAttribute('HomePartyId', 'global', $label);
                $order->setAttribute('SalesResponsible', 'global', $initials);
            }
        }
    }
}

<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\BasketBundle\Event\Listener\Model\Orders;

use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OnPreSave
 * @package Hanzo\Bundle\BasketBundle
 */
class OnPreSaveEvent
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }


    /**
     * @param  ModelEvent $event
     * @throws \Exception
     */
    public function handle(ModelEvent $event)
    {
        $order = $event->getModel();

        if (!$order instanceof Orders) {
            return;
        }

        if (!$order->getSessionId() && $this->request) {
            $order->setSessionId($this->request->getSession()->getId());
        }

        if ($order->isNew()) {
            $hanzo = Hanzo::getInstance();
            $order->setAttribute('domain_key', 'global', $hanzo->get('core.domain_key'));
            $order->setCurrencyCode($hanzo->get('core.currency'));
            $order->setLanguagesId($hanzo->get('core.language_id'));
            $order->setPaymentGatewayId(Tools::getPaymentGatewayId());

            if ($this->request) {
                $order->setAttribute('client_ip',  'global', $this->request->getClientIp());
                $order->setAttribute('user_agent', 'global', $this->request->server->get('HTTP_USER_AGENT'));
            }
        }

        // set billing address - if not already set.
        if ('' == $order->getBillingFirstName()) {
            $customer = CustomersPeer::getCurrent();
            if (!$customer->isNew()) {
                $c = new \Criteria();
                $c->add(AddressesPeer::TYPE, 'payment');
                $address = $customer->getAddressess($c)->getFirst();

                if ($address) {
                    $order->setBillingAddress($address);
                    $order->setPhone($customer->getPhone());
                } else {
                    Tools::log('Missing payment address: '.$customer->getId());
                }
            }
        }

    }
}

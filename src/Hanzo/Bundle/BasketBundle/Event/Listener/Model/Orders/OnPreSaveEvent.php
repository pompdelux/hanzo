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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OnPreSave
 * @package Hanzo\Bundle\BasketBundle
 */
class OnPreSaveEvent
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer = null)
    {
        $this->serviceContainer = $serviceContainer;
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

        if (!$order->getSessionId() && $this->serviceContainer->has('request')) {
            $order->setSessionId($this->serviceContainer->get('request')->getSession()->getId());
        }

        $this->init($order);
        $this->setBillingAddress($order);
    }

    /**
     * @param  Orders $order
     * @throws \Exception
     */
    private function init(Orders $order)
    {
        if ($order->isNew()) {
            $hanzo = Hanzo::getInstance();
            $order->setAttribute('domain_key', 'global', $hanzo->get('core.domain_key'));
            $order->setCurrencyCode($hanzo->get('core.currency'));
            $order->setLanguagesId($hanzo->get('core.language_id'));
            $order->setPaymentGatewayId(Tools::getPaymentGatewayId());

            if ($this->serviceContainer->has('request')) {
                $request = $this->serviceContainer->get('request');
                $order->setAttribute('client_ip',  'global', $request->getClientIp());
                $order->setAttribute('user_agent', 'global', $request->server->get('HTTP_USER_AGENT'));
            }
        }
    }


    /**
     * Attach customer billing address.
     *
     * @param  Orders $order
     * @throws \Exception
     */
    private function setBillingAddress(Orders $order)
    {
        if ($order->getBillingFirstName()) {
            return;
        }

        $customer = CustomersPeer::getCurrent();
        if ($customer->isNew()) {
            return;
        }

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

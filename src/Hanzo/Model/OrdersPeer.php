<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use Propel;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseOrdersPeer;

/**
 * Class OrdersPeer
 *
 * @package Hanzo\Model
 */
class OrdersPeer extends BaseOrdersPeer
{
    /**
     * @param bool $forceReload
     *
     * @return Orders
     * @throws \Exception
     * @throws \PropelException
     */
    public static function getCurrent($forceReload = true)
    {
        $hanzo = Hanzo::getInstance();
        $session = $hanzo->getSession();
        $order = null;

        if ($session->has('order_id')) {
            $order = OrdersQuery::create()
                ->useOrdersLinesQuery()
                    ->orderByType()
                    ->orderByProductsName()
                    ->orderByPrice()
                ->endUse()
                ->leftJoinWithOrdersLines()
                ->findOneById(
                    $session->get('order_id'),
                    Propel::getConnection(null, Propel::CONNECTION_WRITE)
                );

            // attach the customer to the order.
            if ($order instanceOf Orders) {
                if (!$order->getCustomersId()) {
                    $security = $hanzo->container->get('security.context');

                    if ($security->isGranted('ROLE_USER')) {
                        $user = $security->getToken()->getUser();

                        $order->setCustomersId($user->getId());
                        $order->setEmail($user->getEmail());
                        $order->setFirstName($user->getFirstName());
                        $order->setLastName($user->getLastName());
                        $order->save();
                    }
                }

                if ($forceReload) {
                    try {
                        $order->reload(true);
                    } catch (\PropelException $e) {
                        // sometimes reload failes, but these are ok - we can safely ignore those.
                    }
                }
            }
        }

        return ($order instanceOf Orders) ? $order : new Orders();
    }


    /**
     * Fetch order by its payment gateway id
     *
     * @param mixed $gatewayId
     *
     * @return Orders object
     */
    public static function retriveByPaymentGatewayId($gatewayId)
    {
        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId(
                $gatewayId,
                Propel::getConnection(null, Propel::CONNECTION_WRITE));

        try {
            $order->reload(true);
        } catch (\PropelException $e) {
            // again - its safe to ignore reload errors.
        }

        return $order;
    }


    /**
     * get edit state
     * @return boolean true if in edit, false otherwise
     */
    public static function inEdit()
    {
        if (!empty($_COOKIE['__ice'])) {
            $session = Hanzo::getInstance()->getSession();
            if ($session->get('in_edit') && $session->get('order_id')) {
                return true;
            }
        }

        return false;
    }

} // OrdersPeer

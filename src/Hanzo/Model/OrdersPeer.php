<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use Propel;

use Hanzo\Model\om\BaseOrdersPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

use Hanzo\Model\CustomersPeer;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

/**
 * Skeleton subclass for performing query and update operations on the 'orders' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class OrdersPeer extends BaseOrdersPeer
{
    public static function getCurrent($force_reload = true)
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
                )
            ;

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

                if ($force_reload) {
                    try {
                        $order->reload(true);
                    } catch(\PropelException $e) {}
                }
            }
        }

        return ($order instanceOf Orders) ? $order : new Orders();
    }


    /**
     * Fetch order by its payment gateway id
     *
     * @param  mixed $gateway_id
     * @return Orders object
     */
    public static function retriveByPaymentGatewayId($gateway_id)
    {
        $order = OrdersQuery::create()
            ->findOneByPaymentGatewayId(
                $gateway_id,
                Propel::getConnection(null, Propel::CONNECTION_WRITE)
        );

        try {
            $order->reload(true);
        } catch(\PropelException $e) {}

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

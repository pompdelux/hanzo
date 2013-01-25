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
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class OrdersPeer extends BaseOrdersPeer
{
    static $current;

    public static function getCurrent($flush = FALSE)
    {
        if ((FALSE === $flush) && (!empty(self::$current))) {
            return self::$current;
        }

        $hanzo = Hanzo::getInstance();
        $session = $hanzo->getSession();

        if ($session->has('order_id')) {
            $query = OrdersQuery::create()
                ->useOrdersLinesQuery()
                    ->orderByType()
                    ->orderByProductsName()
                    ->orderByPrice()
                ->endUse()
                ->leftJoinWithOrdersLines()
            ;

            self::$current = $query->findPk(
                $session->get('order_id'),
                Propel::getConnection(null, Propel::CONNECTION_WRITE)
            );

            // attach the customer to the order.
            if ((self::$current instanceOf Orders) && !self::$current->getCustomersId()) {
                $security = $hanzo->container->get('security.context');

                if ($security->isGranted('ROLE_USER')) {
                    $user = $security->getToken()->getUser();
                    self::$current->setCustomersId($user->getId());
                    self::$current->setEmail($user->getEmail());
                    self::$current->setFirstName($user->getFirstName());
                    self::$current->setLastName($user->getLastName());
                    self::$current->save();
                }
            }
        }

        self::$current = self::$current ?: new Orders;
        return self::$current;
    }


    /**
     * Fetch order by its payment gateway id
     *
     * @param  mixed $gateway_id
     * @return Orders object
     */
    public static function retriveByPaymentGatewayId($gateway_id)
    {
        $con = Propel::getConnection(null, Propel::CONNECTION_WRITE);
        return OrdersQuery::create()->findOneByPaymentGatewayId($gateway_id, $con);
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

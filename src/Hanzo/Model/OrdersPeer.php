<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use Hanzo\Model\om\BaseOrdersPeer,
    Hanzo\Model\Orders,
    Hanzo\Model\OrdersQuery
    ;

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

        if (!empty($_SESSION['order_id'])) {
            $query = OrdersQuery::create()
                ->leftJoinWithOrdersLines()
            ;
            self::$current = $query->findPk($_SESSION['order_id']);

            // attach the customer to the order.
            if (!self::$current->getCustomersId()) {
                $hanzo = Hanzo::getInstance();
                $security = $hanzo->container->get('security.context');

                if ($security->isGranted('ROLE_USER')) {
                    $user = $security->getToken()->getUser()->getUser();
                    self::$current->setCustomersId($user->getId());
                    self::$current->save();
                }
            }
        }

        self::$current = self::$current ?: new Orders;
        return self::$current;
    }

} // OrdersPeer

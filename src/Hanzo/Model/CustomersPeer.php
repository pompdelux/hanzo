<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\om\BaseCustomersPeer;

/**
 * Skeleton subclass for performing query and update operations on the 'customers' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class CustomersPeer extends BaseCustomersPeer
{
    static $current;

    /**
     * getCurrenct() returns the current user, if the user is not logged ind, a new Customers object is returned
     * Based on Orders::getCurrent()
     * @return Customers
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public static function getCurrent()
    {
        if (!empty (self::$current)) {
            return self::$current;
        }

        $acl = Hanzo::getInstance()->container->get('security.context');
        if ($acl->isGranted('IS_AUTHENTICATED_FULLY')) {
            self::$current = $acl->getToken()->getUser()->getUser();
        }

        self::$current = self::$current ?: new Customers;
        return self::$current;
    }

} // CustomersPeer

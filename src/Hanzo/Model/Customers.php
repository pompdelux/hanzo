<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseCustomers;


/**
 * Skeleton subclass for representing a row from the 'customers' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class Customers extends BaseCustomers
{
    protected $acl;


    /**
     * shortcut for access checks on the customer.
     */
    public function isGranted($role)
    {
        if (empty($this->acl)) {
            $this->acl = Hanzo::getInstance()->container->get('security.context');
        }

        return $this->acl->isGranted($role);
    }

} // Customers

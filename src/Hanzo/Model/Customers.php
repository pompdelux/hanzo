<?php /* vim: set sw=4: */

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
     *
     * @return boolean
     */
    public function isGranted($role)
    {
        if (empty($this->acl)) {
            $this->acl = Hanzo::getInstance()->container->get('security.context');
        }

        return $this->acl->isGranted($role);
    }

    /**
     * login check
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }


    /**
     * get full name
     */
     public function getName()
     {
         return trim($this->getFirstName() . ' ' . $this->getLastName());
     }


    /**
     * The following methods is needed by the form component.....
     */

    public function getAddresses()
    {
        return $this->getAddressess();
    }

    protected $accept = false;
    public function getAccept()
    {
        return (bool) $this->getName();
    }

    /*protected $newsletter = FALSE;
    public function getNewsletter()
    {
        return $this->newsletter;
    }
    public function setNewsletter($value = TRUE)
    {
        return $this->newsletter = (bool) $value;
    }*/

    /*protected $accept = FALSE;
    public function getAccept()
    {
        return $this->accept;
    }
    public function setAccept($value = TRUE)
    {
        return $this->accept = (bool) $value;
    }*/

} // Customers

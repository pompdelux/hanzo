<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use PropelPDO;

use Hanzo\Model\om\BaseCustomers;
use Hanzo\Model\CustomersQuery;

use Symfony\Component\Security\Core\User\UserInterface;

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
class Customers extends BaseCustomers implements UserInterface
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

    public function getAddresses($criteria = null, PropelPDO $con = null)
    {
        return $this->getAddressess($criteria, $con);
    }

    protected $accept = false;
    public function getAccept()
    {
        return (bool) $this->getName();
    }

    /**
     * validate uniq emails
     *
     * @return boolean
     */
    public function isEmailUniq()
    {
        if ($email = $this->getEmail()) {
            $customer = CustomersQuery::create()->findOneByEmail($email);
            if (!$customer instanceof Customers) {
                return true;
            }
            return false;
        }

        return true;
    }


    private $map = array(
        'consultant' => array(
            'ROLE_CONSULTANT',
            'ROLE_USER',
        ),
        'customer' => array(
            'ROLE_CUSTOMER',
            'ROLE_USER',
        ),
        'employee' => array(
            'ROLE_EMPLOYEE',
            'ROLE_USER',
        ),
        'admin' => array(
            'ROLE_ADMIN',
            'ROLE_EMPLOYEE',
            'ROLE_SALES',
            'ROLE_USER',
        ),
    );

    // NICETO: should not be hardcoded
    private $admins = array(
        // pompdelux
        'pd@pompdelux.dk',
        'mh@pompdelux.dk',
        'hd@pompdelux.dk',
        'lv@pompdelux.dk',
        // bellcom
        'hf@bellcom.dk',
        'ulrik@bellcom.dk',
        'mmh@bellcom.dk',
        'andersbryrup@gmail.com',
        'hanzo@bellcom.dk',
    );

    private $sales = array(
        'kk@pompdelux.dk',
        'ak@pompdelux.dk',
        'sj@pompdelux.dk',
        'nj@pompdelux.dk',
        'pc@pompdelux.dk',
        'mc@pompdelux.dk',
        'mle@pompdelux.dk',
        // admins
        'hd@pompdelux.dk',
        'lv@pompdelux.dk',
     );

    public function getRoles()
    {
        $group = $this->getGroups();
        $roles = $this->map[$group->getName()];

        // NICETO: should not be hardcoded
        if (in_array($this->getUsername(), $this->admins)) {
            $roles[] = 'ROLE_EMPLOYEE';
            $roles[] = 'ROLE_ADMIN';
        }

        // NICETO: should not be hardcoded
        if (in_array($this->getUsername(), $this->sales)) {
            $roles[] = 'ROLE_SALES';
            $roles[] = 'ROLE_CONSULTANT';
        }

        return $roles;
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getUser()
    {
        return $this;
    }

    public function eraseCredentials() {}
} // Customers

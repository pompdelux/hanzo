<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use PropelPDO;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseCustomers;
use Hanzo\Model\CustomersQuery;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\ExecutionContext;

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
class Customers extends BaseCustomers implements AdvancedUserInterface
{
    protected $acl;


    /**
     * shortcut for access checks on the customer.
     *
     * @param $role
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
            if ($customer instanceof Customers) {
                return false;
            }
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
        'customers_service' => array(
            'ROLE_USER',
            'ROLE_CUSTOMERS_SERVICE'
        ),
        'admin' => array(
            'ROLE_ADMIN',
            'ROLE_EMPLOYEE',
            'ROLE_SALES',
            'ROLE_USER',
        ),
    );

    // NICETO: should not be hardcoded
    private $extended = [
        // admin
        'hd@pompdelux.dk'        => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'jm@pompdelux.dk'        => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'lv@pompdelux.dk'        => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        // admin (bellcom)
        'andersbryrup@gmail.com' => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'hanzo@bellcom.dk'       => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'hf@bellcom.dk'          => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'mmh@bellcom.dk'         => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'ulrik@bellcom.dk'       => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        // stats
        'mh@pompdelux.dk'        => ['ROLE_STATS', 'ROLE_EMPLOYEE'],
        'jj@pompdelux.dk'        => ['ROLE_STATS', 'ROLE_EMPLOYEE'],
        'pd@pompdelux.dk'        => ['ROLE_STATS', 'ROLE_EMPLOYEE'],
        // marketing
        'tj@pompdelux.dk'        => ['ROLE_MARKETING', 'ROLE_EMPLOYEE'],
        // design
        'design@pompdelux.dk'    => ['ROLE_DESIGN', 'ROLE_EMPLOYEE'],
        // sales
        'hp@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'kg@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'mc@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'mlade@pompdelux.dk'     => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'mle@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'nj@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'pc@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'sj@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'ls@pompdelux.dk'        => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        // customer service
        'cd@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'hb@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'hbo@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'js@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'ln@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'ml@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'mpe@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'nmj@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'pf@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'tt@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'vs@pompdelux.dk'        => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
    ];

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $group = $this->getGroups();
        $roles = $this->map[$group->getName()];
        $email = strtolower($this->getUsername());

        if (isset($this->extended[$email])) {
            $roles = array_merge($roles, $this->extended[$email]);
        }

        return $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials() {}

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->getIsActive();
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Validate length of users full name
     *
     * @param  ExecutionContext $context
     */
    public function isFullNameWithinLimits(ExecutionContext $context)
    {
        $domain = strtoupper(Hanzo::getInstance()->get('core.domain_key'));
        $maxLength = 30;

        if (substr($domain, -2) == 'DK') {
            $maxLength = 35;
        }

        if (substr($domain, -2) == 'DE') {
            // In germany the max length are including the Frau/Herr prefix
            // plus a space. Subtract 5 chars.
            $maxLength = 25;
        }

        $length = mb_strlen($this->getFirstName().' '.$this->getLastName());
        if ($maxLength < $length) {
            $context->addViolationAt('first_name', 'name.max.length', ['{{ limit }}' => $maxLength], $length, $length);
        }
    }
} // Customers

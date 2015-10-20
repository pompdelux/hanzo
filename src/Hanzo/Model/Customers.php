<?php /* vim: set sw=4: */

namespace Hanzo\Model;

use PropelPDO;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseCustomers;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class Customers
 *
 * @package Hanzo\Model
 */
class Customers extends BaseCustomers implements AdvancedUserInterface
{
    protected $acl;
    protected $accept = false;

    private $map = [
        'consultant' => [
            'ROLE_CONSULTANT',
            'ROLE_USER',
        ],
        'customer' => [
            'ROLE_CUSTOMER',
            'ROLE_USER',
        ],
        'employee' => [
            'ROLE_EMPLOYEE',
            'ROLE_USER',
        ],
        'customers_service' => [
            'ROLE_USER',
            'ROLE_CUSTOMERS_SERVICE'
        ],
        'admin' => [
            'ROLE_ADMIN',
            'ROLE_EMPLOYEE',
            'ROLE_SALES',
            'ROLE_USER',
        ],
        'logistics' => [
            'ROLE_USER',
            'ROLE_LOGISTICS',
        ],
        'support' => [
            'ROLE_USER',
            'ROLE_SUPPORT',
        ],
    ];

    // NICETO: should not be hardcoded
    private $extended = [
        // admin
        'hd@pompdelux.dk'       => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'sc@pompdelux.dk'       => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'cc@pompdelux.dk'       => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'mped@pompdelux.dk'     => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'kst@pompdelux.dk'      => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        // admin (bellcom)
        'hanzo@bellcom.dk'      => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'hf@bellcom.dk'         => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'mmh@bellcom.dk'        => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'ulrik@bellcom.dk'      => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        'mn@bellcom.dk'         => ['ROLE_ADMIN', 'ROLE_SALES', 'ROLE_EMPLOYEE', 'ROLE_CONSULTANT'],
        // stats
        'mh@pompdelux.dk'       => ['ROLE_STATS', 'ROLE_EMPLOYEE'],
        'pd@pompdelux.dk'       => ['ROLE_STATS', 'ROLE_EMPLOYEE'],
        'de@pompdelux.dk'       => ['ROLE_STATS', 'ROLE_EMPLOYEE'],
        // marketing
        'tj@pompdelux.dk'       => ['ROLE_MARKETING', 'ROLE_EMPLOYEE'],
        'design@pompdelux.dk'   => ['ROLE_MARKETING', 'ROLE_EMPLOYEE'],
        // design
        'design@pompdelux.dk'   => ['ROLE_DESIGN', 'ROLE_EMPLOYEE'],
        // sales
        'hp@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'kg@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'mc@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'mlade@pompdelux.dk'    => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'mle@pompdelux.dk'      => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'nj@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'pc@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'sj@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'ls@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        'eh@pompdelux.dk'       => ['ROLE_SALES', 'ROLE_CUSTOMERS_SERVICE', 'ROLE_CONSULTANT', 'ROLE_EMPLOYEE'],
        // customer service
        'pf@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_CUSTOMERS_SERVICE_EXTRA', 'ROLE_EMPLOYEE'],
        'ln@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_CUSTOMERS_SERVICE_EXTRA', 'ROLE_EMPLOYEE'],
        'ka@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_CUSTOMERS_SERVICE_EXTRA', 'ROLE_EMPLOYEE'],
        'cd@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'hb@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'hbo@pompdelux.dk'      => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'js@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'ml@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'tt@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'vs@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'nk@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'ep@pompdelux.dk'       => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        'lmach@pompdelux.dk'    => ['ROLE_CUSTOMERS_SERVICE', 'ROLE_EMPLOYEE'],
        // logistics
        'nh@pompdelux.dk'       => ['ROLE_LOGISTICS', 'ROLE_EMPLOYEE'],
        'tta@pompdelux.dk'      => ['ROLE_LOGISTICS', 'ROLE_EMPLOYEE'],
        // support
        'cr@pompdelux.dk'       => ['ROLE_SUPPORT', 'ROLE_EMPLOYEE'],
        'ma@pompdelux.dk'       => ['ROLE_SUPPORT', 'ROLE_EMPLOYEE'],
        'hbo@pompdelux.dk'      => ['ROLE_SUPPORT', 'ROLE_EMPLOYEE'],
    ];

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
     * shortcut for access checks on the customer.
     *
     * @param string $role
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
     * The following methods is needed by the form component.....
     */

    /**
     * @param \Criteria|null $criteria
     * @param PropelPDO      $con
     *
     * @return Addresses[]|\PropelObjectCollection
     */
    public function getAddresses($criteria = null, PropelPDO $con = null)
    {
        return $this->getAddressess($criteria, $con);
    }

    /**
     * @return bool
     */
    public function getAccept()
    {
        return (bool) $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
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
    public function getUsername()
    {
        return $this->getEmail();
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
    public function getUser()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
    }


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
     * @param ExecutionContextInterface $context
     */
    public function passesExtendedValidation(ExecutionContextInterface $context)
    {
        $this->isFullNameWithinLimits($context);
        $this->isValidEmail($context);
    }

    /**
     * @param \PropelObjectCollection $address
     */
    public function setAddresses(\PropelObjectCollection $address)
    {
        /** @var Addresses $address */
        $address = $address->getFirst();
        $address->setFirstName($this->getFirstName());
        $address->setLastName($this->getLastName());
        $address->save();
    }

    /**
     * Validate length of users full name
     *
     * @param ExecutionContextInterface $context
     */
    private function isFullNameWithinLimits(ExecutionContextInterface $context)
    {
        $domain    = strtoupper(Hanzo::getInstance()->get('core.domain_key'));
        $maxLength = 30;

        if (substr($domain, -2) == 'DK') {
            $maxLength = 35;
        }

        if (substr($domain, -2) == 'DE') {
            // In germany the max length are including the Frau/Herr prefix
            // plus a space. Subtract 5 chars.
            $maxLength = 25;
        }

        $length = mb_strlen($this->getFirstName() . ' ' . $this->getLastName());
        if ($maxLength < $length) {
            $context->buildViolation('name.max.length', [
                '{{ limit }}'        => $maxLength,
                'translation_domain' => 'account'
            ])->addViolation();
        }
    }

    /**
     * Validate emails based on phps internal FILTER_VALIDATE_EMAIL a
     * Note, we do this to not have to deal with IDN addresses - which swiftmailer does not support.
     * @param ExecutionContextInterface $context
     */
    private function isValidEmail(ExecutionContextInterface $context)
    {
        if (false === filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $context->buildViolation('email.not.valid', ['translation_domain' => 'account'])->addViolation();
        }
    }
}

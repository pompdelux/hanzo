<?php
// src/Acme/SecuredBundle/Proxy/User.php

namespace Hanzo\Bundle\AccountBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Hanzo\Model\Customers as ModelUser;

class ProxyUser implements AdvancedUserInterface
{
    /**
     * The model user
     *
     * @var \Acme\SecuredBundle\Model\User
     */
    private $user;

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
        'hd@pompdelux.dk',
        'lv@pompdelux.dk',
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
        // admins
        'hd@pompdelux.dk',
        'lv@pompdelux.dk',
     );

    public function __construct(ModelUser $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $group = $this->getUser()->getGroups();
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

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return $this->getUser()->getPassword();
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return '';
        // return $this->getUser()->getSalt();
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->getUser()->getEmail();
    }

    public function getPrimaryKey() {
        return $this->getUser()->getPrimaryKey();
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials(){}

    /**
     * {@inheritDoc}
     */
    public function equals(UserInterface $user)                                                                                                          {
        return $this->getUser()->equals($user);
    }

    /**
     * @return \Acme\SecuredBundle\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }


    // satisfy AdvancedUserInterface
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->getUser()->getIsActive();
    }
}

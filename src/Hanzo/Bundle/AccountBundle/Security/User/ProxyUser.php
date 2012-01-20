<?php
// src/Acme/SecuredBundle/Proxy/User.php

namespace Hanzo\Bundle\AccountBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

use Hanzo\Model\Customers as ModelUser;

class ProxyUser implements UserInterface
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
        return $this->map[$group->getName()];
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
    public function eraseCredentials()
    {
    }

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
}

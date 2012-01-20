<?php

namespace Hanzo\Bundle\AccountBundle\Security\User;

use Propel\PropelBundle\Security\User\ModelUserProvider;

class UserProvider extends ModelUserProvider
{
    public function __construct()
    {
        parent::__construct('Hanzo\Model\Customers', 'Hanzo\Bundle\AccountBundle\Security\User\ProxyUser', 'email');
    }
}

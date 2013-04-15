<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseAddresses;


/**
 * Skeleton subclass for representing a row from the 'addresses' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Hanzo.Model
 */
class Addresses extends BaseAddresses
{
    /**
     * shortcut phone setters and getters into the address object
     *
     * @var int
     */
    protected $phone;


    /**
     * shortcut phone getters into the address object, but only for payment addresses
     *
     * @return mixed null or number
     */
    public function getPhone()
    {
        if (('payment' == $this->getType()) && $this->getCustomersId()) {
            return $this->getCustomers()->getPhone();
        }

        return null;
    }


    /**
     * shortcut phone setters into the address object, but only for payment addresses
     *
     * @param int $v phone number
     * @return mixed null or Customers object
     */
    public function setPhone($v)
    {
        if (('payment' == $this->getType()) && $this->getCustomersId()) {
            $this->phone = $v;
            $c = $this->getCustomers();
            $c->setPhone($v);

            return $c;
        }

        return null;
    }
} // Addresses

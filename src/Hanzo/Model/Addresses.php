<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseAddresses;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Validator\ExecutionContext;

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

    public function getTitle(Translator $translator = null)
    {
        $title = parent::getTitle();
        if ($title && ($translator instanceof Translator)) {
            $title = $translator->trans('title.'.parent::getTitle(), [], 'account');
        }

        return $title;
    }


    /**
     * Validate length of users full name
     *
     * @param  ExecutionContext $context
     */
    public function isFullNameWithinLimits(ExecutionContext $context)
    {
        $length = mb_strlen($this->getFirstName().' '.$this->getLastName());
        if (30 < $length) {
            $context->addViolationAtSubPath('first_name', 'name.max.length', ['{{ limit }}' => 30], null, $length);
        }
    }

} // Addresses

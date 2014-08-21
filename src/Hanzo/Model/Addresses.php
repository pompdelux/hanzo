<?php

namespace Hanzo\Model;

use Hanzo\Core\Hanzo;
use Hanzo\Model\om\BaseAddresses;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Validator\ExecutionContextInterface;

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
     * get full name
     */
    public function getName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }


    /**
     * Validate length of users full name
     *
     * @param  ExecutionContextInterface $context
     */
    public function isFullNameWithinLimits(ExecutionContextInterface $context)
    {
        $domain = Hanzo::getInstance()->get('core.domain_key');
        $maxLength = 30;
        if ($domain == 'DE') {
            // In germany the max length are including the Frau/Herr prefix
            // plus a space. Subtract 5 chars.
            $maxLength = 25;
        }

        $length = mb_strlen($this->getFirstName().' '.$this->getLastName());
        if ($maxLength < $length) {
            $context->addViolationAt('first_name', 'name.max.length', ['{{ limit }}' => $maxLength], null, $length);
        }
    }


    /**
     * update geocode information
     */
    public function forceGeocode()
    {
        $geocoder      = new \Geocoder\Geocoder(new \Geocoder\Provider\GoogleMapsProvider(new \Geocoder\HttpAdapter\CurlHttpAdapter()));
        $address_parts = array();

        $address_parts['AddressLine1']  = $this->getAddressLine1();
        $address_parts['AddressLine2']  = $this->getAddressLine2();
        $address_parts['StateProvince'] = $this->getStateProvince();
        $address_parts['PostalCode']    = $this->getPostalCode();
        $address_parts['Country']       = $this->getCountry();

        $result = $geocoder->geocode(join(',', array_filter($address_parts)));
        if (isset($result) && $coordinates = $result->getCoordinates()) {
            $this->setLatitude($coordinates[0]);
            $this->setLongitude($coordinates[1]);
        }
    }
}

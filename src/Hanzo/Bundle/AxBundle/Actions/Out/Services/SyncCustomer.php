<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services;

use Hanzo\Core\Tools;
use Hanzo\Model\Addresses;
use Hanzo\Model\Customers;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class SyncCustomer
 *
 * @package Hanzo\Bundle\AxBundle
 */
class SyncCustomer extends BaseService
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Customers
     */
    private $customer;

    /**
     * @var Addresses
     */
    private $address;


    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Reset object
     */
    public function reset()
    {
        $this->setDBConnection(null);
        $this->setEndPoint('');

        $this->customer = null;
        $this->address  = null;
        $this->data     = [];

    }

    /**
     * Set customer object.
     *
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->customer = $customer;
    }


    /**
     * Set address object.
     *
     * @param Addresses $address
     */
    public function setAddress(Addresses $address)
    {
        $this->address = $address;
    }


    /**
     * Build and retrieve for AX sync.
     *
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public function get()
    {
        if (empty($this->customer) || empty($this->address)) {
            throw new \InvalidArgumentException("Customer or Address object not set !");
        }

        $this->translator->setLocale(Tools::getLocaleFromDomainKey($this->getEndPoint()));

        $this->data = [
            'customer' => [
                'CustTable' => [
                    'AccountNum'             => $this->customer->getId(),
                    'AddressCity'            => $this->address->getCity(),
                    'AddressCountryRegionId' => $this->address->getCountries()->getIso2(),
                    'AddressStreet'          => $this->address->getAddressLine1(),
                    'AddressZipCode'         => $this->address->getPostalCode(),
                    'CustName'               => $this->address->getName($this->translator),
                    'Email'                  => $this->customer->getEmail(),
                    'Phone'                  => $this->customer->getPhone(),
                ]
            ],
            'endpointDomain' => $this->getEndPoint(),
        ];

        if (2 == $this->customer->getGroupsId()) {
            $this->data['customer']['CustTable']['InitialsId'] = $this->customer->getInitials();
        }

        return $this->data;
    }
}

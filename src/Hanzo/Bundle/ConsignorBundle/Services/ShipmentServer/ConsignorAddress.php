<?php

namespace Hanzo\Bundle\ConsignorBundle\Services\ShipmentServer;


class ConsignorAddress
{
    /**
     * @var array
     */
    protected $address = [];

    /**
     * @var array
     */
    protected $address_keys = [
        'Attention',
        'City',
        'CountryCode',
        'Email',
        'Kind',
        'Name1',
        'Phone',
        'PostCode',
        'Street1',
        'Street2',
    ];

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->address;
    }

    /**
     * Construct a new address
     *
     * @param int    $Kind
     * @param string $Name1
     * @param string $Street1
     * @param string $Street2
     * @param string $PostCode
     * @param string $City
     * @param string $CountryCode
     * @param string $Email
     * @param string $Phone
     * @param string $Attention
     */
    public function __construct($Kind = 1, $Name1, $Street1, $Street2, $PostCode, $City, $CountryCode, $Email = '', $Phone = '', $Attention = '')
    {
        foreach ($this->address_keys as $key) {
            if (!empty($$key)) {
                $this->address[$key] = $$key;
            }
        }
    }
}

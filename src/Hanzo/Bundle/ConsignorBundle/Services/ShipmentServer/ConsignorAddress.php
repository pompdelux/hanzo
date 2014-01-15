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
        'Kind',
        'Name1',
        'PostCode',
        'Street1',
        'Street2',
    ];

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
     * @param string $Attention
     */
    public function __construct($Kind = 1, $Name1, $Street1, $Street2, $PostCode, $City, $CountryCode, $Attention = '')
    {
        foreach ($this->address_keys as $key) {
            if (!empty($$key)) {
                $this->address[$key] = $$key;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->address;
    }
}

<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Providers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;

/**
 * Location provider interface
 *
 * @author Ulrik Nielsen <un@bellcom.dk>
 */
interface LocationProviderInterface
{
    /**
     * Find locations by address
     *
     * @param  array   $address_parts   Associative array of address parts to use in search
     * @param  integer $limit           Number of results to return
     * @throws LocatorException         If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array                    Array of results, empty array if none found
     */
    public function findByAddress(array $address_parts = [], $limit = 5);

    /**
     * Find locations by postal code
     *
     * @param  mixed   $postal_code     The postal code "string" to lookup
     * @param  integer $limit           Number of results to return
     * @throws LocatorException         If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array                    Array of results, empty array if none found
     */
    public function findByPostalCode($postal_code, $limit = 5);

    /**
     * Find locations by coordinates
     *
     * @param  float   $latitude        Latitude point
     * @param  float   $longitude       Longitude point
     * @param  string  $country_code    ISO 3166-1, Alpha-2 Country code
     * @param  integer $limit           Number of results to return
     * @throws LocatorException         If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array                    Array of results, empty array if none found
     */
    public function findByLocation($latitude, $longitude, $country_code, $limit = 5);

    /**
     * Build the form used to retrive the information needed for the lookup
     *
     * @param  FormBuilder $builder FormBuilder object
     * @param  Request     $request Request object
     * @throws LocatorException     If there are any logical problems
     * @return FormBuilder
     */
    public function getLookupForm(FormBuilder $builder, Request $request);
}

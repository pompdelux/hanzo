<?php

namespace Hanzo\Model;

use \Criteria;
use Hanzo\Core\Hanzo;
use Hanzo\Model\CountriesQuery;

use Hanzo\Model\om\BaseCountriesPeer;

/**
 * Skeleton subclass for performing query and update operations on the 'countries' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model
 */
class CountriesPeer extends BaseCountriesPeer
{


    /**
     * Get list of countries allowed on the current domain
     *
     * @param  boolean $as_select set to true, the method will return an assocoative array in id=>contry format
     * @return mixed
     */
    static public function getAvailableDomainCountries($as_select = false)
    {
        $hanzo = Hanzo::getInstance();
        $country_list = $hanzo->get('limits.allowed_countries', false);

        $query = CountriesQuery::create();

        if ('!' == substr($country_list, 0, 1)) {
            // black list
            $country_list = array_map('trim',explode(',', substr($country_list, 1)));
            $query->filterByName($country_list, Criteria::NOT_IN);
        } elseif ($country_list) {
            // white list
            $country_list = array_map('trim',explode(',', $country_list));
            $query->filterByName($country_list, Criteria::IN);
        }

        $query->orderByName();
        $result = $query->find();

        if ($as_select) {
            $data = array();
            foreach ($result as $record) {
                $data[$record->getId()] = $record->getName();
            }

            return $data;
        }

        return $result;
    }

} // CountriesPeer

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'addresses' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Hanzo.Model.map
 */
class AddressesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.AddressesTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('addresses');
        $this->setPhpName('Addresses');
        $this->setClassname('Hanzo\\Model\\Addresses');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('customers_id', 'CustomersId', 'INTEGER' , 'customers', 'id', true, null, null);
        $this->addPrimaryKey('type', 'Type', 'VARCHAR', true, 32, 'payment');
        $this->addColumn('title', 'Title', 'VARCHAR', false, 12, null);
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', true, 128, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', true, 128, null);
        $this->addColumn('address_line_1', 'AddressLine1', 'VARCHAR', true, 255, null);
        $this->addColumn('address_line_2', 'AddressLine2', 'VARCHAR', false, 255, null);
        $this->addColumn('postal_code', 'PostalCode', 'VARCHAR', true, 12, null);
        $this->addColumn('city', 'City', 'VARCHAR', true, 64, null);
        $this->addColumn('country', 'Country', 'VARCHAR', true, 128, null);
        $this->addForeignKey('countries_id', 'CountriesId', 'INTEGER', 'countries', 'id', true, null, null);
        $this->addColumn('state_province', 'StateProvince', 'VARCHAR', false, 64, null);
        $this->addColumn('company_name', 'CompanyName', 'VARCHAR', false, 128, null);
        $this->addColumn('external_address_id', 'ExternalAddressId', 'VARCHAR', false, 128, null);
        $this->addColumn('latitude', 'Latitude', 'DOUBLE', false, null, null);
        $this->addColumn('longitude', 'Longitude', 'DOUBLE', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Countries', 'Hanzo\\Model\\Countries', RelationMap::MANY_TO_ONE, array('countries_id' => 'id', ), null, null);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'geocodable' =>  array (
  'auto_update' => 'false',
  'latitude_column' => 'latitude',
  'longitude_column' => 'longitude',
  'geocode_ip' => 'false',
  'ip_column' => 'ip_address',
  'geocode_address' => 'true',
  'address_columns' => 'address_line_1,address_line_2,state_province,postal_code,country',
  'geocoder_provider' => '\\Geocoder\\Provider\\GoogleMapsProvider',
  'geocoder_adapter' => '\\Geocoder\\HttpAdapter\\CurlHttpAdapter',
  'geocoder_api_key' => 'false',
  'geocoder_api_key_provider' => '\\Hanzo\\Core\\Hanzo::getInstance()->getGoogleMapsKey()',
),
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
            'event' =>  array (
),
        );
    } // getBehaviors()

} // AddressesTableMap

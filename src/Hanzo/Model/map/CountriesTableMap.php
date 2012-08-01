<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'countries' table.
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
class CountriesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.CountriesTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
		// attributes
		$this->setName('countries');
		$this->setPhpName('Countries');
		$this->setClassname('Hanzo\\Model\\Countries');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, null);
		$this->addColumn('LOCAL_NAME', 'LocalName', 'VARCHAR', true, 128, null);
		$this->addColumn('CODE', 'Code', 'INTEGER', false, null, null);
		$this->addColumn('ISO2', 'Iso2', 'VARCHAR', true, 2, null);
		$this->addColumn('ISO3', 'Iso3', 'VARCHAR', true, 3, null);
		$this->addColumn('CONTINENT', 'Continent', 'VARCHAR', true, 2, null);
		$this->addColumn('CURRENCY_ID', 'CurrencyId', 'INTEGER', true, null, null);
		$this->addColumn('CURRENCY_CODE', 'CurrencyCode', 'VARCHAR', true, 3, null);
		$this->addColumn('CURRENCY_NAME', 'CurrencyName', 'VARCHAR', true, 32, null);
		$this->addColumn('VAT', 'Vat', 'DECIMAL', false, 4, null);
		$this->addColumn('CALLING_CODE', 'CallingCode', 'INTEGER', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Addresses', 'Hanzo\\Model\\Addresses', RelationMap::ONE_TO_MANY, array('id' => 'countries_id', ), null, null, 'Addressess');
		$this->addRelation('ZipToCity', 'Hanzo\\Model\\ZipToCity', RelationMap::ONE_TO_MANY, array('iso2' => 'countries_iso2', ), null, null, 'ZipToCitys');
		$this->addRelation('OrdersRelatedByBillingCountriesId', 'Hanzo\\Model\\Orders', RelationMap::ONE_TO_MANY, array('id' => 'billing_countries_id', ), null, null, 'OrderssRelatedByBillingCountriesId');
		$this->addRelation('OrdersRelatedByDeliveryCountriesId', 'Hanzo\\Model\\Orders', RelationMap::ONE_TO_MANY, array('id' => 'delivery_countries_id', ), null, null, 'OrderssRelatedByDeliveryCountriesId');
	} // buildRelations()

} // CountriesTableMap

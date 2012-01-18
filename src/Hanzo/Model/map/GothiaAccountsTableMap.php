<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'gothia_accounts' table.
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
class GothiaAccountsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.GothiaAccountsTableMap';

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
		$this->setName('gothia_accounts');
		$this->setPhpName('GothiaAccounts');
		$this->setClassname('Hanzo\\Model\\GothiaAccounts');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('CUSTOMERS_ID', 'CustomersId', 'INTEGER' , 'customers', 'ID', true, null, null);
		$this->addColumn('FIRST_NAME', 'FirstName', 'VARCHAR', true, 128, null);
		$this->addColumn('LAST_NAME', 'LastName', 'VARCHAR', true, 128, null);
		$this->addColumn('ADDRESS', 'Address', 'VARCHAR', true, 255, null);
		$this->addColumn('POSTAL_CODE', 'PostalCode', 'VARCHAR', true, 12, null);
		$this->addColumn('POSTAL_PLACE', 'PostalPlace', 'VARCHAR', true, 64, null);
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 255, null);
		$this->addColumn('PHONE', 'Phone', 'VARCHAR', true, 32, null);
		$this->addColumn('MOBILE_PHONE', 'MobilePhone', 'VARCHAR', false, 32, null);
		$this->addColumn('FAX', 'Fax', 'VARCHAR', false, 32, null);
		$this->addColumn('COUNTRY_CODE', 'CountryCode', 'VARCHAR', false, 4, null);
		$this->addColumn('DISTRIBUTION_BY', 'DistributionBy', 'VARCHAR', false, 255, null);
		$this->addColumn('DISTRIBUTION_TYPE', 'DistributionType', 'VARCHAR', false, 255, null);
		$this->addColumn('SOCIAL_SECURITY_NUM', 'SocialSecurityNum', 'VARCHAR', true, 12, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // GothiaAccountsTableMap

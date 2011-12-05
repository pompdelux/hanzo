<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'coupons_to_customers' table.
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
class CouponsToCustomersTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.CouponsToCustomersTableMap';

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
		$this->setName('coupons_to_customers');
		$this->setPhpName('CouponsToCustomers');
		$this->setClassname('Hanzo\\Model\\CouponsToCustomers');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('COUPONS_ID', 'CouponsId', 'INTEGER' , 'coupons', 'ID', true, null, null);
		$this->addForeignPrimaryKey('CUSTOMERS_ID', 'CustomersId', 'INTEGER' , 'customers', 'ID', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Coupons', 'Hanzo\\Model\\Coupons', RelationMap::MANY_TO_ONE, array('coupons_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CouponsToCustomersTableMap

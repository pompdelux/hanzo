<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'customers_gotia_attributes' table.
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
class CustomersGotiaAttributesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.CustomersGotiaAttributesTableMap';

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
		$this->setName('customers_gotia_attributes');
		$this->setPhpName('CustomersGotiaAttributes');
		$this->setClassname('Hanzo\\Model\\CustomersGotiaAttributes');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('CUSTOMERS_ID', 'CustomersId', 'INTEGER' , 'customers', 'ID', true, null, null);
		$this->addPrimaryKey('C_KEY', 'CKey', 'VARCHAR', true, 255, null);
		$this->addColumn('C_VALUE', 'CValue', 'VARCHAR', true, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CustomersGotiaAttributesTableMap

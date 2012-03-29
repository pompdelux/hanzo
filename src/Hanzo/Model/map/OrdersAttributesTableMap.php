<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_attributes' table.
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
class OrdersAttributesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.OrdersAttributesTableMap';

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
		$this->setName('orders_attributes');
		$this->setPhpName('OrdersAttributes');
		$this->setClassname('Hanzo\\Model\\OrdersAttributes');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ORDERS_ID', 'OrdersId', 'INTEGER' , 'orders', 'ID', true, null, null);
		$this->addPrimaryKey('NS', 'Ns', 'VARCHAR', true, 64, null);
		$this->addPrimaryKey('C_KEY', 'CKey', 'VARCHAR', true, 64, null);
		$this->addColumn('C_VALUE', 'CValue', 'VARCHAR', false, 255, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('OrdersAttributesVersion', 'Hanzo\\Model\\OrdersAttributesVersion', RelationMap::ONE_TO_MANY, array('orders_id' => 'orders_id', 'ns' => 'ns', 'c_key' => 'c_key', ), 'CASCADE', null, 'OrdersAttributesVersions');
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
			'versionable' => array('version_column' => 'version', 'version_table' => '', 'log_created_at' => 'false', 'log_created_by' => 'false', 'log_comment' => 'false', 'version_created_at_column' => 'version_created_at', 'version_created_by_column' => 'version_created_by', 'version_comment_column' => 'version_comment', ),
		);
	} // getBehaviors()

} // OrdersAttributesTableMap

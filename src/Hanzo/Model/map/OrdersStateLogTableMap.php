<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_state_log' table.
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
class OrdersStateLogTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.OrdersStateLogTableMap';

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
		$this->setName('orders_state_log');
		$this->setPhpName('OrdersStateLog');
		$this->setClassname('Hanzo\\Model\\OrdersStateLog');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ORDERS_ID', 'OrdersId', 'INTEGER' , 'orders', 'ID', true, null, null);
		$this->addPrimaryKey('STATE', 'State', 'INTEGER', true, null, null);
		$this->addPrimaryKey('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('MESSAGE', 'Message', 'VARCHAR', true, 128, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('OrdersStateLogVersion', 'Hanzo\\Model\\OrdersStateLogVersion', RelationMap::ONE_TO_MANY, array('orders_id' => 'orders_id', 'state' => 'state', 'created_at' => 'created_at', ), 'CASCADE', null, 'OrdersStateLogVersions');
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

} // OrdersStateLogTableMap

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'wall' table.
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
class WallTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.WallTableMap';

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
		$this->setName('wall');
		$this->setPhpName('Wall');
		$this->setClassname('Hanzo\\Model\\Wall');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('PARENT_ID', 'ParentId', 'INTEGER', 'wall', 'ID', false, null, null);
		$this->addForeignKey('CUSTOMERS_ID', 'CustomersId', 'INTEGER', 'customers', 'ID', true, null, null);
		$this->addColumn('MESSATE', 'Messate', 'CLOB', true, null, null);
		$this->addColumn('STATUS', 'Status', 'BOOLEAN', true, 1, true);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('WallRelatedByParentId', 'Hanzo\\Model\\Wall', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('WallRelatedById', 'Hanzo\\Model\\Wall', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), 'CASCADE', null, 'WallsRelatedById');
		$this->addRelation('WallLikes', 'Hanzo\\Model\\WallLikes', RelationMap::ONE_TO_MANY, array('id' => 'wall_id', ), 'CASCADE', null, 'WallLikess');
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
			'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
		);
	} // getBehaviors()

} // WallTableMap

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
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.map
 */
class OrdersAttributesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.map.OrdersAttributesTableMap';

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
		$this->setPackage('home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('C_KEY', 'CKey', 'VARCHAR', true, 64, null);
		$this->addPrimaryKey('NS', 'Ns', 'VARCHAR', true, 64, null);
		$this->addColumn('C_VALUE', 'CValue', 'VARCHAR', false, 255, null);
		$this->addForeignKey('ORDERS_ID', 'OrdersId', 'INTEGER', 'orders', 'ID', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // OrdersAttributesTableMap

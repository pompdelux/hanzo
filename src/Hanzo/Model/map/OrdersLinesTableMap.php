<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_lines' table.
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
class OrdersLinesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.OrdersLinesTableMap';

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
		$this->setName('orders_lines');
		$this->setPhpName('OrdersLines');
		$this->setClassname('Hanzo\\Model\\OrdersLines');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('ORDERS_ID', 'OrdersId', 'INTEGER', 'orders', 'ID', true, null, null);
		$this->addColumn('TYPE', 'Type', 'VARCHAR', true, 12, null);
		$this->addForeignKey('PRODUCTS_ID', 'ProductsId', 'INTEGER', 'products', 'ID', false, null, null);
		$this->addColumn('PRODUCTS_SKU', 'ProductsSku', 'VARCHAR', false, 255, null);
		$this->addColumn('PRODUCTS_NAME', 'ProductsName', 'VARCHAR', true, 255, null);
		$this->addColumn('PRODUCTS_COLOR', 'ProductsColor', 'VARCHAR', false, 128, null);
		$this->addColumn('PRODUCTS_SIZE', 'ProductsSize', 'VARCHAR', false, 32, null);
		$this->addColumn('EXPECTED_AT', 'ExpectedAt', 'DATE', false, null, '1970-01-01');
		$this->addColumn('ORIGINAL_PRICE', 'OriginalPrice', 'DECIMAL', false, 15, null);
		$this->addColumn('PRICE', 'Price', 'DECIMAL', false, 15, null);
		$this->addColumn('VAT', 'Vat', 'DECIMAL', false, 15, 0);
		$this->addColumn('QUANTITY', 'Quantity', 'INTEGER', false, null, null);
		$this->addColumn('UNIT', 'Unit', 'VARCHAR', false, 12, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'SET NULL', null);
	} // buildRelations()

} // OrdersLinesTableMap

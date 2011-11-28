<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products' table.
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
class ProductsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.map.ProductsTableMap';

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
		$this->setName('products');
		$this->setPhpName('Products');
		$this->setClassname('Hanzo\\Model\\Products');
		$this->setPackage('home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('SKU', 'Sku', 'VARCHAR', 'products', 'MASTER', true, 128, null);
		$this->addColumn('MASTER', 'Master', 'VARCHAR', true, 128, null);
		$this->addColumn('SIZE', 'Size', 'VARCHAR', true, 32, null);
		$this->addColumn('COLOR', 'Color', 'VARCHAR', true, 128, null);
		$this->addColumn('UNIT', 'Unit', 'VARCHAR', true, 12, null);
		$this->addColumn('WASHING', 'Washing', 'INTEGER', false, null, null);
		$this->addColumn('HAS_VIDEO', 'HasVideo', 'BOOLEAN', true, 1, true);
		$this->addColumn('IS_OUT_OF_STOCK', 'IsOutOfStock', 'BOOLEAN', true, 1, false);
		$this->addColumn('IS_ACTIVE', 'IsActive', 'BOOLEAN', true, 1, true);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('ProductsRelatedBySku', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('sku' => 'master', ), 'CASCADE', null);
		$this->addRelation('MannequinImages', 'Hanzo\\Model\\MannequinImages', RelationMap::ONE_TO_ONE, array('master' => 'master', ), null, null);
		$this->addRelation('ProductsRelatedByMaster', 'Hanzo\\Model\\Products', RelationMap::ONE_TO_MANY, array('master' => 'sku', ), 'CASCADE', null, 'ProductssRelatedByMaster');
		$this->addRelation('ProductsDomainsPrices', 'Hanzo\\Model\\ProductsDomainsPrices', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsDomainsPricess');
		$this->addRelation('ProductsImages', 'Hanzo\\Model\\ProductsImages', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), null, null, 'ProductsImagess');
		$this->addRelation('ProductsImagesCategoriesSort', 'Hanzo\\Model\\ProductsImagesCategoriesSort', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsImagesCategoriesSorts');
		$this->addRelation('ProductsImagesProductReferences', 'Hanzo\\Model\\ProductsImagesProductReferences', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsImagesProductReferencess');
		$this->addRelation('ProductsStock', 'Hanzo\\Model\\ProductsStock', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsStocks');
		$this->addRelation('ProductsToCategories', 'Hanzo\\Model\\ProductsToCategories', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsToCategoriess');
		$this->addRelation('OrdersLines', 'Hanzo\\Model\\OrdersLines', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'SET NULL', null, 'OrdersLiness');
		$this->addRelation('ProductsI18n', 'Hanzo\\Model\\ProductsI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'ProductsI18ns');
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
			'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'title, content', 'locale_column' => 'locale', 'default_locale' => '', 'locale_alias' => '', ),
			'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
		);
	} // getBehaviors()

} // ProductsTableMap

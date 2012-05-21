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
 * @package    propel.generator.src.Hanzo.Model.map
 */
class ProductsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.ProductsTableMap';

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
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('SKU', 'Sku', 'VARCHAR', true, 128, null);
		$this->addForeignKey('MASTER', 'Master', 'VARCHAR', 'products', 'SKU', false, 128, null);
		$this->addColumn('SIZE', 'Size', 'VARCHAR', false, 32, null);
		$this->addColumn('COLOR', 'Color', 'VARCHAR', false, 128, null);
		$this->addColumn('UNIT', 'Unit', 'VARCHAR', false, 12, null);
		$this->addForeignKey('WASHING', 'Washing', 'INTEGER', 'products_washing_instructions', 'CODE', false, null, null);
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
		$this->addRelation('ProductsRelatedByMaster', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('master' => 'sku', ), 'CASCADE', null);
		$this->addRelation('ProductsWashingInstructions', 'Hanzo\\Model\\ProductsWashingInstructions', RelationMap::MANY_TO_ONE, array('washing' => 'code', ), null, null);
		$this->addRelation('MannequinImages', 'Hanzo\\Model\\MannequinImages', RelationMap::ONE_TO_MANY, array('sku' => 'master', ), null, null, 'MannequinImagess');
		$this->addRelation('ProductsRelatedBySku', 'Hanzo\\Model\\Products', RelationMap::ONE_TO_MANY, array('sku' => 'master', ), 'CASCADE', null, 'ProductssRelatedBySku');
		$this->addRelation('ProductsDomainsPrices', 'Hanzo\\Model\\ProductsDomainsPrices', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsDomainsPricess');
		$this->addRelation('ProductsImages', 'Hanzo\\Model\\ProductsImages', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsImagess');
		$this->addRelation('ProductsImagesCategoriesSort', 'Hanzo\\Model\\ProductsImagesCategoriesSort', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsImagesCategoriesSorts');
		$this->addRelation('ProductsImagesProductReferences', 'Hanzo\\Model\\ProductsImagesProductReferences', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsImagesProductReferencess');
		$this->addRelation('ProductsQuantityDiscount', 'Hanzo\\Model\\ProductsQuantityDiscount', RelationMap::ONE_TO_MANY, array('sku' => 'products_master', ), 'CASCADE', null, 'ProductsQuantityDiscounts');
		$this->addRelation('ProductsStock', 'Hanzo\\Model\\ProductsStock', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsStocks');
		$this->addRelation('ProductsToCategories', 'Hanzo\\Model\\ProductsToCategories', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'CASCADE', null, 'ProductsToCategoriess');
		$this->addRelation('OrdersLines', 'Hanzo\\Model\\OrdersLines', RelationMap::ONE_TO_MANY, array('id' => 'products_id', ), 'SET NULL', null, 'OrdersLiness');
		$this->addRelation('RelatedProductsRelatedByMaster', 'Hanzo\\Model\\RelatedProducts', RelationMap::ONE_TO_MANY, array('sku' => 'master', ), 'CASCADE', null, 'RelatedProductssRelatedByMaster');
		$this->addRelation('RelatedProductsRelatedBySku', 'Hanzo\\Model\\RelatedProducts', RelationMap::ONE_TO_MANY, array('sku' => 'sku', ), 'CASCADE', null, 'RelatedProductssRelatedBySku');
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
			'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'title, content', 'locale_column' => 'locale', 'default_locale' => 'da_DK', 'locale_alias' => '', ),
			'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
		);
	} // getBehaviors()

} // ProductsTableMap

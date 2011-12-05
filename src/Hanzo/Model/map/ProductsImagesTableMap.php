<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products_images' table.
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
class ProductsImagesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.ProductsImagesTableMap';

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
		$this->setName('products_images');
		$this->setPhpName('ProductsImages');
		$this->setClassname('Hanzo\\Model\\ProductsImages');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('PRODUCTS_ID', 'ProductsId', 'INTEGER', 'products', 'ID', true, null, null);
		$this->addColumn('IMAGE', 'Image', 'VARCHAR', true, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), null, null);
		$this->addRelation('ProductsImagesCategoriesSort', 'Hanzo\\Model\\ProductsImagesCategoriesSort', RelationMap::ONE_TO_MANY, array('id' => 'products_images_id', ), 'CASCADE', null, 'ProductsImagesCategoriesSorts');
		$this->addRelation('ProductsImagesProductReferences', 'Hanzo\\Model\\ProductsImagesProductReferences', RelationMap::ONE_TO_MANY, array('id' => 'products_images_id', ), 'CASCADE', null, 'ProductsImagesProductReferencess');
	} // buildRelations()

} // ProductsImagesTableMap

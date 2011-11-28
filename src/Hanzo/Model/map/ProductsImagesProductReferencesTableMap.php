<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products_images_product_references' table.
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
class ProductsImagesProductReferencesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.map.ProductsImagesProductReferencesTableMap';

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
		$this->setName('products_images_product_references');
		$this->setPhpName('ProductsImagesProductReferences');
		$this->setClassname('Hanzo\\Model\\ProductsImagesProductReferences');
		$this->setPackage('home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('PRODUCTS_IMAGES_ID', 'ProductsImagesId', 'INTEGER' , 'products_images', 'ID', true, null, null);
		$this->addForeignPrimaryKey('PRODUCTS_ID', 'ProductsId', 'INTEGER' , 'products', 'ID', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('ProductsImages', 'Hanzo\\Model\\ProductsImages', RelationMap::MANY_TO_ONE, array('products_images_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // ProductsImagesProductReferencesTableMap

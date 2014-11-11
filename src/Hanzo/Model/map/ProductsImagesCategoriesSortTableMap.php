<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products_images_categories_sort' table.
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
class ProductsImagesCategoriesSortTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.ProductsImagesCategoriesSortTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('products_images_categories_sort');
        $this->setPhpName('ProductsImagesCategoriesSort');
        $this->setClassname('Hanzo\\Model\\ProductsImagesCategoriesSort');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('products_id', 'ProductsId', 'INTEGER' , 'products', 'id', true, null, null);
        $this->addForeignPrimaryKey('categories_id', 'CategoriesId', 'INTEGER' , 'categories', 'id', true, null, null);
        $this->addForeignPrimaryKey('products_images_id', 'ProductsImagesId', 'INTEGER' , 'products_images', 'id', true, null, null);
        $this->addColumn('sort', 'Sort', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('ProductsImages', 'Hanzo\\Model\\ProductsImages', RelationMap::MANY_TO_ONE, array('products_images_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Categories', 'Hanzo\\Model\\Categories', RelationMap::MANY_TO_ONE, array('categories_id' => 'id', ), 'CASCADE', null);
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
            'event' =>  array (
),
        );
    } // getBehaviors()

} // ProductsImagesCategoriesSortTableMap

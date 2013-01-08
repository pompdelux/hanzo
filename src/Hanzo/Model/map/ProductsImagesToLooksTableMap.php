<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products_images_to_looks' table.
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
class ProductsImagesToLooksTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.ProductsImagesToLooksTableMap';

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
        $this->setName('products_images_to_looks');
        $this->setPhpName('ProductsImagesToLooks');
        $this->setClassname('Hanzo\\Model\\ProductsImagesToLooks');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('products_images_id', 'ProductsImagesId', 'INTEGER' , 'products_images', 'id', true, null, null);
        $this->addForeignPrimaryKey('looks_id', 'LooksId', 'INTEGER' , 'looks', 'id', true, null, null);
        $this->addColumn('sort', 'Sort', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ProductsImages', 'Hanzo\\Model\\ProductsImages', RelationMap::MANY_TO_ONE, array('products_images_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Looks', 'Hanzo\\Model\\Looks', RelationMap::MANY_TO_ONE, array('looks_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // ProductsImagesToLooksTableMap

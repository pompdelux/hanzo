<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'wishlists_lines' table.
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
class WishlistsLinesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.WishlistsLinesTableMap';

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
        $this->setName('wishlists_lines');
        $this->setPhpName('WishlistsLines');
        $this->setClassname('Hanzo\\Model\\WishlistsLines');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('wishlists_id', 'WishlistsId', 'VARCHAR', 'wishlists', 'id', true, 5, null);
        $this->addForeignKey('products_id', 'ProductsId', 'INTEGER', 'products', 'id', false, null, null);
        $this->addColumn('quantity', 'Quantity', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Wishlists', 'Hanzo\\Model\\Wishlists', RelationMap::MANY_TO_ONE, array('wishlists_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // WishlistsLinesTableMap

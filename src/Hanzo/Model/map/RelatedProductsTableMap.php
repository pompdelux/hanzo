<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'related_products' table.
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
class RelatedProductsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.RelatedProductsTableMap';

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
        $this->setName('related_products');
        $this->setPhpName('RelatedProducts');
        $this->setClassname('Hanzo\\Model\\RelatedProducts');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('master', 'Master', 'VARCHAR' , 'products', 'sku', true, 128, null);
        $this->addForeignPrimaryKey('sku', 'Sku', 'VARCHAR' , 'products', 'sku', true, 128, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ProductsRelatedByMaster', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('master' => 'sku', ), 'CASCADE', null);
        $this->addRelation('ProductsRelatedBySku', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('sku' => 'sku', ), 'CASCADE', null);
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

} // RelatedProductsTableMap

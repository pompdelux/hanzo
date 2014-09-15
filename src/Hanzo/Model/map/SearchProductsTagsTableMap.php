<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'search_products_tags' table.
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
class SearchProductsTagsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.SearchProductsTagsTableMap';

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
        $this->setName('search_products_tags');
        $this->setPhpName('SearchProductsTags');
        $this->setClassname('Hanzo\\Model\\SearchProductsTags');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('master_products_id', 'MasterProductsId', 'INTEGER', 'products', 'id', true, null, null);
        $this->addForeignKey('products_id', 'ProductsId', 'INTEGER', 'products', 'id', true, null, null);
        $this->addColumn('token', 'Token', 'VARCHAR', true, 128, null);
        $this->addColumn('locale', 'Locale', 'VARCHAR', true, 12, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ProductsRelatedByMasterProductsId', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('master_products_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('ProductsRelatedByProductsId', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'CASCADE', null);
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

} // SearchProductsTagsTableMap

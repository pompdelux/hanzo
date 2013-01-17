<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products_quantity_discount' table.
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
class ProductsQuantityDiscountTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.ProductsQuantityDiscountTableMap';

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
        $this->setName('products_quantity_discount');
        $this->setPhpName('ProductsQuantityDiscount');
        $this->setClassname('Hanzo\\Model\\ProductsQuantityDiscount');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('PRODUCTS_MASTER', 'ProductsMaster', 'VARCHAR' , 'products', 'SKU', true, 128, null);
        $this->addForeignPrimaryKey('DOMAINS_ID', 'DomainsId', 'INTEGER' , 'domains', 'ID', true, null, null);
        $this->addPrimaryKey('SPAN', 'Span', 'INTEGER', true, null, null);
        $this->addColumn('DISCOUNT', 'Discount', 'DECIMAL', true, 15, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_master' => 'sku', ), 'CASCADE', null);
        $this->addRelation('Domains', 'Hanzo\\Model\\Domains', RelationMap::MANY_TO_ONE, array('domains_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // ProductsQuantityDiscountTableMap

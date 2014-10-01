<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_lines' table.
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
class OrdersLinesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.OrdersLinesTableMap';

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
        $this->setName('orders_lines');
        $this->setPhpName('OrdersLines');
        $this->setClassname('Hanzo\\Model\\OrdersLines');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('orders_id', 'OrdersId', 'INTEGER', 'orders', 'id', true, null, null);
        $this->addColumn('type', 'Type', 'VARCHAR', true, 12, null);
        $this->addForeignKey('products_id', 'ProductsId', 'INTEGER', 'products', 'id', false, null, null);
        $this->addColumn('products_sku', 'ProductsSku', 'VARCHAR', false, 255, null);
        $this->addColumn('products_name', 'ProductsName', 'VARCHAR', true, 255, null);
        $this->addColumn('products_color', 'ProductsColor', 'VARCHAR', false, 128, null);
        $this->addColumn('products_size', 'ProductsSize', 'VARCHAR', true, 32, null);
        $this->addColumn('expected_at', 'ExpectedAt', 'DATE', false, null, '1970-01-01');
        $this->addColumn('original_price', 'OriginalPrice', 'DECIMAL', false, 15, null);
        $this->addColumn('price', 'Price', 'DECIMAL', false, 15, null);
        $this->addColumn('vat', 'Vat', 'DECIMAL', false, 15, 0);
        $this->addColumn('quantity', 'Quantity', 'INTEGER', false, null, null);
        $this->addColumn('unit', 'Unit', 'VARCHAR', false, 12, null);
        $this->addColumn('is_voucher', 'IsVoucher', 'BOOLEAN', true, 1, false);
        $this->addColumn('note', 'Note', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'SET NULL', null);
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

} // OrdersLinesTableMap

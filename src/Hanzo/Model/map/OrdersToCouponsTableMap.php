<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_to_coupons' table.
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
class OrdersToCouponsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.OrdersToCouponsTableMap';

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
        $this->setName('orders_to_coupons');
        $this->setPhpName('OrdersToCoupons');
        $this->setClassname('Hanzo\\Model\\OrdersToCoupons');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('ORDERS_ID', 'OrdersId', 'INTEGER' , 'orders', 'ID', true, null, null);
        $this->addForeignPrimaryKey('COUPONS_ID', 'CouponsId', 'INTEGER' , 'coupons', 'ID', true, null, null);
        $this->addColumn('AMOUNT', 'Amount', 'DECIMAL', true, 15, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Coupons', 'Hanzo\\Model\\Coupons', RelationMap::MANY_TO_ONE, array('coupons_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // OrdersToCouponsTableMap

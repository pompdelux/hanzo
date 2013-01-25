<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_deleted_log' table.
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
class OrdersDeletedLogTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.OrdersDeletedLogTableMap';

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
        $this->setName('orders_deleted_log');
        $this->setPhpName('OrdersDeletedLog');
        $this->setClassname('Hanzo\\Model\\OrdersDeletedLog');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('ORDERS_ID', 'OrdersId', 'INTEGER', true, null, null);
        $this->addColumn('CUSTOMERS_ID', 'CustomersId', 'INTEGER', false, null, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', false, 255, null);
        $this->addColumn('EMAIL', 'Email', 'VARCHAR', false, 255, null);
        $this->addColumn('TRIGGER', 'Trigger', 'VARCHAR', false, 255, null);
        $this->addColumn('CONTENT', 'Content', 'CLOB', true, null, null);
        $this->addColumn('DELETED_BY', 'DeletedBy', 'VARCHAR', true, 255, null);
        $this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // OrdersDeletedLogTableMap

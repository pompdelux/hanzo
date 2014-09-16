<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders_to_ax_queue_log' table.
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
class OrdersToAxQueueLogTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.OrdersToAxQueueLogTableMap';

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
        $this->setName('orders_to_ax_queue_log');
        $this->setPhpName('OrdersToAxQueueLog');
        $this->setClassname('Hanzo\\Model\\OrdersToAxQueueLog');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('orders_id', 'OrdersId', 'INTEGER' , 'orders', 'id', true, null, null);
        $this->addPrimaryKey('queue_id', 'QueueId', 'INTEGER', true, null, null);
        $this->addColumn('iteration', 'Iteration', 'INTEGER', true, null, 1);
        $this->addPrimaryKey('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::MANY_TO_ONE, array('orders_id' => 'id', ), 'CASCADE', null);
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
            'event' =>  array (),
        );
    } // getBehaviors()

} // OrdersToAxQueueLogTableMap

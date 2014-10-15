<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'shipping_methods' table.
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
class ShippingMethodsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.ShippingMethodsTableMap';

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
        $this->setName('shipping_methods');
        $this->setPhpName('ShippingMethods');
        $this->setClassname('Hanzo\\Model\\ShippingMethods');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('carrier', 'Carrier', 'VARCHAR', true, 255, null);
        $this->addColumn('method', 'Method', 'VARCHAR', true, 255, null);
        $this->addColumn('external_id', 'ExternalId', 'VARCHAR', true, 32, null);
        $this->addColumn('calc_engine', 'CalcEngine', 'VARCHAR', true, 32, 'flat');
        $this->addColumn('price', 'Price', 'DECIMAL', true, 15, null);
        $this->addColumn('fee', 'Fee', 'DECIMAL', false, 15, 0);
        $this->addColumn('fee_external_id', 'FeeExternalId', 'VARCHAR', false, 32, null);
        $this->addColumn('is_active', 'IsActive', 'BOOLEAN', true, 1, true);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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

} // ShippingMethodsTableMap

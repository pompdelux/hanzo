<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'coupons' table.
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
class CouponsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.CouponsTableMap';

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
        $this->setName('coupons');
        $this->setPhpName('Coupons');
        $this->setClassname('Hanzo\\Model\\Coupons');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('code', 'Code', 'VARCHAR', true, 12, null);
        $this->addColumn('amount', 'Amount', 'DECIMAL', true, 15, null);
        $this->addColumn('amount_type', 'AmountType', 'VARCHAR', true, 6, 'amount');
        $this->addColumn('min_purchase_amount', 'MinPurchaseAmount', 'DECIMAL', false, 15, null);
        $this->addColumn('currency_code', 'CurrencyCode', 'VARCHAR', true, 3, null);
        $this->addColumn('active_from', 'ActiveFrom', 'TIMESTAMP', false, null, null);
        $this->addColumn('active_to', 'ActiveTo', 'TIMESTAMP', false, null, null);
        $this->addColumn('is_active', 'IsActive', 'BOOLEAN', true, 1, true);
        $this->addColumn('is_used', 'IsUsed', 'BOOLEAN', true, 1, false);
        $this->addColumn('is_reusable', 'IsReusable', 'BOOLEAN', true, 1, false);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('OrdersToCoupons', 'Hanzo\\Model\\OrdersToCoupons', RelationMap::ONE_TO_MANY, array('id' => 'coupons_id', ), 'CASCADE', null, 'OrdersToCouponss');
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
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
            'event' =>  array (
),
        );
    } // getBehaviors()

} // CouponsTableMap

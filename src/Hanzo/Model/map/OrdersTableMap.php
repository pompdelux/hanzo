<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'orders' table.
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
class OrdersTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.OrdersTableMap';

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
        $this->setName('orders');
        $this->setPhpName('Orders');
        $this->setClassname('Hanzo\\Model\\Orders');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('version_id', 'VersionId', 'INTEGER', true, null, 1);
        $this->addColumn('session_id', 'SessionId', 'VARCHAR', true, 32, null);
        $this->addColumn('payment_gateway_id', 'PaymentGatewayId', 'INTEGER', false, null, null);
        $this->addColumn('state', 'State', 'INTEGER', true, null, -50);
        $this->addColumn('in_edit', 'InEdit', 'BOOLEAN', true, 1, false);
        $this->addForeignKey('customers_id', 'CustomersId', 'INTEGER', 'customers', 'id', false, null, null);
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', false, 128, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', false, 128, null);
        $this->addColumn('email', 'Email', 'VARCHAR', false, 255, null);
        $this->addColumn('phone', 'Phone', 'VARCHAR', false, 32, null);
        $this->addColumn('languages_id', 'LanguagesId', 'INTEGER', true, null, null);
        $this->addColumn('currency_code', 'CurrencyCode', 'VARCHAR', true, 12, '');
        $this->addColumn('billing_title', 'BillingTitle', 'VARCHAR', false, 12, null);
        $this->addColumn('billing_first_name', 'BillingFirstName', 'VARCHAR', true, 128, null);
        $this->addColumn('billing_last_name', 'BillingLastName', 'VARCHAR', true, 128, null);
        $this->addColumn('billing_address_line_1', 'BillingAddressLine1', 'VARCHAR', false, 255, null);
        $this->addColumn('billing_address_line_2', 'BillingAddressLine2', 'VARCHAR', false, 255, null);
        $this->addColumn('billing_postal_code', 'BillingPostalCode', 'VARCHAR', false, 12, null);
        $this->addColumn('billing_city', 'BillingCity', 'VARCHAR', false, 64, null);
        $this->addColumn('billing_country', 'BillingCountry', 'VARCHAR', false, 128, null);
        $this->addForeignKey('billing_countries_id', 'BillingCountriesId', 'INTEGER', 'countries', 'id', false, null, null);
        $this->addColumn('billing_state_province', 'BillingStateProvince', 'VARCHAR', false, 64, null);
        $this->addColumn('billing_company_name', 'BillingCompanyName', 'VARCHAR', false, 128, null);
        $this->addColumn('billing_method', 'BillingMethod', 'VARCHAR', false, 64, null);
        $this->addColumn('billing_external_address_id', 'BillingExternalAddressId', 'VARCHAR', false, 128, null);
        $this->addColumn('delivery_title', 'DeliveryTitle', 'VARCHAR', false, 12, null);
        $this->addColumn('delivery_first_name', 'DeliveryFirstName', 'VARCHAR', true, 128, null);
        $this->addColumn('delivery_last_name', 'DeliveryLastName', 'VARCHAR', true, 128, null);
        $this->addColumn('delivery_address_line_1', 'DeliveryAddressLine1', 'VARCHAR', false, 255, null);
        $this->addColumn('delivery_address_line_2', 'DeliveryAddressLine2', 'VARCHAR', false, 255, null);
        $this->addColumn('delivery_postal_code', 'DeliveryPostalCode', 'VARCHAR', false, 12, null);
        $this->addColumn('delivery_city', 'DeliveryCity', 'VARCHAR', false, 64, null);
        $this->addColumn('delivery_country', 'DeliveryCountry', 'VARCHAR', false, 128, null);
        $this->addForeignKey('delivery_countries_id', 'DeliveryCountriesId', 'INTEGER', 'countries', 'id', false, null, null);
        $this->addColumn('delivery_state_province', 'DeliveryStateProvince', 'VARCHAR', false, 64, null);
        $this->addColumn('delivery_company_name', 'DeliveryCompanyName', 'VARCHAR', false, 128, null);
        $this->addColumn('delivery_method', 'DeliveryMethod', 'VARCHAR', false, 64, null);
        $this->addColumn('delivery_external_address_id', 'DeliveryExternalAddressId', 'VARCHAR', false, 128, null);
        $this->addForeignKey('events_id', 'EventsId', 'INTEGER', 'events', 'id', false, null, null);
        $this->addColumn('finished_at', 'FinishedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), 'SET NULL', 'CASCADE');
        $this->addRelation('CountriesRelatedByBillingCountriesId', 'Hanzo\\Model\\Countries', RelationMap::MANY_TO_ONE, array('billing_countries_id' => 'id', ), null, null);
        $this->addRelation('CountriesRelatedByDeliveryCountriesId', 'Hanzo\\Model\\Countries', RelationMap::MANY_TO_ONE, array('delivery_countries_id' => 'id', ), null, null);
        $this->addRelation('Events', 'Hanzo\\Model\\Events', RelationMap::MANY_TO_ONE, array('events_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('OrdersToCoupons', 'Hanzo\\Model\\OrdersToCoupons', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersToCouponss');
        $this->addRelation('OrdersAttributes', 'Hanzo\\Model\\OrdersAttributes', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersAttributess');
        $this->addRelation('OrdersLines', 'Hanzo\\Model\\OrdersLines', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersLiness');
        $this->addRelation('OrdersStateLog', 'Hanzo\\Model\\OrdersStateLog', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersStateLogs');
        $this->addRelation('OrdersSyncLog', 'Hanzo\\Model\\OrdersSyncLog', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersSyncLogs');
        $this->addRelation('OrdersVersions', 'Hanzo\\Model\\OrdersVersions', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersVersionss');
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

} // OrdersTableMap

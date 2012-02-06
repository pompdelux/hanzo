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
	 * @return     void
	 * @throws     PropelException
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
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('SESSION_ID', 'SessionId', 'VARCHAR', true, 32, null);
		$this->addColumn('PAYMENT_GATEWAY_ID', 'PaymentGatewayId', 'INTEGER', false, null, null);
		$this->addColumn('STATE', 'State', 'INTEGER', true, null, -3);
		$this->addColumn('IN_EDIT', 'InEdit', 'BOOLEAN', true, 1, false);
		$this->addColumn('CUSTOMERS_ID', 'CustomersId', 'INTEGER', false, null, null);
		$this->addColumn('FIRST_NAME', 'FirstName', 'VARCHAR', false, 128, null);
		$this->addColumn('LAST_NAME', 'LastName', 'VARCHAR', false, 128, null);
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', false, 255, null);
		$this->addColumn('PHONE', 'Phone', 'VARCHAR', false, 32, null);
		$this->addColumn('LANGUAGES_ID', 'LanguagesId', 'INTEGER', true, null, null);
		$this->addColumn('CURRENCY_ID', 'CurrencyId', 'INTEGER', true, null, null);
		$this->addColumn('BILLING_FIRST_NAME', 'BillingFirstName', 'VARCHAR', true, 128, null);
		$this->addColumn('BILLING_LAST_NAME', 'BillingLastName', 'VARCHAR', true, 128, null);
		$this->addColumn('BILLING_ADDRESS_LINE_1', 'BillingAddressLine1', 'VARCHAR', false, 255, null);
		$this->addColumn('BILLING_ADDRESS_LINE_2', 'BillingAddressLine2', 'VARCHAR', false, 255, null);
		$this->addColumn('BILLING_POSTAL_CODE', 'BillingPostalCode', 'VARCHAR', false, 12, null);
		$this->addColumn('BILLING_CITY', 'BillingCity', 'VARCHAR', false, 64, null);
		$this->addColumn('BILLING_COUNTRY', 'BillingCountry', 'VARCHAR', false, 128, null);
		$this->addForeignKey('BILLING_COUNTRIES_ID', 'BillingCountriesId', 'INTEGER', 'countries', 'ID', false, null, null);
		$this->addColumn('BILLING_STATE_PROVINCE', 'BillingStateProvince', 'VARCHAR', false, 64, null);
		$this->addColumn('BILLING_COMPANY_NAME', 'BillingCompanyName', 'VARCHAR', false, 128, null);
		$this->addColumn('BILLING_METHOD', 'BillingMethod', 'VARCHAR', false, 64, null);
		$this->addColumn('DELIVERY_FIRST_NAME', 'DeliveryFirstName', 'VARCHAR', true, 128, null);
		$this->addColumn('DELIVERY_LAST_NAME', 'DeliveryLastName', 'VARCHAR', true, 128, null);
		$this->addColumn('DELIVERY_ADDRESS_LINE_1', 'DeliveryAddressLine1', 'VARCHAR', false, 255, null);
		$this->addColumn('DELIVERY_ADDRESS_LINE_2', 'DeliveryAddressLine2', 'VARCHAR', false, 255, null);
		$this->addColumn('DELIVERY_POSTAL_CODE', 'DeliveryPostalCode', 'VARCHAR', false, 12, null);
		$this->addColumn('DELIVERY_CITY', 'DeliveryCity', 'VARCHAR', false, 64, null);
		$this->addColumn('DELIVERY_COUNTRY', 'DeliveryCountry', 'VARCHAR', false, 128, null);
		$this->addForeignKey('DELIVERY_COUNTRIES_ID', 'DeliveryCountriesId', 'INTEGER', 'countries', 'ID', false, null, null);
		$this->addColumn('DELIVERY_STATE_PROVINCE', 'DeliveryStateProvince', 'VARCHAR', false, 64, null);
		$this->addColumn('DELIVERY_COMPANY_NAME', 'DeliveryCompanyName', 'VARCHAR', false, 128, null);
		$this->addColumn('DELIVERY_METHOD', 'DeliveryMethod', 'VARCHAR', false, 64, null);
		$this->addColumn('FINISHED_AT', 'FinishedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('CountriesRelatedByBillingCountriesId', 'Hanzo\\Model\\Countries', RelationMap::MANY_TO_ONE, array('billing_countries_id' => 'id', ), null, null);
		$this->addRelation('CountriesRelatedByDeliveryCountriesId', 'Hanzo\\Model\\Countries', RelationMap::MANY_TO_ONE, array('delivery_countries_id' => 'id', ), null, null);
		$this->addRelation('OrdersAttributes', 'Hanzo\\Model\\OrdersAttributes', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersAttributess');
		$this->addRelation('OrdersLines', 'Hanzo\\Model\\OrdersLines', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersLiness');
		$this->addRelation('OrdersStateLog', 'Hanzo\\Model\\OrdersStateLog', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersStateLogs');
		$this->addRelation('OrdersSyncLog', 'Hanzo\\Model\\OrdersSyncLog', RelationMap::ONE_TO_MANY, array('id' => 'orders_id', ), 'CASCADE', null, 'OrdersSyncLogs');
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
			'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
		);
	} // getBehaviors()

} // OrdersTableMap

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'events' table.
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
class EventsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.EventsTableMap';

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
		$this->setName('events');
		$this->setPhpName('Events');
		$this->setClassname('Hanzo\\Model\\Events');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CODE', 'Code', 'VARCHAR', true, 32, null);
		$this->addColumn('KEY', 'Key', 'VARCHAR', true, 64, null);
		$this->addForeignKey('CONSULTANTS_ID', 'ConsultantsId', 'INTEGER', 'customers', 'ID', true, null, null);
		$this->addForeignKey('CUSTOMERS_ID', 'CustomersId', 'INTEGER', 'customers', 'ID', true, null, null);
		$this->addColumn('EVENT_DATE', 'EventDate', 'TIMESTAMP', true, null, null);
		$this->addColumn('HOST', 'Host', 'VARCHAR', true, 128, null);
		$this->addColumn('ADDRESS_LINE_1', 'AddressLine1', 'VARCHAR', true, 128, null);
		$this->addColumn('ADDRESS_LINE_2', 'AddressLine2', 'VARCHAR', false, 128, null);
		$this->addColumn('POSTAL_CODE', 'PostalCode', 'VARCHAR', true, 12, null);
		$this->addColumn('CITY', 'City', 'VARCHAR', true, 64, null);
		$this->addColumn('PHONE', 'Phone', 'VARCHAR', true, 32, null);
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 255, null);
		$this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
		$this->addColumn('TYPE', 'Type', 'VARCHAR', true, 3, 'AR');
		$this->addColumn('IS_OPEN', 'IsOpen', 'BOOLEAN', true, 1, false);
		$this->addColumn('NOTIFY_HOSTESS', 'NotifyHostess', 'BOOLEAN', true, 1, true);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('CustomersRelatedByConsultantsId', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('consultants_id' => 'id', ), null, null);
		$this->addRelation('CustomersRelatedByCustomersId', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('customers_id' => 'id', ), null, null);
		$this->addRelation('EventsParticipants', 'Hanzo\\Model\\EventsParticipants', RelationMap::ONE_TO_MANY, array('id' => 'events_id', ), 'CASCADE', null, 'EventsParticipantss');
		$this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::ONE_TO_MANY, array('id' => 'events_id', ), 'CASCADE', 'CASCADE', 'Orderss');
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

} // EventsTableMap

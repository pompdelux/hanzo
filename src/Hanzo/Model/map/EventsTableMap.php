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
     * @return void
     * @throws PropelException
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
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('code', 'Code', 'VARCHAR', true, 32, null);
        $this->addColumn('key', 'Key', 'VARCHAR', true, 64, null);
        $this->addForeignKey('consultants_id', 'ConsultantsId', 'INTEGER', 'customers', 'id', true, null, null);
        $this->addForeignKey('customers_id', 'CustomersId', 'INTEGER', 'customers', 'id', true, null, null);
        $this->addColumn('event_date', 'EventDate', 'TIMESTAMP', true, null, null);
        $this->addColumn('event_end_time', 'EventEndTime', 'TIMESTAMP', false, null, null);
        $this->addColumn('host', 'Host', 'VARCHAR', true, 128, null);
        $this->addColumn('address_line_1', 'AddressLine1', 'VARCHAR', true, 128, null);
        $this->addColumn('address_line_2', 'AddressLine2', 'VARCHAR', false, 128, null);
        $this->addColumn('postal_code', 'PostalCode', 'VARCHAR', true, 12, null);
        $this->addColumn('city', 'City', 'VARCHAR', true, 64, null);
        $this->addColumn('phone', 'Phone', 'VARCHAR', true, 32, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 255, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addColumn('type', 'Type', 'VARCHAR', true, 3, 'AR');
        $this->addColumn('is_open', 'IsOpen', 'BOOLEAN', false, 1, null);
        $this->addColumn('notify_hostess', 'NotifyHostess', 'BOOLEAN', true, 1, true);
        $this->addColumn('rsvp_type', 'RsvpType', 'SMALLINT', false, 1, null);
        $this->addColumn('public_note', 'PublicNote', 'LONGVARCHAR', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
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
        $this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::ONE_TO_MANY, array('id' => 'events_id', ), null, 'CASCADE', 'Orderss');
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

} // EventsTableMap

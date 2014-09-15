<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'events_participants' table.
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
class EventsParticipantsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.EventsParticipantsTableMap';

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
        $this->setName('events_participants');
        $this->setPhpName('EventsParticipants');
        $this->setClassname('Hanzo\\Model\\EventsParticipants');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('events_id', 'EventsId', 'INTEGER', 'events', 'id', true, null, null);
        $this->addColumn('key', 'Key', 'VARCHAR', true, 64, null);
        $this->addColumn('invited_by', 'InvitedBy', 'INTEGER', false, null, null);
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', true, 128, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', false, 128, null);
        $this->addColumn('email', 'Email', 'VARCHAR', false, 255, null);
        $this->addColumn('phone', 'Phone', 'VARCHAR', false, 32, null);
        $this->addColumn('tell_a_friend', 'TellAFriend', 'BOOLEAN', true, 1, false);
        $this->addColumn('notify_by_sms', 'NotifyBySms', 'BOOLEAN', true, 1, false);
        $this->addColumn('sms_send_at', 'SmsSendAt', 'DATE', false, null, null);
        $this->addColumn('has_accepted', 'HasAccepted', 'BOOLEAN', true, 1, false);
        $this->addColumn('expires_at', 'ExpiresAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('responded_at', 'RespondedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Events', 'Hanzo\\Model\\Events', RelationMap::MANY_TO_ONE, array('events_id' => 'id', ), 'CASCADE', null);
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

} // EventsParticipantsTableMap

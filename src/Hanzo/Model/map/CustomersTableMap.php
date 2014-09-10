<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'customers' table.
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
class CustomersTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.CustomersTableMap';

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
        $this->setName('customers');
        $this->setPhpName('Customers');
        $this->setClassname('Hanzo\\Model\\Customers');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('groups_id', 'GroupsId', 'INTEGER', 'groups', 'id', true, null, 1);
        $this->addColumn('title', 'Title', 'VARCHAR', false, 12, null);
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', true, 128, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', true, 128, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 255, null);
        $this->addColumn('phone', 'Phone', 'VARCHAR', false, 32, null);
        $this->addColumn('password', 'Password', 'VARCHAR', true, 128, null);
        $this->addColumn('password_clear', 'PasswordClear', 'VARCHAR', false, 45, null);
        $this->addColumn('discount', 'Discount', 'DECIMAL', false, 8, 0);
        $this->addColumn('is_active', 'IsActive', 'BOOLEAN', true, 1, true);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Groups', 'Hanzo\\Model\\Groups', RelationMap::MANY_TO_ONE, array('groups_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('Addresses', 'Hanzo\\Model\\Addresses', RelationMap::ONE_TO_MANY, array('id' => 'customers_id', ), 'CASCADE', null, 'Addressess');
        $this->addRelation('EventsRelatedByConsultantsId', 'Hanzo\\Model\\Events', RelationMap::ONE_TO_MANY, array('id' => 'consultants_id', ), null, null, 'EventssRelatedByConsultantsId');
        $this->addRelation('EventsRelatedByCustomersId', 'Hanzo\\Model\\Events', RelationMap::ONE_TO_MANY, array('id' => 'customers_id', ), null, null, 'EventssRelatedByCustomersId');
        $this->addRelation('Orders', 'Hanzo\\Model\\Orders', RelationMap::ONE_TO_MANY, array('id' => 'customers_id', ), 'SET NULL', 'CASCADE', 'Orderss');
        $this->addRelation('Wall', 'Hanzo\\Model\\Wall', RelationMap::ONE_TO_MANY, array('id' => 'customers_id', ), 'CASCADE', null, 'Walls');
        $this->addRelation('WallLikes', 'Hanzo\\Model\\WallLikes', RelationMap::ONE_TO_MANY, array('id' => 'customers_id', ), 'CASCADE', null, 'WallLikess');
        $this->addRelation('ConsultantNewsletterDrafts', 'Hanzo\\Model\\ConsultantNewsletterDrafts', RelationMap::ONE_TO_MANY, array('id' => 'consultants_id', ), null, null, 'ConsultantNewsletterDraftss');
        $this->addRelation('GothiaAccounts', 'Hanzo\\Model\\GothiaAccounts', RelationMap::ONE_TO_ONE, array('id' => 'customers_id', ), 'CASCADE', null);
        $this->addRelation('Consultants', 'Hanzo\\Model\\Consultants', RelationMap::ONE_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
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
            'delegate' =>  array (
  'to' => 'consultants',
),
            'event' =>  array (
),
        );
    } // getBehaviors()

} // CustomersTableMap

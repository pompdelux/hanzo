<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'consultants' table.
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
class ConsultantsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.ConsultantsTableMap';

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
        $this->setName('consultants');
        $this->setPhpName('Consultants');
        $this->setClassname('Hanzo\\Model\\Consultants');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addColumn('initials', 'Initials', 'VARCHAR', false, 10, null);
        $this->addColumn('info', 'Info', 'LONGVARCHAR', false, null, null);
        $this->addColumn('event_notes', 'EventNotes', 'LONGVARCHAR', false, null, null);
        $this->addColumn('hide_info', 'HideInfo', 'BOOLEAN', true, 1, false);
        $this->addColumn('max_notified', 'MaxNotified', 'BOOLEAN', true, 1, false);
        $this->addForeignPrimaryKey('id', 'Id', 'INTEGER' , 'customers', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // ConsultantsTableMap

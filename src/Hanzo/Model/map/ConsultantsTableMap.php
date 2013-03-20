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
        $this->addColumn('INITIALS', 'Initials', 'VARCHAR', false, 12, null);
        $this->addColumn('INFO', 'Info', 'LONGVARCHAR', false, null, null);
        $this->addColumn('EVENT_NOTES', 'EventNotes', 'LONGVARCHAR', false, null, null);
        $this->addColumn('HIDE_INFO', 'HideInfo', 'BOOLEAN', true, 1, false);
        $this->addColumn('MAX_NOTIFIED', 'MaxNotified', 'BOOLEAN', true, 1, false);
        $this->addForeignPrimaryKey('ID', 'Id', 'INTEGER' , 'customers', 'ID', true, null, null);
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

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'mannequin_images' table.
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
class MannequinImagesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.MannequinImagesTableMap';

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
        $this->setName('mannequin_images');
        $this->setPhpName('MannequinImages');
        $this->setClassname('Hanzo\\Model\\MannequinImages');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('master', 'Master', 'VARCHAR' , 'products', 'sku', true, 128, null);
        $this->addPrimaryKey('color', 'Color', 'VARCHAR', true, 32, null);
        $this->addColumn('layer', 'Layer', 'INTEGER', true, null, null);
        $this->addColumn('image', 'Image', 'VARCHAR', true, 128, null);
        $this->addColumn('icon', 'Icon', 'VARCHAR', true, 128, null);
        $this->addColumn('weight', 'Weight', 'INTEGER', true, null, 0);
        $this->addColumn('is_main', 'IsMain', 'BOOLEAN', true, 1, false);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('master' => 'sku', ), 'CASCADE', null);
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

} // MannequinImagesTableMap

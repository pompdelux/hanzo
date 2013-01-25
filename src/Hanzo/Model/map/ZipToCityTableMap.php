<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'zip_to_city' table.
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
class ZipToCityTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.ZipToCityTableMap';

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
        $this->setName('zip_to_city');
        $this->setPhpName('ZipToCity');
        $this->setClassname('Hanzo\\Model\\ZipToCity');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('ZIP', 'Zip', 'VARCHAR', true, 12, null);
        $this->addForeignKey('COUNTRIES_ISO2', 'CountriesIso2', 'VARCHAR', 'countries', 'ISO2', true, 2, null);
        $this->addColumn('CITY', 'City', 'VARCHAR', true, 128, null);
        $this->addColumn('COUNTY_ID', 'CountyId', 'VARCHAR', false, 12, null);
        $this->addColumn('COUNTY_NAME', 'CountyName', 'VARCHAR', false, 128, null);
        $this->addColumn('COMMENT', 'Comment', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Countries', 'Hanzo\\Model\\Countries', RelationMap::MANY_TO_ONE, array('countries_iso2' => 'iso2', ), null, null);
    } // buildRelations()

} // ZipToCityTableMap

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'domains' table.
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
class DomainsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.DomainsTableMap';

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
        $this->setName('domains');
        $this->setPhpName('Domains');
        $this->setClassname('Hanzo\\Model\\Domains');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('domain_name', 'DomainName', 'VARCHAR', true, 255, null);
        $this->addColumn('domain_key', 'DomainKey', 'VARCHAR', true, 12, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('DomainsSettings', 'Hanzo\\Model\\DomainsSettings', RelationMap::ONE_TO_MANY, array('domain_key' => 'domain_key', ), 'CASCADE', null, 'DomainsSettingss');
        $this->addRelation('ProductsDomainsPrices', 'Hanzo\\Model\\ProductsDomainsPrices', RelationMap::ONE_TO_MANY, array('id' => 'domains_id', ), 'CASCADE', null, 'ProductsDomainsPricess');
        $this->addRelation('ProductsQuantityDiscount', 'Hanzo\\Model\\ProductsQuantityDiscount', RelationMap::ONE_TO_MANY, array('id' => 'domains_id', ), 'CASCADE', null, 'ProductsQuantityDiscounts');
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

} // DomainsTableMap

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'categories' table.
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
class CategoriesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Hanzo.Model.map.CategoriesTableMap';

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
        $this->setName('categories');
        $this->setPhpName('Categories');
        $this->setClassname('Hanzo\\Model\\Categories');
        $this->setPackage('src.Hanzo.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('parent_id', 'ParentId', 'INTEGER', 'categories', 'id', false, null, null);
        $this->addColumn('context', 'Context', 'VARCHAR', false, 32, '');
        $this->addColumn('is_active', 'IsActive', 'BOOLEAN', true, 1, true);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CategoriesRelatedByParentId', 'Hanzo\\Model\\Categories', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CategoriesRelatedById', 'Hanzo\\Model\\Categories', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), 'SET NULL', null, 'CategoriessRelatedById');
        $this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::ONE_TO_MANY, array('id' => 'primary_categories_id', ), 'SET NULL', 'CASCADE', 'Productss');
        $this->addRelation('ProductsImagesCategoriesSort', 'Hanzo\\Model\\ProductsImagesCategoriesSort', RelationMap::ONE_TO_MANY, array('id' => 'categories_id', ), 'CASCADE', null, 'ProductsImagesCategoriesSorts');
        $this->addRelation('ProductsToCategories', 'Hanzo\\Model\\ProductsToCategories', RelationMap::ONE_TO_MANY, array('id' => 'categories_id', ), 'CASCADE', null, 'ProductsToCategoriess');
        $this->addRelation('CategoriesI18n', 'Hanzo\\Model\\CategoriesI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'CategoriesI18ns');
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
            'i18n' =>  array (
  'i18n_table' => '%TABLE%_i18n',
  'i18n_phpname' => '%PHPNAME%I18n',
  'i18n_columns' => 'title, content',
  'i18n_pk_name' => NULL,
  'locale_column' => 'locale',
  'default_locale' => 'da_DK',
  'locale_alias' => '',
),
        );
    } // getBehaviors()

} // CategoriesTableMap

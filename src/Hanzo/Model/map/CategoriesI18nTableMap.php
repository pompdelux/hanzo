<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'categories_i18n' table.
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
class CategoriesI18nTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.CategoriesI18nTableMap';

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
		$this->setName('categories_i18n');
		$this->setPhpName('CategoriesI18n');
		$this->setClassname('Hanzo\\Model\\CategoriesI18n');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'Id', 'INTEGER' , 'categories', 'ID', true, null, null);
		$this->addPrimaryKey('LOCALE', 'Locale', 'VARCHAR', true, 5, 'en_EN');
		$this->addColumn('TITLE', 'Title', 'VARCHAR', true, 255, null);
		$this->getColumn('TITLE', false)->setPrimaryString(true);
		$this->addColumn('CONTENT', 'Content', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Categories', 'Hanzo\\Model\\Categories', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CategoriesI18nTableMap

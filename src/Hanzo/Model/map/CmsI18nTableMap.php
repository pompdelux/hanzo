<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'cms_i18n' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/hanzo/src/Hanzo/Model.map
 */
class CmsI18nTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'home/un/Documents/Arbejde/Pompdelux/www/hanzo/hanzo/src/Hanzo/Model.map.CmsI18nTableMap';

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
		$this->setName('cms_i18n');
		$this->setPhpName('CmsI18n');
		$this->setClassname('Hanzo\\Model\\CmsI18n');
		$this->setPackage('home/un/Documents/Arbejde/Pompdelux/www/hanzo/hanzo/src/Hanzo/Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'Id', 'INTEGER' , 'cms', 'ID', true, null, null);
		$this->addPrimaryKey('LOCALE', 'Locale', 'VARCHAR', true, 5, 'en_EN');
		$this->addColumn('TITLE', 'Title', 'VARCHAR', true, 255, null);
		$this->getColumn('TITLE', false)->setPrimaryString(true);
		$this->addColumn('PATH', 'Path', 'VARCHAR', true, 255, null);
		$this->addColumn('CONTENT', 'Content', 'LONGVARCHAR', false, null, null);
		$this->addColumn('SETTINGS', 'Settings', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Cms', 'Hanzo\\Model\\Cms', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CmsI18nTableMap

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'consultant_newsletter_drafts' table.
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
class ConsultantNewsletterDraftsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Hanzo.Model.map.ConsultantNewsletterDraftsTableMap';

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
		$this->setName('consultant_newsletter_drafts');
		$this->setPhpName('ConsultantNewsletterDrafts');
		$this->setClassname('Hanzo\\Model\\ConsultantNewsletterDrafts');
		$this->setPackage('src.Hanzo.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('CONSULTANTS_ID', 'ConsultantsId', 'INTEGER', 'customers', 'ID', true, null, null);
		$this->addColumn('SUBJECT', 'Subject', 'VARCHAR', true, 255, null);
		$this->getColumn('SUBJECT', false)->setPrimaryString(true);
		$this->addColumn('CONTENT', 'Content', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Customers', 'Hanzo\\Model\\Customers', RelationMap::MANY_TO_ONE, array('consultants_id' => 'id', ), null, null);
	} // buildRelations()

} // ConsultantNewsletterDraftsTableMap

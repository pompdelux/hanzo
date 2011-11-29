<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'helpdesk_data_log' table.
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
class HelpdeskDataLogTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'home/un/Documents/Arbejde/Pompdelux/www/hanzo/hanzo/src/Hanzo/Model.map.HelpdeskDataLogTableMap';

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
		$this->setName('helpdesk_data_log');
		$this->setPhpName('HelpdeskDataLog');
		$this->setClassname('Hanzo\\Model\\HelpdeskDataLog');
		$this->setPackage('home/un/Documents/Arbejde/Pompdelux/www/hanzo/hanzo/src/Hanzo/Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('KEY', 'Key', 'VARCHAR', true, 64, null);
		$this->addColumn('DATA', 'Data', 'CLOB', true, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // HelpdeskDataLogTableMap

<?php

namespace Hanzo\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'products_domains_prices' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.map
 */
class ProductsDomainsPricesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.map.ProductsDomainsPricesTableMap';

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
		$this->setName('products_domains_prices');
		$this->setPhpName('ProductsDomainsPrices');
		$this->setClassname('Hanzo\\Model\\ProductsDomainsPrices');
		$this->setPackage('home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('PRODUCTS_ID', 'ProductsId', 'INTEGER' , 'products', 'ID', true, null, null);
		$this->addForeignPrimaryKey('DOMAINS_ID', 'DomainsId', 'INTEGER' , 'domains', 'ID', true, null, null);
		$this->addColumn('PRICE', 'Price', 'DECIMAL', true, 15, null);
		$this->addColumn('VAT', 'Vat', 'DECIMAL', true, 4, null);
		$this->addColumn('CURRENCY_CODE', 'CurrencyCode', 'VARCHAR', false, 3, null);
		$this->addColumn('FROM_DATE', 'FromDate', 'DATE', false, null, null);
		$this->addColumn('TO_DATE', 'ToDate', 'DATE', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Products', 'Hanzo\\Model\\Products', RelationMap::MANY_TO_ONE, array('products_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Domains', 'Hanzo\\Model\\Domains', RelationMap::MANY_TO_ONE, array('domains_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // ProductsDomainsPricesTableMap

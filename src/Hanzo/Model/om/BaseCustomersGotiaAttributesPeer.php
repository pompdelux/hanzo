<?php

namespace Hanzo\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Hanzo\Model\CustomersGotiaAttributes;
use Hanzo\Model\CustomersGotiaAttributesPeer;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\map\CustomersGotiaAttributesTableMap;

/**
 * Base static class for performing query and update operations on the 'customers_gotia_attributes' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCustomersGotiaAttributesPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'default';

	/** the table name for this class */
	const TABLE_NAME = 'customers_gotia_attributes';

	/** the related Propel class for this table */
	const OM_CLASS = 'Hanzo\\Model\\CustomersGotiaAttributes';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CustomersGotiaAttributesTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 3;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 3;

	/** the column name for the CUSTOMERS_ID field */
	const CUSTOMERS_ID = 'customers_gotia_attributes.CUSTOMERS_ID';

	/** the column name for the C_KEY field */
	const C_KEY = 'customers_gotia_attributes.C_KEY';

	/** the column name for the C_VALUE field */
	const C_VALUE = 'customers_gotia_attributes.C_VALUE';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';

	/**
	 * An identiy map to hold any loaded instances of CustomersGotiaAttributes objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CustomersGotiaAttributes[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('CustomersId', 'CKey', 'CValue', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('customersId', 'cKey', 'cValue', ),
		BasePeer::TYPE_COLNAME => array (self::CUSTOMERS_ID, self::C_KEY, self::C_VALUE, ),
		BasePeer::TYPE_RAW_COLNAME => array ('CUSTOMERS_ID', 'C_KEY', 'C_VALUE', ),
		BasePeer::TYPE_FIELDNAME => array ('customers_id', 'c_key', 'c_value', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('CustomersId' => 0, 'CKey' => 1, 'CValue' => 2, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('customersId' => 0, 'cKey' => 1, 'cValue' => 2, ),
		BasePeer::TYPE_COLNAME => array (self::CUSTOMERS_ID => 0, self::C_KEY => 1, self::C_VALUE => 2, ),
		BasePeer::TYPE_RAW_COLNAME => array ('CUSTOMERS_ID' => 0, 'C_KEY' => 1, 'C_VALUE' => 2, ),
		BasePeer::TYPE_FIELDNAME => array ('customers_id' => 0, 'c_key' => 1, 'c_value' => 2, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. CustomersGotiaAttributesPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CustomersGotiaAttributesPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      Criteria $criteria object containing the columns to add.
	 * @param      string   $alias    optional table alias
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria, $alias = null)
	{
		if (null === $alias) {
			$criteria->addSelectColumn(CustomersGotiaAttributesPeer::CUSTOMERS_ID);
			$criteria->addSelectColumn(CustomersGotiaAttributesPeer::C_KEY);
			$criteria->addSelectColumn(CustomersGotiaAttributesPeer::C_VALUE);
		} else {
			$criteria->addSelectColumn($alias . '.CUSTOMERS_ID');
			$criteria->addSelectColumn($alias . '.C_KEY');
			$criteria->addSelectColumn($alias . '.C_VALUE');
		}
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CustomersGotiaAttributesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CustomersGotiaAttributesPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Selects one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     CustomersGotiaAttributes
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CustomersGotiaAttributesPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Selects several row from the DB.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return CustomersGotiaAttributesPeer::populateObjects(CustomersGotiaAttributesPeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CustomersGotiaAttributesPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      CustomersGotiaAttributes $value A CustomersGotiaAttributes object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getCustomersId(), (string) $obj->getCKey()));
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A CustomersGotiaAttributes object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CustomersGotiaAttributes) {
				$key = serialize(array((string) $value->getCustomersId(), (string) $value->getCKey()));
			} elseif (is_array($value) && count($value) === 2) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CustomersGotiaAttributes object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     CustomersGotiaAttributes Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to customers_gotia_attributes
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null && $row[$startcol + 1] === null) {
			return null;
		}
		return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1]));
	}

	/**
	 * Retrieves the primary key from the DB resultset row
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, an array of the primary key columns will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return array((int) $row[$startcol], (string) $row[$startcol + 1]);
	}
	
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = CustomersGotiaAttributesPeer::getOMClass();
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CustomersGotiaAttributesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CustomersGotiaAttributesPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CustomersGotiaAttributesPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Populates an object of the default type or an object that inherit from the default.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (CustomersGotiaAttributes object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CustomersGotiaAttributesPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CustomersGotiaAttributesPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CustomersGotiaAttributesPeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = CustomersGotiaAttributesPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CustomersGotiaAttributesPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Customers table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCustomers(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CustomersGotiaAttributesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CustomersGotiaAttributesPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CustomersGotiaAttributesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of CustomersGotiaAttributes objects pre-filled with their Customers objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CustomersGotiaAttributes objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCustomers(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CustomersGotiaAttributesPeer::addSelectColumns($criteria);
		$startcol = CustomersGotiaAttributesPeer::NUM_HYDRATE_COLUMNS;
		CustomersPeer::addSelectColumns($criteria);

		$criteria->addJoin(CustomersGotiaAttributesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CustomersGotiaAttributesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CustomersGotiaAttributesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CustomersGotiaAttributesPeer::getOMClass();

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CustomersGotiaAttributesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CustomersPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CustomersPeer::getOMClass();

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CustomersPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CustomersGotiaAttributes) to $obj2 (Customers)
				$obj2->addCustomersGotiaAttributes($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CustomersGotiaAttributesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CustomersGotiaAttributesPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CustomersGotiaAttributesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of CustomersGotiaAttributes objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CustomersGotiaAttributes objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CustomersGotiaAttributesPeer::addSelectColumns($criteria);
		$startcol2 = CustomersGotiaAttributesPeer::NUM_HYDRATE_COLUMNS;

		CustomersPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(CustomersGotiaAttributesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CustomersGotiaAttributesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CustomersGotiaAttributesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CustomersGotiaAttributesPeer::getOMClass();

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CustomersGotiaAttributesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Customers rows

			$key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = CustomersPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CustomersPeer::getOMClass();

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CustomersPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CustomersGotiaAttributes) to the collection in $obj2 (Customers)
				$obj2->addCustomersGotiaAttributes($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseCustomersGotiaAttributesPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCustomersGotiaAttributesPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CustomersGotiaAttributesTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 *
	 * @return     string ClassName
	 */
	public static function getOMClass()
	{
		return CustomersGotiaAttributesPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a CustomersGotiaAttributes or Criteria object.
	 *
	 * @param      mixed $values Criteria or CustomersGotiaAttributes object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CustomersGotiaAttributes object
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Performs an UPDATE on the database, given a CustomersGotiaAttributes or Criteria object.
	 *
	 * @param      mixed $values Criteria or CustomersGotiaAttributes object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CustomersGotiaAttributesPeer::CUSTOMERS_ID);
			$value = $criteria->remove(CustomersGotiaAttributesPeer::CUSTOMERS_ID);
			if ($value) {
				$selectCriteria->add(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CustomersGotiaAttributesPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(CustomersGotiaAttributesPeer::C_KEY);
			$value = $criteria->remove(CustomersGotiaAttributesPeer::C_KEY);
			if ($value) {
				$selectCriteria->add(CustomersGotiaAttributesPeer::C_KEY, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CustomersGotiaAttributesPeer::TABLE_NAME);
			}

		} else { // $values is CustomersGotiaAttributes object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the customers_gotia_attributes table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(CustomersGotiaAttributesPeer::TABLE_NAME, $con, CustomersGotiaAttributesPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CustomersGotiaAttributesPeer::clearInstancePool();
			CustomersGotiaAttributesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a CustomersGotiaAttributes or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CustomersGotiaAttributes object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CustomersGotiaAttributesPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CustomersGotiaAttributes) { // it's a model object
			// invalidate the cache for this single object
			CustomersGotiaAttributesPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			// primary key is composite; we therefore, expect
			// the primary key passed to be an array of pkey values
			if (count($values) == count($values, COUNT_RECURSIVE)) {
				// array is not multi-dimensional
				$values = array($values);
			}
			foreach ($values as $value) {
				$criterion = $criteria->getNewCriterion(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $value[0]);
				$criterion->addAnd($criteria->getNewCriterion(CustomersGotiaAttributesPeer::C_KEY, $value[1]));
				$criteria->addOr($criterion);
				// we can invalidate the cache for this single PK
				CustomersGotiaAttributesPeer::removeInstanceFromPool($value);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			CustomersGotiaAttributesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given CustomersGotiaAttributes object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CustomersGotiaAttributes $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CustomersGotiaAttributesPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CustomersGotiaAttributesPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(CustomersGotiaAttributesPeer::DATABASE_NAME, CustomersGotiaAttributesPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      int $customers_id
	 * @param      string $c_key
	 * @param      PropelPDO $con
	 * @return     CustomersGotiaAttributes
	 */
	public static function retrieveByPK($customers_id, $c_key, PropelPDO $con = null) {
		$_instancePoolKey = serialize(array((string) $customers_id, (string) $c_key));
 		if (null !== ($obj = CustomersGotiaAttributesPeer::getInstanceFromPool($_instancePoolKey))) {
 			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$criteria = new Criteria(CustomersGotiaAttributesPeer::DATABASE_NAME);
		$criteria->add(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $customers_id);
		$criteria->add(CustomersGotiaAttributesPeer::C_KEY, $c_key);
		$v = CustomersGotiaAttributesPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseCustomersGotiaAttributesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCustomersGotiaAttributesPeer::buildTableMap();


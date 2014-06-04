<?php

namespace Hanzo\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Hanzo\Model\Coupons;
use Hanzo\Model\CouponsPeer;
use Hanzo\Model\OrdersToCouponsPeer;
use Hanzo\Model\map\CouponsTableMap;

abstract class BaseCouponsPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'coupons';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\Coupons';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\CouponsTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 13;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 13;

    /** the column name for the id field */
    const ID = 'coupons.id';

    /** the column name for the code field */
    const CODE = 'coupons.code';

    /** the column name for the amount field */
    const AMOUNT = 'coupons.amount';

    /** the column name for the amount_type field */
    const AMOUNT_TYPE = 'coupons.amount_type';

    /** the column name for the min_purchase_amount field */
    const MIN_PURCHASE_AMOUNT = 'coupons.min_purchase_amount';

    /** the column name for the currency_code field */
    const CURRENCY_CODE = 'coupons.currency_code';

    /** the column name for the active_from field */
    const ACTIVE_FROM = 'coupons.active_from';

    /** the column name for the active_to field */
    const ACTIVE_TO = 'coupons.active_to';

    /** the column name for the is_active field */
    const IS_ACTIVE = 'coupons.is_active';

    /** the column name for the is_used field */
    const IS_USED = 'coupons.is_used';

    /** the column name for the is_reusable field */
    const IS_REUSABLE = 'coupons.is_reusable';

    /** the column name for the created_at field */
    const CREATED_AT = 'coupons.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'coupons.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Coupons objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Coupons[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. CouponsPeer::$fieldNames[CouponsPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Code', 'Amount', 'AmountType', 'MinPurchaseAmount', 'CurrencyCode', 'ActiveFrom', 'ActiveTo', 'IsActive', 'IsUsed', 'IsReusable', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'code', 'amount', 'amountType', 'minPurchaseAmount', 'currencyCode', 'activeFrom', 'activeTo', 'isActive', 'isUsed', 'isReusable', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (CouponsPeer::ID, CouponsPeer::CODE, CouponsPeer::AMOUNT, CouponsPeer::AMOUNT_TYPE, CouponsPeer::MIN_PURCHASE_AMOUNT, CouponsPeer::CURRENCY_CODE, CouponsPeer::ACTIVE_FROM, CouponsPeer::ACTIVE_TO, CouponsPeer::IS_ACTIVE, CouponsPeer::IS_USED, CouponsPeer::IS_REUSABLE, CouponsPeer::CREATED_AT, CouponsPeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'CODE', 'AMOUNT', 'AMOUNT_TYPE', 'MIN_PURCHASE_AMOUNT', 'CURRENCY_CODE', 'ACTIVE_FROM', 'ACTIVE_TO', 'IS_ACTIVE', 'IS_USED', 'IS_REUSABLE', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'code', 'amount', 'amount_type', 'min_purchase_amount', 'currency_code', 'active_from', 'active_to', 'is_active', 'is_used', 'is_reusable', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. CouponsPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Code' => 1, 'Amount' => 2, 'AmountType' => 3, 'MinPurchaseAmount' => 4, 'CurrencyCode' => 5, 'ActiveFrom' => 6, 'ActiveTo' => 7, 'IsActive' => 8, 'IsUsed' => 9, 'IsReusable' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'code' => 1, 'amount' => 2, 'amountType' => 3, 'minPurchaseAmount' => 4, 'currencyCode' => 5, 'activeFrom' => 6, 'activeTo' => 7, 'isActive' => 8, 'isUsed' => 9, 'isReusable' => 10, 'createdAt' => 11, 'updatedAt' => 12, ),
        BasePeer::TYPE_COLNAME => array (CouponsPeer::ID => 0, CouponsPeer::CODE => 1, CouponsPeer::AMOUNT => 2, CouponsPeer::AMOUNT_TYPE => 3, CouponsPeer::MIN_PURCHASE_AMOUNT => 4, CouponsPeer::CURRENCY_CODE => 5, CouponsPeer::ACTIVE_FROM => 6, CouponsPeer::ACTIVE_TO => 7, CouponsPeer::IS_ACTIVE => 8, CouponsPeer::IS_USED => 9, CouponsPeer::IS_REUSABLE => 10, CouponsPeer::CREATED_AT => 11, CouponsPeer::UPDATED_AT => 12, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'CODE' => 1, 'AMOUNT' => 2, 'AMOUNT_TYPE' => 3, 'MIN_PURCHASE_AMOUNT' => 4, 'CURRENCY_CODE' => 5, 'ACTIVE_FROM' => 6, 'ACTIVE_TO' => 7, 'IS_ACTIVE' => 8, 'IS_USED' => 9, 'IS_REUSABLE' => 10, 'CREATED_AT' => 11, 'UPDATED_AT' => 12, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'code' => 1, 'amount' => 2, 'amount_type' => 3, 'min_purchase_amount' => 4, 'currency_code' => 5, 'active_from' => 6, 'active_to' => 7, 'is_active' => 8, 'is_used' => 9, 'is_reusable' => 10, 'created_at' => 11, 'updated_at' => 12, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = CouponsPeer::getFieldNames($toType);
        $key = isset(CouponsPeer::$fieldKeys[$fromType][$name]) ? CouponsPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(CouponsPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, CouponsPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return CouponsPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. CouponsPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(CouponsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CouponsPeer::ID);
            $criteria->addSelectColumn(CouponsPeer::CODE);
            $criteria->addSelectColumn(CouponsPeer::AMOUNT);
            $criteria->addSelectColumn(CouponsPeer::AMOUNT_TYPE);
            $criteria->addSelectColumn(CouponsPeer::MIN_PURCHASE_AMOUNT);
            $criteria->addSelectColumn(CouponsPeer::CURRENCY_CODE);
            $criteria->addSelectColumn(CouponsPeer::ACTIVE_FROM);
            $criteria->addSelectColumn(CouponsPeer::ACTIVE_TO);
            $criteria->addSelectColumn(CouponsPeer::IS_ACTIVE);
            $criteria->addSelectColumn(CouponsPeer::IS_USED);
            $criteria->addSelectColumn(CouponsPeer::IS_REUSABLE);
            $criteria->addSelectColumn(CouponsPeer::CREATED_AT);
            $criteria->addSelectColumn(CouponsPeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.code');
            $criteria->addSelectColumn($alias . '.amount');
            $criteria->addSelectColumn($alias . '.amount_type');
            $criteria->addSelectColumn($alias . '.min_purchase_amount');
            $criteria->addSelectColumn($alias . '.currency_code');
            $criteria->addSelectColumn($alias . '.active_from');
            $criteria->addSelectColumn($alias . '.active_to');
            $criteria->addSelectColumn($alias . '.is_active');
            $criteria->addSelectColumn($alias . '.is_used');
            $criteria->addSelectColumn($alias . '.is_reusable');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CouponsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CouponsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(CouponsPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return Coupons
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = CouponsPeer::doSelect($critcopy, $con);
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
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return CouponsPeer::populateObjects(CouponsPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            CouponsPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(CouponsPeer::DATABASE_NAME);

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
     * @param Coupons $obj A Coupons object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            CouponsPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Coupons object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Coupons) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Coupons object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(CouponsPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Coupons Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(CouponsPeer::$instances[$key])) {
                return CouponsPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (CouponsPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        CouponsPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to coupons
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in OrdersToCouponsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersToCouponsPeer::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = CouponsPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = CouponsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = CouponsPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CouponsPeer::addInstanceToPool($obj, $key);
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
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Coupons object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = CouponsPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = CouponsPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + CouponsPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CouponsPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            CouponsPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(CouponsPeer::DATABASE_NAME)->getTable(CouponsPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseCouponsPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseCouponsPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\CouponsTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return CouponsPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Coupons or Criteria object.
     *
     * @param      mixed $values Criteria or Coupons object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Coupons object
        }

        if ($criteria->containsKey(CouponsPeer::ID) && $criteria->keyContainsValue(CouponsPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CouponsPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(CouponsPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Coupons or Criteria object.
     *
     * @param      mixed $values Criteria or Coupons object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(CouponsPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(CouponsPeer::ID);
            $value = $criteria->remove(CouponsPeer::ID);
            if ($value) {
                $selectCriteria->add(CouponsPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(CouponsPeer::TABLE_NAME);
            }

        } else { // $values is Coupons object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(CouponsPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the coupons table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(CouponsPeer::TABLE_NAME, $con, CouponsPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CouponsPeer::clearInstancePool();
            CouponsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Coupons or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Coupons object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            CouponsPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Coupons) { // it's a model object
            // invalidate the cache for this single object
            CouponsPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CouponsPeer::DATABASE_NAME);
            $criteria->add(CouponsPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                CouponsPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(CouponsPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            CouponsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Coupons object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Coupons $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CouponsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CouponsPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(CouponsPeer::DATABASE_NAME, CouponsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Coupons
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = CouponsPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(CouponsPeer::DATABASE_NAME);
        $criteria->add(CouponsPeer::ID, $pk);

        $v = CouponsPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Coupons[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(CouponsPeer::DATABASE_NAME);
            $criteria->add(CouponsPeer::ID, $pks, Criteria::IN);
            $objs = CouponsPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseCouponsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCouponsPeer::buildTableMap();


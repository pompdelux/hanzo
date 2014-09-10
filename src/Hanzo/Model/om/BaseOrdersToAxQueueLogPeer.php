<?php

namespace Hanzo\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\PeerEvent;
use Hanzo\Model\OrdersToAxQueueLog;
use Hanzo\Model\OrdersToAxQueueLogPeer;
use Hanzo\Model\map\OrdersToAxQueueLogTableMap;

abstract class BaseOrdersToAxQueueLogPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'orders_to_ax_queue_log';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\OrdersToAxQueueLog';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\OrdersToAxQueueLogTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 4;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 4;

    /** the column name for the orders_id field */
    const ORDERS_ID = 'orders_to_ax_queue_log.orders_id';

    /** the column name for the queue_id field */
    const QUEUE_ID = 'orders_to_ax_queue_log.queue_id';

    /** the column name for the iteration field */
    const ITERATION = 'orders_to_ax_queue_log.iteration';

    /** the column name for the created_at field */
    const CREATED_AT = 'orders_to_ax_queue_log.created_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of OrdersToAxQueueLog objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array OrdersToAxQueueLog[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. OrdersToAxQueueLogPeer::$fieldNames[OrdersToAxQueueLogPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('OrdersId', 'QueueId', 'Iteration', 'CreatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('ordersId', 'queueId', 'iteration', 'createdAt', ),
        BasePeer::TYPE_COLNAME => array (OrdersToAxQueueLogPeer::ORDERS_ID, OrdersToAxQueueLogPeer::QUEUE_ID, OrdersToAxQueueLogPeer::ITERATION, OrdersToAxQueueLogPeer::CREATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ORDERS_ID', 'QUEUE_ID', 'ITERATION', 'CREATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('orders_id', 'queue_id', 'iteration', 'created_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. OrdersToAxQueueLogPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('OrdersId' => 0, 'QueueId' => 1, 'Iteration' => 2, 'CreatedAt' => 3, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('ordersId' => 0, 'queueId' => 1, 'iteration' => 2, 'createdAt' => 3, ),
        BasePeer::TYPE_COLNAME => array (OrdersToAxQueueLogPeer::ORDERS_ID => 0, OrdersToAxQueueLogPeer::QUEUE_ID => 1, OrdersToAxQueueLogPeer::ITERATION => 2, OrdersToAxQueueLogPeer::CREATED_AT => 3, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ORDERS_ID' => 0, 'QUEUE_ID' => 1, 'ITERATION' => 2, 'CREATED_AT' => 3, ),
        BasePeer::TYPE_FIELDNAME => array ('orders_id' => 0, 'queue_id' => 1, 'iteration' => 2, 'created_at' => 3, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, )
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
        $toNames = OrdersToAxQueueLogPeer::getFieldNames($toType);
        $key = isset(OrdersToAxQueueLogPeer::$fieldKeys[$fromType][$name]) ? OrdersToAxQueueLogPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(OrdersToAxQueueLogPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, OrdersToAxQueueLogPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return OrdersToAxQueueLogPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. OrdersToAxQueueLogPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(OrdersToAxQueueLogPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(OrdersToAxQueueLogPeer::ORDERS_ID);
            $criteria->addSelectColumn(OrdersToAxQueueLogPeer::QUEUE_ID);
            $criteria->addSelectColumn(OrdersToAxQueueLogPeer::ITERATION);
            $criteria->addSelectColumn(OrdersToAxQueueLogPeer::CREATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.orders_id');
            $criteria->addSelectColumn($alias . '.queue_id');
            $criteria->addSelectColumn($alias . '.iteration');
            $criteria->addSelectColumn($alias . '.created_at');
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
        $criteria->setPrimaryTableName(OrdersToAxQueueLogPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersToAxQueueLogPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(OrdersToAxQueueLogPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return OrdersToAxQueueLog
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = OrdersToAxQueueLogPeer::doSelect($critcopy, $con);
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
        return OrdersToAxQueueLogPeer::populateObjects(OrdersToAxQueueLogPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            OrdersToAxQueueLogPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(OrdersToAxQueueLogPeer::DATABASE_NAME);

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
     * @param OrdersToAxQueueLog $obj A OrdersToAxQueueLog object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = serialize(array((string) $obj->getOrdersId(), (string) $obj->getQueueId(), (string) $obj->getCreatedAt('U')));
            } // if key === null
            OrdersToAxQueueLogPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A OrdersToAxQueueLog object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof OrdersToAxQueueLog) {
                $key = serialize(array((string) $value->getOrdersId(), (string) $value->getQueueId(), (string) $value->getCreatedAt()));
            } elseif (is_array($value) && count($value) === 3) {
                // assume we've been passed a primary key
                $key = serialize(array((string) $value[0], (string) $value[1], (string) $value[2]));
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or OrdersToAxQueueLog object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(OrdersToAxQueueLogPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return OrdersToAxQueueLog Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(OrdersToAxQueueLogPeer::$instances[$key])) {
                return OrdersToAxQueueLogPeer::$instances[$key];
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
        foreach (OrdersToAxQueueLogPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        OrdersToAxQueueLogPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to orders_to_ax_queue_log
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
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null && $row[$startcol + 1] === null && $row[$startcol + 3] === null) {
            return null;
        }

        return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1], (string) $row[$startcol + 3]));
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

        return array((int) $row[$startcol], (int) $row[$startcol + 1], (string) $row[$startcol + 3]);
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
        $cls = OrdersToAxQueueLogPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = OrdersToAxQueueLogPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = OrdersToAxQueueLogPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrdersToAxQueueLogPeer::addInstanceToPool($obj, $key);
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
     * @return array (OrdersToAxQueueLog object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = OrdersToAxQueueLogPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = OrdersToAxQueueLogPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + OrdersToAxQueueLogPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrdersToAxQueueLogPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            OrdersToAxQueueLogPeer::addInstanceToPool($obj, $key);
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
        return Propel::getDatabaseMap(OrdersToAxQueueLogPeer::DATABASE_NAME)->getTable(OrdersToAxQueueLogPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseOrdersToAxQueueLogPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseOrdersToAxQueueLogPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\OrdersToAxQueueLogTableMap());
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
        return OrdersToAxQueueLogPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a OrdersToAxQueueLog or Criteria object.
     *
     * @param      mixed $values Criteria or OrdersToAxQueueLog object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from OrdersToAxQueueLog object
        }


        // Set the correct dbName
        $criteria->setDbName(OrdersToAxQueueLogPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a OrdersToAxQueueLog or Criteria object.
     *
     * @param      mixed $values Criteria or OrdersToAxQueueLog object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(OrdersToAxQueueLogPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(OrdersToAxQueueLogPeer::ORDERS_ID);
            $value = $criteria->remove(OrdersToAxQueueLogPeer::ORDERS_ID);
            if ($value) {
                $selectCriteria->add(OrdersToAxQueueLogPeer::ORDERS_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(OrdersToAxQueueLogPeer::TABLE_NAME);
            }

            $comparison = $criteria->getComparison(OrdersToAxQueueLogPeer::QUEUE_ID);
            $value = $criteria->remove(OrdersToAxQueueLogPeer::QUEUE_ID);
            if ($value) {
                $selectCriteria->add(OrdersToAxQueueLogPeer::QUEUE_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(OrdersToAxQueueLogPeer::TABLE_NAME);
            }

            $comparison = $criteria->getComparison(OrdersToAxQueueLogPeer::CREATED_AT);
            $value = $criteria->remove(OrdersToAxQueueLogPeer::CREATED_AT);
            if ($value) {
                $selectCriteria->add(OrdersToAxQueueLogPeer::CREATED_AT, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(OrdersToAxQueueLogPeer::TABLE_NAME);
            }

        } else { // $values is OrdersToAxQueueLog object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(OrdersToAxQueueLogPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the orders_to_ax_queue_log table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(OrdersToAxQueueLogPeer::TABLE_NAME, $con, OrdersToAxQueueLogPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OrdersToAxQueueLogPeer::clearInstancePool();
            OrdersToAxQueueLogPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a OrdersToAxQueueLog or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or OrdersToAxQueueLog object or primary key or array of primary keys
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
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            OrdersToAxQueueLogPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof OrdersToAxQueueLog) { // it's a model object
            // invalidate the cache for this single object
            OrdersToAxQueueLogPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrdersToAxQueueLogPeer::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(OrdersToAxQueueLogPeer::ORDERS_ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(OrdersToAxQueueLogPeer::QUEUE_ID, $value[1]));
                $criterion->addAnd($criteria->getNewCriterion(OrdersToAxQueueLogPeer::CREATED_AT, $value[2]));
                $criteria->addOr($criterion);
                // we can invalidate the cache for this single PK
                OrdersToAxQueueLogPeer::removeInstanceFromPool($value);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(OrdersToAxQueueLogPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            OrdersToAxQueueLogPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given OrdersToAxQueueLog object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param OrdersToAxQueueLog $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(OrdersToAxQueueLogPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(OrdersToAxQueueLogPeer::TABLE_NAME);

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

        return BasePeer::doValidate(OrdersToAxQueueLogPeer::DATABASE_NAME, OrdersToAxQueueLogPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param   int $orders_id
     * @param   int $queue_id
     * @param   string $created_at
     * @param      PropelPDO $con
     * @return OrdersToAxQueueLog
     */
    public static function retrieveByPK($orders_id, $queue_id, $created_at, PropelPDO $con = null) {
        $_instancePoolKey = serialize(array((string) $orders_id, (string) $queue_id, (string) $created_at));
         if (null !== ($obj = OrdersToAxQueueLogPeer::getInstanceFromPool($_instancePoolKey))) {
             return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $criteria = new Criteria(OrdersToAxQueueLogPeer::DATABASE_NAME);
        $criteria->add(OrdersToAxQueueLogPeer::ORDERS_ID, $orders_id);
        $criteria->add(OrdersToAxQueueLogPeer::QUEUE_ID, $queue_id);
        $criteria->add(OrdersToAxQueueLogPeer::CREATED_AT, $created_at);
        $v = OrdersToAxQueueLogPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
} // BaseOrdersToAxQueueLogPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseOrdersToAxQueueLogPeer::buildTableMap();

EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('Hanzo\Model\om\BaseOrdersToAxQueueLogPeer'));

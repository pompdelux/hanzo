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
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\map\OrdersLinesTableMap;

abstract class BaseOrdersLinesPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'orders_lines';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\OrdersLines';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\OrdersLinesTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 16;

    /** the column name for the id field */
    const ID = 'orders_lines.id';

    /** the column name for the orders_id field */
    const ORDERS_ID = 'orders_lines.orders_id';

    /** the column name for the type field */
    const TYPE = 'orders_lines.type';

    /** the column name for the products_id field */
    const PRODUCTS_ID = 'orders_lines.products_id';

    /** the column name for the products_sku field */
    const PRODUCTS_SKU = 'orders_lines.products_sku';

    /** the column name for the products_name field */
    const PRODUCTS_NAME = 'orders_lines.products_name';

    /** the column name for the products_color field */
    const PRODUCTS_COLOR = 'orders_lines.products_color';

    /** the column name for the products_size field */
    const PRODUCTS_SIZE = 'orders_lines.products_size';

    /** the column name for the expected_at field */
    const EXPECTED_AT = 'orders_lines.expected_at';

    /** the column name for the original_price field */
    const ORIGINAL_PRICE = 'orders_lines.original_price';

    /** the column name for the price field */
    const PRICE = 'orders_lines.price';

    /** the column name for the vat field */
    const VAT = 'orders_lines.vat';

    /** the column name for the quantity field */
    const QUANTITY = 'orders_lines.quantity';

    /** the column name for the unit field */
    const UNIT = 'orders_lines.unit';

    /** the column name for the is_voucher field */
    const IS_VOUCHER = 'orders_lines.is_voucher';

    /** the column name for the note field */
    const NOTE = 'orders_lines.note';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of OrdersLines objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array OrdersLines[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. OrdersLinesPeer::$fieldNames[OrdersLinesPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'OrdersId', 'Type', 'ProductsId', 'ProductsSku', 'ProductsName', 'ProductsColor', 'ProductsSize', 'ExpectedAt', 'OriginalPrice', 'Price', 'Vat', 'Quantity', 'Unit', 'IsVoucher', 'Note', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'ordersId', 'type', 'productsId', 'productsSku', 'productsName', 'productsColor', 'productsSize', 'expectedAt', 'originalPrice', 'price', 'vat', 'quantity', 'unit', 'isVoucher', 'note', ),
        BasePeer::TYPE_COLNAME => array (OrdersLinesPeer::ID, OrdersLinesPeer::ORDERS_ID, OrdersLinesPeer::TYPE, OrdersLinesPeer::PRODUCTS_ID, OrdersLinesPeer::PRODUCTS_SKU, OrdersLinesPeer::PRODUCTS_NAME, OrdersLinesPeer::PRODUCTS_COLOR, OrdersLinesPeer::PRODUCTS_SIZE, OrdersLinesPeer::EXPECTED_AT, OrdersLinesPeer::ORIGINAL_PRICE, OrdersLinesPeer::PRICE, OrdersLinesPeer::VAT, OrdersLinesPeer::QUANTITY, OrdersLinesPeer::UNIT, OrdersLinesPeer::IS_VOUCHER, OrdersLinesPeer::NOTE, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'ORDERS_ID', 'TYPE', 'PRODUCTS_ID', 'PRODUCTS_SKU', 'PRODUCTS_NAME', 'PRODUCTS_COLOR', 'PRODUCTS_SIZE', 'EXPECTED_AT', 'ORIGINAL_PRICE', 'PRICE', 'VAT', 'QUANTITY', 'UNIT', 'IS_VOUCHER', 'NOTE', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'orders_id', 'type', 'products_id', 'products_sku', 'products_name', 'products_color', 'products_size', 'expected_at', 'original_price', 'price', 'vat', 'quantity', 'unit', 'is_voucher', 'note', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. OrdersLinesPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'OrdersId' => 1, 'Type' => 2, 'ProductsId' => 3, 'ProductsSku' => 4, 'ProductsName' => 5, 'ProductsColor' => 6, 'ProductsSize' => 7, 'ExpectedAt' => 8, 'OriginalPrice' => 9, 'Price' => 10, 'Vat' => 11, 'Quantity' => 12, 'Unit' => 13, 'IsVoucher' => 14, 'Note' => 15, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'ordersId' => 1, 'type' => 2, 'productsId' => 3, 'productsSku' => 4, 'productsName' => 5, 'productsColor' => 6, 'productsSize' => 7, 'expectedAt' => 8, 'originalPrice' => 9, 'price' => 10, 'vat' => 11, 'quantity' => 12, 'unit' => 13, 'isVoucher' => 14, 'note' => 15, ),
        BasePeer::TYPE_COLNAME => array (OrdersLinesPeer::ID => 0, OrdersLinesPeer::ORDERS_ID => 1, OrdersLinesPeer::TYPE => 2, OrdersLinesPeer::PRODUCTS_ID => 3, OrdersLinesPeer::PRODUCTS_SKU => 4, OrdersLinesPeer::PRODUCTS_NAME => 5, OrdersLinesPeer::PRODUCTS_COLOR => 6, OrdersLinesPeer::PRODUCTS_SIZE => 7, OrdersLinesPeer::EXPECTED_AT => 8, OrdersLinesPeer::ORIGINAL_PRICE => 9, OrdersLinesPeer::PRICE => 10, OrdersLinesPeer::VAT => 11, OrdersLinesPeer::QUANTITY => 12, OrdersLinesPeer::UNIT => 13, OrdersLinesPeer::IS_VOUCHER => 14, OrdersLinesPeer::NOTE => 15, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'ORDERS_ID' => 1, 'TYPE' => 2, 'PRODUCTS_ID' => 3, 'PRODUCTS_SKU' => 4, 'PRODUCTS_NAME' => 5, 'PRODUCTS_COLOR' => 6, 'PRODUCTS_SIZE' => 7, 'EXPECTED_AT' => 8, 'ORIGINAL_PRICE' => 9, 'PRICE' => 10, 'VAT' => 11, 'QUANTITY' => 12, 'UNIT' => 13, 'IS_VOUCHER' => 14, 'NOTE' => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'orders_id' => 1, 'type' => 2, 'products_id' => 3, 'products_sku' => 4, 'products_name' => 5, 'products_color' => 6, 'products_size' => 7, 'expected_at' => 8, 'original_price' => 9, 'price' => 10, 'vat' => 11, 'quantity' => 12, 'unit' => 13, 'is_voucher' => 14, 'note' => 15, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
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
        $toNames = OrdersLinesPeer::getFieldNames($toType);
        $key = isset(OrdersLinesPeer::$fieldKeys[$fromType][$name]) ? OrdersLinesPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(OrdersLinesPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, OrdersLinesPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return OrdersLinesPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. OrdersLinesPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(OrdersLinesPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(OrdersLinesPeer::ID);
            $criteria->addSelectColumn(OrdersLinesPeer::ORDERS_ID);
            $criteria->addSelectColumn(OrdersLinesPeer::TYPE);
            $criteria->addSelectColumn(OrdersLinesPeer::PRODUCTS_ID);
            $criteria->addSelectColumn(OrdersLinesPeer::PRODUCTS_SKU);
            $criteria->addSelectColumn(OrdersLinesPeer::PRODUCTS_NAME);
            $criteria->addSelectColumn(OrdersLinesPeer::PRODUCTS_COLOR);
            $criteria->addSelectColumn(OrdersLinesPeer::PRODUCTS_SIZE);
            $criteria->addSelectColumn(OrdersLinesPeer::EXPECTED_AT);
            $criteria->addSelectColumn(OrdersLinesPeer::ORIGINAL_PRICE);
            $criteria->addSelectColumn(OrdersLinesPeer::PRICE);
            $criteria->addSelectColumn(OrdersLinesPeer::VAT);
            $criteria->addSelectColumn(OrdersLinesPeer::QUANTITY);
            $criteria->addSelectColumn(OrdersLinesPeer::UNIT);
            $criteria->addSelectColumn(OrdersLinesPeer::IS_VOUCHER);
            $criteria->addSelectColumn(OrdersLinesPeer::NOTE);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.orders_id');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.products_id');
            $criteria->addSelectColumn($alias . '.products_sku');
            $criteria->addSelectColumn($alias . '.products_name');
            $criteria->addSelectColumn($alias . '.products_color');
            $criteria->addSelectColumn($alias . '.products_size');
            $criteria->addSelectColumn($alias . '.expected_at');
            $criteria->addSelectColumn($alias . '.original_price');
            $criteria->addSelectColumn($alias . '.price');
            $criteria->addSelectColumn($alias . '.vat');
            $criteria->addSelectColumn($alias . '.quantity');
            $criteria->addSelectColumn($alias . '.unit');
            $criteria->addSelectColumn($alias . '.is_voucher');
            $criteria->addSelectColumn($alias . '.note');
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
        $criteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return OrdersLines
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = OrdersLinesPeer::doSelect($critcopy, $con);
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
        return OrdersLinesPeer::populateObjects(OrdersLinesPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

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
     * @param OrdersLines $obj A OrdersLines object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            OrdersLinesPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A OrdersLines object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof OrdersLines) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or OrdersLines object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(OrdersLinesPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return OrdersLines Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(OrdersLinesPeer::$instances[$key])) {
                return OrdersLinesPeer::$instances[$key];
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
        foreach (OrdersLinesPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        OrdersLinesPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to orders_lines
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
        $cls = OrdersLinesPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = OrdersLinesPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrdersLinesPeer::addInstanceToPool($obj, $key);
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
     * @return array (OrdersLines object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = OrdersLinesPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + OrdersLinesPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrdersLinesPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            OrdersLinesPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Orders table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinOrders(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersLinesPeer::ORDERS_ID, OrdersPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related Products table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinProducts(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersLinesPeer::PRODUCTS_ID, ProductsPeer::ID, $join_behavior);

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
     * Selects a collection of OrdersLines objects pre-filled with their Orders objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of OrdersLines objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinOrders(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);
        }

        OrdersLinesPeer::addSelectColumns($criteria);
        $startcol = OrdersLinesPeer::NUM_HYDRATE_COLUMNS;
        OrdersPeer::addSelectColumns($criteria);

        $criteria->addJoin(OrdersLinesPeer::ORDERS_ID, OrdersPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersLinesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = OrdersLinesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersLinesPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = OrdersPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = OrdersPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = OrdersPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    OrdersPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (OrdersLines) to $obj2 (Orders)
                $obj2->addOrdersLines($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of OrdersLines objects pre-filled with their Products objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of OrdersLines objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinProducts(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);
        }

        OrdersLinesPeer::addSelectColumns($criteria);
        $startcol = OrdersLinesPeer::NUM_HYDRATE_COLUMNS;
        ProductsPeer::addSelectColumns($criteria);

        $criteria->addJoin(OrdersLinesPeer::PRODUCTS_ID, ProductsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersLinesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = OrdersLinesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersLinesPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = ProductsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = ProductsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ProductsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    ProductsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (OrdersLines) to $obj2 (Products)
                $obj2->addOrdersLines($obj1);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersLinesPeer::ORDERS_ID, OrdersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersLinesPeer::PRODUCTS_ID, ProductsPeer::ID, $join_behavior);

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
     * Selects a collection of OrdersLines objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of OrdersLines objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);
        }

        OrdersLinesPeer::addSelectColumns($criteria);
        $startcol2 = OrdersLinesPeer::NUM_HYDRATE_COLUMNS;

        OrdersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + OrdersPeer::NUM_HYDRATE_COLUMNS;

        ProductsPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + ProductsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersLinesPeer::ORDERS_ID, OrdersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersLinesPeer::PRODUCTS_ID, ProductsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersLinesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersLinesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersLinesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Orders rows

            $key2 = OrdersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = OrdersPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = OrdersPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    OrdersPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (OrdersLines) to the collection in $obj2 (Orders)
                $obj2->addOrdersLines($obj1);
            } // if joined row not null

            // Add objects for joined Products rows

            $key3 = ProductsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = ProductsPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = ProductsPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    ProductsPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (OrdersLines) to the collection in $obj3 (Products)
                $obj3->addOrdersLines($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Orders table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptOrders(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersLinesPeer::PRODUCTS_ID, ProductsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related Products table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptProducts(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersLinesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersLinesPeer::ORDERS_ID, OrdersPeer::ID, $join_behavior);

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
     * Selects a collection of OrdersLines objects pre-filled with all related objects except Orders.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of OrdersLines objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptOrders(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);
        }

        OrdersLinesPeer::addSelectColumns($criteria);
        $startcol2 = OrdersLinesPeer::NUM_HYDRATE_COLUMNS;

        ProductsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + ProductsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersLinesPeer::PRODUCTS_ID, ProductsPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersLinesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersLinesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersLinesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Products rows

                $key2 = ProductsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = ProductsPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = ProductsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    ProductsPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (OrdersLines) to the collection in $obj2 (Products)
                $obj2->addOrdersLines($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of OrdersLines objects pre-filled with all related objects except Products.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of OrdersLines objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptProducts(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);
        }

        OrdersLinesPeer::addSelectColumns($criteria);
        $startcol2 = OrdersLinesPeer::NUM_HYDRATE_COLUMNS;

        OrdersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + OrdersPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersLinesPeer::ORDERS_ID, OrdersPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersLinesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersLinesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersLinesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersLinesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Orders rows

                $key2 = OrdersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = OrdersPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = OrdersPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    OrdersPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (OrdersLines) to the collection in $obj2 (Orders)
                $obj2->addOrdersLines($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
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
        return Propel::getDatabaseMap(OrdersLinesPeer::DATABASE_NAME)->getTable(OrdersLinesPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseOrdersLinesPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseOrdersLinesPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\OrdersLinesTableMap());
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
        return OrdersLinesPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a OrdersLines or Criteria object.
     *
     * @param      mixed $values Criteria or OrdersLines object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from OrdersLines object
        }


        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a OrdersLines or Criteria object.
     *
     * @param      mixed $values Criteria or OrdersLines object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(OrdersLinesPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(OrdersLinesPeer::ID);
            $value = $criteria->remove(OrdersLinesPeer::ID);
            if ($value) {
                $selectCriteria->add(OrdersLinesPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(OrdersLinesPeer::TABLE_NAME);
            }

        } else { // $values is OrdersLines object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the orders_lines table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(OrdersLinesPeer::TABLE_NAME, $con, OrdersLinesPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OrdersLinesPeer::clearInstancePool();
            OrdersLinesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a OrdersLines or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or OrdersLines object or primary key or array of primary keys
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
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            OrdersLinesPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof OrdersLines) { // it's a model object
            // invalidate the cache for this single object
            OrdersLinesPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrdersLinesPeer::DATABASE_NAME);
            $criteria->add(OrdersLinesPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                OrdersLinesPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(OrdersLinesPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            OrdersLinesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given OrdersLines object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param OrdersLines $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(OrdersLinesPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(OrdersLinesPeer::TABLE_NAME);

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

        return BasePeer::doValidate(OrdersLinesPeer::DATABASE_NAME, OrdersLinesPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return OrdersLines
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = OrdersLinesPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(OrdersLinesPeer::DATABASE_NAME);
        $criteria->add(OrdersLinesPeer::ID, $pk);

        $v = OrdersLinesPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return OrdersLines[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(OrdersLinesPeer::DATABASE_NAME);
            $criteria->add(OrdersLinesPeer::ID, $pks, Criteria::IN);
            $objs = OrdersLinesPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseOrdersLinesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseOrdersLinesPeer::buildTableMap();

EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('Hanzo\Model\om\BaseOrdersLinesPeer'));

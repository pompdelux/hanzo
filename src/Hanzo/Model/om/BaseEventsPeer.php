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
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipantsPeer;
use Hanzo\Model\EventsPeer;
use Hanzo\Model\map\EventsTableMap;

abstract class BaseEventsPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'events';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\Events';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\EventsTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 22;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 22;

    /** the column name for the id field */
    const ID = 'events.id';

    /** the column name for the code field */
    const CODE = 'events.code';

    /** the column name for the key field */
    const KEY = 'events.key';

    /** the column name for the consultants_id field */
    const CONSULTANTS_ID = 'events.consultants_id';

    /** the column name for the customers_id field */
    const CUSTOMERS_ID = 'events.customers_id';

    /** the column name for the event_date field */
    const EVENT_DATE = 'events.event_date';

    /** the column name for the event_end_time field */
    const EVENT_END_TIME = 'events.event_end_time';

    /** the column name for the host field */
    const HOST = 'events.host';

    /** the column name for the address_line_1 field */
    const ADDRESS_LINE_1 = 'events.address_line_1';

    /** the column name for the address_line_2 field */
    const ADDRESS_LINE_2 = 'events.address_line_2';

    /** the column name for the postal_code field */
    const POSTAL_CODE = 'events.postal_code';

    /** the column name for the city field */
    const CITY = 'events.city';

    /** the column name for the phone field */
    const PHONE = 'events.phone';

    /** the column name for the email field */
    const EMAIL = 'events.email';

    /** the column name for the description field */
    const DESCRIPTION = 'events.description';

    /** the column name for the type field */
    const TYPE = 'events.type';

    /** the column name for the is_open field */
    const IS_OPEN = 'events.is_open';

    /** the column name for the notify_hostess field */
    const NOTIFY_HOSTESS = 'events.notify_hostess';

    /** the column name for the rsvp_type field */
    const RSVP_TYPE = 'events.rsvp_type';

    /** the column name for the public_note field */
    const PUBLIC_NOTE = 'events.public_note';

    /** the column name for the created_at field */
    const CREATED_AT = 'events.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'events.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Events objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Events[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. EventsPeer::$fieldNames[EventsPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Code', 'Key', 'ConsultantsId', 'CustomersId', 'EventDate', 'EventEndTime', 'Host', 'AddressLine1', 'AddressLine2', 'PostalCode', 'City', 'Phone', 'Email', 'Description', 'Type', 'IsOpen', 'NotifyHostess', 'RsvpType', 'PublicNote', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'code', 'key', 'consultantsId', 'customersId', 'eventDate', 'eventEndTime', 'host', 'addressLine1', 'addressLine2', 'postalCode', 'city', 'phone', 'email', 'description', 'type', 'isOpen', 'notifyHostess', 'rsvpType', 'publicNote', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (EventsPeer::ID, EventsPeer::CODE, EventsPeer::KEY, EventsPeer::CONSULTANTS_ID, EventsPeer::CUSTOMERS_ID, EventsPeer::EVENT_DATE, EventsPeer::EVENT_END_TIME, EventsPeer::HOST, EventsPeer::ADDRESS_LINE_1, EventsPeer::ADDRESS_LINE_2, EventsPeer::POSTAL_CODE, EventsPeer::CITY, EventsPeer::PHONE, EventsPeer::EMAIL, EventsPeer::DESCRIPTION, EventsPeer::TYPE, EventsPeer::IS_OPEN, EventsPeer::NOTIFY_HOSTESS, EventsPeer::RSVP_TYPE, EventsPeer::PUBLIC_NOTE, EventsPeer::CREATED_AT, EventsPeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'CODE', 'KEY', 'CONSULTANTS_ID', 'CUSTOMERS_ID', 'EVENT_DATE', 'EVENT_END_TIME', 'HOST', 'ADDRESS_LINE_1', 'ADDRESS_LINE_2', 'POSTAL_CODE', 'CITY', 'PHONE', 'EMAIL', 'DESCRIPTION', 'TYPE', 'IS_OPEN', 'NOTIFY_HOSTESS', 'RSVP_TYPE', 'PUBLIC_NOTE', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'code', 'key', 'consultants_id', 'customers_id', 'event_date', 'event_end_time', 'host', 'address_line_1', 'address_line_2', 'postal_code', 'city', 'phone', 'email', 'description', 'type', 'is_open', 'notify_hostess', 'rsvp_type', 'public_note', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. EventsPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Code' => 1, 'Key' => 2, 'ConsultantsId' => 3, 'CustomersId' => 4, 'EventDate' => 5, 'EventEndTime' => 6, 'Host' => 7, 'AddressLine1' => 8, 'AddressLine2' => 9, 'PostalCode' => 10, 'City' => 11, 'Phone' => 12, 'Email' => 13, 'Description' => 14, 'Type' => 15, 'IsOpen' => 16, 'NotifyHostess' => 17, 'RsvpType' => 18, 'PublicNote' => 19, 'CreatedAt' => 20, 'UpdatedAt' => 21, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'code' => 1, 'key' => 2, 'consultantsId' => 3, 'customersId' => 4, 'eventDate' => 5, 'eventEndTime' => 6, 'host' => 7, 'addressLine1' => 8, 'addressLine2' => 9, 'postalCode' => 10, 'city' => 11, 'phone' => 12, 'email' => 13, 'description' => 14, 'type' => 15, 'isOpen' => 16, 'notifyHostess' => 17, 'rsvpType' => 18, 'publicNote' => 19, 'createdAt' => 20, 'updatedAt' => 21, ),
        BasePeer::TYPE_COLNAME => array (EventsPeer::ID => 0, EventsPeer::CODE => 1, EventsPeer::KEY => 2, EventsPeer::CONSULTANTS_ID => 3, EventsPeer::CUSTOMERS_ID => 4, EventsPeer::EVENT_DATE => 5, EventsPeer::EVENT_END_TIME => 6, EventsPeer::HOST => 7, EventsPeer::ADDRESS_LINE_1 => 8, EventsPeer::ADDRESS_LINE_2 => 9, EventsPeer::POSTAL_CODE => 10, EventsPeer::CITY => 11, EventsPeer::PHONE => 12, EventsPeer::EMAIL => 13, EventsPeer::DESCRIPTION => 14, EventsPeer::TYPE => 15, EventsPeer::IS_OPEN => 16, EventsPeer::NOTIFY_HOSTESS => 17, EventsPeer::RSVP_TYPE => 18, EventsPeer::PUBLIC_NOTE => 19, EventsPeer::CREATED_AT => 20, EventsPeer::UPDATED_AT => 21, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'CODE' => 1, 'KEY' => 2, 'CONSULTANTS_ID' => 3, 'CUSTOMERS_ID' => 4, 'EVENT_DATE' => 5, 'EVENT_END_TIME' => 6, 'HOST' => 7, 'ADDRESS_LINE_1' => 8, 'ADDRESS_LINE_2' => 9, 'POSTAL_CODE' => 10, 'CITY' => 11, 'PHONE' => 12, 'EMAIL' => 13, 'DESCRIPTION' => 14, 'TYPE' => 15, 'IS_OPEN' => 16, 'NOTIFY_HOSTESS' => 17, 'RSVP_TYPE' => 18, 'PUBLIC_NOTE' => 19, 'CREATED_AT' => 20, 'UPDATED_AT' => 21, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'code' => 1, 'key' => 2, 'consultants_id' => 3, 'customers_id' => 4, 'event_date' => 5, 'event_end_time' => 6, 'host' => 7, 'address_line_1' => 8, 'address_line_2' => 9, 'postal_code' => 10, 'city' => 11, 'phone' => 12, 'email' => 13, 'description' => 14, 'type' => 15, 'is_open' => 16, 'notify_hostess' => 17, 'rsvp_type' => 18, 'public_note' => 19, 'created_at' => 20, 'updated_at' => 21, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, )
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
        $toNames = EventsPeer::getFieldNames($toType);
        $key = isset(EventsPeer::$fieldKeys[$fromType][$name]) ? EventsPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(EventsPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, EventsPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return EventsPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. EventsPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(EventsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(EventsPeer::ID);
            $criteria->addSelectColumn(EventsPeer::CODE);
            $criteria->addSelectColumn(EventsPeer::KEY);
            $criteria->addSelectColumn(EventsPeer::CONSULTANTS_ID);
            $criteria->addSelectColumn(EventsPeer::CUSTOMERS_ID);
            $criteria->addSelectColumn(EventsPeer::EVENT_DATE);
            $criteria->addSelectColumn(EventsPeer::EVENT_END_TIME);
            $criteria->addSelectColumn(EventsPeer::HOST);
            $criteria->addSelectColumn(EventsPeer::ADDRESS_LINE_1);
            $criteria->addSelectColumn(EventsPeer::ADDRESS_LINE_2);
            $criteria->addSelectColumn(EventsPeer::POSTAL_CODE);
            $criteria->addSelectColumn(EventsPeer::CITY);
            $criteria->addSelectColumn(EventsPeer::PHONE);
            $criteria->addSelectColumn(EventsPeer::EMAIL);
            $criteria->addSelectColumn(EventsPeer::DESCRIPTION);
            $criteria->addSelectColumn(EventsPeer::TYPE);
            $criteria->addSelectColumn(EventsPeer::IS_OPEN);
            $criteria->addSelectColumn(EventsPeer::NOTIFY_HOSTESS);
            $criteria->addSelectColumn(EventsPeer::RSVP_TYPE);
            $criteria->addSelectColumn(EventsPeer::PUBLIC_NOTE);
            $criteria->addSelectColumn(EventsPeer::CREATED_AT);
            $criteria->addSelectColumn(EventsPeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.code');
            $criteria->addSelectColumn($alias . '.key');
            $criteria->addSelectColumn($alias . '.consultants_id');
            $criteria->addSelectColumn($alias . '.customers_id');
            $criteria->addSelectColumn($alias . '.event_date');
            $criteria->addSelectColumn($alias . '.event_end_time');
            $criteria->addSelectColumn($alias . '.host');
            $criteria->addSelectColumn($alias . '.address_line_1');
            $criteria->addSelectColumn($alias . '.address_line_2');
            $criteria->addSelectColumn($alias . '.postal_code');
            $criteria->addSelectColumn($alias . '.city');
            $criteria->addSelectColumn($alias . '.phone');
            $criteria->addSelectColumn($alias . '.email');
            $criteria->addSelectColumn($alias . '.description');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.is_open');
            $criteria->addSelectColumn($alias . '.notify_hostess');
            $criteria->addSelectColumn($alias . '.rsvp_type');
            $criteria->addSelectColumn($alias . '.public_note');
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
        $criteria->setPrimaryTableName(EventsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(EventsPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return Events
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = EventsPeer::doSelect($critcopy, $con);
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
        return EventsPeer::populateObjects(EventsPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            EventsPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

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
     * @param Events $obj A Events object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            EventsPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Events object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Events) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Events object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(EventsPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Events Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(EventsPeer::$instances[$key])) {
                return EventsPeer::$instances[$key];
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
        foreach (EventsPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        EventsPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to events
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in EventsParticipantsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        EventsParticipantsPeer::clearInstancePool();
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
        $cls = EventsPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = EventsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = EventsPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                EventsPeer::addInstanceToPool($obj, $key);
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
     * @return array (Events object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = EventsPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + EventsPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = EventsPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            EventsPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related CustomersRelatedByConsultantsId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCustomersRelatedByConsultantsId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(EventsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(EventsPeer::CONSULTANTS_ID, CustomersPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CustomersRelatedByCustomersId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCustomersRelatedByCustomersId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(EventsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(EventsPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

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
     * Selects a collection of Events objects pre-filled with their Customers objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Events objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCustomersRelatedByConsultantsId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsPeer::DATABASE_NAME);
        }

        EventsPeer::addSelectColumns($criteria);
        $startcol = EventsPeer::NUM_HYDRATE_COLUMNS;
        CustomersPeer::addSelectColumns($criteria);

        $criteria->addJoin(EventsPeer::CONSULTANTS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = EventsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Events) to $obj2 (Customers)
                $obj2->addEventsRelatedByConsultantsId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Events objects pre-filled with their Customers objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Events objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCustomersRelatedByCustomersId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsPeer::DATABASE_NAME);
        }

        EventsPeer::addSelectColumns($criteria);
        $startcol = EventsPeer::NUM_HYDRATE_COLUMNS;
        CustomersPeer::addSelectColumns($criteria);

        $criteria->addJoin(EventsPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = EventsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Events) to $obj2 (Customers)
                $obj2->addEventsRelatedByCustomersId($obj1);

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
        $criteria->setPrimaryTableName(EventsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(EventsPeer::CONSULTANTS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(EventsPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

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
     * Selects a collection of Events objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Events objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsPeer::DATABASE_NAME);
        }

        EventsPeer::addSelectColumns($criteria);
        $startcol2 = EventsPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(EventsPeer::CONSULTANTS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(EventsPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = EventsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Events) to the collection in $obj2 (Customers)
                $obj2->addEventsRelatedByConsultantsId($obj1);
            } // if joined row not null

            // Add objects for joined Customers rows

            $key3 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CustomersPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CustomersPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CustomersPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (Events) to the collection in $obj3 (Customers)
                $obj3->addEventsRelatedByCustomersId($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CustomersRelatedByConsultantsId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCustomersRelatedByConsultantsId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(EventsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

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
     * Returns the number of rows matching criteria, joining the related CustomersRelatedByCustomersId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCustomersRelatedByCustomersId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(EventsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

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
     * Selects a collection of Events objects pre-filled with all related objects except CustomersRelatedByConsultantsId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Events objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCustomersRelatedByConsultantsId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsPeer::DATABASE_NAME);
        }

        EventsPeer::addSelectColumns($criteria);
        $startcol2 = EventsPeer::NUM_HYDRATE_COLUMNS;


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = EventsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Events objects pre-filled with all related objects except CustomersRelatedByCustomersId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Events objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCustomersRelatedByCustomersId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsPeer::DATABASE_NAME);
        }

        EventsPeer::addSelectColumns($criteria);
        $startcol2 = EventsPeer::NUM_HYDRATE_COLUMNS;


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = EventsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

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
        return Propel::getDatabaseMap(EventsPeer::DATABASE_NAME)->getTable(EventsPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseEventsPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseEventsPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\EventsTableMap());
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
        return EventsPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Events or Criteria object.
     *
     * @param      mixed $values Criteria or Events object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Events object
        }


        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a Events or Criteria object.
     *
     * @param      mixed $values Criteria or Events object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(EventsPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(EventsPeer::ID);
            $value = $criteria->remove(EventsPeer::ID);
            if ($value) {
                $selectCriteria->add(EventsPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(EventsPeer::TABLE_NAME);
            }

        } else { // $values is Events object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the events table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(EventsPeer::TABLE_NAME, $con, EventsPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            EventsPeer::clearInstancePool();
            EventsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Events or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Events object or primary key or array of primary keys
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
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            EventsPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Events) { // it's a model object
            // invalidate the cache for this single object
            EventsPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(EventsPeer::DATABASE_NAME);
            $criteria->add(EventsPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                EventsPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(EventsPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            EventsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Events object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Events $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(EventsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(EventsPeer::TABLE_NAME);

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

        return BasePeer::doValidate(EventsPeer::DATABASE_NAME, EventsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Events
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = EventsPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(EventsPeer::DATABASE_NAME);
        $criteria->add(EventsPeer::ID, $pk);

        $v = EventsPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Events[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(EventsPeer::DATABASE_NAME);
            $criteria->add(EventsPeer::ID, $pks, Criteria::IN);
            $objs = EventsPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseEventsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseEventsPeer::buildTableMap();

EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('Hanzo\Model\om\BaseEventsPeer'));

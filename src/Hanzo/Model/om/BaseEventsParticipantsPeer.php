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
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\EventsParticipantsPeer;
use Hanzo\Model\EventsPeer;
use Hanzo\Model\map\EventsParticipantsTableMap;

abstract class BaseEventsParticipantsPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'events_participants';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\EventsParticipants';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\EventsParticipantsTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 16;

    /** the column name for the id field */
    const ID = 'events_participants.id';

    /** the column name for the events_id field */
    const EVENTS_ID = 'events_participants.events_id';

    /** the column name for the key field */
    const KEY = 'events_participants.key';

    /** the column name for the invited_by field */
    const INVITED_BY = 'events_participants.invited_by';

    /** the column name for the first_name field */
    const FIRST_NAME = 'events_participants.first_name';

    /** the column name for the last_name field */
    const LAST_NAME = 'events_participants.last_name';

    /** the column name for the email field */
    const EMAIL = 'events_participants.email';

    /** the column name for the phone field */
    const PHONE = 'events_participants.phone';

    /** the column name for the tell_a_friend field */
    const TELL_A_FRIEND = 'events_participants.tell_a_friend';

    /** the column name for the notify_by_sms field */
    const NOTIFY_BY_SMS = 'events_participants.notify_by_sms';

    /** the column name for the sms_send_at field */
    const SMS_SEND_AT = 'events_participants.sms_send_at';

    /** the column name for the has_accepted field */
    const HAS_ACCEPTED = 'events_participants.has_accepted';

    /** the column name for the expires_at field */
    const EXPIRES_AT = 'events_participants.expires_at';

    /** the column name for the responded_at field */
    const RESPONDED_AT = 'events_participants.responded_at';

    /** the column name for the created_at field */
    const CREATED_AT = 'events_participants.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'events_participants.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of EventsParticipants objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array EventsParticipants[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. EventsParticipantsPeer::$fieldNames[EventsParticipantsPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'EventsId', 'Key', 'InvitedBy', 'FirstName', 'LastName', 'Email', 'Phone', 'TellAFriend', 'NotifyBySms', 'SmsSendAt', 'HasAccepted', 'ExpiresAt', 'RespondedAt', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'eventsId', 'key', 'invitedBy', 'firstName', 'lastName', 'email', 'phone', 'tellAFriend', 'notifyBySms', 'smsSendAt', 'hasAccepted', 'expiresAt', 'respondedAt', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (EventsParticipantsPeer::ID, EventsParticipantsPeer::EVENTS_ID, EventsParticipantsPeer::KEY, EventsParticipantsPeer::INVITED_BY, EventsParticipantsPeer::FIRST_NAME, EventsParticipantsPeer::LAST_NAME, EventsParticipantsPeer::EMAIL, EventsParticipantsPeer::PHONE, EventsParticipantsPeer::TELL_A_FRIEND, EventsParticipantsPeer::NOTIFY_BY_SMS, EventsParticipantsPeer::SMS_SEND_AT, EventsParticipantsPeer::HAS_ACCEPTED, EventsParticipantsPeer::EXPIRES_AT, EventsParticipantsPeer::RESPONDED_AT, EventsParticipantsPeer::CREATED_AT, EventsParticipantsPeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'EVENTS_ID', 'KEY', 'INVITED_BY', 'FIRST_NAME', 'LAST_NAME', 'EMAIL', 'PHONE', 'TELL_A_FRIEND', 'NOTIFY_BY_SMS', 'SMS_SEND_AT', 'HAS_ACCEPTED', 'EXPIRES_AT', 'RESPONDED_AT', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'events_id', 'key', 'invited_by', 'first_name', 'last_name', 'email', 'phone', 'tell_a_friend', 'notify_by_sms', 'sms_send_at', 'has_accepted', 'expires_at', 'responded_at', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. EventsParticipantsPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'EventsId' => 1, 'Key' => 2, 'InvitedBy' => 3, 'FirstName' => 4, 'LastName' => 5, 'Email' => 6, 'Phone' => 7, 'TellAFriend' => 8, 'NotifyBySms' => 9, 'SmsSendAt' => 10, 'HasAccepted' => 11, 'ExpiresAt' => 12, 'RespondedAt' => 13, 'CreatedAt' => 14, 'UpdatedAt' => 15, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'eventsId' => 1, 'key' => 2, 'invitedBy' => 3, 'firstName' => 4, 'lastName' => 5, 'email' => 6, 'phone' => 7, 'tellAFriend' => 8, 'notifyBySms' => 9, 'smsSendAt' => 10, 'hasAccepted' => 11, 'expiresAt' => 12, 'respondedAt' => 13, 'createdAt' => 14, 'updatedAt' => 15, ),
        BasePeer::TYPE_COLNAME => array (EventsParticipantsPeer::ID => 0, EventsParticipantsPeer::EVENTS_ID => 1, EventsParticipantsPeer::KEY => 2, EventsParticipantsPeer::INVITED_BY => 3, EventsParticipantsPeer::FIRST_NAME => 4, EventsParticipantsPeer::LAST_NAME => 5, EventsParticipantsPeer::EMAIL => 6, EventsParticipantsPeer::PHONE => 7, EventsParticipantsPeer::TELL_A_FRIEND => 8, EventsParticipantsPeer::NOTIFY_BY_SMS => 9, EventsParticipantsPeer::SMS_SEND_AT => 10, EventsParticipantsPeer::HAS_ACCEPTED => 11, EventsParticipantsPeer::EXPIRES_AT => 12, EventsParticipantsPeer::RESPONDED_AT => 13, EventsParticipantsPeer::CREATED_AT => 14, EventsParticipantsPeer::UPDATED_AT => 15, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'EVENTS_ID' => 1, 'KEY' => 2, 'INVITED_BY' => 3, 'FIRST_NAME' => 4, 'LAST_NAME' => 5, 'EMAIL' => 6, 'PHONE' => 7, 'TELL_A_FRIEND' => 8, 'NOTIFY_BY_SMS' => 9, 'SMS_SEND_AT' => 10, 'HAS_ACCEPTED' => 11, 'EXPIRES_AT' => 12, 'RESPONDED_AT' => 13, 'CREATED_AT' => 14, 'UPDATED_AT' => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'events_id' => 1, 'key' => 2, 'invited_by' => 3, 'first_name' => 4, 'last_name' => 5, 'email' => 6, 'phone' => 7, 'tell_a_friend' => 8, 'notify_by_sms' => 9, 'sms_send_at' => 10, 'has_accepted' => 11, 'expires_at' => 12, 'responded_at' => 13, 'created_at' => 14, 'updated_at' => 15, ),
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
        $toNames = EventsParticipantsPeer::getFieldNames($toType);
        $key = isset(EventsParticipantsPeer::$fieldKeys[$fromType][$name]) ? EventsParticipantsPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(EventsParticipantsPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, EventsParticipantsPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return EventsParticipantsPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. EventsParticipantsPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(EventsParticipantsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(EventsParticipantsPeer::ID);
            $criteria->addSelectColumn(EventsParticipantsPeer::EVENTS_ID);
            $criteria->addSelectColumn(EventsParticipantsPeer::KEY);
            $criteria->addSelectColumn(EventsParticipantsPeer::INVITED_BY);
            $criteria->addSelectColumn(EventsParticipantsPeer::FIRST_NAME);
            $criteria->addSelectColumn(EventsParticipantsPeer::LAST_NAME);
            $criteria->addSelectColumn(EventsParticipantsPeer::EMAIL);
            $criteria->addSelectColumn(EventsParticipantsPeer::PHONE);
            $criteria->addSelectColumn(EventsParticipantsPeer::TELL_A_FRIEND);
            $criteria->addSelectColumn(EventsParticipantsPeer::NOTIFY_BY_SMS);
            $criteria->addSelectColumn(EventsParticipantsPeer::SMS_SEND_AT);
            $criteria->addSelectColumn(EventsParticipantsPeer::HAS_ACCEPTED);
            $criteria->addSelectColumn(EventsParticipantsPeer::EXPIRES_AT);
            $criteria->addSelectColumn(EventsParticipantsPeer::RESPONDED_AT);
            $criteria->addSelectColumn(EventsParticipantsPeer::CREATED_AT);
            $criteria->addSelectColumn(EventsParticipantsPeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.events_id');
            $criteria->addSelectColumn($alias . '.key');
            $criteria->addSelectColumn($alias . '.invited_by');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.last_name');
            $criteria->addSelectColumn($alias . '.email');
            $criteria->addSelectColumn($alias . '.phone');
            $criteria->addSelectColumn($alias . '.tell_a_friend');
            $criteria->addSelectColumn($alias . '.notify_by_sms');
            $criteria->addSelectColumn($alias . '.sms_send_at');
            $criteria->addSelectColumn($alias . '.has_accepted');
            $criteria->addSelectColumn($alias . '.expires_at');
            $criteria->addSelectColumn($alias . '.responded_at');
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
        $criteria->setPrimaryTableName(EventsParticipantsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsParticipantsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return EventsParticipants
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = EventsParticipantsPeer::doSelect($critcopy, $con);
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
        return EventsParticipantsPeer::populateObjects(EventsParticipantsPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            EventsParticipantsPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);

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
     * @param EventsParticipants $obj A EventsParticipants object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            EventsParticipantsPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A EventsParticipants object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof EventsParticipants) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or EventsParticipants object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(EventsParticipantsPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return EventsParticipants Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(EventsParticipantsPeer::$instances[$key])) {
                return EventsParticipantsPeer::$instances[$key];
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
        foreach (EventsParticipantsPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        EventsParticipantsPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to events_participants
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
        $cls = EventsParticipantsPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = EventsParticipantsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = EventsParticipantsPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                EventsParticipantsPeer::addInstanceToPool($obj, $key);
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
     * @return array (EventsParticipants object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = EventsParticipantsPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = EventsParticipantsPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + EventsParticipantsPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = EventsParticipantsPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            EventsParticipantsPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Events table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinEvents(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(EventsParticipantsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsParticipantsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(EventsParticipantsPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Selects a collection of EventsParticipants objects pre-filled with their Events objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of EventsParticipants objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinEvents(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);
        }

        EventsParticipantsPeer::addSelectColumns($criteria);
        $startcol = EventsParticipantsPeer::NUM_HYDRATE_COLUMNS;
        EventsPeer::addSelectColumns($criteria);

        $criteria->addJoin(EventsParticipantsPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsParticipantsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsParticipantsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = EventsParticipantsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsParticipantsPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = EventsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = EventsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    EventsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (EventsParticipants) to $obj2 (Events)
                $obj2->addEventsParticipants($obj1);

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
        $criteria->setPrimaryTableName(EventsParticipantsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            EventsParticipantsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(EventsParticipantsPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Selects a collection of EventsParticipants objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of EventsParticipants objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);
        }

        EventsParticipantsPeer::addSelectColumns($criteria);
        $startcol2 = EventsParticipantsPeer::NUM_HYDRATE_COLUMNS;

        EventsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + EventsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(EventsParticipantsPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = EventsParticipantsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = EventsParticipantsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = EventsParticipantsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                EventsParticipantsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Events rows

            $key2 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = EventsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = EventsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    EventsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (EventsParticipants) to the collection in $obj2 (Events)
                $obj2->addEventsParticipants($obj1);
            } // if joined row not null

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
        return Propel::getDatabaseMap(EventsParticipantsPeer::DATABASE_NAME)->getTable(EventsParticipantsPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseEventsParticipantsPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseEventsParticipantsPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\EventsParticipantsTableMap());
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
        return EventsParticipantsPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a EventsParticipants or Criteria object.
     *
     * @param      mixed $values Criteria or EventsParticipants object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from EventsParticipants object
        }


        // Set the correct dbName
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a EventsParticipants or Criteria object.
     *
     * @param      mixed $values Criteria or EventsParticipants object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(EventsParticipantsPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(EventsParticipantsPeer::ID);
            $value = $criteria->remove(EventsParticipantsPeer::ID);
            if ($value) {
                $selectCriteria->add(EventsParticipantsPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(EventsParticipantsPeer::TABLE_NAME);
            }

        } else { // $values is EventsParticipants object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the events_participants table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(EventsParticipantsPeer::TABLE_NAME, $con, EventsParticipantsPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            EventsParticipantsPeer::clearInstancePool();
            EventsParticipantsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a EventsParticipants or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or EventsParticipants object or primary key or array of primary keys
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
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            EventsParticipantsPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof EventsParticipants) { // it's a model object
            // invalidate the cache for this single object
            EventsParticipantsPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(EventsParticipantsPeer::DATABASE_NAME);
            $criteria->add(EventsParticipantsPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                EventsParticipantsPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(EventsParticipantsPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            EventsParticipantsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given EventsParticipants object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param EventsParticipants $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(EventsParticipantsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(EventsParticipantsPeer::TABLE_NAME);

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

        return BasePeer::doValidate(EventsParticipantsPeer::DATABASE_NAME, EventsParticipantsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return EventsParticipants
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = EventsParticipantsPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(EventsParticipantsPeer::DATABASE_NAME);
        $criteria->add(EventsParticipantsPeer::ID, $pk);

        $v = EventsParticipantsPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return EventsParticipants[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(EventsParticipantsPeer::DATABASE_NAME);
            $criteria->add(EventsParticipantsPeer::ID, $pks, Criteria::IN);
            $objs = EventsParticipantsPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseEventsParticipantsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseEventsParticipantsPeer::buildTableMap();

EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('Hanzo\Model\om\BaseEventsParticipantsPeer'));

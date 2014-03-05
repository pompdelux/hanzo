<?php

namespace Hanzo\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Wall;
use Hanzo\Model\WallLikes;
use Hanzo\Model\WallLikesQuery;
use Hanzo\Model\WallPeer;
use Hanzo\Model\WallQuery;

abstract class BaseWall extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\WallPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        WallPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the parent_id field.
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the customers_id field.
     * @var        int
     */
    protected $customers_id;

    /**
     * The value for the messate field.
     * @var        string
     */
    protected $messate;

    /**
     * The value for the status field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $status;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        Wall
     */
    protected $aWallRelatedByParentId;

    /**
     * @var        Customers
     */
    protected $aCustomers;

    /**
     * @var        PropelObjectCollection|Wall[] Collection to store aggregation of Wall objects.
     */
    protected $collWallsRelatedById;
    protected $collWallsRelatedByIdPartial;

    /**
     * @var        PropelObjectCollection|WallLikes[] Collection to store aggregation of WallLikes objects.
     */
    protected $collWallLikess;
    protected $collWallLikessPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $wallsRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $wallLikessScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->status = true;
    }

    /**
     * Initializes internal state of BaseWall object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {

        return $this->parent_id;
    }

    /**
     * Get the [customers_id] column value.
     *
     * @return int
     */
    public function getCustomersId()
    {

        return $this->customers_id;
    }

    /**
     * Get the [messate] column value.
     *
     * @return string
     */
    public function getMessate()
    {

        return $this->messate;
    }

    /**
     * Get the [status] column value.
     *
     * @return boolean
     */
    public function getStatus()
    {

        return $this->status;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
     * option in order to avoid conversions to integers (which are limited in the dates they can express).
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw unix timestamp integer will be returned.
     * @return mixed Formatted date/time value as string or (integer) unix timestamp (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->created_at === null) {
            return null;
        }

        if ($this->created_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
            return (int) $dt->format('U');
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
     * option in order to avoid conversions to integers (which are limited in the dates they can express).
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw unix timestamp integer will be returned.
     * @return mixed Formatted date/time value as string or (integer) unix timestamp (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->updated_at === null) {
            return null;
        }

        if ($this->updated_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
            return (int) $dt->format('U');
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Wall The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = WallPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param  int $v new value
     * @return Wall The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[] = WallPeer::PARENT_ID;
        }

        if ($this->aWallRelatedByParentId !== null && $this->aWallRelatedByParentId->getId() !== $v) {
            $this->aWallRelatedByParentId = null;
        }


        return $this;
    } // setParentId()

    /**
     * Set the value of [customers_id] column.
     *
     * @param  int $v new value
     * @return Wall The current object (for fluent API support)
     */
    public function setCustomersId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->customers_id !== $v) {
            $this->customers_id = $v;
            $this->modifiedColumns[] = WallPeer::CUSTOMERS_ID;
        }

        if ($this->aCustomers !== null && $this->aCustomers->getId() !== $v) {
            $this->aCustomers = null;
        }


        return $this;
    } // setCustomersId()

    /**
     * Set the value of [messate] column.
     *
     * @param  string $v new value
     * @return Wall The current object (for fluent API support)
     */
    public function setMessate($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->messate !== $v) {
            $this->messate = $v;
            $this->modifiedColumns[] = WallPeer::MESSATE;
        }


        return $this;
    } // setMessate()

    /**
     * Sets the value of the [status] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Wall The current object (for fluent API support)
     */
    public function setStatus($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->status !== $v) {
            $this->status = $v;
            $this->modifiedColumns[] = WallPeer::STATUS;
        }


        return $this;
    } // setStatus()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Wall The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = WallPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Wall The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = WallPeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->status !== true) {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->customers_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->messate = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->status = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->created_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->updated_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = WallPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Wall object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aWallRelatedByParentId !== null && $this->parent_id !== $this->aWallRelatedByParentId->getId()) {
            $this->aWallRelatedByParentId = null;
        }
        if ($this->aCustomers !== null && $this->customers_id !== $this->aCustomers->getId()) {
            $this->aCustomers = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(WallPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = WallPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aWallRelatedByParentId = null;
            $this->aCustomers = null;
            $this->collWallsRelatedById = null;

            $this->collWallLikess = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(WallPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = WallQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(WallPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(WallPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(WallPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(WallPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                WallPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aWallRelatedByParentId !== null) {
                if ($this->aWallRelatedByParentId->isModified() || $this->aWallRelatedByParentId->isNew()) {
                    $affectedRows += $this->aWallRelatedByParentId->save($con);
                }
                $this->setWallRelatedByParentId($this->aWallRelatedByParentId);
            }

            if ($this->aCustomers !== null) {
                if ($this->aCustomers->isModified() || $this->aCustomers->isNew()) {
                    $affectedRows += $this->aCustomers->save($con);
                }
                $this->setCustomers($this->aCustomers);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->wallsRelatedByIdScheduledForDeletion !== null) {
                if (!$this->wallsRelatedByIdScheduledForDeletion->isEmpty()) {
                    WallQuery::create()
                        ->filterByPrimaryKeys($this->wallsRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->wallsRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collWallsRelatedById !== null) {
                foreach ($this->collWallsRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->wallLikessScheduledForDeletion !== null) {
                if (!$this->wallLikessScheduledForDeletion->isEmpty()) {
                    WallLikesQuery::create()
                        ->filterByPrimaryKeys($this->wallLikessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->wallLikessScheduledForDeletion = null;
                }
            }

            if ($this->collWallLikess !== null) {
                foreach ($this->collWallLikess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = WallPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . WallPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(WallPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(WallPeer::PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`parent_id`';
        }
        if ($this->isColumnModified(WallPeer::CUSTOMERS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`customers_id`';
        }
        if ($this->isColumnModified(WallPeer::MESSATE)) {
            $modifiedColumns[':p' . $index++]  = '`messate`';
        }
        if ($this->isColumnModified(WallPeer::STATUS)) {
            $modifiedColumns[':p' . $index++]  = '`status`';
        }
        if ($this->isColumnModified(WallPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(WallPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `wall` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`parent_id`':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case '`customers_id`':
                        $stmt->bindValue($identifier, $this->customers_id, PDO::PARAM_INT);
                        break;
                    case '`messate`':
                        $stmt->bindValue($identifier, $this->messate, PDO::PARAM_STR);
                        break;
                    case '`status`':
                        $stmt->bindValue($identifier, (int) $this->status, PDO::PARAM_INT);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aWallRelatedByParentId !== null) {
                if (!$this->aWallRelatedByParentId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aWallRelatedByParentId->getValidationFailures());
                }
            }

            if ($this->aCustomers !== null) {
                if (!$this->aCustomers->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCustomers->getValidationFailures());
                }
            }


            if (($retval = WallPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collWallsRelatedById !== null) {
                    foreach ($this->collWallsRelatedById as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collWallLikess !== null) {
                    foreach ($this->collWallLikess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = WallPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getParentId();
                break;
            case 2:
                return $this->getCustomersId();
                break;
            case 3:
                return $this->getMessate();
                break;
            case 4:
                return $this->getStatus();
                break;
            case 5:
                return $this->getCreatedAt();
                break;
            case 6:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Wall'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Wall'][$this->getPrimaryKey()] = true;
        $keys = WallPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getParentId(),
            $keys[2] => $this->getCustomersId(),
            $keys[3] => $this->getMessate(),
            $keys[4] => $this->getStatus(),
            $keys[5] => $this->getCreatedAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aWallRelatedByParentId) {
                $result['WallRelatedByParentId'] = $this->aWallRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCustomers) {
                $result['Customers'] = $this->aCustomers->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collWallsRelatedById) {
                $result['WallsRelatedById'] = $this->collWallsRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWallLikess) {
                $result['WallLikess'] = $this->collWallLikess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = WallPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setParentId($value);
                break;
            case 2:
                $this->setCustomersId($value);
                break;
            case 3:
                $this->setMessate($value);
                break;
            case 4:
                $this->setStatus($value);
                break;
            case 5:
                $this->setCreatedAt($value);
                break;
            case 6:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = WallPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCustomersId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setMessate($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setStatus($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCreatedAt($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUpdatedAt($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(WallPeer::DATABASE_NAME);

        if ($this->isColumnModified(WallPeer::ID)) $criteria->add(WallPeer::ID, $this->id);
        if ($this->isColumnModified(WallPeer::PARENT_ID)) $criteria->add(WallPeer::PARENT_ID, $this->parent_id);
        if ($this->isColumnModified(WallPeer::CUSTOMERS_ID)) $criteria->add(WallPeer::CUSTOMERS_ID, $this->customers_id);
        if ($this->isColumnModified(WallPeer::MESSATE)) $criteria->add(WallPeer::MESSATE, $this->messate);
        if ($this->isColumnModified(WallPeer::STATUS)) $criteria->add(WallPeer::STATUS, $this->status);
        if ($this->isColumnModified(WallPeer::CREATED_AT)) $criteria->add(WallPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(WallPeer::UPDATED_AT)) $criteria->add(WallPeer::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(WallPeer::DATABASE_NAME);
        $criteria->add(WallPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Wall (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentId($this->getParentId());
        $copyObj->setCustomersId($this->getCustomersId());
        $copyObj->setMessate($this->getMessate());
        $copyObj->setStatus($this->getStatus());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getWallsRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWallRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWallLikess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWallLikes($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Wall Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return WallPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new WallPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Wall object.
     *
     * @param                  Wall $v
     * @return Wall The current object (for fluent API support)
     * @throws PropelException
     */
    public function setWallRelatedByParentId(Wall $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aWallRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Wall object, it will not be re-added.
        if ($v !== null) {
            $v->addWallRelatedById($this);
        }


        return $this;
    }


    /**
     * Get the associated Wall object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Wall The associated Wall object.
     * @throws PropelException
     */
    public function getWallRelatedByParentId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aWallRelatedByParentId === null && ($this->parent_id !== null) && $doQuery) {
            $this->aWallRelatedByParentId = WallQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aWallRelatedByParentId->addWallsRelatedById($this);
             */
        }

        return $this->aWallRelatedByParentId;
    }

    /**
     * Declares an association between this object and a Customers object.
     *
     * @param                  Customers $v
     * @return Wall The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCustomers(Customers $v = null)
    {
        if ($v === null) {
            $this->setCustomersId(NULL);
        } else {
            $this->setCustomersId($v->getId());
        }

        $this->aCustomers = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Customers object, it will not be re-added.
        if ($v !== null) {
            $v->addWall($this);
        }


        return $this;
    }


    /**
     * Get the associated Customers object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Customers The associated Customers object.
     * @throws PropelException
     */
    public function getCustomers(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCustomers === null && ($this->customers_id !== null) && $doQuery) {
            $this->aCustomers = CustomersQuery::create()->findPk($this->customers_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCustomers->addWalls($this);
             */
        }

        return $this->aCustomers;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('WallRelatedById' == $relationName) {
            $this->initWallsRelatedById();
        }
        if ('WallLikes' == $relationName) {
            $this->initWallLikess();
        }
    }

    /**
     * Clears out the collWallsRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Wall The current object (for fluent API support)
     * @see        addWallsRelatedById()
     */
    public function clearWallsRelatedById()
    {
        $this->collWallsRelatedById = null; // important to set this to null since that means it is uninitialized
        $this->collWallsRelatedByIdPartial = null;

        return $this;
    }

    /**
     * reset is the collWallsRelatedById collection loaded partially
     *
     * @return void
     */
    public function resetPartialWallsRelatedById($v = true)
    {
        $this->collWallsRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collWallsRelatedById collection.
     *
     * By default this just sets the collWallsRelatedById collection to an empty array (like clearcollWallsRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWallsRelatedById($overrideExisting = true)
    {
        if (null !== $this->collWallsRelatedById && !$overrideExisting) {
            return;
        }
        $this->collWallsRelatedById = new PropelObjectCollection();
        $this->collWallsRelatedById->setModel('Wall');
    }

    /**
     * Gets an array of Wall objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Wall is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Wall[] List of Wall objects
     * @throws PropelException
     */
    public function getWallsRelatedById($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWallsRelatedByIdPartial && !$this->isNew();
        if (null === $this->collWallsRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWallsRelatedById) {
                // return empty collection
                $this->initWallsRelatedById();
            } else {
                $collWallsRelatedById = WallQuery::create(null, $criteria)
                    ->filterByWallRelatedByParentId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWallsRelatedByIdPartial && count($collWallsRelatedById)) {
                      $this->initWallsRelatedById(false);

                      foreach ($collWallsRelatedById as $obj) {
                        if (false == $this->collWallsRelatedById->contains($obj)) {
                          $this->collWallsRelatedById->append($obj);
                        }
                      }

                      $this->collWallsRelatedByIdPartial = true;
                    }

                    $collWallsRelatedById->getInternalIterator()->rewind();

                    return $collWallsRelatedById;
                }

                if ($partial && $this->collWallsRelatedById) {
                    foreach ($this->collWallsRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collWallsRelatedById[] = $obj;
                        }
                    }
                }

                $this->collWallsRelatedById = $collWallsRelatedById;
                $this->collWallsRelatedByIdPartial = false;
            }
        }

        return $this->collWallsRelatedById;
    }

    /**
     * Sets a collection of WallRelatedById objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $wallsRelatedById A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Wall The current object (for fluent API support)
     */
    public function setWallsRelatedById(PropelCollection $wallsRelatedById, PropelPDO $con = null)
    {
        $wallsRelatedByIdToDelete = $this->getWallsRelatedById(new Criteria(), $con)->diff($wallsRelatedById);


        $this->wallsRelatedByIdScheduledForDeletion = $wallsRelatedByIdToDelete;

        foreach ($wallsRelatedByIdToDelete as $wallRelatedByIdRemoved) {
            $wallRelatedByIdRemoved->setWallRelatedByParentId(null);
        }

        $this->collWallsRelatedById = null;
        foreach ($wallsRelatedById as $wallRelatedById) {
            $this->addWallRelatedById($wallRelatedById);
        }

        $this->collWallsRelatedById = $wallsRelatedById;
        $this->collWallsRelatedByIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Wall objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Wall objects.
     * @throws PropelException
     */
    public function countWallsRelatedById(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWallsRelatedByIdPartial && !$this->isNew();
        if (null === $this->collWallsRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWallsRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWallsRelatedById());
            }
            $query = WallQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByWallRelatedByParentId($this)
                ->count($con);
        }

        return count($this->collWallsRelatedById);
    }

    /**
     * Method called to associate a Wall object to this object
     * through the Wall foreign key attribute.
     *
     * @param    Wall $l Wall
     * @return Wall The current object (for fluent API support)
     */
    public function addWallRelatedById(Wall $l)
    {
        if ($this->collWallsRelatedById === null) {
            $this->initWallsRelatedById();
            $this->collWallsRelatedByIdPartial = true;
        }

        if (!in_array($l, $this->collWallsRelatedById->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWallRelatedById($l);

            if ($this->wallsRelatedByIdScheduledForDeletion and $this->wallsRelatedByIdScheduledForDeletion->contains($l)) {
                $this->wallsRelatedByIdScheduledForDeletion->remove($this->wallsRelatedByIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	WallRelatedById $wallRelatedById The wallRelatedById object to add.
     */
    protected function doAddWallRelatedById($wallRelatedById)
    {
        $this->collWallsRelatedById[]= $wallRelatedById;
        $wallRelatedById->setWallRelatedByParentId($this);
    }

    /**
     * @param	WallRelatedById $wallRelatedById The wallRelatedById object to remove.
     * @return Wall The current object (for fluent API support)
     */
    public function removeWallRelatedById($wallRelatedById)
    {
        if ($this->getWallsRelatedById()->contains($wallRelatedById)) {
            $this->collWallsRelatedById->remove($this->collWallsRelatedById->search($wallRelatedById));
            if (null === $this->wallsRelatedByIdScheduledForDeletion) {
                $this->wallsRelatedByIdScheduledForDeletion = clone $this->collWallsRelatedById;
                $this->wallsRelatedByIdScheduledForDeletion->clear();
            }
            $this->wallsRelatedByIdScheduledForDeletion[]= $wallRelatedById;
            $wallRelatedById->setWallRelatedByParentId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Wall is new, it will return
     * an empty collection; or if this Wall has previously
     * been saved, it will retrieve related WallsRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Wall.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Wall[] List of Wall objects
     */
    public function getWallsRelatedByIdJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WallQuery::create(null, $criteria);
        $query->joinWith('Customers', $join_behavior);

        return $this->getWallsRelatedById($query, $con);
    }

    /**
     * Clears out the collWallLikess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Wall The current object (for fluent API support)
     * @see        addWallLikess()
     */
    public function clearWallLikess()
    {
        $this->collWallLikess = null; // important to set this to null since that means it is uninitialized
        $this->collWallLikessPartial = null;

        return $this;
    }

    /**
     * reset is the collWallLikess collection loaded partially
     *
     * @return void
     */
    public function resetPartialWallLikess($v = true)
    {
        $this->collWallLikessPartial = $v;
    }

    /**
     * Initializes the collWallLikess collection.
     *
     * By default this just sets the collWallLikess collection to an empty array (like clearcollWallLikess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWallLikess($overrideExisting = true)
    {
        if (null !== $this->collWallLikess && !$overrideExisting) {
            return;
        }
        $this->collWallLikess = new PropelObjectCollection();
        $this->collWallLikess->setModel('WallLikes');
    }

    /**
     * Gets an array of WallLikes objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Wall is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|WallLikes[] List of WallLikes objects
     * @throws PropelException
     */
    public function getWallLikess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWallLikessPartial && !$this->isNew();
        if (null === $this->collWallLikess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWallLikess) {
                // return empty collection
                $this->initWallLikess();
            } else {
                $collWallLikess = WallLikesQuery::create(null, $criteria)
                    ->filterByWall($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWallLikessPartial && count($collWallLikess)) {
                      $this->initWallLikess(false);

                      foreach ($collWallLikess as $obj) {
                        if (false == $this->collWallLikess->contains($obj)) {
                          $this->collWallLikess->append($obj);
                        }
                      }

                      $this->collWallLikessPartial = true;
                    }

                    $collWallLikess->getInternalIterator()->rewind();

                    return $collWallLikess;
                }

                if ($partial && $this->collWallLikess) {
                    foreach ($this->collWallLikess as $obj) {
                        if ($obj->isNew()) {
                            $collWallLikess[] = $obj;
                        }
                    }
                }

                $this->collWallLikess = $collWallLikess;
                $this->collWallLikessPartial = false;
            }
        }

        return $this->collWallLikess;
    }

    /**
     * Sets a collection of WallLikes objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $wallLikess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Wall The current object (for fluent API support)
     */
    public function setWallLikess(PropelCollection $wallLikess, PropelPDO $con = null)
    {
        $wallLikessToDelete = $this->getWallLikess(new Criteria(), $con)->diff($wallLikess);


        $this->wallLikessScheduledForDeletion = $wallLikessToDelete;

        foreach ($wallLikessToDelete as $wallLikesRemoved) {
            $wallLikesRemoved->setWall(null);
        }

        $this->collWallLikess = null;
        foreach ($wallLikess as $wallLikes) {
            $this->addWallLikes($wallLikes);
        }

        $this->collWallLikess = $wallLikess;
        $this->collWallLikessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related WallLikes objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related WallLikes objects.
     * @throws PropelException
     */
    public function countWallLikess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWallLikessPartial && !$this->isNew();
        if (null === $this->collWallLikess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWallLikess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWallLikess());
            }
            $query = WallLikesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByWall($this)
                ->count($con);
        }

        return count($this->collWallLikess);
    }

    /**
     * Method called to associate a WallLikes object to this object
     * through the WallLikes foreign key attribute.
     *
     * @param    WallLikes $l WallLikes
     * @return Wall The current object (for fluent API support)
     */
    public function addWallLikes(WallLikes $l)
    {
        if ($this->collWallLikess === null) {
            $this->initWallLikess();
            $this->collWallLikessPartial = true;
        }

        if (!in_array($l, $this->collWallLikess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWallLikes($l);

            if ($this->wallLikessScheduledForDeletion and $this->wallLikessScheduledForDeletion->contains($l)) {
                $this->wallLikessScheduledForDeletion->remove($this->wallLikessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	WallLikes $wallLikes The wallLikes object to add.
     */
    protected function doAddWallLikes($wallLikes)
    {
        $this->collWallLikess[]= $wallLikes;
        $wallLikes->setWall($this);
    }

    /**
     * @param	WallLikes $wallLikes The wallLikes object to remove.
     * @return Wall The current object (for fluent API support)
     */
    public function removeWallLikes($wallLikes)
    {
        if ($this->getWallLikess()->contains($wallLikes)) {
            $this->collWallLikess->remove($this->collWallLikess->search($wallLikes));
            if (null === $this->wallLikessScheduledForDeletion) {
                $this->wallLikessScheduledForDeletion = clone $this->collWallLikess;
                $this->wallLikessScheduledForDeletion->clear();
            }
            $this->wallLikessScheduledForDeletion[]= clone $wallLikes;
            $wallLikes->setWall(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Wall is new, it will return
     * an empty collection; or if this Wall has previously
     * been saved, it will retrieve related WallLikess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Wall.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|WallLikes[] List of WallLikes objects
     */
    public function getWallLikessJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WallLikesQuery::create(null, $criteria);
        $query->joinWith('Customers', $join_behavior);

        return $this->getWallLikess($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->parent_id = null;
        $this->customers_id = null;
        $this->messate = null;
        $this->status = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collWallsRelatedById) {
                foreach ($this->collWallsRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWallLikess) {
                foreach ($this->collWallLikess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aWallRelatedByParentId instanceof Persistent) {
              $this->aWallRelatedByParentId->clearAllReferences($deep);
            }
            if ($this->aCustomers instanceof Persistent) {
              $this->aCustomers->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collWallsRelatedById instanceof PropelCollection) {
            $this->collWallsRelatedById->clearIterator();
        }
        $this->collWallsRelatedById = null;
        if ($this->collWallLikess instanceof PropelCollection) {
            $this->collWallLikess->clearIterator();
        }
        $this->collWallLikess = null;
        $this->aWallRelatedByParentId = null;
        $this->aCustomers = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(WallPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     Wall The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = WallPeer::UPDATED_AT;

        return $this;
    }

}

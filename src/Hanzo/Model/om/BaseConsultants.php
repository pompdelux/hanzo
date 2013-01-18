<?php

namespace Hanzo\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Hanzo\Model\Consultants;
use Hanzo\Model\ConsultantsPeer;
use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Events;
use Hanzo\Model\EventsQuery;

abstract class BaseConsultants extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\ConsultantsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ConsultantsPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the initials field.
     * @var        string
     */
    protected $initials;

    /**
     * The value for the info field.
     * @var        string
     */
    protected $info;

    /**
     * The value for the event_notes field.
     * @var        string
     */
    protected $event_notes;

    /**
     * The value for the hide_info field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $hide_info;

    /**
     * The value for the max_notified field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $max_notified;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * @var        Customers
     */
    protected $aCustomers;

    /**
     * @var        PropelObjectCollection|Events[] Collection to store aggregation of Events objects.
     */
    protected $collEventss;
    protected $collEventssPartial;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $eventssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->hide_info = false;
        $this->max_notified = false;
    }

    /**
     * Initializes internal state of BaseConsultants object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [initials] column value.
     *
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * Get the [info] column value.
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Get the [event_notes] column value.
     *
     * @return string
     */
    public function getEventNotes()
    {
        return $this->event_notes;
    }

    /**
     * Get the [hide_info] column value.
     *
     * @return boolean
     */
    public function getHideInfo()
    {
        return $this->hide_info;
    }

    /**
     * Get the [max_notified] column value.
     *
     * @return boolean
     */
    public function getMaxNotified()
    {
        return $this->max_notified;
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
     * Set the value of [initials] column.
     *
     * @param string $v new value
     * @return Consultants The current object (for fluent API support)
     */
    public function setInitials($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->initials !== $v) {
            $this->initials = $v;
            $this->modifiedColumns[] = ConsultantsPeer::INITIALS;
        }


        return $this;
    } // setInitials()

    /**
     * Set the value of [info] column.
     *
     * @param string $v new value
     * @return Consultants The current object (for fluent API support)
     */
    public function setInfo($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->info !== $v) {
            $this->info = $v;
            $this->modifiedColumns[] = ConsultantsPeer::INFO;
        }


        return $this;
    } // setInfo()

    /**
     * Set the value of [event_notes] column.
     *
     * @param string $v new value
     * @return Consultants The current object (for fluent API support)
     */
    public function setEventNotes($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->event_notes !== $v) {
            $this->event_notes = $v;
            $this->modifiedColumns[] = ConsultantsPeer::EVENT_NOTES;
        }


        return $this;
    } // setEventNotes()

    /**
     * Sets the value of the [hide_info] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Consultants The current object (for fluent API support)
     */
    public function setHideInfo($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->hide_info !== $v) {
            $this->hide_info = $v;
            $this->modifiedColumns[] = ConsultantsPeer::HIDE_INFO;
        }


        return $this;
    } // setHideInfo()

    /**
     * Sets the value of the [max_notified] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Consultants The current object (for fluent API support)
     */
    public function setMaxNotified($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->max_notified !== $v) {
            $this->max_notified = $v;
            $this->modifiedColumns[] = ConsultantsPeer::MAX_NOTIFIED;
        }


        return $this;
    } // setMaxNotified()

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Consultants The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ConsultantsPeer::ID;
        }

        if ($this->aCustomers !== null && $this->aCustomers->getId() !== $v) {
            $this->aCustomers = null;
        }


        return $this;
    } // setId()

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
            if ($this->hide_info !== false) {
                return false;
            }

            if ($this->max_notified !== false) {
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
     * @param int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->initials = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
            $this->info = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->event_notes = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->hide_info = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
            $this->max_notified = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 6; // 6 = ConsultantsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Consultants object", $e);
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

        if ($this->aCustomers !== null && $this->id !== $this->aCustomers->getId()) {
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
            $con = Propel::getConnection(ConsultantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ConsultantsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCustomers = null;
            $this->collEventss = null;

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
            $con = Propel::getConnection(ConsultantsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ConsultantsQuery::create()
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
            $con = Propel::getConnection(ConsultantsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ConsultantsPeer::addInstanceToPool($this);
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
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

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

            if ($this->eventssScheduledForDeletion !== null) {
                if (!$this->eventssScheduledForDeletion->isEmpty()) {
                    EventsQuery::create()
                        ->filterByPrimaryKeys($this->eventssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->eventssScheduledForDeletion = null;
                }
            }

            if ($this->collEventss !== null) {
                foreach ($this->collEventss as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ConsultantsPeer::INITIALS)) {
            $modifiedColumns[':p' . $index++]  = '`INITIALS`';
        }
        if ($this->isColumnModified(ConsultantsPeer::INFO)) {
            $modifiedColumns[':p' . $index++]  = '`INFO`';
        }
        if ($this->isColumnModified(ConsultantsPeer::EVENT_NOTES)) {
            $modifiedColumns[':p' . $index++]  = '`EVENT_NOTES`';
        }
        if ($this->isColumnModified(ConsultantsPeer::HIDE_INFO)) {
            $modifiedColumns[':p' . $index++]  = '`HIDE_INFO`';
        }
        if ($this->isColumnModified(ConsultantsPeer::MAX_NOTIFIED)) {
            $modifiedColumns[':p' . $index++]  = '`MAX_NOTIFIED`';
        }
        if ($this->isColumnModified(ConsultantsPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`ID`';
        }

        $sql = sprintf(
            'INSERT INTO `consultants` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`INITIALS`':
                        $stmt->bindValue($identifier, $this->initials, PDO::PARAM_STR);
                        break;
                    case '`INFO`':
                        $stmt->bindValue($identifier, $this->info, PDO::PARAM_STR);
                        break;
                    case '`EVENT_NOTES`':
                        $stmt->bindValue($identifier, $this->event_notes, PDO::PARAM_STR);
                        break;
                    case '`HIDE_INFO`':
                        $stmt->bindValue($identifier, (int) $this->hide_info, PDO::PARAM_INT);
                        break;
                    case '`MAX_NOTIFIED`':
                        $stmt->bindValue($identifier, (int) $this->max_notified, PDO::PARAM_INT);
                        break;
                    case '`ID`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

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
        } else {
            $this->validationFailures = $res;

            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCustomers !== null) {
                if (!$this->aCustomers->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCustomers->getValidationFailures());
                }
            }


            if (($retval = ConsultantsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collEventss !== null) {
                    foreach ($this->collEventss as $referrerFK) {
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
        $pos = ConsultantsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getInitials();
                break;
            case 1:
                return $this->getInfo();
                break;
            case 2:
                return $this->getEventNotes();
                break;
            case 3:
                return $this->getHideInfo();
                break;
            case 4:
                return $this->getMaxNotified();
                break;
            case 5:
                return $this->getId();
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
        if (isset($alreadyDumpedObjects['Consultants'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Consultants'][$this->getPrimaryKey()] = true;
        $keys = ConsultantsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getInitials(),
            $keys[1] => $this->getInfo(),
            $keys[2] => $this->getEventNotes(),
            $keys[3] => $this->getHideInfo(),
            $keys[4] => $this->getMaxNotified(),
            $keys[5] => $this->getId(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aCustomers) {
                $result['Customers'] = $this->aCustomers->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collEventss) {
                $result['Eventss'] = $this->collEventss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ConsultantsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setInitials($value);
                break;
            case 1:
                $this->setInfo($value);
                break;
            case 2:
                $this->setEventNotes($value);
                break;
            case 3:
                $this->setHideInfo($value);
                break;
            case 4:
                $this->setMaxNotified($value);
                break;
            case 5:
                $this->setId($value);
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
        $keys = ConsultantsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setInitials($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setInfo($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setEventNotes($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setHideInfo($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setMaxNotified($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setId($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ConsultantsPeer::DATABASE_NAME);

        if ($this->isColumnModified(ConsultantsPeer::INITIALS)) $criteria->add(ConsultantsPeer::INITIALS, $this->initials);
        if ($this->isColumnModified(ConsultantsPeer::INFO)) $criteria->add(ConsultantsPeer::INFO, $this->info);
        if ($this->isColumnModified(ConsultantsPeer::EVENT_NOTES)) $criteria->add(ConsultantsPeer::EVENT_NOTES, $this->event_notes);
        if ($this->isColumnModified(ConsultantsPeer::HIDE_INFO)) $criteria->add(ConsultantsPeer::HIDE_INFO, $this->hide_info);
        if ($this->isColumnModified(ConsultantsPeer::MAX_NOTIFIED)) $criteria->add(ConsultantsPeer::MAX_NOTIFIED, $this->max_notified);
        if ($this->isColumnModified(ConsultantsPeer::ID)) $criteria->add(ConsultantsPeer::ID, $this->id);

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
        $criteria = new Criteria(ConsultantsPeer::DATABASE_NAME);
        $criteria->add(ConsultantsPeer::ID, $this->id);

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
     * @param object $copyObj An object of Consultants (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setInitials($this->getInitials());
        $copyObj->setInfo($this->getInfo());
        $copyObj->setEventNotes($this->getEventNotes());
        $copyObj->setHideInfo($this->getHideInfo());
        $copyObj->setMaxNotified($this->getMaxNotified());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getEventss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addEvents($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getCustomers();
            if ($relObj) {
                $copyObj->setCustomers($relObj->copy($deepCopy));
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
     * @return Consultants Clone of current object.
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
     * @return ConsultantsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ConsultantsPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Customers object.
     *
     * @param             Customers $v
     * @return Consultants The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCustomers(Customers $v = null)
    {
        if ($v === null) {
            $this->setId(NULL);
        } else {
            $this->setId($v->getId());
        }

        $this->aCustomers = $v;

        // Add binding for other direction of this 1:1 relationship.
        if ($v !== null) {
            $v->setConsultants($this);
        }


        return $this;
    }


    /**
     * Get the associated Customers object
     *
     * @param PropelPDO $con Optional Connection object.
     * @return Customers The associated Customers object.
     * @throws PropelException
     */
    public function getCustomers(PropelPDO $con = null)
    {
        if ($this->aCustomers === null && ($this->id !== null)) {
            $this->aCustomers = CustomersQuery::create()->findPk($this->id, $con);
            // Because this foreign key represents a one-to-one relationship, we will create a bi-directional association.
            $this->aCustomers->setConsultants($this);
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
        if ('Events' == $relationName) {
            $this->initEventss();
        }
    }

    /**
     * Clears out the collEventss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addEventss()
     */
    public function clearEventss()
    {
        $this->collEventss = null; // important to set this to null since that means it is uninitialized
        $this->collEventssPartial = null;
    }

    /**
     * reset is the collEventss collection loaded partially
     *
     * @return void
     */
    public function resetPartialEventss($v = true)
    {
        $this->collEventssPartial = $v;
    }

    /**
     * Initializes the collEventss collection.
     *
     * By default this just sets the collEventss collection to an empty array (like clearcollEventss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initEventss($overrideExisting = true)
    {
        if (null !== $this->collEventss && !$overrideExisting) {
            return;
        }
        $this->collEventss = new PropelObjectCollection();
        $this->collEventss->setModel('Events');
    }

    /**
     * Gets an array of Events objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Consultants is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Events[] List of Events objects
     * @throws PropelException
     */
    public function getEventss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collEventssPartial && !$this->isNew();
        if (null === $this->collEventss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collEventss) {
                // return empty collection
                $this->initEventss();
            } else {
                $collEventss = EventsQuery::create(null, $criteria)
                    ->filterByConsultants($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collEventssPartial && count($collEventss)) {
                      $this->initEventss(false);

                      foreach($collEventss as $obj) {
                        if (false == $this->collEventss->contains($obj)) {
                          $this->collEventss->append($obj);
                        }
                      }

                      $this->collEventssPartial = true;
                    }

                    return $collEventss;
                }

                if($partial && $this->collEventss) {
                    foreach($this->collEventss as $obj) {
                        if($obj->isNew()) {
                            $collEventss[] = $obj;
                        }
                    }
                }

                $this->collEventss = $collEventss;
                $this->collEventssPartial = false;
            }
        }

        return $this->collEventss;
    }

    /**
     * Sets a collection of Events objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $eventss A Propel collection.
     * @param PropelPDO $con Optional connection object
     */
    public function setEventss(PropelCollection $eventss, PropelPDO $con = null)
    {
        $this->eventssScheduledForDeletion = $this->getEventss(new Criteria(), $con)->diff($eventss);

        foreach ($this->eventssScheduledForDeletion as $eventsRemoved) {
            $eventsRemoved->setConsultants(null);
        }

        $this->collEventss = null;
        foreach ($eventss as $events) {
            $this->addEvents($events);
        }

        $this->collEventss = $eventss;
        $this->collEventssPartial = false;
    }

    /**
     * Returns the number of related Events objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Events objects.
     * @throws PropelException
     */
    public function countEventss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collEventssPartial && !$this->isNew();
        if (null === $this->collEventss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collEventss) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getEventss());
                }
                $query = EventsQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByConsultants($this)
                    ->count($con);
            }
        } else {
            return count($this->collEventss);
        }
    }

    /**
     * Method called to associate a Events object to this object
     * through the Events foreign key attribute.
     *
     * @param    Events $l Events
     * @return Consultants The current object (for fluent API support)
     */
    public function addEvents(Events $l)
    {
        if ($this->collEventss === null) {
            $this->initEventss();
            $this->collEventssPartial = true;
        }
        if (!$this->collEventss->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddEvents($l);
        }

        return $this;
    }

    /**
     * @param	Events $events The events object to add.
     */
    protected function doAddEvents($events)
    {
        $this->collEventss[]= $events;
        $events->setConsultants($this);
    }

    /**
     * @param	Events $events The events object to remove.
     */
    public function removeEvents($events)
    {
        if ($this->getEventss()->contains($events)) {
            $this->collEventss->remove($this->collEventss->search($events));
            if (null === $this->eventssScheduledForDeletion) {
                $this->eventssScheduledForDeletion = clone $this->collEventss;
                $this->eventssScheduledForDeletion->clear();
            }
            $this->eventssScheduledForDeletion[]= $events;
            $events->setConsultants(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Consultants is new, it will return
     * an empty collection; or if this Consultants has previously
     * been saved, it will retrieve related Eventss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Consultants.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Events[] List of Events objects
     */
    public function getEventssJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = EventsQuery::create(null, $criteria);
        $query->joinWith('Customers', $join_behavior);

        return $this->getEventss($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->initials = null;
        $this->info = null;
        $this->event_notes = null;
        $this->hide_info = null;
        $this->max_notified = null;
        $this->id = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
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
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collEventss) {
                foreach ($this->collEventss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collEventss instanceof PropelCollection) {
            $this->collEventss->clearIterator();
        }
        $this->collEventss = null;
        $this->aCustomers = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ConsultantsPeer::DEFAULT_STRING_FORMAT);
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

}

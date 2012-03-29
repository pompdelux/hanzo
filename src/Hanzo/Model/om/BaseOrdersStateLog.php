<?php

namespace Hanzo\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \DateTimeZone;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersStateLogPeer;
use Hanzo\Model\OrdersStateLogQuery;
use Hanzo\Model\OrdersStateLogVersion;
use Hanzo\Model\OrdersStateLogVersionQuery;
use Hanzo\Model\OrdersVersionQuery;

/**
 * Base class that represents a row from the 'orders_state_log' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseOrdersStateLog extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\OrdersStateLogPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        OrdersStateLogPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the orders_id field.
	 * @var        int
	 */
	protected $orders_id;

	/**
	 * The value for the state field.
	 * @var        int
	 */
	protected $state;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the message field.
	 * @var        string
	 */
	protected $message;

	/**
	 * The value for the version field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $version;

	/**
	 * @var        Orders
	 */
	protected $aOrders;

	/**
	 * @var        array OrdersStateLogVersion[] Collection to store aggregation of OrdersStateLogVersion objects.
	 */
	protected $collOrdersStateLogVersions;

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
	 * @var		array
	 */
	protected $ordersStateLogVersionsScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->version = 0;
	}

	/**
	 * Initializes internal state of BaseOrdersStateLog object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [orders_id] column value.
	 * 
	 * @return     int
	 */
	public function getOrdersId()
	{
		return $this->orders_id;
	}

	/**
	 * Get the [state] column value.
	 * 
	 * @return     int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [message] column value.
	 * 
	 * @return     string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Get the [version] column value.
	 * 
	 * @return     int
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Set the value of [orders_id] column.
	 * 
	 * @param      int $v new value
	 * @return     OrdersStateLog The current object (for fluent API support)
	 */
	public function setOrdersId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->orders_id !== $v) {
			$this->orders_id = $v;
			$this->modifiedColumns[] = OrdersStateLogPeer::ORDERS_ID;
		}

		if ($this->aOrders !== null && $this->aOrders->getId() !== $v) {
			$this->aOrders = null;
		}

		return $this;
	} // setOrdersId()

	/**
	 * Set the value of [state] column.
	 * 
	 * @param      int $v new value
	 * @return     OrdersStateLog The current object (for fluent API support)
	 */
	public function setState($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->state !== $v) {
			$this->state = $v;
			$this->modifiedColumns[] = OrdersStateLogPeer::STATE;
		}

		return $this;
	} // setState()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     OrdersStateLog The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = OrdersStateLogPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     OrdersStateLog The current object (for fluent API support)
	 */
	public function setMessage($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->message !== $v) {
			$this->message = $v;
			$this->modifiedColumns[] = OrdersStateLogPeer::MESSAGE;
		}

		return $this;
	} // setMessage()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      int $v new value
	 * @return     OrdersStateLog The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = OrdersStateLogPeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->version !== 0) {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
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
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->orders_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->state = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->created_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->message = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->version = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 5; // 5 = OrdersStateLogPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating OrdersStateLog object", $e);
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
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aOrders !== null && $this->orders_id !== $this->aOrders->getId()) {
			$this->aOrders = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
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
			$con = Propel::getConnection(OrdersStateLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = OrdersStateLogPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aOrders = null;
			$this->collOrdersStateLogVersions = null;

		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(OrdersStateLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = OrdersStateLogQuery::create()
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
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(OrdersStateLogPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			// versionable behavior
			if ($this->isVersioningNecessary()) {
				$this->setVersion($this->isNew() ? 1 : $this->getLastVersionNumber($con) + 1);
				$createVersion = true; // for postSave hook
			}
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
				// versionable behavior
				if (isset($createVersion)) {
					$this->addVersion($con);
				}
				OrdersStateLogPeer::addInstanceToPool($this);
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
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
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

			if ($this->aOrders !== null) {
				if ($this->aOrders->isModified() || $this->aOrders->isNew()) {
					$affectedRows += $this->aOrders->save($con);
				}
				$this->setOrders($this->aOrders);
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

			if ($this->ordersStateLogVersionsScheduledForDeletion !== null) {
				if (!$this->ordersStateLogVersionsScheduledForDeletion->isEmpty()) {
					OrdersStateLogVersionQuery::create()
						->filterByPrimaryKeys($this->ordersStateLogVersionsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ordersStateLogVersionsScheduledForDeletion = null;
				}
			}

			if ($this->collOrdersStateLogVersions !== null) {
				foreach ($this->collOrdersStateLogVersions as $referrerFK) {
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
	 * @param      PropelPDO $con
	 *
	 * @throws     PropelException
	 * @see        doSave()
	 */
	protected function doInsert(PropelPDO $con)
	{
		$modifiedColumns = array();
		$index = 0;


		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(OrdersStateLogPeer::ORDERS_ID)) {
			$modifiedColumns[':p' . $index++]  = '`ORDERS_ID`';
		}
		if ($this->isColumnModified(OrdersStateLogPeer::STATE)) {
			$modifiedColumns[':p' . $index++]  = '`STATE`';
		}
		if ($this->isColumnModified(OrdersStateLogPeer::CREATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`CREATED_AT`';
		}
		if ($this->isColumnModified(OrdersStateLogPeer::MESSAGE)) {
			$modifiedColumns[':p' . $index++]  = '`MESSAGE`';
		}
		if ($this->isColumnModified(OrdersStateLogPeer::VERSION)) {
			$modifiedColumns[':p' . $index++]  = '`VERSION`';
		}

		$sql = sprintf(
			'INSERT INTO `orders_state_log` (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case '`ORDERS_ID`':
						$stmt->bindValue($identifier, $this->orders_id, PDO::PARAM_INT);
						break;
					case '`STATE`':
						$stmt->bindValue($identifier, $this->state, PDO::PARAM_INT);
						break;
					case '`CREATED_AT`':
						$stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
						break;
					case '`MESSAGE`':
						$stmt->bindValue($identifier, $this->message, PDO::PARAM_STR);
						break;
					case '`VERSION`':
						$stmt->bindValue($identifier, $this->version, PDO::PARAM_INT);
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
	 * @param      PropelPDO $con
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
	 * @return     array ValidationFailed[]
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
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
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
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
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

			if ($this->aOrders !== null) {
				if (!$this->aOrders->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aOrders->getValidationFailures());
				}
			}


			if (($retval = OrdersStateLogPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collOrdersStateLogVersions !== null) {
					foreach ($this->collOrdersStateLogVersions as $referrerFK) {
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
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = OrdersStateLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getOrdersId();
				break;
			case 1:
				return $this->getState();
				break;
			case 2:
				return $this->getCreatedAt();
				break;
			case 3:
				return $this->getMessage();
				break;
			case 4:
				return $this->getVersion();
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
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		if (isset($alreadyDumpedObjects['OrdersStateLog'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['OrdersStateLog'][serialize($this->getPrimaryKey())] = true;
		$keys = OrdersStateLogPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getOrdersId(),
			$keys[1] => $this->getState(),
			$keys[2] => $this->getCreatedAt(),
			$keys[3] => $this->getMessage(),
			$keys[4] => $this->getVersion(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aOrders) {
				$result['Orders'] = $this->aOrders->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collOrdersStateLogVersions) {
				$result['OrdersStateLogVersions'] = $this->collOrdersStateLogVersions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
		}
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = OrdersStateLogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setOrdersId($value);
				break;
			case 1:
				$this->setState($value);
				break;
			case 2:
				$this->setCreatedAt($value);
				break;
			case 3:
				$this->setMessage($value);
				break;
			case 4:
				$this->setVersion($value);
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
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = OrdersStateLogPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setOrdersId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setState($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMessage($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setVersion($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(OrdersStateLogPeer::DATABASE_NAME);

		if ($this->isColumnModified(OrdersStateLogPeer::ORDERS_ID)) $criteria->add(OrdersStateLogPeer::ORDERS_ID, $this->orders_id);
		if ($this->isColumnModified(OrdersStateLogPeer::STATE)) $criteria->add(OrdersStateLogPeer::STATE, $this->state);
		if ($this->isColumnModified(OrdersStateLogPeer::CREATED_AT)) $criteria->add(OrdersStateLogPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(OrdersStateLogPeer::MESSAGE)) $criteria->add(OrdersStateLogPeer::MESSAGE, $this->message);
		if ($this->isColumnModified(OrdersStateLogPeer::VERSION)) $criteria->add(OrdersStateLogPeer::VERSION, $this->version);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(OrdersStateLogPeer::DATABASE_NAME);
		$criteria->add(OrdersStateLogPeer::ORDERS_ID, $this->orders_id);
		$criteria->add(OrdersStateLogPeer::STATE, $this->state);
		$criteria->add(OrdersStateLogPeer::CREATED_AT, $this->created_at);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();
		$pks[0] = $this->getOrdersId();
		$pks[1] = $this->getState();
		$pks[2] = $this->getCreatedAt();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{
		$this->setOrdersId($keys[0]);
		$this->setState($keys[1]);
		$this->setCreatedAt($keys[2]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getOrdersId()) && (null === $this->getState()) && (null === $this->getCreatedAt());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of OrdersStateLog (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setOrdersId($this->getOrdersId());
		$copyObj->setState($this->getState());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setMessage($this->getMessage());
		$copyObj->setVersion($this->getVersion());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getOrdersStateLogVersions() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersStateLogVersion($relObj->copy($deepCopy));
				}
			}

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
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
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     OrdersStateLog Clone of current object.
	 * @throws     PropelException
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
	 * @return     OrdersStateLogPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new OrdersStateLogPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Orders object.
	 *
	 * @param      Orders $v
	 * @return     OrdersStateLog The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setOrders(Orders $v = null)
	{
		if ($v === null) {
			$this->setOrdersId(NULL);
		} else {
			$this->setOrdersId($v->getId());
		}

		$this->aOrders = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Orders object, it will not be re-added.
		if ($v !== null) {
			$v->addOrdersStateLog($this);
		}

		return $this;
	}


	/**
	 * Get the associated Orders object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Orders The associated Orders object.
	 * @throws     PropelException
	 */
	public function getOrders(PropelPDO $con = null)
	{
		if ($this->aOrders === null && ($this->orders_id !== null)) {
			$this->aOrders = OrdersQuery::create()->findPk($this->orders_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aOrders->addOrdersStateLogs($this);
			 */
		}
		return $this->aOrders;
	}


	/**
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('OrdersStateLogVersion' == $relationName) {
			return $this->initOrdersStateLogVersions();
		}
	}

	/**
	 * Clears out the collOrdersStateLogVersions collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrdersStateLogVersions()
	 */
	public function clearOrdersStateLogVersions()
	{
		$this->collOrdersStateLogVersions = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrdersStateLogVersions collection.
	 *
	 * By default this just sets the collOrdersStateLogVersions collection to an empty array (like clearcollOrdersStateLogVersions());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrdersStateLogVersions($overrideExisting = true)
	{
		if (null !== $this->collOrdersStateLogVersions && !$overrideExisting) {
			return;
		}
		$this->collOrdersStateLogVersions = new PropelObjectCollection();
		$this->collOrdersStateLogVersions->setModel('OrdersStateLogVersion');
	}

	/**
	 * Gets an array of OrdersStateLogVersion objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this OrdersStateLog is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array OrdersStateLogVersion[] List of OrdersStateLogVersion objects
	 * @throws     PropelException
	 */
	public function getOrdersStateLogVersions($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrdersStateLogVersions || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersStateLogVersions) {
				// return empty collection
				$this->initOrdersStateLogVersions();
			} else {
				$collOrdersStateLogVersions = OrdersStateLogVersionQuery::create(null, $criteria)
					->filterByOrdersStateLog($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrdersStateLogVersions;
				}
				$this->collOrdersStateLogVersions = $collOrdersStateLogVersions;
			}
		}
		return $this->collOrdersStateLogVersions;
	}

	/**
	 * Sets a collection of OrdersStateLogVersion objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $ordersStateLogVersions A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrdersStateLogVersions(PropelCollection $ordersStateLogVersions, PropelPDO $con = null)
	{
		$this->ordersStateLogVersionsScheduledForDeletion = $this->getOrdersStateLogVersions(new Criteria(), $con)->diff($ordersStateLogVersions);

		foreach ($ordersStateLogVersions as $ordersStateLogVersion) {
			// Fix issue with collection modified by reference
			if ($ordersStateLogVersion->isNew()) {
				$ordersStateLogVersion->setOrdersStateLog($this);
			}
			$this->addOrdersStateLogVersion($ordersStateLogVersion);
		}

		$this->collOrdersStateLogVersions = $ordersStateLogVersions;
	}

	/**
	 * Returns the number of related OrdersStateLogVersion objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related OrdersStateLogVersion objects.
	 * @throws     PropelException
	 */
	public function countOrdersStateLogVersions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrdersStateLogVersions || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersStateLogVersions) {
				return 0;
			} else {
				$query = OrdersStateLogVersionQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByOrdersStateLog($this)
					->count($con);
			}
		} else {
			return count($this->collOrdersStateLogVersions);
		}
	}

	/**
	 * Method called to associate a OrdersStateLogVersion object to this object
	 * through the OrdersStateLogVersion foreign key attribute.
	 *
	 * @param      OrdersStateLogVersion $l OrdersStateLogVersion
	 * @return     OrdersStateLog The current object (for fluent API support)
	 */
	public function addOrdersStateLogVersion(OrdersStateLogVersion $l)
	{
		if ($this->collOrdersStateLogVersions === null) {
			$this->initOrdersStateLogVersions();
		}
		if (!$this->collOrdersStateLogVersions->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersStateLogVersion($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersStateLogVersion $ordersStateLogVersion The ordersStateLogVersion object to add.
	 */
	protected function doAddOrdersStateLogVersion($ordersStateLogVersion)
	{
		$this->collOrdersStateLogVersions[]= $ordersStateLogVersion;
		$ordersStateLogVersion->setOrdersStateLog($this);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->orders_id = null;
		$this->state = null;
		$this->created_at = null;
		$this->message = null;
		$this->version = null;
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
	 * @param      boolean $deep Whether to also clear the references on all referrer objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
			if ($this->collOrdersStateLogVersions) {
				foreach ($this->collOrdersStateLogVersions as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collOrdersStateLogVersions instanceof PropelCollection) {
			$this->collOrdersStateLogVersions->clearIterator();
		}
		$this->collOrdersStateLogVersions = null;
		$this->aOrders = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(OrdersStateLogPeer::DEFAULT_STRING_FORMAT);
	}

	// versionable behavior
	
	/**
	 * Checks whether the current state must be recorded as a version
	 *
	 * @return  boolean
	 */
	public function isVersioningNecessary($con = null)
	{
		if ($this->alreadyInSave) {
			return false;
		}
		if (OrdersStateLogPeer::isVersioningEnabled() && ($this->isNew() || $this->isModified())) {
			return true;
		}
		if ($this->getOrders($con)->isVersioningNecessary($con)) {
			return true;
		}
	
		return false;
	}
	
	/**
	 * Creates a version of the current object and saves it.
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  OrdersStateLogVersion A version object
	 */
	public function addVersion($con = null)
	{
		$version = new OrdersStateLogVersion();
		$version->setOrdersId($this->getOrdersId());
		$version->setState($this->getState());
		$version->setCreatedAt($this->getCreatedAt());
		$version->setMessage($this->getMessage());
		$version->setVersion($this->getVersion());
		$version->setOrdersStateLog($this);
		if (($related = $this->getOrders($con)) && $related->getVersion()) {
			$version->setOrdersIdVersion($related->getVersion());
		}
		$version->save($con);
	
		return $version;
	}
	
	/**
	 * Sets the properties of the curent object to the value they had at a specific version
	 *
	 * @param   integer $versionNumber The version number to read
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  OrdersStateLog The current object (for fluent API support)
	 */
	public function toVersion($versionNumber, $con = null)
	{
		$version = $this->getOneVersion($versionNumber, $con);
		if (!$version) {
			throw new PropelException(sprintf('No OrdersStateLog object found with version %d', $version));
		}
		$this->populateFromVersion($version, $con);
	
		return $this;
	}
	
	/**
	 * Sets the properties of the curent object to the value they had at a specific version
	 *
	 * @param   OrdersStateLogVersion $version The version object to use
	 * @param   PropelPDO $con the connection to use
	 * @param   array $loadedObjects objects thats been loaded in a chain of populateFromVersion calls on referrer or fk objects.
	 *
	 * @return  OrdersStateLog The current object (for fluent API support)
	 */
	public function populateFromVersion($version, $con = null, &$loadedObjects = array())
	{
	
		$loadedObjects['OrdersStateLog'][$version->getOrdersId()][$version->getVersion()] = $this;
		$this->setOrdersId($version->getOrdersId());
		$this->setState($version->getState());
		$this->setCreatedAt($version->getCreatedAt());
		$this->setMessage($version->getMessage());
		$this->setVersion($version->getVersion());
		if ($fkValue = $version->getOrdersId()) {
			if (isset($loadedObjects['Orders']) && isset($loadedObjects['Orders'][$fkValue]) && isset($loadedObjects['Orders'][$fkValue][$version->getOrdersIdVersion()])) {
				$related = $loadedObjects['Orders'][$fkValue][$version->getOrdersIdVersion()];
			} else {
				$related = new Orders();
				$relatedVersion = OrdersVersionQuery::create()
					->filterById($fkValue)
					->filterByVersion($version->getOrdersIdVersion())
					->findOne($con);
				$related->populateFromVersion($relatedVersion, $con, $loadedObjects);
				$related->setNew(false);
			}
			$this->setOrders($related);
		}
		return $this;
	}
	
	/**
	 * Gets the latest persisted version number for the current object
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  integer
	 */
	public function getLastVersionNumber($con = null)
	{
		$v = OrdersStateLogVersionQuery::create()
			->filterByOrdersStateLog($this)
			->orderByVersion('desc')
			->findOne($con);
		if (!$v) {
			return 0;
		}
		return $v->getVersion();
	}
	
	/**
	 * Checks whether the current object is the latest one
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  Boolean
	 */
	public function isLastVersion($con = null)
	{
		return $this->getLastVersionNumber($con) == $this->getVersion();
	}
	
	/**
	 * Retrieves a version object for this entity and a version number
	 *
	 * @param   integer $versionNumber The version number to read
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  OrdersStateLogVersion A version object
	 */
	public function getOneVersion($versionNumber, $con = null)
	{
		return OrdersStateLogVersionQuery::create()
			->filterByOrdersStateLog($this)
			->filterByVersion($versionNumber)
			->findOne($con);
	}
	
	/**
	 * Gets all the versions of this object, in incremental order
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  PropelObjectCollection A list of OrdersStateLogVersion objects
	 */
	public function getAllVersions($con = null)
	{
		$criteria = new Criteria();
		$criteria->addAscendingOrderByColumn(OrdersStateLogVersionPeer::VERSION);
		return $this->getOrdersStateLogVersions($criteria, $con);
	}
	
	/**
	 * Gets all the versions of this object, in incremental order.
	 * <code>
	 * print_r($book->compare(1, 2));
	 * => array(
	 *   '1' => array('Title' => 'Book title at version 1'),
	 *   '2' => array('Title' => 'Book title at version 2')
	 * );
	 * </code>
	 *
	 * @param   integer   $fromVersionNumber
	 * @param   integer   $toVersionNumber
	 * @param   string    $keys Main key used for the result diff (versions|columns)
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  array A list of differences
	 */
	public function compareVersions($fromVersionNumber, $toVersionNumber, $keys = 'columns', $con = null)
	{
		$fromVersion = $this->getOneVersion($fromVersionNumber, $con)->toArray();
		$toVersion = $this->getOneVersion($toVersionNumber, $con)->toArray();
		$ignoredColumns = array(
			'Version',
		);
		$diff = array();
		foreach ($fromVersion as $key => $value) {
			if (in_array($key, $ignoredColumns)) {
				continue;
			}
			if ($toVersion[$key] != $value) {
				switch ($keys) {
					case 'versions':
						$diff[$fromVersionNumber][$key] = $value;
						$diff[$toVersionNumber][$key] = $toVersion[$key];
						break;
					default:
						$diff[$key] = array(
							$fromVersionNumber => $value,
							$toVersionNumber => $toVersion[$key],
						);
						break;
				}
			}
		}
		return $diff;
	}

} // BaseOrdersStateLog

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
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributesPeer;
use Hanzo\Model\OrdersAttributesQuery;
use Hanzo\Model\OrdersAttributesVersion;
use Hanzo\Model\OrdersAttributesVersionQuery;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersVersionQuery;

/**
 * Base class that represents a row from the 'orders_attributes' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseOrdersAttributes extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\OrdersAttributesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        OrdersAttributesPeer
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
	 * The value for the ns field.
	 * @var        string
	 */
	protected $ns;

	/**
	 * The value for the c_key field.
	 * @var        string
	 */
	protected $c_key;

	/**
	 * The value for the c_value field.
	 * @var        string
	 */
	protected $c_value;

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
	 * @var        array OrdersAttributesVersion[] Collection to store aggregation of OrdersAttributesVersion objects.
	 */
	protected $collOrdersAttributesVersions;

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
	protected $ordersAttributesVersionsScheduledForDeletion = null;

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
	 * Initializes internal state of BaseOrdersAttributes object.
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
	 * Get the [ns] column value.
	 * 
	 * @return     string
	 */
	public function getNs()
	{
		return $this->ns;
	}

	/**
	 * Get the [c_key] column value.
	 * 
	 * @return     string
	 */
	public function getCKey()
	{
		return $this->c_key;
	}

	/**
	 * Get the [c_value] column value.
	 * 
	 * @return     string
	 */
	public function getCValue()
	{
		return $this->c_value;
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
	 * @return     OrdersAttributes The current object (for fluent API support)
	 */
	public function setOrdersId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->orders_id !== $v) {
			$this->orders_id = $v;
			$this->modifiedColumns[] = OrdersAttributesPeer::ORDERS_ID;
		}

		if ($this->aOrders !== null && $this->aOrders->getId() !== $v) {
			$this->aOrders = null;
		}

		return $this;
	} // setOrdersId()

	/**
	 * Set the value of [ns] column.
	 * 
	 * @param      string $v new value
	 * @return     OrdersAttributes The current object (for fluent API support)
	 */
	public function setNs($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ns !== $v) {
			$this->ns = $v;
			$this->modifiedColumns[] = OrdersAttributesPeer::NS;
		}

		return $this;
	} // setNs()

	/**
	 * Set the value of [c_key] column.
	 * 
	 * @param      string $v new value
	 * @return     OrdersAttributes The current object (for fluent API support)
	 */
	public function setCKey($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->c_key !== $v) {
			$this->c_key = $v;
			$this->modifiedColumns[] = OrdersAttributesPeer::C_KEY;
		}

		return $this;
	} // setCKey()

	/**
	 * Set the value of [c_value] column.
	 * 
	 * @param      string $v new value
	 * @return     OrdersAttributes The current object (for fluent API support)
	 */
	public function setCValue($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->c_value !== $v) {
			$this->c_value = $v;
			$this->modifiedColumns[] = OrdersAttributesPeer::C_VALUE;
		}

		return $this;
	} // setCValue()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      int $v new value
	 * @return     OrdersAttributes The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->version !== $v) {
			$this->version = $v;
			$this->modifiedColumns[] = OrdersAttributesPeer::VERSION;
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
			$this->ns = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->c_key = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->c_value = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->version = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 5; // 5 = OrdersAttributesPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating OrdersAttributes object", $e);
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
			$con = Propel::getConnection(OrdersAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = OrdersAttributesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aOrders = null;
			$this->collOrdersAttributesVersions = null;

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
			$con = Propel::getConnection(OrdersAttributesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = OrdersAttributesQuery::create()
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
			$con = Propel::getConnection(OrdersAttributesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				OrdersAttributesPeer::addInstanceToPool($this);
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

			if ($this->ordersAttributesVersionsScheduledForDeletion !== null) {
				if (!$this->ordersAttributesVersionsScheduledForDeletion->isEmpty()) {
					OrdersAttributesVersionQuery::create()
						->filterByPrimaryKeys($this->ordersAttributesVersionsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ordersAttributesVersionsScheduledForDeletion = null;
				}
			}

			if ($this->collOrdersAttributesVersions !== null) {
				foreach ($this->collOrdersAttributesVersions as $referrerFK) {
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
		if ($this->isColumnModified(OrdersAttributesPeer::ORDERS_ID)) {
			$modifiedColumns[':p' . $index++]  = '`ORDERS_ID`';
		}
		if ($this->isColumnModified(OrdersAttributesPeer::NS)) {
			$modifiedColumns[':p' . $index++]  = '`NS`';
		}
		if ($this->isColumnModified(OrdersAttributesPeer::C_KEY)) {
			$modifiedColumns[':p' . $index++]  = '`C_KEY`';
		}
		if ($this->isColumnModified(OrdersAttributesPeer::C_VALUE)) {
			$modifiedColumns[':p' . $index++]  = '`C_VALUE`';
		}
		if ($this->isColumnModified(OrdersAttributesPeer::VERSION)) {
			$modifiedColumns[':p' . $index++]  = '`VERSION`';
		}

		$sql = sprintf(
			'INSERT INTO `orders_attributes` (%s) VALUES (%s)',
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
					case '`NS`':
						$stmt->bindValue($identifier, $this->ns, PDO::PARAM_STR);
						break;
					case '`C_KEY`':
						$stmt->bindValue($identifier, $this->c_key, PDO::PARAM_STR);
						break;
					case '`C_VALUE`':
						$stmt->bindValue($identifier, $this->c_value, PDO::PARAM_STR);
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


			if (($retval = OrdersAttributesPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collOrdersAttributesVersions !== null) {
					foreach ($this->collOrdersAttributesVersions as $referrerFK) {
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
		$pos = OrdersAttributesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNs();
				break;
			case 2:
				return $this->getCKey();
				break;
			case 3:
				return $this->getCValue();
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
		if (isset($alreadyDumpedObjects['OrdersAttributes'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['OrdersAttributes'][serialize($this->getPrimaryKey())] = true;
		$keys = OrdersAttributesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getOrdersId(),
			$keys[1] => $this->getNs(),
			$keys[2] => $this->getCKey(),
			$keys[3] => $this->getCValue(),
			$keys[4] => $this->getVersion(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aOrders) {
				$result['Orders'] = $this->aOrders->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collOrdersAttributesVersions) {
				$result['OrdersAttributesVersions'] = $this->collOrdersAttributesVersions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = OrdersAttributesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNs($value);
				break;
			case 2:
				$this->setCKey($value);
				break;
			case 3:
				$this->setCValue($value);
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
		$keys = OrdersAttributesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setOrdersId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNs($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCKey($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCValue($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setVersion($arr[$keys[4]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(OrdersAttributesPeer::DATABASE_NAME);

		if ($this->isColumnModified(OrdersAttributesPeer::ORDERS_ID)) $criteria->add(OrdersAttributesPeer::ORDERS_ID, $this->orders_id);
		if ($this->isColumnModified(OrdersAttributesPeer::NS)) $criteria->add(OrdersAttributesPeer::NS, $this->ns);
		if ($this->isColumnModified(OrdersAttributesPeer::C_KEY)) $criteria->add(OrdersAttributesPeer::C_KEY, $this->c_key);
		if ($this->isColumnModified(OrdersAttributesPeer::C_VALUE)) $criteria->add(OrdersAttributesPeer::C_VALUE, $this->c_value);
		if ($this->isColumnModified(OrdersAttributesPeer::VERSION)) $criteria->add(OrdersAttributesPeer::VERSION, $this->version);

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
		$criteria = new Criteria(OrdersAttributesPeer::DATABASE_NAME);
		$criteria->add(OrdersAttributesPeer::ORDERS_ID, $this->orders_id);
		$criteria->add(OrdersAttributesPeer::NS, $this->ns);
		$criteria->add(OrdersAttributesPeer::C_KEY, $this->c_key);

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
		$pks[1] = $this->getNs();
		$pks[2] = $this->getCKey();

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
		$this->setNs($keys[1]);
		$this->setCKey($keys[2]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getOrdersId()) && (null === $this->getNs()) && (null === $this->getCKey());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of OrdersAttributes (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setOrdersId($this->getOrdersId());
		$copyObj->setNs($this->getNs());
		$copyObj->setCKey($this->getCKey());
		$copyObj->setCValue($this->getCValue());
		$copyObj->setVersion($this->getVersion());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getOrdersAttributesVersions() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersAttributesVersion($relObj->copy($deepCopy));
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
	 * @return     OrdersAttributes Clone of current object.
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
	 * @return     OrdersAttributesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new OrdersAttributesPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Orders object.
	 *
	 * @param      Orders $v
	 * @return     OrdersAttributes The current object (for fluent API support)
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
			$v->addOrdersAttributes($this);
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
				$this->aOrders->addOrdersAttributess($this);
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
		if ('OrdersAttributesVersion' == $relationName) {
			return $this->initOrdersAttributesVersions();
		}
	}

	/**
	 * Clears out the collOrdersAttributesVersions collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrdersAttributesVersions()
	 */
	public function clearOrdersAttributesVersions()
	{
		$this->collOrdersAttributesVersions = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrdersAttributesVersions collection.
	 *
	 * By default this just sets the collOrdersAttributesVersions collection to an empty array (like clearcollOrdersAttributesVersions());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrdersAttributesVersions($overrideExisting = true)
	{
		if (null !== $this->collOrdersAttributesVersions && !$overrideExisting) {
			return;
		}
		$this->collOrdersAttributesVersions = new PropelObjectCollection();
		$this->collOrdersAttributesVersions->setModel('OrdersAttributesVersion');
	}

	/**
	 * Gets an array of OrdersAttributesVersion objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this OrdersAttributes is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array OrdersAttributesVersion[] List of OrdersAttributesVersion objects
	 * @throws     PropelException
	 */
	public function getOrdersAttributesVersions($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrdersAttributesVersions || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersAttributesVersions) {
				// return empty collection
				$this->initOrdersAttributesVersions();
			} else {
				$collOrdersAttributesVersions = OrdersAttributesVersionQuery::create(null, $criteria)
					->filterByOrdersAttributes($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrdersAttributesVersions;
				}
				$this->collOrdersAttributesVersions = $collOrdersAttributesVersions;
			}
		}
		return $this->collOrdersAttributesVersions;
	}

	/**
	 * Sets a collection of OrdersAttributesVersion objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $ordersAttributesVersions A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrdersAttributesVersions(PropelCollection $ordersAttributesVersions, PropelPDO $con = null)
	{
		$this->ordersAttributesVersionsScheduledForDeletion = $this->getOrdersAttributesVersions(new Criteria(), $con)->diff($ordersAttributesVersions);

		foreach ($ordersAttributesVersions as $ordersAttributesVersion) {
			// Fix issue with collection modified by reference
			if ($ordersAttributesVersion->isNew()) {
				$ordersAttributesVersion->setOrdersAttributes($this);
			}
			$this->addOrdersAttributesVersion($ordersAttributesVersion);
		}

		$this->collOrdersAttributesVersions = $ordersAttributesVersions;
	}

	/**
	 * Returns the number of related OrdersAttributesVersion objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related OrdersAttributesVersion objects.
	 * @throws     PropelException
	 */
	public function countOrdersAttributesVersions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrdersAttributesVersions || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersAttributesVersions) {
				return 0;
			} else {
				$query = OrdersAttributesVersionQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByOrdersAttributes($this)
					->count($con);
			}
		} else {
			return count($this->collOrdersAttributesVersions);
		}
	}

	/**
	 * Method called to associate a OrdersAttributesVersion object to this object
	 * through the OrdersAttributesVersion foreign key attribute.
	 *
	 * @param      OrdersAttributesVersion $l OrdersAttributesVersion
	 * @return     OrdersAttributes The current object (for fluent API support)
	 */
	public function addOrdersAttributesVersion(OrdersAttributesVersion $l)
	{
		if ($this->collOrdersAttributesVersions === null) {
			$this->initOrdersAttributesVersions();
		}
		if (!$this->collOrdersAttributesVersions->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersAttributesVersion($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersAttributesVersion $ordersAttributesVersion The ordersAttributesVersion object to add.
	 */
	protected function doAddOrdersAttributesVersion($ordersAttributesVersion)
	{
		$this->collOrdersAttributesVersions[]= $ordersAttributesVersion;
		$ordersAttributesVersion->setOrdersAttributes($this);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->orders_id = null;
		$this->ns = null;
		$this->c_key = null;
		$this->c_value = null;
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
			if ($this->collOrdersAttributesVersions) {
				foreach ($this->collOrdersAttributesVersions as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collOrdersAttributesVersions instanceof PropelCollection) {
			$this->collOrdersAttributesVersions->clearIterator();
		}
		$this->collOrdersAttributesVersions = null;
		$this->aOrders = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(OrdersAttributesPeer::DEFAULT_STRING_FORMAT);
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
		if (OrdersAttributesPeer::isVersioningEnabled() && ($this->isNew() || $this->isModified())) {
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
	 * @return  OrdersAttributesVersion A version object
	 */
	public function addVersion($con = null)
	{
		$version = new OrdersAttributesVersion();
		$version->setOrdersId($this->getOrdersId());
		$version->setNs($this->getNs());
		$version->setCKey($this->getCKey());
		$version->setCValue($this->getCValue());
		$version->setVersion($this->getVersion());
		$version->setOrdersAttributes($this);
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
	 * @return  OrdersAttributes The current object (for fluent API support)
	 */
	public function toVersion($versionNumber, $con = null)
	{
		$version = $this->getOneVersion($versionNumber, $con);
		if (!$version) {
			throw new PropelException(sprintf('No OrdersAttributes object found with version %d', $version));
		}
		$this->populateFromVersion($version, $con);
	
		return $this;
	}
	
	/**
	 * Sets the properties of the curent object to the value they had at a specific version
	 *
	 * @param   OrdersAttributesVersion $version The version object to use
	 * @param   PropelPDO $con the connection to use
	 * @param   array $loadedObjects objects thats been loaded in a chain of populateFromVersion calls on referrer or fk objects.
	 *
	 * @return  OrdersAttributes The current object (for fluent API support)
	 */
	public function populateFromVersion($version, $con = null, &$loadedObjects = array())
	{
	
		$loadedObjects['OrdersAttributes'][$version->getOrdersId()][$version->getVersion()] = $this;
		$this->setOrdersId($version->getOrdersId());
		$this->setNs($version->getNs());
		$this->setCKey($version->getCKey());
		$this->setCValue($version->getCValue());
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
		$v = OrdersAttributesVersionQuery::create()
			->filterByOrdersAttributes($this)
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
	 * @return  OrdersAttributesVersion A version object
	 */
	public function getOneVersion($versionNumber, $con = null)
	{
		return OrdersAttributesVersionQuery::create()
			->filterByOrdersAttributes($this)
			->filterByVersion($versionNumber)
			->findOne($con);
	}
	
	/**
	 * Gets all the versions of this object, in incremental order
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  PropelObjectCollection A list of OrdersAttributesVersion objects
	 */
	public function getAllVersions($con = null)
	{
		$criteria = new Criteria();
		$criteria->addAscendingOrderByColumn(OrdersAttributesVersionPeer::VERSION);
		return $this->getOrdersAttributesVersions($criteria, $con);
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

} // BaseOrdersAttributes

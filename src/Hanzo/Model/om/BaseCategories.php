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
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesI18n;
use Hanzo\Model\CategoriesI18nQuery;
use Hanzo\Model\CategoriesPeer;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsToCategoriesQuery;

/**
 * Base class that represents a row from the 'categories' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCategories extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\CategoriesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CategoriesPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
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
	 * The value for the context field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $context;

	/**
	 * The value for the is_active field.
	 * Note: this column has a database default value of: true
	 * @var        boolean
	 */
	protected $is_active;

	/**
	 * @var        Categories
	 */
	protected $aCategoriesRelatedByParentId;

	/**
	 * @var        array Categories[] Collection to store aggregation of Categories objects.
	 */
	protected $collCategoriessRelatedById;

	/**
	 * @var        array ProductsToCategories[] Collection to store aggregation of ProductsToCategories objects.
	 */
	protected $collProductsToCategoriess;

	/**
	 * @var        array CategoriesI18n[] Collection to store aggregation of CategoriesI18n objects.
	 */
	protected $collCategoriesI18ns;

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

	// i18n behavior
	
	/**
	 * Current locale
	 * @var        string
	 */
	protected $currentLocale = 'en_EN';
	
	/**
	 * Current translation objects
	 * @var        array[CategoriesI18n]
	 */
	protected $currentTranslations;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $categoriessRelatedByIdScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $productsToCategoriessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $categoriesI18nsScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->context = '';
		$this->is_active = true;
	}

	/**
	 * Initializes internal state of BaseCategories object.
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
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [parent_id] column value.
	 * 
	 * @return     int
	 */
	public function getParentId()
	{
		return $this->parent_id;
	}

	/**
	 * Get the [context] column value.
	 * 
	 * @return     string
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Get the [is_active] column value.
	 * 
	 * @return     boolean
	 */
	public function getIsActive()
	{
		return $this->is_active;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     Categories The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CategoriesPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [parent_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Categories The current object (for fluent API support)
	 */
	public function setParentId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->parent_id !== $v) {
			$this->parent_id = $v;
			$this->modifiedColumns[] = CategoriesPeer::PARENT_ID;
		}

		if ($this->aCategoriesRelatedByParentId !== null && $this->aCategoriesRelatedByParentId->getId() !== $v) {
			$this->aCategoriesRelatedByParentId = null;
		}

		return $this;
	} // setParentId()

	/**
	 * Set the value of [context] column.
	 * 
	 * @param      string $v new value
	 * @return     Categories The current object (for fluent API support)
	 */
	public function setContext($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->context !== $v) {
			$this->context = $v;
			$this->modifiedColumns[] = CategoriesPeer::CONTEXT;
		}

		return $this;
	} // setContext()

	/**
	 * Sets the value of the [is_active] column.
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * 
	 * @param      boolean|integer|string $v The new value
	 * @return     Categories The current object (for fluent API support)
	 */
	public function setIsActive($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->is_active !== $v) {
			$this->is_active = $v;
			$this->modifiedColumns[] = CategoriesPeer::IS_ACTIVE;
		}

		return $this;
	} // setIsActive()

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
			if ($this->context !== '') {
				return false;
			}

			if ($this->is_active !== true) {
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

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->context = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->is_active = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 4; // 4 = CategoriesPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Categories object", $e);
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

		if ($this->aCategoriesRelatedByParentId !== null && $this->parent_id !== $this->aCategoriesRelatedByParentId->getId()) {
			$this->aCategoriesRelatedByParentId = null;
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
			$con = Propel::getConnection(CategoriesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CategoriesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCategoriesRelatedByParentId = null;
			$this->collCategoriessRelatedById = null;

			$this->collProductsToCategoriess = null;

			$this->collCategoriesI18ns = null;

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
			$con = Propel::getConnection(CategoriesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = CategoriesQuery::create()
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
			$con = Propel::getConnection(CategoriesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CategoriesPeer::addInstanceToPool($this);
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

			if ($this->aCategoriesRelatedByParentId !== null) {
				if ($this->aCategoriesRelatedByParentId->isModified() || $this->aCategoriesRelatedByParentId->isNew()) {
					$affectedRows += $this->aCategoriesRelatedByParentId->save($con);
				}
				$this->setCategoriesRelatedByParentId($this->aCategoriesRelatedByParentId);
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

			if ($this->categoriessRelatedByIdScheduledForDeletion !== null) {
				if (!$this->categoriessRelatedByIdScheduledForDeletion->isEmpty()) {
					CategoriesQuery::create()
						->filterByPrimaryKeys($this->categoriessRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->categoriessRelatedByIdScheduledForDeletion = null;
				}
			}

			if ($this->collCategoriessRelatedById !== null) {
				foreach ($this->collCategoriessRelatedById as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->productsToCategoriessScheduledForDeletion !== null) {
				if (!$this->productsToCategoriessScheduledForDeletion->isEmpty()) {
					ProductsToCategoriesQuery::create()
						->filterByPrimaryKeys($this->productsToCategoriessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->productsToCategoriessScheduledForDeletion = null;
				}
			}

			if ($this->collProductsToCategoriess !== null) {
				foreach ($this->collProductsToCategoriess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->categoriesI18nsScheduledForDeletion !== null) {
				if (!$this->categoriesI18nsScheduledForDeletion->isEmpty()) {
					CategoriesI18nQuery::create()
						->filterByPrimaryKeys($this->categoriesI18nsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->categoriesI18nsScheduledForDeletion = null;
				}
			}

			if ($this->collCategoriesI18ns !== null) {
				foreach ($this->collCategoriesI18ns as $referrerFK) {
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

		$this->modifiedColumns[] = CategoriesPeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . CategoriesPeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(CategoriesPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = '`ID`';
		}
		if ($this->isColumnModified(CategoriesPeer::PARENT_ID)) {
			$modifiedColumns[':p' . $index++]  = '`PARENT_ID`';
		}
		if ($this->isColumnModified(CategoriesPeer::CONTEXT)) {
			$modifiedColumns[':p' . $index++]  = '`CONTEXT`';
		}
		if ($this->isColumnModified(CategoriesPeer::IS_ACTIVE)) {
			$modifiedColumns[':p' . $index++]  = '`IS_ACTIVE`';
		}

		$sql = sprintf(
			'INSERT INTO `categories` (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case '`ID`':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
						break;
					case '`PARENT_ID`':
						$stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
						break;
					case '`CONTEXT`':
						$stmt->bindValue($identifier, $this->context, PDO::PARAM_STR);
						break;
					case '`IS_ACTIVE`':
						$stmt->bindValue($identifier, (int) $this->is_active, PDO::PARAM_INT);
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

			if ($this->aCategoriesRelatedByParentId !== null) {
				if (!$this->aCategoriesRelatedByParentId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCategoriesRelatedByParentId->getValidationFailures());
				}
			}


			if (($retval = CategoriesPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCategoriessRelatedById !== null) {
					foreach ($this->collCategoriessRelatedById as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collProductsToCategoriess !== null) {
					foreach ($this->collProductsToCategoriess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCategoriesI18ns !== null) {
					foreach ($this->collCategoriesI18ns as $referrerFK) {
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
		$pos = CategoriesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getId();
				break;
			case 1:
				return $this->getParentId();
				break;
			case 2:
				return $this->getContext();
				break;
			case 3:
				return $this->getIsActive();
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
		if (isset($alreadyDumpedObjects['Categories'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Categories'][$this->getPrimaryKey()] = true;
		$keys = CategoriesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getParentId(),
			$keys[2] => $this->getContext(),
			$keys[3] => $this->getIsActive(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCategoriesRelatedByParentId) {
				$result['CategoriesRelatedByParentId'] = $this->aCategoriesRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collCategoriessRelatedById) {
				$result['CategoriessRelatedById'] = $this->collCategoriessRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collProductsToCategoriess) {
				$result['ProductsToCategoriess'] = $this->collProductsToCategoriess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCategoriesI18ns) {
				$result['CategoriesI18ns'] = $this->collCategoriesI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = CategoriesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setId($value);
				break;
			case 1:
				$this->setParentId($value);
				break;
			case 2:
				$this->setContext($value);
				break;
			case 3:
				$this->setIsActive($value);
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
		$keys = CategoriesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setContext($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setIsActive($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CategoriesPeer::DATABASE_NAME);

		if ($this->isColumnModified(CategoriesPeer::ID)) $criteria->add(CategoriesPeer::ID, $this->id);
		if ($this->isColumnModified(CategoriesPeer::PARENT_ID)) $criteria->add(CategoriesPeer::PARENT_ID, $this->parent_id);
		if ($this->isColumnModified(CategoriesPeer::CONTEXT)) $criteria->add(CategoriesPeer::CONTEXT, $this->context);
		if ($this->isColumnModified(CategoriesPeer::IS_ACTIVE)) $criteria->add(CategoriesPeer::IS_ACTIVE, $this->is_active);

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
		$criteria = new Criteria(CategoriesPeer::DATABASE_NAME);
		$criteria->add(CategoriesPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
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
	 * @param      object $copyObj An object of Categories (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setParentId($this->getParentId());
		$copyObj->setContext($this->getContext());
		$copyObj->setIsActive($this->getIsActive());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getCategoriessRelatedById() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCategoriesRelatedById($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getProductsToCategoriess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addProductsToCategories($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCategoriesI18ns() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCategoriesI18n($relObj->copy($deepCopy));
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
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     Categories Clone of current object.
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
	 * @return     CategoriesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CategoriesPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Categories object.
	 *
	 * @param      Categories $v
	 * @return     Categories The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCategoriesRelatedByParentId(Categories $v = null)
	{
		if ($v === null) {
			$this->setParentId(NULL);
		} else {
			$this->setParentId($v->getId());
		}

		$this->aCategoriesRelatedByParentId = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Categories object, it will not be re-added.
		if ($v !== null) {
			$v->addCategoriesRelatedById($this);
		}

		return $this;
	}


	/**
	 * Get the associated Categories object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Categories The associated Categories object.
	 * @throws     PropelException
	 */
	public function getCategoriesRelatedByParentId(PropelPDO $con = null)
	{
		if ($this->aCategoriesRelatedByParentId === null && ($this->parent_id !== null)) {
			$this->aCategoriesRelatedByParentId = CategoriesQuery::create()->findPk($this->parent_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCategoriesRelatedByParentId->addCategoriessRelatedById($this);
			 */
		}
		return $this->aCategoriesRelatedByParentId;
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
		if ('CategoriesRelatedById' == $relationName) {
			return $this->initCategoriessRelatedById();
		}
		if ('ProductsToCategories' == $relationName) {
			return $this->initProductsToCategoriess();
		}
		if ('CategoriesI18n' == $relationName) {
			return $this->initCategoriesI18ns();
		}
	}

	/**
	 * Clears out the collCategoriessRelatedById collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCategoriessRelatedById()
	 */
	public function clearCategoriessRelatedById()
	{
		$this->collCategoriessRelatedById = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCategoriessRelatedById collection.
	 *
	 * By default this just sets the collCategoriessRelatedById collection to an empty array (like clearcollCategoriessRelatedById());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCategoriessRelatedById($overrideExisting = true)
	{
		if (null !== $this->collCategoriessRelatedById && !$overrideExisting) {
			return;
		}
		$this->collCategoriessRelatedById = new PropelObjectCollection();
		$this->collCategoriessRelatedById->setModel('Categories');
	}

	/**
	 * Gets an array of Categories objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Categories is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Categories[] List of Categories objects
	 * @throws     PropelException
	 */
	public function getCategoriessRelatedById($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCategoriessRelatedById || null !== $criteria) {
			if ($this->isNew() && null === $this->collCategoriessRelatedById) {
				// return empty collection
				$this->initCategoriessRelatedById();
			} else {
				$collCategoriessRelatedById = CategoriesQuery::create(null, $criteria)
					->filterByCategoriesRelatedByParentId($this)
					->find($con);
				if (null !== $criteria) {
					return $collCategoriessRelatedById;
				}
				$this->collCategoriessRelatedById = $collCategoriessRelatedById;
			}
		}
		return $this->collCategoriessRelatedById;
	}

	/**
	 * Sets a collection of CategoriesRelatedById objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $categoriessRelatedById A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCategoriessRelatedById(PropelCollection $categoriessRelatedById, PropelPDO $con = null)
	{
		$this->categoriessRelatedByIdScheduledForDeletion = $this->getCategoriessRelatedById(new Criteria(), $con)->diff($categoriessRelatedById);

		foreach ($categoriessRelatedById as $categoriesRelatedById) {
			// Fix issue with collection modified by reference
			if ($categoriesRelatedById->isNew()) {
				$categoriesRelatedById->setCategoriesRelatedByParentId($this);
			}
			$this->addCategoriesRelatedById($categoriesRelatedById);
		}

		$this->collCategoriessRelatedById = $categoriessRelatedById;
	}

	/**
	 * Returns the number of related Categories objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Categories objects.
	 * @throws     PropelException
	 */
	public function countCategoriessRelatedById(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCategoriessRelatedById || null !== $criteria) {
			if ($this->isNew() && null === $this->collCategoriessRelatedById) {
				return 0;
			} else {
				$query = CategoriesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategoriesRelatedByParentId($this)
					->count($con);
			}
		} else {
			return count($this->collCategoriessRelatedById);
		}
	}

	/**
	 * Method called to associate a Categories object to this object
	 * through the Categories foreign key attribute.
	 *
	 * @param      Categories $l Categories
	 * @return     Categories The current object (for fluent API support)
	 */
	public function addCategoriesRelatedById(Categories $l)
	{
		if ($this->collCategoriessRelatedById === null) {
			$this->initCategoriessRelatedById();
		}
		if (!$this->collCategoriessRelatedById->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCategoriesRelatedById($l);
		}

		return $this;
	}

	/**
	 * @param	CategoriesRelatedById $categoriesRelatedById The categoriesRelatedById object to add.
	 */
	protected function doAddCategoriesRelatedById($categoriesRelatedById)
	{
		$this->collCategoriessRelatedById[]= $categoriesRelatedById;
		$categoriesRelatedById->setCategoriesRelatedByParentId($this);
	}

	/**
	 * Clears out the collProductsToCategoriess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addProductsToCategoriess()
	 */
	public function clearProductsToCategoriess()
	{
		$this->collProductsToCategoriess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collProductsToCategoriess collection.
	 *
	 * By default this just sets the collProductsToCategoriess collection to an empty array (like clearcollProductsToCategoriess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initProductsToCategoriess($overrideExisting = true)
	{
		if (null !== $this->collProductsToCategoriess && !$overrideExisting) {
			return;
		}
		$this->collProductsToCategoriess = new PropelObjectCollection();
		$this->collProductsToCategoriess->setModel('ProductsToCategories');
	}

	/**
	 * Gets an array of ProductsToCategories objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Categories is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ProductsToCategories[] List of ProductsToCategories objects
	 * @throws     PropelException
	 */
	public function getProductsToCategoriess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collProductsToCategoriess || null !== $criteria) {
			if ($this->isNew() && null === $this->collProductsToCategoriess) {
				// return empty collection
				$this->initProductsToCategoriess();
			} else {
				$collProductsToCategoriess = ProductsToCategoriesQuery::create(null, $criteria)
					->filterByCategories($this)
					->find($con);
				if (null !== $criteria) {
					return $collProductsToCategoriess;
				}
				$this->collProductsToCategoriess = $collProductsToCategoriess;
			}
		}
		return $this->collProductsToCategoriess;
	}

	/**
	 * Sets a collection of ProductsToCategories objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $productsToCategoriess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setProductsToCategoriess(PropelCollection $productsToCategoriess, PropelPDO $con = null)
	{
		$this->productsToCategoriessScheduledForDeletion = $this->getProductsToCategoriess(new Criteria(), $con)->diff($productsToCategoriess);

		foreach ($productsToCategoriess as $productsToCategories) {
			// Fix issue with collection modified by reference
			if ($productsToCategories->isNew()) {
				$productsToCategories->setCategories($this);
			}
			$this->addProductsToCategories($productsToCategories);
		}

		$this->collProductsToCategoriess = $productsToCategoriess;
	}

	/**
	 * Returns the number of related ProductsToCategories objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ProductsToCategories objects.
	 * @throws     PropelException
	 */
	public function countProductsToCategoriess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collProductsToCategoriess || null !== $criteria) {
			if ($this->isNew() && null === $this->collProductsToCategoriess) {
				return 0;
			} else {
				$query = ProductsToCategoriesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategories($this)
					->count($con);
			}
		} else {
			return count($this->collProductsToCategoriess);
		}
	}

	/**
	 * Method called to associate a ProductsToCategories object to this object
	 * through the ProductsToCategories foreign key attribute.
	 *
	 * @param      ProductsToCategories $l ProductsToCategories
	 * @return     Categories The current object (for fluent API support)
	 */
	public function addProductsToCategories(ProductsToCategories $l)
	{
		if ($this->collProductsToCategoriess === null) {
			$this->initProductsToCategoriess();
		}
		if (!$this->collProductsToCategoriess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddProductsToCategories($l);
		}

		return $this;
	}

	/**
	 * @param	ProductsToCategories $productsToCategories The productsToCategories object to add.
	 */
	protected function doAddProductsToCategories($productsToCategories)
	{
		$this->collProductsToCategoriess[]= $productsToCategories;
		$productsToCategories->setCategories($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Categories is new, it will return
	 * an empty collection; or if this Categories has previously
	 * been saved, it will retrieve related ProductsToCategoriess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Categories.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array ProductsToCategories[] List of ProductsToCategories objects
	 */
	public function getProductsToCategoriessJoinProducts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = ProductsToCategoriesQuery::create(null, $criteria);
		$query->joinWith('Products', $join_behavior);

		return $this->getProductsToCategoriess($query, $con);
	}

	/**
	 * Clears out the collCategoriesI18ns collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCategoriesI18ns()
	 */
	public function clearCategoriesI18ns()
	{
		$this->collCategoriesI18ns = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCategoriesI18ns collection.
	 *
	 * By default this just sets the collCategoriesI18ns collection to an empty array (like clearcollCategoriesI18ns());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCategoriesI18ns($overrideExisting = true)
	{
		if (null !== $this->collCategoriesI18ns && !$overrideExisting) {
			return;
		}
		$this->collCategoriesI18ns = new PropelObjectCollection();
		$this->collCategoriesI18ns->setModel('CategoriesI18n');
	}

	/**
	 * Gets an array of CategoriesI18n objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Categories is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CategoriesI18n[] List of CategoriesI18n objects
	 * @throws     PropelException
	 */
	public function getCategoriesI18ns($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCategoriesI18ns || null !== $criteria) {
			if ($this->isNew() && null === $this->collCategoriesI18ns) {
				// return empty collection
				$this->initCategoriesI18ns();
			} else {
				$collCategoriesI18ns = CategoriesI18nQuery::create(null, $criteria)
					->filterByCategories($this)
					->find($con);
				if (null !== $criteria) {
					return $collCategoriesI18ns;
				}
				$this->collCategoriesI18ns = $collCategoriesI18ns;
			}
		}
		return $this->collCategoriesI18ns;
	}

	/**
	 * Sets a collection of CategoriesI18n objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $categoriesI18ns A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCategoriesI18ns(PropelCollection $categoriesI18ns, PropelPDO $con = null)
	{
		$this->categoriesI18nsScheduledForDeletion = $this->getCategoriesI18ns(new Criteria(), $con)->diff($categoriesI18ns);

		foreach ($categoriesI18ns as $categoriesI18n) {
			// Fix issue with collection modified by reference
			if ($categoriesI18n->isNew()) {
				$categoriesI18n->setCategories($this);
			}
			$this->addCategoriesI18n($categoriesI18n);
		}

		$this->collCategoriesI18ns = $categoriesI18ns;
	}

	/**
	 * Returns the number of related CategoriesI18n objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CategoriesI18n objects.
	 * @throws     PropelException
	 */
	public function countCategoriesI18ns(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCategoriesI18ns || null !== $criteria) {
			if ($this->isNew() && null === $this->collCategoriesI18ns) {
				return 0;
			} else {
				$query = CategoriesI18nQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategories($this)
					->count($con);
			}
		} else {
			return count($this->collCategoriesI18ns);
		}
	}

	/**
	 * Method called to associate a CategoriesI18n object to this object
	 * through the CategoriesI18n foreign key attribute.
	 *
	 * @param      CategoriesI18n $l CategoriesI18n
	 * @return     Categories The current object (for fluent API support)
	 */
	public function addCategoriesI18n(CategoriesI18n $l)
	{
		if ($l && $locale = $l->getLocale()) {
			$this->setLocale($locale);
			$this->currentTranslations[$locale] = $l;
		}
		if ($this->collCategoriesI18ns === null) {
			$this->initCategoriesI18ns();
		}
		if (!$this->collCategoriesI18ns->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCategoriesI18n($l);
		}

		return $this;
	}

	/**
	 * @param	CategoriesI18n $categoriesI18n The categoriesI18n object to add.
	 */
	protected function doAddCategoriesI18n($categoriesI18n)
	{
		$this->collCategoriesI18ns[]= $categoriesI18n;
		$categoriesI18n->setCategories($this);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->parent_id = null;
		$this->context = null;
		$this->is_active = null;
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
			if ($this->collCategoriessRelatedById) {
				foreach ($this->collCategoriessRelatedById as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collProductsToCategoriess) {
				foreach ($this->collProductsToCategoriess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCategoriesI18ns) {
				foreach ($this->collCategoriesI18ns as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		// i18n behavior
		$this->currentLocale = 'en_EN';
		$this->currentTranslations = null;
		if ($this->collCategoriessRelatedById instanceof PropelCollection) {
			$this->collCategoriessRelatedById->clearIterator();
		}
		$this->collCategoriessRelatedById = null;
		if ($this->collProductsToCategoriess instanceof PropelCollection) {
			$this->collProductsToCategoriess->clearIterator();
		}
		$this->collProductsToCategoriess = null;
		if ($this->collCategoriesI18ns instanceof PropelCollection) {
			$this->collCategoriesI18ns->clearIterator();
		}
		$this->collCategoriesI18ns = null;
		$this->aCategoriesRelatedByParentId = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(CategoriesPeer::DEFAULT_STRING_FORMAT);
	}

	// i18n behavior
	
	/**
	 * Sets the locale for translations
	 *
	 * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
	 *
	 * @return    Categories The current object (for fluent API support)
	 */
	public function setLocale($locale = 'en_EN')
	{
		$this->currentLocale = $locale;
	
		return $this;
	}
	
	/**
	 * Gets the locale for translations
	 *
	 * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
	 */
	public function getLocale()
	{
		return $this->currentLocale;
	}
	
	/**
	 * Returns the current translation for a given locale
	 *
	 * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return CategoriesI18n */
	public function getTranslation($locale = 'en_EN', PropelPDO $con = null)
	{
		if (!isset($this->currentTranslations[$locale])) {
			if (null !== $this->collCategoriesI18ns) {
				foreach ($this->collCategoriesI18ns as $translation) {
					if ($translation->getLocale() == $locale) {
						$this->currentTranslations[$locale] = $translation;
						return $translation;
					}
				}
			}
			if ($this->isNew()) {
				$translation = new CategoriesI18n();
				$translation->setLocale($locale);
			} else {
				$translation = CategoriesI18nQuery::create()
					->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
					->findOneOrCreate($con);
				$this->currentTranslations[$locale] = $translation;
			}
			$this->addCategoriesI18n($translation);
		}
	
		return $this->currentTranslations[$locale];
	}
	
	/**
	 * Remove the translation for a given locale
	 *
	 * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Categories The current object (for fluent API support)
	 */
	public function removeTranslation($locale = 'en_EN', PropelPDO $con = null)
	{
		if (!$this->isNew()) {
			CategoriesI18nQuery::create()
				->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
				->delete($con);
		}
		if (isset($this->currentTranslations[$locale])) {
			unset($this->currentTranslations[$locale]);
		}
		foreach ($this->collCategoriesI18ns as $key => $translation) {
			if ($translation->getLocale() == $locale) {
				unset($this->collCategoriesI18ns[$key]);
				break;
			}
		}
	
		return $this;
	}
	
	/**
	 * Returns the current translation
	 *
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return CategoriesI18n */
	public function getCurrentTranslation(PropelPDO $con = null)
	{
		return $this->getTranslation($this->getLocale(), $con);
	}
	
	
	/**
	 * Get the [title] column value.
	 * 
	 * @return     string
	 */
	public function getTitle()
	{	return $this->getCurrentTranslation()->getTitle();
	}
	
	
	/**
	 * Set the value of [title] column.
	 * 
	 * @param      string $v new value
	 * @return     Categories The current object (for fluent API support)
	 */
	public function setTitle($v)
	{	$this->getCurrentTranslation()->setTitle($v);
	
		return $this;
	}
	
	
	/**
	 * Get the [content] column value.
	 * 
	 * @return     string
	 */
	public function getContent()
	{	return $this->getCurrentTranslation()->getContent();
	}
	
	
	/**
	 * Set the value of [content] column.
	 * 
	 * @param      string $v new value
	 * @return     Categories The current object (for fluent API support)
	 */
	public function setContent($v)
	{	$this->getCurrentTranslation()->setContent($v);
	
		return $this;
	}

} // BaseCategories

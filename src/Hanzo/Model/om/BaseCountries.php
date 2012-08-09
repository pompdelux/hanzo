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
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\ZipToCity;
use Hanzo\Model\ZipToCityQuery;

/**
 * Base class that represents a row from the 'countries' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCountries extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\CountriesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CountriesPeer
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
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the local_name field.
	 * @var        string
	 */
	protected $local_name;

	/**
	 * The value for the code field.
	 * @var        int
	 */
	protected $code;

	/**
	 * The value for the iso2 field.
	 * @var        string
	 */
	protected $iso2;

	/**
	 * The value for the iso3 field.
	 * @var        string
	 */
	protected $iso3;

	/**
	 * The value for the continent field.
	 * @var        string
	 */
	protected $continent;

	/**
	 * The value for the currency_id field.
	 * @var        int
	 */
	protected $currency_id;

	/**
	 * The value for the currency_code field.
	 * @var        string
	 */
	protected $currency_code;

	/**
	 * The value for the currency_name field.
	 * @var        string
	 */
	protected $currency_name;

	/**
	 * The value for the vat field.
	 * @var        string
	 */
	protected $vat;

	/**
	 * The value for the calling_code field.
	 * @var        int
	 */
	protected $calling_code;

	/**
	 * @var        array Addresses[] Collection to store aggregation of Addresses objects.
	 */
	protected $collAddressess;

	/**
	 * @var        array ZipToCity[] Collection to store aggregation of ZipToCity objects.
	 */
	protected $collZipToCitys;

	/**
	 * @var        array Orders[] Collection to store aggregation of Orders objects.
	 */
	protected $collOrderssRelatedByBillingCountriesId;

	/**
	 * @var        array Orders[] Collection to store aggregation of Orders objects.
	 */
	protected $collOrderssRelatedByDeliveryCountriesId;

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
	protected $addressessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $zipToCitysScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $orderssRelatedByBillingCountriesIdScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $orderssRelatedByDeliveryCountriesIdScheduledForDeletion = null;

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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the [local_name] column value.
	 * 
	 * @return     string
	 */
	public function getLocalName()
	{
		return $this->local_name;
	}

	/**
	 * Get the [code] column value.
	 * 
	 * @return     int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Get the [iso2] column value.
	 * 
	 * @return     string
	 */
	public function getIso2()
	{
		return $this->iso2;
	}

	/**
	 * Get the [iso3] column value.
	 * 
	 * @return     string
	 */
	public function getIso3()
	{
		return $this->iso3;
	}

	/**
	 * Get the [continent] column value.
	 * 
	 * @return     string
	 */
	public function getContinent()
	{
		return $this->continent;
	}

	/**
	 * Get the [currency_id] column value.
	 * 
	 * @return     int
	 */
	public function getCurrencyId()
	{
		return $this->currency_id;
	}

	/**
	 * Get the [currency_code] column value.
	 * 
	 * @return     string
	 */
	public function getCurrencyCode()
	{
		return $this->currency_code;
	}

	/**
	 * Get the [currency_name] column value.
	 * 
	 * @return     string
	 */
	public function getCurrencyName()
	{
		return $this->currency_name;
	}

	/**
	 * Get the [vat] column value.
	 * 
	 * @return     string
	 */
	public function getVat()
	{
		return $this->vat;
	}

	/**
	 * Get the [calling_code] column value.
	 * 
	 * @return     int
	 */
	public function getCallingCode()
	{
		return $this->calling_code;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CountriesPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = CountriesPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [local_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setLocalName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->local_name !== $v) {
			$this->local_name = $v;
			$this->modifiedColumns[] = CountriesPeer::LOCAL_NAME;
		}

		return $this;
	} // setLocalName()

	/**
	 * Set the value of [code] column.
	 * 
	 * @param      int $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setCode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->code !== $v) {
			$this->code = $v;
			$this->modifiedColumns[] = CountriesPeer::CODE;
		}

		return $this;
	} // setCode()

	/**
	 * Set the value of [iso2] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setIso2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->iso2 !== $v) {
			$this->iso2 = $v;
			$this->modifiedColumns[] = CountriesPeer::ISO2;
		}

		return $this;
	} // setIso2()

	/**
	 * Set the value of [iso3] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setIso3($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->iso3 !== $v) {
			$this->iso3 = $v;
			$this->modifiedColumns[] = CountriesPeer::ISO3;
		}

		return $this;
	} // setIso3()

	/**
	 * Set the value of [continent] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setContinent($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->continent !== $v) {
			$this->continent = $v;
			$this->modifiedColumns[] = CountriesPeer::CONTINENT;
		}

		return $this;
	} // setContinent()

	/**
	 * Set the value of [currency_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setCurrencyId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->currency_id !== $v) {
			$this->currency_id = $v;
			$this->modifiedColumns[] = CountriesPeer::CURRENCY_ID;
		}

		return $this;
	} // setCurrencyId()

	/**
	 * Set the value of [currency_code] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setCurrencyCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->currency_code !== $v) {
			$this->currency_code = $v;
			$this->modifiedColumns[] = CountriesPeer::CURRENCY_CODE;
		}

		return $this;
	} // setCurrencyCode()

	/**
	 * Set the value of [currency_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setCurrencyName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->currency_name !== $v) {
			$this->currency_name = $v;
			$this->modifiedColumns[] = CountriesPeer::CURRENCY_NAME;
		}

		return $this;
	} // setCurrencyName()

	/**
	 * Set the value of [vat] column.
	 * 
	 * @param      string $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setVat($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->vat !== $v) {
			$this->vat = $v;
			$this->modifiedColumns[] = CountriesPeer::VAT;
		}

		return $this;
	} // setVat()

	/**
	 * Set the value of [calling_code] column.
	 * 
	 * @param      int $v new value
	 * @return     Countries The current object (for fluent API support)
	 */
	public function setCallingCode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->calling_code !== $v) {
			$this->calling_code = $v;
			$this->modifiedColumns[] = CountriesPeer::CALLING_CODE;
		}

		return $this;
	} // setCallingCode()

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
			$this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->local_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->code = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->iso2 = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->iso3 = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->continent = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->currency_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->currency_code = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->currency_name = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->vat = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->calling_code = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 12; // 12 = CountriesPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Countries object", $e);
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
			$con = Propel::getConnection(CountriesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CountriesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collAddressess = null;

			$this->collZipToCitys = null;

			$this->collOrderssRelatedByBillingCountriesId = null;

			$this->collOrderssRelatedByDeliveryCountriesId = null;

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
			$con = Propel::getConnection(CountriesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = CountriesQuery::create()
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
			$con = Propel::getConnection(CountriesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CountriesPeer::addInstanceToPool($this);
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

			if ($this->addressessScheduledForDeletion !== null) {
				if (!$this->addressessScheduledForDeletion->isEmpty()) {
					AddressesQuery::create()
						->filterByPrimaryKeys($this->addressessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->addressessScheduledForDeletion = null;
				}
			}

			if ($this->collAddressess !== null) {
				foreach ($this->collAddressess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->zipToCitysScheduledForDeletion !== null) {
				if (!$this->zipToCitysScheduledForDeletion->isEmpty()) {
					ZipToCityQuery::create()
						->filterByPrimaryKeys($this->zipToCitysScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->zipToCitysScheduledForDeletion = null;
				}
			}

			if ($this->collZipToCitys !== null) {
				foreach ($this->collZipToCitys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->orderssRelatedByBillingCountriesIdScheduledForDeletion !== null) {
				if (!$this->orderssRelatedByBillingCountriesIdScheduledForDeletion->isEmpty()) {
					OrdersQuery::create()
						->filterByPrimaryKeys($this->orderssRelatedByBillingCountriesIdScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->orderssRelatedByBillingCountriesIdScheduledForDeletion = null;
				}
			}

			if ($this->collOrderssRelatedByBillingCountriesId !== null) {
				foreach ($this->collOrderssRelatedByBillingCountriesId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->orderssRelatedByDeliveryCountriesIdScheduledForDeletion !== null) {
				if (!$this->orderssRelatedByDeliveryCountriesIdScheduledForDeletion->isEmpty()) {
					OrdersQuery::create()
						->filterByPrimaryKeys($this->orderssRelatedByDeliveryCountriesIdScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->orderssRelatedByDeliveryCountriesIdScheduledForDeletion = null;
				}
			}

			if ($this->collOrderssRelatedByDeliveryCountriesId !== null) {
				foreach ($this->collOrderssRelatedByDeliveryCountriesId as $referrerFK) {
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

		$this->modifiedColumns[] = CountriesPeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . CountriesPeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(CountriesPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = '`ID`';
		}
		if ($this->isColumnModified(CountriesPeer::NAME)) {
			$modifiedColumns[':p' . $index++]  = '`NAME`';
		}
		if ($this->isColumnModified(CountriesPeer::LOCAL_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`LOCAL_NAME`';
		}
		if ($this->isColumnModified(CountriesPeer::CODE)) {
			$modifiedColumns[':p' . $index++]  = '`CODE`';
		}
		if ($this->isColumnModified(CountriesPeer::ISO2)) {
			$modifiedColumns[':p' . $index++]  = '`ISO2`';
		}
		if ($this->isColumnModified(CountriesPeer::ISO3)) {
			$modifiedColumns[':p' . $index++]  = '`ISO3`';
		}
		if ($this->isColumnModified(CountriesPeer::CONTINENT)) {
			$modifiedColumns[':p' . $index++]  = '`CONTINENT`';
		}
		if ($this->isColumnModified(CountriesPeer::CURRENCY_ID)) {
			$modifiedColumns[':p' . $index++]  = '`CURRENCY_ID`';
		}
		if ($this->isColumnModified(CountriesPeer::CURRENCY_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`CURRENCY_CODE`';
		}
		if ($this->isColumnModified(CountriesPeer::CURRENCY_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`CURRENCY_NAME`';
		}
		if ($this->isColumnModified(CountriesPeer::VAT)) {
			$modifiedColumns[':p' . $index++]  = '`VAT`';
		}
		if ($this->isColumnModified(CountriesPeer::CALLING_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`CALLING_CODE`';
		}

		$sql = sprintf(
			'INSERT INTO `countries` (%s) VALUES (%s)',
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
					case '`NAME`':
						$stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
						break;
					case '`LOCAL_NAME`':
						$stmt->bindValue($identifier, $this->local_name, PDO::PARAM_STR);
						break;
					case '`CODE`':
						$stmt->bindValue($identifier, $this->code, PDO::PARAM_INT);
						break;
					case '`ISO2`':
						$stmt->bindValue($identifier, $this->iso2, PDO::PARAM_STR);
						break;
					case '`ISO3`':
						$stmt->bindValue($identifier, $this->iso3, PDO::PARAM_STR);
						break;
					case '`CONTINENT`':
						$stmt->bindValue($identifier, $this->continent, PDO::PARAM_STR);
						break;
					case '`CURRENCY_ID`':
						$stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
						break;
					case '`CURRENCY_CODE`':
						$stmt->bindValue($identifier, $this->currency_code, PDO::PARAM_STR);
						break;
					case '`CURRENCY_NAME`':
						$stmt->bindValue($identifier, $this->currency_name, PDO::PARAM_STR);
						break;
					case '`VAT`':
						$stmt->bindValue($identifier, $this->vat, PDO::PARAM_STR);
						break;
					case '`CALLING_CODE`':
						$stmt->bindValue($identifier, $this->calling_code, PDO::PARAM_INT);
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


			if (($retval = CountriesPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAddressess !== null) {
					foreach ($this->collAddressess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collZipToCitys !== null) {
					foreach ($this->collZipToCitys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collOrderssRelatedByBillingCountriesId !== null) {
					foreach ($this->collOrderssRelatedByBillingCountriesId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collOrderssRelatedByDeliveryCountriesId !== null) {
					foreach ($this->collOrderssRelatedByDeliveryCountriesId as $referrerFK) {
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
		$pos = CountriesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getName();
				break;
			case 2:
				return $this->getLocalName();
				break;
			case 3:
				return $this->getCode();
				break;
			case 4:
				return $this->getIso2();
				break;
			case 5:
				return $this->getIso3();
				break;
			case 6:
				return $this->getContinent();
				break;
			case 7:
				return $this->getCurrencyId();
				break;
			case 8:
				return $this->getCurrencyCode();
				break;
			case 9:
				return $this->getCurrencyName();
				break;
			case 10:
				return $this->getVat();
				break;
			case 11:
				return $this->getCallingCode();
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
		if (isset($alreadyDumpedObjects['Countries'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Countries'][$this->getPrimaryKey()] = true;
		$keys = CountriesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getName(),
			$keys[2] => $this->getLocalName(),
			$keys[3] => $this->getCode(),
			$keys[4] => $this->getIso2(),
			$keys[5] => $this->getIso3(),
			$keys[6] => $this->getContinent(),
			$keys[7] => $this->getCurrencyId(),
			$keys[8] => $this->getCurrencyCode(),
			$keys[9] => $this->getCurrencyName(),
			$keys[10] => $this->getVat(),
			$keys[11] => $this->getCallingCode(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collAddressess) {
				$result['Addressess'] = $this->collAddressess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collZipToCitys) {
				$result['ZipToCitys'] = $this->collZipToCitys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collOrderssRelatedByBillingCountriesId) {
				$result['OrderssRelatedByBillingCountriesId'] = $this->collOrderssRelatedByBillingCountriesId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collOrderssRelatedByDeliveryCountriesId) {
				$result['OrderssRelatedByDeliveryCountriesId'] = $this->collOrderssRelatedByDeliveryCountriesId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = CountriesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setName($value);
				break;
			case 2:
				$this->setLocalName($value);
				break;
			case 3:
				$this->setCode($value);
				break;
			case 4:
				$this->setIso2($value);
				break;
			case 5:
				$this->setIso3($value);
				break;
			case 6:
				$this->setContinent($value);
				break;
			case 7:
				$this->setCurrencyId($value);
				break;
			case 8:
				$this->setCurrencyCode($value);
				break;
			case 9:
				$this->setCurrencyName($value);
				break;
			case 10:
				$this->setVat($value);
				break;
			case 11:
				$this->setCallingCode($value);
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
		$keys = CountriesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setLocalName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCode($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setIso2($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setIso3($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setContinent($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCurrencyId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCurrencyCode($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCurrencyName($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setVat($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCallingCode($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CountriesPeer::DATABASE_NAME);

		if ($this->isColumnModified(CountriesPeer::ID)) $criteria->add(CountriesPeer::ID, $this->id);
		if ($this->isColumnModified(CountriesPeer::NAME)) $criteria->add(CountriesPeer::NAME, $this->name);
		if ($this->isColumnModified(CountriesPeer::LOCAL_NAME)) $criteria->add(CountriesPeer::LOCAL_NAME, $this->local_name);
		if ($this->isColumnModified(CountriesPeer::CODE)) $criteria->add(CountriesPeer::CODE, $this->code);
		if ($this->isColumnModified(CountriesPeer::ISO2)) $criteria->add(CountriesPeer::ISO2, $this->iso2);
		if ($this->isColumnModified(CountriesPeer::ISO3)) $criteria->add(CountriesPeer::ISO3, $this->iso3);
		if ($this->isColumnModified(CountriesPeer::CONTINENT)) $criteria->add(CountriesPeer::CONTINENT, $this->continent);
		if ($this->isColumnModified(CountriesPeer::CURRENCY_ID)) $criteria->add(CountriesPeer::CURRENCY_ID, $this->currency_id);
		if ($this->isColumnModified(CountriesPeer::CURRENCY_CODE)) $criteria->add(CountriesPeer::CURRENCY_CODE, $this->currency_code);
		if ($this->isColumnModified(CountriesPeer::CURRENCY_NAME)) $criteria->add(CountriesPeer::CURRENCY_NAME, $this->currency_name);
		if ($this->isColumnModified(CountriesPeer::VAT)) $criteria->add(CountriesPeer::VAT, $this->vat);
		if ($this->isColumnModified(CountriesPeer::CALLING_CODE)) $criteria->add(CountriesPeer::CALLING_CODE, $this->calling_code);

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
		$criteria = new Criteria(CountriesPeer::DATABASE_NAME);
		$criteria->add(CountriesPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Countries (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setName($this->getName());
		$copyObj->setLocalName($this->getLocalName());
		$copyObj->setCode($this->getCode());
		$copyObj->setIso2($this->getIso2());
		$copyObj->setIso3($this->getIso3());
		$copyObj->setContinent($this->getContinent());
		$copyObj->setCurrencyId($this->getCurrencyId());
		$copyObj->setCurrencyCode($this->getCurrencyCode());
		$copyObj->setCurrencyName($this->getCurrencyName());
		$copyObj->setVat($this->getVat());
		$copyObj->setCallingCode($this->getCallingCode());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getAddressess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAddresses($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getZipToCitys() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addZipToCity($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getOrderssRelatedByBillingCountriesId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersRelatedByBillingCountriesId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getOrderssRelatedByDeliveryCountriesId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersRelatedByDeliveryCountriesId($relObj->copy($deepCopy));
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
	 * @return     Countries Clone of current object.
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
	 * @return     CountriesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CountriesPeer();
		}
		return self::$peer;
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
		if ('Addresses' == $relationName) {
			return $this->initAddressess();
		}
		if ('ZipToCity' == $relationName) {
			return $this->initZipToCitys();
		}
		if ('OrdersRelatedByBillingCountriesId' == $relationName) {
			return $this->initOrderssRelatedByBillingCountriesId();
		}
		if ('OrdersRelatedByDeliveryCountriesId' == $relationName) {
			return $this->initOrderssRelatedByDeliveryCountriesId();
		}
	}

	/**
	 * Clears out the collAddressess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAddressess()
	 */
	public function clearAddressess()
	{
		$this->collAddressess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAddressess collection.
	 *
	 * By default this just sets the collAddressess collection to an empty array (like clearcollAddressess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAddressess($overrideExisting = true)
	{
		if (null !== $this->collAddressess && !$overrideExisting) {
			return;
		}
		$this->collAddressess = new PropelObjectCollection();
		$this->collAddressess->setModel('Addresses');
	}

	/**
	 * Gets an array of Addresses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Countries is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Addresses[] List of Addresses objects
	 * @throws     PropelException
	 */
	public function getAddressess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAddressess || null !== $criteria) {
			if ($this->isNew() && null === $this->collAddressess) {
				// return empty collection
				$this->initAddressess();
			} else {
				$collAddressess = AddressesQuery::create(null, $criteria)
					->filterByCountries($this)
					->find($con);
				if (null !== $criteria) {
					return $collAddressess;
				}
				$this->collAddressess = $collAddressess;
			}
		}
		return $this->collAddressess;
	}

	/**
	 * Sets a collection of Addresses objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $addressess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAddressess(PropelCollection $addressess, PropelPDO $con = null)
	{
		$this->addressessScheduledForDeletion = $this->getAddressess(new Criteria(), $con)->diff($addressess);

		foreach ($addressess as $addresses) {
			// Fix issue with collection modified by reference
			if ($addresses->isNew()) {
				$addresses->setCountries($this);
			}
			$this->addAddresses($addresses);
		}

		$this->collAddressess = $addressess;
	}

	/**
	 * Returns the number of related Addresses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Addresses objects.
	 * @throws     PropelException
	 */
	public function countAddressess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAddressess || null !== $criteria) {
			if ($this->isNew() && null === $this->collAddressess) {
				return 0;
			} else {
				$query = AddressesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCountries($this)
					->count($con);
			}
		} else {
			return count($this->collAddressess);
		}
	}

	/**
	 * Method called to associate a Addresses object to this object
	 * through the Addresses foreign key attribute.
	 *
	 * @param      Addresses $l Addresses
	 * @return     Countries The current object (for fluent API support)
	 */
	public function addAddresses(Addresses $l)
	{
		if ($this->collAddressess === null) {
			$this->initAddressess();
		}
		if (!$this->collAddressess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAddresses($l);
		}

		return $this;
	}

	/**
	 * @param	Addresses $addresses The addresses object to add.
	 */
	protected function doAddAddresses($addresses)
	{
		$this->collAddressess[]= $addresses;
		$addresses->setCountries($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Countries is new, it will return
	 * an empty collection; or if this Countries has previously
	 * been saved, it will retrieve related Addressess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Countries.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array Addresses[] List of Addresses objects
	 */
	public function getAddressessJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AddressesQuery::create(null, $criteria);
		$query->joinWith('Customers', $join_behavior);

		return $this->getAddressess($query, $con);
	}

	/**
	 * Clears out the collZipToCitys collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addZipToCitys()
	 */
	public function clearZipToCitys()
	{
		$this->collZipToCitys = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collZipToCitys collection.
	 *
	 * By default this just sets the collZipToCitys collection to an empty array (like clearcollZipToCitys());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initZipToCitys($overrideExisting = true)
	{
		if (null !== $this->collZipToCitys && !$overrideExisting) {
			return;
		}
		$this->collZipToCitys = new PropelObjectCollection();
		$this->collZipToCitys->setModel('ZipToCity');
	}

	/**
	 * Gets an array of ZipToCity objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Countries is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ZipToCity[] List of ZipToCity objects
	 * @throws     PropelException
	 */
	public function getZipToCitys($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collZipToCitys || null !== $criteria) {
			if ($this->isNew() && null === $this->collZipToCitys) {
				// return empty collection
				$this->initZipToCitys();
			} else {
				$collZipToCitys = ZipToCityQuery::create(null, $criteria)
					->filterByCountries($this)
					->find($con);
				if (null !== $criteria) {
					return $collZipToCitys;
				}
				$this->collZipToCitys = $collZipToCitys;
			}
		}
		return $this->collZipToCitys;
	}

	/**
	 * Sets a collection of ZipToCity objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $zipToCitys A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setZipToCitys(PropelCollection $zipToCitys, PropelPDO $con = null)
	{
		$this->zipToCitysScheduledForDeletion = $this->getZipToCitys(new Criteria(), $con)->diff($zipToCitys);

		foreach ($zipToCitys as $zipToCity) {
			// Fix issue with collection modified by reference
			if ($zipToCity->isNew()) {
				$zipToCity->setCountries($this);
			}
			$this->addZipToCity($zipToCity);
		}

		$this->collZipToCitys = $zipToCitys;
	}

	/**
	 * Returns the number of related ZipToCity objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ZipToCity objects.
	 * @throws     PropelException
	 */
	public function countZipToCitys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collZipToCitys || null !== $criteria) {
			if ($this->isNew() && null === $this->collZipToCitys) {
				return 0;
			} else {
				$query = ZipToCityQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCountries($this)
					->count($con);
			}
		} else {
			return count($this->collZipToCitys);
		}
	}

	/**
	 * Method called to associate a ZipToCity object to this object
	 * through the ZipToCity foreign key attribute.
	 *
	 * @param      ZipToCity $l ZipToCity
	 * @return     Countries The current object (for fluent API support)
	 */
	public function addZipToCity(ZipToCity $l)
	{
		if ($this->collZipToCitys === null) {
			$this->initZipToCitys();
		}
		if (!$this->collZipToCitys->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddZipToCity($l);
		}

		return $this;
	}

	/**
	 * @param	ZipToCity $zipToCity The zipToCity object to add.
	 */
	protected function doAddZipToCity($zipToCity)
	{
		$this->collZipToCitys[]= $zipToCity;
		$zipToCity->setCountries($this);
	}

	/**
	 * Clears out the collOrderssRelatedByBillingCountriesId collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrderssRelatedByBillingCountriesId()
	 */
	public function clearOrderssRelatedByBillingCountriesId()
	{
		$this->collOrderssRelatedByBillingCountriesId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrderssRelatedByBillingCountriesId collection.
	 *
	 * By default this just sets the collOrderssRelatedByBillingCountriesId collection to an empty array (like clearcollOrderssRelatedByBillingCountriesId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrderssRelatedByBillingCountriesId($overrideExisting = true)
	{
		if (null !== $this->collOrderssRelatedByBillingCountriesId && !$overrideExisting) {
			return;
		}
		$this->collOrderssRelatedByBillingCountriesId = new PropelObjectCollection();
		$this->collOrderssRelatedByBillingCountriesId->setModel('Orders');
	}

	/**
	 * Gets an array of Orders objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Countries is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Orders[] List of Orders objects
	 * @throws     PropelException
	 */
	public function getOrderssRelatedByBillingCountriesId($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrderssRelatedByBillingCountriesId || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrderssRelatedByBillingCountriesId) {
				// return empty collection
				$this->initOrderssRelatedByBillingCountriesId();
			} else {
				$collOrderssRelatedByBillingCountriesId = OrdersQuery::create(null, $criteria)
					->filterByCountriesRelatedByBillingCountriesId($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrderssRelatedByBillingCountriesId;
				}
				$this->collOrderssRelatedByBillingCountriesId = $collOrderssRelatedByBillingCountriesId;
			}
		}
		return $this->collOrderssRelatedByBillingCountriesId;
	}

	/**
	 * Sets a collection of OrdersRelatedByBillingCountriesId objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $orderssRelatedByBillingCountriesId A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrderssRelatedByBillingCountriesId(PropelCollection $orderssRelatedByBillingCountriesId, PropelPDO $con = null)
	{
		$this->orderssRelatedByBillingCountriesIdScheduledForDeletion = $this->getOrderssRelatedByBillingCountriesId(new Criteria(), $con)->diff($orderssRelatedByBillingCountriesId);

		foreach ($orderssRelatedByBillingCountriesId as $ordersRelatedByBillingCountriesId) {
			// Fix issue with collection modified by reference
			if ($ordersRelatedByBillingCountriesId->isNew()) {
				$ordersRelatedByBillingCountriesId->setCountriesRelatedByBillingCountriesId($this);
			}
			$this->addOrdersRelatedByBillingCountriesId($ordersRelatedByBillingCountriesId);
		}

		$this->collOrderssRelatedByBillingCountriesId = $orderssRelatedByBillingCountriesId;
	}

	/**
	 * Returns the number of related Orders objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Orders objects.
	 * @throws     PropelException
	 */
	public function countOrderssRelatedByBillingCountriesId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrderssRelatedByBillingCountriesId || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrderssRelatedByBillingCountriesId) {
				return 0;
			} else {
				$query = OrdersQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCountriesRelatedByBillingCountriesId($this)
					->count($con);
			}
		} else {
			return count($this->collOrderssRelatedByBillingCountriesId);
		}
	}

	/**
	 * Method called to associate a Orders object to this object
	 * through the Orders foreign key attribute.
	 *
	 * @param      Orders $l Orders
	 * @return     Countries The current object (for fluent API support)
	 */
	public function addOrdersRelatedByBillingCountriesId(Orders $l)
	{
		if ($this->collOrderssRelatedByBillingCountriesId === null) {
			$this->initOrderssRelatedByBillingCountriesId();
		}
		if (!$this->collOrderssRelatedByBillingCountriesId->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersRelatedByBillingCountriesId($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersRelatedByBillingCountriesId $ordersRelatedByBillingCountriesId The ordersRelatedByBillingCountriesId object to add.
	 */
	protected function doAddOrdersRelatedByBillingCountriesId($ordersRelatedByBillingCountriesId)
	{
		$this->collOrderssRelatedByBillingCountriesId[]= $ordersRelatedByBillingCountriesId;
		$ordersRelatedByBillingCountriesId->setCountriesRelatedByBillingCountriesId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Countries is new, it will return
	 * an empty collection; or if this Countries has previously
	 * been saved, it will retrieve related OrderssRelatedByBillingCountriesId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Countries.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array Orders[] List of Orders objects
	 */
	public function getOrderssRelatedByBillingCountriesIdJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = OrdersQuery::create(null, $criteria);
		$query->joinWith('Customers', $join_behavior);

		return $this->getOrderssRelatedByBillingCountriesId($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Countries is new, it will return
	 * an empty collection; or if this Countries has previously
	 * been saved, it will retrieve related OrderssRelatedByBillingCountriesId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Countries.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array Orders[] List of Orders objects
	 */
	public function getOrderssRelatedByBillingCountriesIdJoinEvents($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = OrdersQuery::create(null, $criteria);
		$query->joinWith('Events', $join_behavior);

		return $this->getOrderssRelatedByBillingCountriesId($query, $con);
	}

	/**
	 * Clears out the collOrderssRelatedByDeliveryCountriesId collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrderssRelatedByDeliveryCountriesId()
	 */
	public function clearOrderssRelatedByDeliveryCountriesId()
	{
		$this->collOrderssRelatedByDeliveryCountriesId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrderssRelatedByDeliveryCountriesId collection.
	 *
	 * By default this just sets the collOrderssRelatedByDeliveryCountriesId collection to an empty array (like clearcollOrderssRelatedByDeliveryCountriesId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrderssRelatedByDeliveryCountriesId($overrideExisting = true)
	{
		if (null !== $this->collOrderssRelatedByDeliveryCountriesId && !$overrideExisting) {
			return;
		}
		$this->collOrderssRelatedByDeliveryCountriesId = new PropelObjectCollection();
		$this->collOrderssRelatedByDeliveryCountriesId->setModel('Orders');
	}

	/**
	 * Gets an array of Orders objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Countries is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Orders[] List of Orders objects
	 * @throws     PropelException
	 */
	public function getOrderssRelatedByDeliveryCountriesId($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrderssRelatedByDeliveryCountriesId || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrderssRelatedByDeliveryCountriesId) {
				// return empty collection
				$this->initOrderssRelatedByDeliveryCountriesId();
			} else {
				$collOrderssRelatedByDeliveryCountriesId = OrdersQuery::create(null, $criteria)
					->filterByCountriesRelatedByDeliveryCountriesId($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrderssRelatedByDeliveryCountriesId;
				}
				$this->collOrderssRelatedByDeliveryCountriesId = $collOrderssRelatedByDeliveryCountriesId;
			}
		}
		return $this->collOrderssRelatedByDeliveryCountriesId;
	}

	/**
	 * Sets a collection of OrdersRelatedByDeliveryCountriesId objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $orderssRelatedByDeliveryCountriesId A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrderssRelatedByDeliveryCountriesId(PropelCollection $orderssRelatedByDeliveryCountriesId, PropelPDO $con = null)
	{
		$this->orderssRelatedByDeliveryCountriesIdScheduledForDeletion = $this->getOrderssRelatedByDeliveryCountriesId(new Criteria(), $con)->diff($orderssRelatedByDeliveryCountriesId);

		foreach ($orderssRelatedByDeliveryCountriesId as $ordersRelatedByDeliveryCountriesId) {
			// Fix issue with collection modified by reference
			if ($ordersRelatedByDeliveryCountriesId->isNew()) {
				$ordersRelatedByDeliveryCountriesId->setCountriesRelatedByDeliveryCountriesId($this);
			}
			$this->addOrdersRelatedByDeliveryCountriesId($ordersRelatedByDeliveryCountriesId);
		}

		$this->collOrderssRelatedByDeliveryCountriesId = $orderssRelatedByDeliveryCountriesId;
	}

	/**
	 * Returns the number of related Orders objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Orders objects.
	 * @throws     PropelException
	 */
	public function countOrderssRelatedByDeliveryCountriesId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrderssRelatedByDeliveryCountriesId || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrderssRelatedByDeliveryCountriesId) {
				return 0;
			} else {
				$query = OrdersQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCountriesRelatedByDeliveryCountriesId($this)
					->count($con);
			}
		} else {
			return count($this->collOrderssRelatedByDeliveryCountriesId);
		}
	}

	/**
	 * Method called to associate a Orders object to this object
	 * through the Orders foreign key attribute.
	 *
	 * @param      Orders $l Orders
	 * @return     Countries The current object (for fluent API support)
	 */
	public function addOrdersRelatedByDeliveryCountriesId(Orders $l)
	{
		if ($this->collOrderssRelatedByDeliveryCountriesId === null) {
			$this->initOrderssRelatedByDeliveryCountriesId();
		}
		if (!$this->collOrderssRelatedByDeliveryCountriesId->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersRelatedByDeliveryCountriesId($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersRelatedByDeliveryCountriesId $ordersRelatedByDeliveryCountriesId The ordersRelatedByDeliveryCountriesId object to add.
	 */
	protected function doAddOrdersRelatedByDeliveryCountriesId($ordersRelatedByDeliveryCountriesId)
	{
		$this->collOrderssRelatedByDeliveryCountriesId[]= $ordersRelatedByDeliveryCountriesId;
		$ordersRelatedByDeliveryCountriesId->setCountriesRelatedByDeliveryCountriesId($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Countries is new, it will return
	 * an empty collection; or if this Countries has previously
	 * been saved, it will retrieve related OrderssRelatedByDeliveryCountriesId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Countries.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array Orders[] List of Orders objects
	 */
	public function getOrderssRelatedByDeliveryCountriesIdJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = OrdersQuery::create(null, $criteria);
		$query->joinWith('Customers', $join_behavior);

		return $this->getOrderssRelatedByDeliveryCountriesId($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Countries is new, it will return
	 * an empty collection; or if this Countries has previously
	 * been saved, it will retrieve related OrderssRelatedByDeliveryCountriesId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Countries.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array Orders[] List of Orders objects
	 */
	public function getOrderssRelatedByDeliveryCountriesIdJoinEvents($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = OrdersQuery::create(null, $criteria);
		$query->joinWith('Events', $join_behavior);

		return $this->getOrderssRelatedByDeliveryCountriesId($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->name = null;
		$this->local_name = null;
		$this->code = null;
		$this->iso2 = null;
		$this->iso3 = null;
		$this->continent = null;
		$this->currency_id = null;
		$this->currency_code = null;
		$this->currency_name = null;
		$this->vat = null;
		$this->calling_code = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
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
			if ($this->collAddressess) {
				foreach ($this->collAddressess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collZipToCitys) {
				foreach ($this->collZipToCitys as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collOrderssRelatedByBillingCountriesId) {
				foreach ($this->collOrderssRelatedByBillingCountriesId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collOrderssRelatedByDeliveryCountriesId) {
				foreach ($this->collOrderssRelatedByDeliveryCountriesId as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collAddressess instanceof PropelCollection) {
			$this->collAddressess->clearIterator();
		}
		$this->collAddressess = null;
		if ($this->collZipToCitys instanceof PropelCollection) {
			$this->collZipToCitys->clearIterator();
		}
		$this->collZipToCitys = null;
		if ($this->collOrderssRelatedByBillingCountriesId instanceof PropelCollection) {
			$this->collOrderssRelatedByBillingCountriesId->clearIterator();
		}
		$this->collOrderssRelatedByBillingCountriesId = null;
		if ($this->collOrderssRelatedByDeliveryCountriesId instanceof PropelCollection) {
			$this->collOrderssRelatedByDeliveryCountriesId->clearIterator();
		}
		$this->collOrderssRelatedByDeliveryCountriesId = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(CountriesPeer::DEFAULT_STRING_FORMAT);
	}

} // BaseCountries

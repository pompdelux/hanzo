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
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;

/**
 * Base class that represents a row from the 'addresses' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseAddresses extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\AddressesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AddressesPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the customers_id field.
	 * @var        int
	 */
	protected $customers_id;

	/**
	 * The value for the type field.
	 * Note: this column has a database default value of: 'payment'
	 * @var        string
	 */
	protected $type;

	/**
	 * The value for the address_line_1 field.
	 * @var        string
	 */
	protected $address_line_1;

	/**
	 * The value for the address_line_2 field.
	 * @var        string
	 */
	protected $address_line_2;

	/**
	 * The value for the postal_code field.
	 * @var        string
	 */
	protected $postal_code;

	/**
	 * The value for the city field.
	 * @var        string
	 */
	protected $city;

	/**
	 * The value for the country field.
	 * @var        string
	 */
	protected $country;

	/**
	 * The value for the countries_id field.
	 * @var        int
	 */
	protected $countries_id;

	/**
	 * The value for the state_province field.
	 * @var        string
	 */
	protected $state_province;

	/**
	 * The value for the company_name field.
	 * @var        string
	 */
	protected $company_name;

	/**
	 * The value for the latitude field.
	 * @var        double
	 */
	protected $latitude;

	/**
	 * The value for the longitude field.
	 * @var        double
	 */
	protected $longitude;

	/**
	 * @var        Customers
	 */
	protected $aCustomers;

	/**
	 * @var        Countries
	 */
	protected $aCountries;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->type = 'payment';
	}

	/**
	 * Initializes internal state of BaseAddresses object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [customers_id] column value.
	 * 
	 * @return     int
	 */
	public function getCustomersId()
	{
		return $this->customers_id;
	}

	/**
	 * Get the [type] column value.
	 * 
	 * @return     string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [address_line_1] column value.
	 * 
	 * @return     string
	 */
	public function getAddressLine1()
	{
		return $this->address_line_1;
	}

	/**
	 * Get the [address_line_2] column value.
	 * 
	 * @return     string
	 */
	public function getAddressLine2()
	{
		return $this->address_line_2;
	}

	/**
	 * Get the [postal_code] column value.
	 * 
	 * @return     string
	 */
	public function getPostalCode()
	{
		return $this->postal_code;
	}

	/**
	 * Get the [city] column value.
	 * 
	 * @return     string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * Get the [country] column value.
	 * 
	 * @return     string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * Get the [countries_id] column value.
	 * 
	 * @return     int
	 */
	public function getCountriesId()
	{
		return $this->countries_id;
	}

	/**
	 * Get the [state_province] column value.
	 * 
	 * @return     string
	 */
	public function getStateProvince()
	{
		return $this->state_province;
	}

	/**
	 * Get the [company_name] column value.
	 * 
	 * @return     string
	 */
	public function getCompanyName()
	{
		return $this->company_name;
	}

	/**
	 * Get the [latitude] column value.
	 * 
	 * @return     double
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * Get the [longitude] column value.
	 * 
	 * @return     double
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * Set the value of [customers_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setCustomersId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->customers_id !== $v) {
			$this->customers_id = $v;
			$this->modifiedColumns[] = AddressesPeer::CUSTOMERS_ID;
		}

		if ($this->aCustomers !== null && $this->aCustomers->getId() !== $v) {
			$this->aCustomers = null;
		}

		return $this;
	} // setCustomersId()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = AddressesPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [address_line_1] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setAddressLine1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->address_line_1 !== $v) {
			$this->address_line_1 = $v;
			$this->modifiedColumns[] = AddressesPeer::ADDRESS_LINE_1;
		}

		return $this;
	} // setAddressLine1()

	/**
	 * Set the value of [address_line_2] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setAddressLine2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->address_line_2 !== $v) {
			$this->address_line_2 = $v;
			$this->modifiedColumns[] = AddressesPeer::ADDRESS_LINE_2;
		}

		return $this;
	} // setAddressLine2()

	/**
	 * Set the value of [postal_code] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setPostalCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->postal_code !== $v) {
			$this->postal_code = $v;
			$this->modifiedColumns[] = AddressesPeer::POSTAL_CODE;
		}

		return $this;
	} // setPostalCode()

	/**
	 * Set the value of [city] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setCity($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->city !== $v) {
			$this->city = $v;
			$this->modifiedColumns[] = AddressesPeer::CITY;
		}

		return $this;
	} // setCity()

	/**
	 * Set the value of [country] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setCountry($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->country !== $v) {
			$this->country = $v;
			$this->modifiedColumns[] = AddressesPeer::COUNTRY;
		}

		return $this;
	} // setCountry()

	/**
	 * Set the value of [countries_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setCountriesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->countries_id !== $v) {
			$this->countries_id = $v;
			$this->modifiedColumns[] = AddressesPeer::COUNTRIES_ID;
		}

		if ($this->aCountries !== null && $this->aCountries->getId() !== $v) {
			$this->aCountries = null;
		}

		return $this;
	} // setCountriesId()

	/**
	 * Set the value of [state_province] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setStateProvince($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->state_province !== $v) {
			$this->state_province = $v;
			$this->modifiedColumns[] = AddressesPeer::STATE_PROVINCE;
		}

		return $this;
	} // setStateProvince()

	/**
	 * Set the value of [company_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setCompanyName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->company_name !== $v) {
			$this->company_name = $v;
			$this->modifiedColumns[] = AddressesPeer::COMPANY_NAME;
		}

		return $this;
	} // setCompanyName()

	/**
	 * Set the value of [latitude] column.
	 * 
	 * @param      double $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setLatitude($v)
	{
		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->latitude !== $v) {
			$this->latitude = $v;
			$this->modifiedColumns[] = AddressesPeer::LATITUDE;
		}

		return $this;
	} // setLatitude()

	/**
	 * Set the value of [longitude] column.
	 * 
	 * @param      double $v new value
	 * @return     Addresses The current object (for fluent API support)
	 */
	public function setLongitude($v)
	{
		if ($v !== null) {
			$v = (double) $v;
		}

		if ($this->longitude !== $v) {
			$this->longitude = $v;
			$this->modifiedColumns[] = AddressesPeer::LONGITUDE;
		}

		return $this;
	} // setLongitude()

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
			if ($this->type !== 'payment') {
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

			$this->customers_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->type = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->address_line_1 = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->address_line_2 = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->postal_code = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->city = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->country = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->countries_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->state_province = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->company_name = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->latitude = ($row[$startcol + 10] !== null) ? (double) $row[$startcol + 10] : null;
			$this->longitude = ($row[$startcol + 11] !== null) ? (double) $row[$startcol + 11] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 12; // 12 = AddressesPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Addresses object", $e);
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

		if ($this->aCustomers !== null && $this->customers_id !== $this->aCustomers->getId()) {
			$this->aCustomers = null;
		}
		if ($this->aCountries !== null && $this->countries_id !== $this->aCountries->getId()) {
			$this->aCountries = null;
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
			$con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AddressesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCustomers = null;
			$this->aCountries = null;
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
			$con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = AddressesQuery::create()
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
			$con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			// geocodable behavior
			if (!$this->isColumnModified(AddressesPeer::LATITUDE) && !$this->isColumnModified(AddressesPeer::LONGITUDE)) {
			    $this->geocode();
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
				AddressesPeer::addInstanceToPool($this);
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

			if ($this->aCustomers !== null) {
				if ($this->aCustomers->isModified() || $this->aCustomers->isNew()) {
					$affectedRows += $this->aCustomers->save($con);
				}
				$this->setCustomers($this->aCustomers);
			}

			if ($this->aCountries !== null) {
				if ($this->aCountries->isModified() || $this->aCountries->isNew()) {
					$affectedRows += $this->aCountries->save($con);
				}
				$this->setCountries($this->aCountries);
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
		if ($this->isColumnModified(AddressesPeer::CUSTOMERS_ID)) {
			$modifiedColumns[':p' . $index++]  = '`CUSTOMERS_ID`';
		}
		if ($this->isColumnModified(AddressesPeer::TYPE)) {
			$modifiedColumns[':p' . $index++]  = '`TYPE`';
		}
		if ($this->isColumnModified(AddressesPeer::ADDRESS_LINE_1)) {
			$modifiedColumns[':p' . $index++]  = '`ADDRESS_LINE_1`';
		}
		if ($this->isColumnModified(AddressesPeer::ADDRESS_LINE_2)) {
			$modifiedColumns[':p' . $index++]  = '`ADDRESS_LINE_2`';
		}
		if ($this->isColumnModified(AddressesPeer::POSTAL_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`POSTAL_CODE`';
		}
		if ($this->isColumnModified(AddressesPeer::CITY)) {
			$modifiedColumns[':p' . $index++]  = '`CITY`';
		}
		if ($this->isColumnModified(AddressesPeer::COUNTRY)) {
			$modifiedColumns[':p' . $index++]  = '`COUNTRY`';
		}
		if ($this->isColumnModified(AddressesPeer::COUNTRIES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`COUNTRIES_ID`';
		}
		if ($this->isColumnModified(AddressesPeer::STATE_PROVINCE)) {
			$modifiedColumns[':p' . $index++]  = '`STATE_PROVINCE`';
		}
		if ($this->isColumnModified(AddressesPeer::COMPANY_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`COMPANY_NAME`';
		}
		if ($this->isColumnModified(AddressesPeer::LATITUDE)) {
			$modifiedColumns[':p' . $index++]  = '`LATITUDE`';
		}
		if ($this->isColumnModified(AddressesPeer::LONGITUDE)) {
			$modifiedColumns[':p' . $index++]  = '`LONGITUDE`';
		}

		$sql = sprintf(
			'INSERT INTO `addresses` (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case '`CUSTOMERS_ID`':
						$stmt->bindValue($identifier, $this->customers_id, PDO::PARAM_INT);
						break;
					case '`TYPE`':
						$stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
						break;
					case '`ADDRESS_LINE_1`':
						$stmt->bindValue($identifier, $this->address_line_1, PDO::PARAM_STR);
						break;
					case '`ADDRESS_LINE_2`':
						$stmt->bindValue($identifier, $this->address_line_2, PDO::PARAM_STR);
						break;
					case '`POSTAL_CODE`':
						$stmt->bindValue($identifier, $this->postal_code, PDO::PARAM_STR);
						break;
					case '`CITY`':
						$stmt->bindValue($identifier, $this->city, PDO::PARAM_STR);
						break;
					case '`COUNTRY`':
						$stmt->bindValue($identifier, $this->country, PDO::PARAM_STR);
						break;
					case '`COUNTRIES_ID`':
						$stmt->bindValue($identifier, $this->countries_id, PDO::PARAM_INT);
						break;
					case '`STATE_PROVINCE`':
						$stmt->bindValue($identifier, $this->state_province, PDO::PARAM_STR);
						break;
					case '`COMPANY_NAME`':
						$stmt->bindValue($identifier, $this->company_name, PDO::PARAM_STR);
						break;
					case '`LATITUDE`':
						$stmt->bindValue($identifier, $this->latitude, PDO::PARAM_STR);
						break;
					case '`LONGITUDE`':
						$stmt->bindValue($identifier, $this->longitude, PDO::PARAM_STR);
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

			if ($this->aCustomers !== null) {
				if (!$this->aCustomers->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCustomers->getValidationFailures());
				}
			}

			if ($this->aCountries !== null) {
				if (!$this->aCountries->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCountries->getValidationFailures());
				}
			}


			if (($retval = AddressesPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
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
		$pos = AddressesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCustomersId();
				break;
			case 1:
				return $this->getType();
				break;
			case 2:
				return $this->getAddressLine1();
				break;
			case 3:
				return $this->getAddressLine2();
				break;
			case 4:
				return $this->getPostalCode();
				break;
			case 5:
				return $this->getCity();
				break;
			case 6:
				return $this->getCountry();
				break;
			case 7:
				return $this->getCountriesId();
				break;
			case 8:
				return $this->getStateProvince();
				break;
			case 9:
				return $this->getCompanyName();
				break;
			case 10:
				return $this->getLatitude();
				break;
			case 11:
				return $this->getLongitude();
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
		if (isset($alreadyDumpedObjects['Addresses'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Addresses'][serialize($this->getPrimaryKey())] = true;
		$keys = AddressesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getCustomersId(),
			$keys[1] => $this->getType(),
			$keys[2] => $this->getAddressLine1(),
			$keys[3] => $this->getAddressLine2(),
			$keys[4] => $this->getPostalCode(),
			$keys[5] => $this->getCity(),
			$keys[6] => $this->getCountry(),
			$keys[7] => $this->getCountriesId(),
			$keys[8] => $this->getStateProvince(),
			$keys[9] => $this->getCompanyName(),
			$keys[10] => $this->getLatitude(),
			$keys[11] => $this->getLongitude(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCustomers) {
				$result['Customers'] = $this->aCustomers->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aCountries) {
				$result['Countries'] = $this->aCountries->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = AddressesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCustomersId($value);
				break;
			case 1:
				$this->setType($value);
				break;
			case 2:
				$this->setAddressLine1($value);
				break;
			case 3:
				$this->setAddressLine2($value);
				break;
			case 4:
				$this->setPostalCode($value);
				break;
			case 5:
				$this->setCity($value);
				break;
			case 6:
				$this->setCountry($value);
				break;
			case 7:
				$this->setCountriesId($value);
				break;
			case 8:
				$this->setStateProvince($value);
				break;
			case 9:
				$this->setCompanyName($value);
				break;
			case 10:
				$this->setLatitude($value);
				break;
			case 11:
				$this->setLongitude($value);
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
		$keys = AddressesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setCustomersId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setType($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAddressLine1($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setAddressLine2($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPostalCode($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCity($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setCountry($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCountriesId($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStateProvince($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCompanyName($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setLatitude($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setLongitude($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AddressesPeer::DATABASE_NAME);

		if ($this->isColumnModified(AddressesPeer::CUSTOMERS_ID)) $criteria->add(AddressesPeer::CUSTOMERS_ID, $this->customers_id);
		if ($this->isColumnModified(AddressesPeer::TYPE)) $criteria->add(AddressesPeer::TYPE, $this->type);
		if ($this->isColumnModified(AddressesPeer::ADDRESS_LINE_1)) $criteria->add(AddressesPeer::ADDRESS_LINE_1, $this->address_line_1);
		if ($this->isColumnModified(AddressesPeer::ADDRESS_LINE_2)) $criteria->add(AddressesPeer::ADDRESS_LINE_2, $this->address_line_2);
		if ($this->isColumnModified(AddressesPeer::POSTAL_CODE)) $criteria->add(AddressesPeer::POSTAL_CODE, $this->postal_code);
		if ($this->isColumnModified(AddressesPeer::CITY)) $criteria->add(AddressesPeer::CITY, $this->city);
		if ($this->isColumnModified(AddressesPeer::COUNTRY)) $criteria->add(AddressesPeer::COUNTRY, $this->country);
		if ($this->isColumnModified(AddressesPeer::COUNTRIES_ID)) $criteria->add(AddressesPeer::COUNTRIES_ID, $this->countries_id);
		if ($this->isColumnModified(AddressesPeer::STATE_PROVINCE)) $criteria->add(AddressesPeer::STATE_PROVINCE, $this->state_province);
		if ($this->isColumnModified(AddressesPeer::COMPANY_NAME)) $criteria->add(AddressesPeer::COMPANY_NAME, $this->company_name);
		if ($this->isColumnModified(AddressesPeer::LATITUDE)) $criteria->add(AddressesPeer::LATITUDE, $this->latitude);
		if ($this->isColumnModified(AddressesPeer::LONGITUDE)) $criteria->add(AddressesPeer::LONGITUDE, $this->longitude);

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
		$criteria = new Criteria(AddressesPeer::DATABASE_NAME);
		$criteria->add(AddressesPeer::CUSTOMERS_ID, $this->customers_id);
		$criteria->add(AddressesPeer::TYPE, $this->type);

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
		$pks[0] = $this->getCustomersId();
		$pks[1] = $this->getType();

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
		$this->setCustomersId($keys[0]);
		$this->setType($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getCustomersId()) && (null === $this->getType());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Addresses (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setCustomersId($this->getCustomersId());
		$copyObj->setType($this->getType());
		$copyObj->setAddressLine1($this->getAddressLine1());
		$copyObj->setAddressLine2($this->getAddressLine2());
		$copyObj->setPostalCode($this->getPostalCode());
		$copyObj->setCity($this->getCity());
		$copyObj->setCountry($this->getCountry());
		$copyObj->setCountriesId($this->getCountriesId());
		$copyObj->setStateProvince($this->getStateProvince());
		$copyObj->setCompanyName($this->getCompanyName());
		$copyObj->setLatitude($this->getLatitude());
		$copyObj->setLongitude($this->getLongitude());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

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
	 * @return     Addresses Clone of current object.
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
	 * @return     AddressesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AddressesPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Customers object.
	 *
	 * @param      Customers $v
	 * @return     Addresses The current object (for fluent API support)
	 * @throws     PropelException
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
			$v->addAddresses($this);
		}

		return $this;
	}


	/**
	 * Get the associated Customers object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Customers The associated Customers object.
	 * @throws     PropelException
	 */
	public function getCustomers(PropelPDO $con = null)
	{
		if ($this->aCustomers === null && ($this->customers_id !== null)) {
			$this->aCustomers = CustomersQuery::create()->findPk($this->customers_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCustomers->addAddressess($this);
			 */
		}
		return $this->aCustomers;
	}

	/**
	 * Declares an association between this object and a Countries object.
	 *
	 * @param      Countries $v
	 * @return     Addresses The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCountries(Countries $v = null)
	{
		if ($v === null) {
			$this->setCountriesId(NULL);
		} else {
			$this->setCountriesId($v->getId());
		}

		$this->aCountries = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Countries object, it will not be re-added.
		if ($v !== null) {
			$v->addAddresses($this);
		}

		return $this;
	}


	/**
	 * Get the associated Countries object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Countries The associated Countries object.
	 * @throws     PropelException
	 */
	public function getCountries(PropelPDO $con = null)
	{
		if ($this->aCountries === null && ($this->countries_id !== null)) {
			$this->aCountries = CountriesQuery::create()->findPk($this->countries_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCountries->addAddressess($this);
			 */
		}
		return $this->aCountries;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->customers_id = null;
		$this->type = null;
		$this->address_line_1 = null;
		$this->address_line_2 = null;
		$this->postal_code = null;
		$this->city = null;
		$this->country = null;
		$this->countries_id = null;
		$this->state_province = null;
		$this->company_name = null;
		$this->latitude = null;
		$this->longitude = null;
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
		} // if ($deep)

		$this->aCustomers = null;
		$this->aCountries = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AddressesPeer::DEFAULT_STRING_FORMAT);
	}

	// geocodable behavior
	/**
	 * Convenient method to set latitude and longitude values.
	 *
	 * @param double $latitude     A latitude value.
	 * @param double $longitude    A longitude value.
	 */
	public function setCoordinates($latitude, $longitude)
	{
	    $this->setLatitude($latitude);
	    $this->setLongitude($longitude);
	}
	
	/**
	 * Returns an array with latitude and longitude values.
	 *
	 * @return array
	 */
	public function getCoordinates()
	{
	    return array(
	        'latitude' => $this->getLatitude(),
	        'longitude' => $this->getLongitude()
	    );
	}
	
	/**
	 * Returns whether this object has been geocoded or not.
	 *
	 * @return Boolean
	 */
	public function isGeocoded()
	{
	    $lat = $this->getLatitude();
	    $lng = $this->getLongitude();
	
	    return (!empty($lat) && !empty($lng));
	}
	
	/**
	 * Calculates the distance between a given addresses and this one.
	 *
	 * @param Addresses $addresses    A Addresses object.
	 * @param $unit    The unit measure.
	 *
	 * @return double   The distance between the two objects.
	 */
	public function getDistanceTo(Addresses $addresses, $unit = AddressesPeer::KILOMETERS_UNIT)
	{
	    $dist = rad2deg(acos(sin(deg2rad($this->getLatitude())) * sin(deg2rad($addresses->getLatitude())) +  cos(deg2rad($this->getLatitude())) * cos(deg2rad($addresses->getLatitude())) * cos(deg2rad($this->getLongitude() - $addresses->getLongitude())))) * 60 * AddressesPeer::MILES_UNIT;
	
	    if (AddressesPeer::MILES_UNIT === $unit) {
	        return $dist;
	    } else if (AddressesPeer::NAUTICAL_MILES_UNIT === $unit) {
	        return $dist * AddressesPeer::NAUTICAL_MILES_UNIT;
	    }
	
	    return $dist * AddressesPeer::KILOMETERS_UNIT;
	}
	
	/**
	 * update geocode information
	 *
	 * @retrun Addresses
	 */
	public function geocode()
	{
	    $geocoder = new \Geocoder\Geocoder(new \Geocoder\Provider\YahooProvider(new \Geocoder\HttpAdapter\CurlHttpAdapter()));
	    if (isset($result) && $coordinates = $result->getCoordinates()) {
	        $this->setLatitude($coordinates[0]);
	        $this->setLongitude($coordinates[1]);
	    }
	
	}

} // BaseAddresses

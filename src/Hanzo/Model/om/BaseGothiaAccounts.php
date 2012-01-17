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
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\GothiaAccountsPeer;
use Hanzo\Model\GothiaAccountsQuery;

/**
 * Base class that represents a row from the 'gothia_accounts' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseGothiaAccounts extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\GothiaAccountsPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        GothiaAccountsPeer
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
	 * The value for the first_name field.
	 * @var        string
	 */
	protected $first_name;

	/**
	 * The value for the last_name field.
	 * @var        string
	 */
	protected $last_name;

	/**
	 * The value for the address field.
	 * @var        string
	 */
	protected $address;

	/**
	 * The value for the postal_code field.
	 * @var        string
	 */
	protected $postal_code;

	/**
	 * The value for the postal_place field.
	 * @var        string
	 */
	protected $postal_place;

	/**
	 * The value for the email field.
	 * @var        string
	 */
	protected $email;

	/**
	 * The value for the phone field.
	 * @var        string
	 */
	protected $phone;

	/**
	 * The value for the mobile_phone field.
	 * @var        string
	 */
	protected $mobile_phone;

	/**
	 * The value for the fax field.
	 * @var        string
	 */
	protected $fax;

	/**
	 * The value for the country_code field.
	 * @var        string
	 */
	protected $country_code;

	/**
	 * The value for the distribution_by field.
	 * @var        string
	 */
	protected $distribution_by;

	/**
	 * The value for the distribution_type field.
	 * @var        string
	 */
	protected $distribution_type;

	/**
	 * The value for the social_security_num field.
	 * @var        string
	 */
	protected $social_security_num;

	/**
	 * @var        Customers
	 */
	protected $aCustomers;

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
	 * Get the [customers_id] column value.
	 * 
	 * @return     int
	 */
	public function getCustomersId()
	{
		return $this->customers_id;
	}

	/**
	 * Get the [first_name] column value.
	 * 
	 * @return     string
	 */
	public function getFirstName()
	{
		return $this->first_name;
	}

	/**
	 * Get the [last_name] column value.
	 * 
	 * @return     string
	 */
	public function getLastName()
	{
		return $this->last_name;
	}

	/**
	 * Get the [address] column value.
	 * 
	 * @return     string
	 */
	public function getAddress()
	{
		return $this->address;
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
	 * Get the [postal_place] column value.
	 * 
	 * @return     string
	 */
	public function getPostalPlace()
	{
		return $this->postal_place;
	}

	/**
	 * Get the [email] column value.
	 * 
	 * @return     string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the [phone] column value.
	 * 
	 * @return     string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * Get the [mobile_phone] column value.
	 * 
	 * @return     string
	 */
	public function getMobilePhone()
	{
		return $this->mobile_phone;
	}

	/**
	 * Get the [fax] column value.
	 * 
	 * @return     string
	 */
	public function getFax()
	{
		return $this->fax;
	}

	/**
	 * Get the [country_code] column value.
	 * 
	 * @return     string
	 */
	public function getCountryCode()
	{
		return $this->country_code;
	}

	/**
	 * Get the [distribution_by] column value.
	 * 
	 * @return     string
	 */
	public function getDistributionBy()
	{
		return $this->distribution_by;
	}

	/**
	 * Get the [distribution_type] column value.
	 * 
	 * @return     string
	 */
	public function getDistributionType()
	{
		return $this->distribution_type;
	}

	/**
	 * Get the [social_security_num] column value.
	 * 
	 * @return     string
	 */
	public function getSocialSecurityNum()
	{
		return $this->social_security_num;
	}

	/**
	 * Set the value of [customers_id] column.
	 * 
	 * @param      int $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setCustomersId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->customers_id !== $v) {
			$this->customers_id = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::CUSTOMERS_ID;
		}

		if ($this->aCustomers !== null && $this->aCustomers->getId() !== $v) {
			$this->aCustomers = null;
		}

		return $this;
	} // setCustomersId()

	/**
	 * Set the value of [first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setFirstName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->first_name !== $v) {
			$this->first_name = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::FIRST_NAME;
		}

		return $this;
	} // setFirstName()

	/**
	 * Set the value of [last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setLastName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->last_name !== $v) {
			$this->last_name = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::LAST_NAME;
		}

		return $this;
	} // setLastName()

	/**
	 * Set the value of [address] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setAddress($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->address !== $v) {
			$this->address = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::ADDRESS;
		}

		return $this;
	} // setAddress()

	/**
	 * Set the value of [postal_code] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setPostalCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->postal_code !== $v) {
			$this->postal_code = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::POSTAL_CODE;
		}

		return $this;
	} // setPostalCode()

	/**
	 * Set the value of [postal_place] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setPostalPlace($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->postal_place !== $v) {
			$this->postal_place = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::POSTAL_PLACE;
		}

		return $this;
	} // setPostalPlace()

	/**
	 * Set the value of [email] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [phone] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setPhone($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->phone !== $v) {
			$this->phone = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::PHONE;
		}

		return $this;
	} // setPhone()

	/**
	 * Set the value of [mobile_phone] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setMobilePhone($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mobile_phone !== $v) {
			$this->mobile_phone = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::MOBILE_PHONE;
		}

		return $this;
	} // setMobilePhone()

	/**
	 * Set the value of [fax] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setFax($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->fax !== $v) {
			$this->fax = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::FAX;
		}

		return $this;
	} // setFax()

	/**
	 * Set the value of [country_code] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setCountryCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->country_code !== $v) {
			$this->country_code = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::COUNTRY_CODE;
		}

		return $this;
	} // setCountryCode()

	/**
	 * Set the value of [distribution_by] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setDistributionBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->distribution_by !== $v) {
			$this->distribution_by = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::DISTRIBUTION_BY;
		}

		return $this;
	} // setDistributionBy()

	/**
	 * Set the value of [distribution_type] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setDistributionType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->distribution_type !== $v) {
			$this->distribution_type = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::DISTRIBUTION_TYPE;
		}

		return $this;
	} // setDistributionType()

	/**
	 * Set the value of [social_security_num] column.
	 * 
	 * @param      string $v new value
	 * @return     GothiaAccounts The current object (for fluent API support)
	 */
	public function setSocialSecurityNum($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->social_security_num !== $v) {
			$this->social_security_num = $v;
			$this->modifiedColumns[] = GothiaAccountsPeer::SOCIAL_SECURITY_NUM;
		}

		return $this;
	} // setSocialSecurityNum()

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

			$this->customers_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->first_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->last_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->address = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->postal_code = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->postal_place = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->email = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->phone = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->mobile_phone = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->fax = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->country_code = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->distribution_by = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->distribution_type = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->social_security_num = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 14; // 14 = GothiaAccountsPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating GothiaAccounts object", $e);
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
			$con = Propel::getConnection(GothiaAccountsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = GothiaAccountsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCustomers = null;
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
			$con = Propel::getConnection(GothiaAccountsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = GothiaAccountsQuery::create()
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
			$con = Propel::getConnection(GothiaAccountsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				GothiaAccountsPeer::addInstanceToPool($this);
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
		if ($this->isColumnModified(GothiaAccountsPeer::CUSTOMERS_ID)) {
			$modifiedColumns[':p' . $index++]  = '`CUSTOMERS_ID`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::FIRST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`FIRST_NAME`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::LAST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`LAST_NAME`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::ADDRESS)) {
			$modifiedColumns[':p' . $index++]  = '`ADDRESS`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::POSTAL_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`POSTAL_CODE`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::POSTAL_PLACE)) {
			$modifiedColumns[':p' . $index++]  = '`POSTAL_PLACE`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::EMAIL)) {
			$modifiedColumns[':p' . $index++]  = '`EMAIL`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::PHONE)) {
			$modifiedColumns[':p' . $index++]  = '`PHONE`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::MOBILE_PHONE)) {
			$modifiedColumns[':p' . $index++]  = '`MOBILE_PHONE`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::FAX)) {
			$modifiedColumns[':p' . $index++]  = '`FAX`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::COUNTRY_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`COUNTRY_CODE`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::DISTRIBUTION_BY)) {
			$modifiedColumns[':p' . $index++]  = '`DISTRIBUTION_BY`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::DISTRIBUTION_TYPE)) {
			$modifiedColumns[':p' . $index++]  = '`DISTRIBUTION_TYPE`';
		}
		if ($this->isColumnModified(GothiaAccountsPeer::SOCIAL_SECURITY_NUM)) {
			$modifiedColumns[':p' . $index++]  = '`SOCIAL_SECURITY_NUM`';
		}

		$sql = sprintf(
			'INSERT INTO `gothia_accounts` (%s) VALUES (%s)',
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
					case '`FIRST_NAME`':
						$stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
						break;
					case '`LAST_NAME`':
						$stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
						break;
					case '`ADDRESS`':
						$stmt->bindValue($identifier, $this->address, PDO::PARAM_STR);
						break;
					case '`POSTAL_CODE`':
						$stmt->bindValue($identifier, $this->postal_code, PDO::PARAM_STR);
						break;
					case '`POSTAL_PLACE`':
						$stmt->bindValue($identifier, $this->postal_place, PDO::PARAM_STR);
						break;
					case '`EMAIL`':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
						break;
					case '`PHONE`':
						$stmt->bindValue($identifier, $this->phone, PDO::PARAM_STR);
						break;
					case '`MOBILE_PHONE`':
						$stmt->bindValue($identifier, $this->mobile_phone, PDO::PARAM_STR);
						break;
					case '`FAX`':
						$stmt->bindValue($identifier, $this->fax, PDO::PARAM_STR);
						break;
					case '`COUNTRY_CODE`':
						$stmt->bindValue($identifier, $this->country_code, PDO::PARAM_STR);
						break;
					case '`DISTRIBUTION_BY`':
						$stmt->bindValue($identifier, $this->distribution_by, PDO::PARAM_STR);
						break;
					case '`DISTRIBUTION_TYPE`':
						$stmt->bindValue($identifier, $this->distribution_type, PDO::PARAM_STR);
						break;
					case '`SOCIAL_SECURITY_NUM`':
						$stmt->bindValue($identifier, $this->social_security_num, PDO::PARAM_STR);
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


			if (($retval = GothiaAccountsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = GothiaAccountsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFirstName();
				break;
			case 2:
				return $this->getLastName();
				break;
			case 3:
				return $this->getAddress();
				break;
			case 4:
				return $this->getPostalCode();
				break;
			case 5:
				return $this->getPostalPlace();
				break;
			case 6:
				return $this->getEmail();
				break;
			case 7:
				return $this->getPhone();
				break;
			case 8:
				return $this->getMobilePhone();
				break;
			case 9:
				return $this->getFax();
				break;
			case 10:
				return $this->getCountryCode();
				break;
			case 11:
				return $this->getDistributionBy();
				break;
			case 12:
				return $this->getDistributionType();
				break;
			case 13:
				return $this->getSocialSecurityNum();
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
		if (isset($alreadyDumpedObjects['GothiaAccounts'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['GothiaAccounts'][$this->getPrimaryKey()] = true;
		$keys = GothiaAccountsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getCustomersId(),
			$keys[1] => $this->getFirstName(),
			$keys[2] => $this->getLastName(),
			$keys[3] => $this->getAddress(),
			$keys[4] => $this->getPostalCode(),
			$keys[5] => $this->getPostalPlace(),
			$keys[6] => $this->getEmail(),
			$keys[7] => $this->getPhone(),
			$keys[8] => $this->getMobilePhone(),
			$keys[9] => $this->getFax(),
			$keys[10] => $this->getCountryCode(),
			$keys[11] => $this->getDistributionBy(),
			$keys[12] => $this->getDistributionType(),
			$keys[13] => $this->getSocialSecurityNum(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCustomers) {
				$result['Customers'] = $this->aCustomers->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = GothiaAccountsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFirstName($value);
				break;
			case 2:
				$this->setLastName($value);
				break;
			case 3:
				$this->setAddress($value);
				break;
			case 4:
				$this->setPostalCode($value);
				break;
			case 5:
				$this->setPostalPlace($value);
				break;
			case 6:
				$this->setEmail($value);
				break;
			case 7:
				$this->setPhone($value);
				break;
			case 8:
				$this->setMobilePhone($value);
				break;
			case 9:
				$this->setFax($value);
				break;
			case 10:
				$this->setCountryCode($value);
				break;
			case 11:
				$this->setDistributionBy($value);
				break;
			case 12:
				$this->setDistributionType($value);
				break;
			case 13:
				$this->setSocialSecurityNum($value);
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
		$keys = GothiaAccountsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setCustomersId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFirstName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setLastName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setAddress($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPostalCode($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPostalPlace($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setEmail($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setPhone($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMobilePhone($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setFax($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setCountryCode($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDistributionBy($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setDistributionType($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setSocialSecurityNum($arr[$keys[13]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(GothiaAccountsPeer::DATABASE_NAME);

		if ($this->isColumnModified(GothiaAccountsPeer::CUSTOMERS_ID)) $criteria->add(GothiaAccountsPeer::CUSTOMERS_ID, $this->customers_id);
		if ($this->isColumnModified(GothiaAccountsPeer::FIRST_NAME)) $criteria->add(GothiaAccountsPeer::FIRST_NAME, $this->first_name);
		if ($this->isColumnModified(GothiaAccountsPeer::LAST_NAME)) $criteria->add(GothiaAccountsPeer::LAST_NAME, $this->last_name);
		if ($this->isColumnModified(GothiaAccountsPeer::ADDRESS)) $criteria->add(GothiaAccountsPeer::ADDRESS, $this->address);
		if ($this->isColumnModified(GothiaAccountsPeer::POSTAL_CODE)) $criteria->add(GothiaAccountsPeer::POSTAL_CODE, $this->postal_code);
		if ($this->isColumnModified(GothiaAccountsPeer::POSTAL_PLACE)) $criteria->add(GothiaAccountsPeer::POSTAL_PLACE, $this->postal_place);
		if ($this->isColumnModified(GothiaAccountsPeer::EMAIL)) $criteria->add(GothiaAccountsPeer::EMAIL, $this->email);
		if ($this->isColumnModified(GothiaAccountsPeer::PHONE)) $criteria->add(GothiaAccountsPeer::PHONE, $this->phone);
		if ($this->isColumnModified(GothiaAccountsPeer::MOBILE_PHONE)) $criteria->add(GothiaAccountsPeer::MOBILE_PHONE, $this->mobile_phone);
		if ($this->isColumnModified(GothiaAccountsPeer::FAX)) $criteria->add(GothiaAccountsPeer::FAX, $this->fax);
		if ($this->isColumnModified(GothiaAccountsPeer::COUNTRY_CODE)) $criteria->add(GothiaAccountsPeer::COUNTRY_CODE, $this->country_code);
		if ($this->isColumnModified(GothiaAccountsPeer::DISTRIBUTION_BY)) $criteria->add(GothiaAccountsPeer::DISTRIBUTION_BY, $this->distribution_by);
		if ($this->isColumnModified(GothiaAccountsPeer::DISTRIBUTION_TYPE)) $criteria->add(GothiaAccountsPeer::DISTRIBUTION_TYPE, $this->distribution_type);
		if ($this->isColumnModified(GothiaAccountsPeer::SOCIAL_SECURITY_NUM)) $criteria->add(GothiaAccountsPeer::SOCIAL_SECURITY_NUM, $this->social_security_num);

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
		$criteria = new Criteria(GothiaAccountsPeer::DATABASE_NAME);
		$criteria->add(GothiaAccountsPeer::CUSTOMERS_ID, $this->customers_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getCustomersId();
	}

	/**
	 * Generic method to set the primary key (customers_id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setCustomersId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getCustomersId();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of GothiaAccounts (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setFirstName($this->getFirstName());
		$copyObj->setLastName($this->getLastName());
		$copyObj->setAddress($this->getAddress());
		$copyObj->setPostalCode($this->getPostalCode());
		$copyObj->setPostalPlace($this->getPostalPlace());
		$copyObj->setEmail($this->getEmail());
		$copyObj->setPhone($this->getPhone());
		$copyObj->setMobilePhone($this->getMobilePhone());
		$copyObj->setFax($this->getFax());
		$copyObj->setCountryCode($this->getCountryCode());
		$copyObj->setDistributionBy($this->getDistributionBy());
		$copyObj->setDistributionType($this->getDistributionType());
		$copyObj->setSocialSecurityNum($this->getSocialSecurityNum());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			$relObj = $this->getCustomers();
			if ($relObj) {
				$copyObj->setCustomers($relObj->copy($deepCopy));
			}

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setCustomersId(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     GothiaAccounts Clone of current object.
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
	 * @return     GothiaAccountsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new GothiaAccountsPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Customers object.
	 *
	 * @param      Customers $v
	 * @return     GothiaAccounts The current object (for fluent API support)
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

		// Add binding for other direction of this 1:1 relationship.
		if ($v !== null) {
			$v->setGothiaAccounts($this);
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
			// Because this foreign key represents a one-to-one relationship, we will create a bi-directional association.
			$this->aCustomers->setGothiaAccounts($this);
		}
		return $this->aCustomers;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->customers_id = null;
		$this->first_name = null;
		$this->last_name = null;
		$this->address = null;
		$this->postal_code = null;
		$this->postal_place = null;
		$this->email = null;
		$this->phone = null;
		$this->mobile_phone = null;
		$this->fax = null;
		$this->country_code = null;
		$this->distribution_by = null;
		$this->distribution_type = null;
		$this->social_security_num = null;
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
		} // if ($deep)

		$this->aCustomers = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(GothiaAccountsPeer::DEFAULT_STRING_FORMAT);
	}

} // BaseGothiaAccounts

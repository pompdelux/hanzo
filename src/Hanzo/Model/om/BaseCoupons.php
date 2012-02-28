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
use Hanzo\Model\CouponsPeer;
use Hanzo\Model\CouponsQuery;
use Hanzo\Model\CouponsToCustomers;
use Hanzo\Model\CouponsToCustomersQuery;

/**
 * Base class that represents a row from the 'coupons' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCoupons extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\CouponsPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CouponsPeer
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
	 * The value for the code field.
	 * @var        string
	 */
	protected $code;

	/**
	 * The value for the amount field.
	 * @var        string
	 */
	protected $amount;

	/**
	 * The value for the vat field.
	 * @var        string
	 */
	protected $vat;

	/**
	 * The value for the currency_id field.
	 * @var        int
	 */
	protected $currency_id;

	/**
	 * The value for the uses_pr_coupon field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $uses_pr_coupon;

	/**
	 * The value for the uses_pr_coustomer field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $uses_pr_coustomer;

	/**
	 * The value for the active_from field.
	 * @var        string
	 */
	protected $active_from;

	/**
	 * The value for the active_to field.
	 * @var        string
	 */
	protected $active_to;

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
	 * @var        array CouponsToCustomers[] Collection to store aggregation of CouponsToCustomers objects.
	 */
	protected $collCouponsToCustomerss;

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
	protected $couponsToCustomerssScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->uses_pr_coupon = 1;
		$this->uses_pr_coustomer = 1;
	}

	/**
	 * Initializes internal state of BaseCoupons object.
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
	 * Get the [code] column value.
	 * 
	 * @return     string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Get the [amount] column value.
	 * 
	 * @return     string
	 */
	public function getAmount()
	{
		return $this->amount;
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
	 * Get the [currency_id] column value.
	 * 
	 * @return     int
	 */
	public function getCurrencyId()
	{
		return $this->currency_id;
	}

	/**
	 * Get the [uses_pr_coupon] column value.
	 * 
	 * @return     int
	 */
	public function getUsesPrCoupon()
	{
		return $this->uses_pr_coupon;
	}

	/**
	 * Get the [uses_pr_coustomer] column value.
	 * 
	 * @return     int
	 */
	public function getUsesPrCoustomer()
	{
		return $this->uses_pr_coustomer;
	}

	/**
	 * Get the [optionally formatted] temporal [active_from] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getActiveFrom($format = 'Y-m-d H:i:s')
	{
		if ($this->active_from === null) {
			return null;
		}


		if ($this->active_from === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->active_from);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->active_from, true), $x);
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
	 * Get the [optionally formatted] temporal [active_to] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getActiveTo($format = 'Y-m-d H:i:s')
	{
		if ($this->active_to === null) {
			return null;
		}


		if ($this->active_to === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->active_to);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->active_to, true), $x);
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
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CouponsPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [code] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->code !== $v) {
			$this->code = $v;
			$this->modifiedColumns[] = CouponsPeer::CODE;
		}

		return $this;
	} // setCode()

	/**
	 * Set the value of [amount] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setAmount($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->amount !== $v) {
			$this->amount = $v;
			$this->modifiedColumns[] = CouponsPeer::AMOUNT;
		}

		return $this;
	} // setAmount()

	/**
	 * Set the value of [vat] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setVat($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->vat !== $v) {
			$this->vat = $v;
			$this->modifiedColumns[] = CouponsPeer::VAT;
		}

		return $this;
	} // setVat()

	/**
	 * Set the value of [currency_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setCurrencyId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->currency_id !== $v) {
			$this->currency_id = $v;
			$this->modifiedColumns[] = CouponsPeer::CURRENCY_ID;
		}

		return $this;
	} // setCurrencyId()

	/**
	 * Set the value of [uses_pr_coupon] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setUsesPrCoupon($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->uses_pr_coupon !== $v) {
			$this->uses_pr_coupon = $v;
			$this->modifiedColumns[] = CouponsPeer::USES_PR_COUPON;
		}

		return $this;
	} // setUsesPrCoupon()

	/**
	 * Set the value of [uses_pr_coustomer] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setUsesPrCoustomer($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->uses_pr_coustomer !== $v) {
			$this->uses_pr_coustomer = $v;
			$this->modifiedColumns[] = CouponsPeer::USES_PR_COUSTOMER;
		}

		return $this;
	} // setUsesPrCoustomer()

	/**
	 * Sets the value of [active_from] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setActiveFrom($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->active_from !== null || $dt !== null) {
			$currentDateAsString = ($this->active_from !== null && $tmpDt = new DateTime($this->active_from)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->active_from = $newDateAsString;
				$this->modifiedColumns[] = CouponsPeer::ACTIVE_FROM;
			}
		} // if either are not null

		return $this;
	} // setActiveFrom()

	/**
	 * Sets the value of [active_to] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setActiveTo($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->active_to !== null || $dt !== null) {
			$currentDateAsString = ($this->active_to !== null && $tmpDt = new DateTime($this->active_to)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->active_to = $newDateAsString;
				$this->modifiedColumns[] = CouponsPeer::ACTIVE_TO;
			}
		} // if either are not null

		return $this;
	} // setActiveTo()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = CouponsPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = CouponsPeer::UPDATED_AT;
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
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->uses_pr_coupon !== 1) {
				return false;
			}

			if ($this->uses_pr_coustomer !== 1) {
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
			$this->code = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->amount = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->vat = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->currency_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->uses_pr_coupon = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->uses_pr_coustomer = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->active_from = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->active_to = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->created_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->updated_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 11; // 11 = CouponsPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Coupons object", $e);
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
			$con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CouponsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collCouponsToCustomerss = null;

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
			$con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = CouponsQuery::create()
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
			$con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(CouponsPeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(CouponsPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(CouponsPeer::UPDATED_AT)) {
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
				CouponsPeer::addInstanceToPool($this);
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

			if ($this->couponsToCustomerssScheduledForDeletion !== null) {
				if (!$this->couponsToCustomerssScheduledForDeletion->isEmpty()) {
					CouponsToCustomersQuery::create()
						->filterByPrimaryKeys($this->couponsToCustomerssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->couponsToCustomerssScheduledForDeletion = null;
				}
			}

			if ($this->collCouponsToCustomerss !== null) {
				foreach ($this->collCouponsToCustomerss as $referrerFK) {
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

		$this->modifiedColumns[] = CouponsPeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . CouponsPeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(CouponsPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = '`ID`';
		}
		if ($this->isColumnModified(CouponsPeer::CODE)) {
			$modifiedColumns[':p' . $index++]  = '`CODE`';
		}
		if ($this->isColumnModified(CouponsPeer::AMOUNT)) {
			$modifiedColumns[':p' . $index++]  = '`AMOUNT`';
		}
		if ($this->isColumnModified(CouponsPeer::VAT)) {
			$modifiedColumns[':p' . $index++]  = '`VAT`';
		}
		if ($this->isColumnModified(CouponsPeer::CURRENCY_ID)) {
			$modifiedColumns[':p' . $index++]  = '`CURRENCY_ID`';
		}
		if ($this->isColumnModified(CouponsPeer::USES_PR_COUPON)) {
			$modifiedColumns[':p' . $index++]  = '`USES_PR_COUPON`';
		}
		if ($this->isColumnModified(CouponsPeer::USES_PR_COUSTOMER)) {
			$modifiedColumns[':p' . $index++]  = '`USES_PR_COUSTOMER`';
		}
		if ($this->isColumnModified(CouponsPeer::ACTIVE_FROM)) {
			$modifiedColumns[':p' . $index++]  = '`ACTIVE_FROM`';
		}
		if ($this->isColumnModified(CouponsPeer::ACTIVE_TO)) {
			$modifiedColumns[':p' . $index++]  = '`ACTIVE_TO`';
		}
		if ($this->isColumnModified(CouponsPeer::CREATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`CREATED_AT`';
		}
		if ($this->isColumnModified(CouponsPeer::UPDATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`UPDATED_AT`';
		}

		$sql = sprintf(
			'INSERT INTO `coupons` (%s) VALUES (%s)',
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
					case '`CODE`':
						$stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
						break;
					case '`AMOUNT`':
						$stmt->bindValue($identifier, $this->amount, PDO::PARAM_STR);
						break;
					case '`VAT`':
						$stmt->bindValue($identifier, $this->vat, PDO::PARAM_STR);
						break;
					case '`CURRENCY_ID`':
						$stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
						break;
					case '`USES_PR_COUPON`':
						$stmt->bindValue($identifier, $this->uses_pr_coupon, PDO::PARAM_INT);
						break;
					case '`USES_PR_COUSTOMER`':
						$stmt->bindValue($identifier, $this->uses_pr_coustomer, PDO::PARAM_INT);
						break;
					case '`ACTIVE_FROM`':
						$stmt->bindValue($identifier, $this->active_from, PDO::PARAM_STR);
						break;
					case '`ACTIVE_TO`':
						$stmt->bindValue($identifier, $this->active_to, PDO::PARAM_STR);
						break;
					case '`CREATED_AT`':
						$stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
						break;
					case '`UPDATED_AT`':
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


			if (($retval = CouponsPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCouponsToCustomerss !== null) {
					foreach ($this->collCouponsToCustomerss as $referrerFK) {
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
		$pos = CouponsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCode();
				break;
			case 2:
				return $this->getAmount();
				break;
			case 3:
				return $this->getVat();
				break;
			case 4:
				return $this->getCurrencyId();
				break;
			case 5:
				return $this->getUsesPrCoupon();
				break;
			case 6:
				return $this->getUsesPrCoustomer();
				break;
			case 7:
				return $this->getActiveFrom();
				break;
			case 8:
				return $this->getActiveTo();
				break;
			case 9:
				return $this->getCreatedAt();
				break;
			case 10:
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
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		if (isset($alreadyDumpedObjects['Coupons'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Coupons'][$this->getPrimaryKey()] = true;
		$keys = CouponsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCode(),
			$keys[2] => $this->getAmount(),
			$keys[3] => $this->getVat(),
			$keys[4] => $this->getCurrencyId(),
			$keys[5] => $this->getUsesPrCoupon(),
			$keys[6] => $this->getUsesPrCoustomer(),
			$keys[7] => $this->getActiveFrom(),
			$keys[8] => $this->getActiveTo(),
			$keys[9] => $this->getCreatedAt(),
			$keys[10] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collCouponsToCustomerss) {
				$result['CouponsToCustomerss'] = $this->collCouponsToCustomerss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = CouponsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCode($value);
				break;
			case 2:
				$this->setAmount($value);
				break;
			case 3:
				$this->setVat($value);
				break;
			case 4:
				$this->setCurrencyId($value);
				break;
			case 5:
				$this->setUsesPrCoupon($value);
				break;
			case 6:
				$this->setUsesPrCoustomer($value);
				break;
			case 7:
				$this->setActiveFrom($value);
				break;
			case 8:
				$this->setActiveTo($value);
				break;
			case 9:
				$this->setCreatedAt($value);
				break;
			case 10:
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
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = CouponsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCode($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAmount($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setVat($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCurrencyId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setUsesPrCoupon($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setUsesPrCoustomer($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setActiveFrom($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setActiveTo($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CouponsPeer::DATABASE_NAME);

		if ($this->isColumnModified(CouponsPeer::ID)) $criteria->add(CouponsPeer::ID, $this->id);
		if ($this->isColumnModified(CouponsPeer::CODE)) $criteria->add(CouponsPeer::CODE, $this->code);
		if ($this->isColumnModified(CouponsPeer::AMOUNT)) $criteria->add(CouponsPeer::AMOUNT, $this->amount);
		if ($this->isColumnModified(CouponsPeer::VAT)) $criteria->add(CouponsPeer::VAT, $this->vat);
		if ($this->isColumnModified(CouponsPeer::CURRENCY_ID)) $criteria->add(CouponsPeer::CURRENCY_ID, $this->currency_id);
		if ($this->isColumnModified(CouponsPeer::USES_PR_COUPON)) $criteria->add(CouponsPeer::USES_PR_COUPON, $this->uses_pr_coupon);
		if ($this->isColumnModified(CouponsPeer::USES_PR_COUSTOMER)) $criteria->add(CouponsPeer::USES_PR_COUSTOMER, $this->uses_pr_coustomer);
		if ($this->isColumnModified(CouponsPeer::ACTIVE_FROM)) $criteria->add(CouponsPeer::ACTIVE_FROM, $this->active_from);
		if ($this->isColumnModified(CouponsPeer::ACTIVE_TO)) $criteria->add(CouponsPeer::ACTIVE_TO, $this->active_to);
		if ($this->isColumnModified(CouponsPeer::CREATED_AT)) $criteria->add(CouponsPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(CouponsPeer::UPDATED_AT)) $criteria->add(CouponsPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(CouponsPeer::DATABASE_NAME);
		$criteria->add(CouponsPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Coupons (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setCode($this->getCode());
		$copyObj->setAmount($this->getAmount());
		$copyObj->setVat($this->getVat());
		$copyObj->setCurrencyId($this->getCurrencyId());
		$copyObj->setUsesPrCoupon($this->getUsesPrCoupon());
		$copyObj->setUsesPrCoustomer($this->getUsesPrCoustomer());
		$copyObj->setActiveFrom($this->getActiveFrom());
		$copyObj->setActiveTo($this->getActiveTo());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setUpdatedAt($this->getUpdatedAt());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getCouponsToCustomerss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCouponsToCustomers($relObj->copy($deepCopy));
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
	 * @return     Coupons Clone of current object.
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
	 * @return     CouponsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CouponsPeer();
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
		if ('CouponsToCustomers' == $relationName) {
			return $this->initCouponsToCustomerss();
		}
	}

	/**
	 * Clears out the collCouponsToCustomerss collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCouponsToCustomerss()
	 */
	public function clearCouponsToCustomerss()
	{
		$this->collCouponsToCustomerss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCouponsToCustomerss collection.
	 *
	 * By default this just sets the collCouponsToCustomerss collection to an empty array (like clearcollCouponsToCustomerss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCouponsToCustomerss($overrideExisting = true)
	{
		if (null !== $this->collCouponsToCustomerss && !$overrideExisting) {
			return;
		}
		$this->collCouponsToCustomerss = new PropelObjectCollection();
		$this->collCouponsToCustomerss->setModel('CouponsToCustomers');
	}

	/**
	 * Gets an array of CouponsToCustomers objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Coupons is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CouponsToCustomers[] List of CouponsToCustomers objects
	 * @throws     PropelException
	 */
	public function getCouponsToCustomerss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCouponsToCustomerss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCouponsToCustomerss) {
				// return empty collection
				$this->initCouponsToCustomerss();
			} else {
				$collCouponsToCustomerss = CouponsToCustomersQuery::create(null, $criteria)
					->filterByCoupons($this)
					->find($con);
				if (null !== $criteria) {
					return $collCouponsToCustomerss;
				}
				$this->collCouponsToCustomerss = $collCouponsToCustomerss;
			}
		}
		return $this->collCouponsToCustomerss;
	}

	/**
	 * Sets a collection of CouponsToCustomers objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $couponsToCustomerss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCouponsToCustomerss(PropelCollection $couponsToCustomerss, PropelPDO $con = null)
	{
		$this->couponsToCustomerssScheduledForDeletion = $this->getCouponsToCustomerss(new Criteria(), $con)->diff($couponsToCustomerss);

		foreach ($couponsToCustomerss as $couponsToCustomers) {
			// Fix issue with collection modified by reference
			if ($couponsToCustomers->isNew()) {
				$couponsToCustomers->setCoupons($this);
			}
			$this->addCouponsToCustomers($couponsToCustomers);
		}

		$this->collCouponsToCustomerss = $couponsToCustomerss;
	}

	/**
	 * Returns the number of related CouponsToCustomers objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CouponsToCustomers objects.
	 * @throws     PropelException
	 */
	public function countCouponsToCustomerss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCouponsToCustomerss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCouponsToCustomerss) {
				return 0;
			} else {
				$query = CouponsToCustomersQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCoupons($this)
					->count($con);
			}
		} else {
			return count($this->collCouponsToCustomerss);
		}
	}

	/**
	 * Method called to associate a CouponsToCustomers object to this object
	 * through the CouponsToCustomers foreign key attribute.
	 *
	 * @param      CouponsToCustomers $l CouponsToCustomers
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function addCouponsToCustomers(CouponsToCustomers $l)
	{
		if ($this->collCouponsToCustomerss === null) {
			$this->initCouponsToCustomerss();
		}
		if (!$this->collCouponsToCustomerss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCouponsToCustomers($l);
		}

		return $this;
	}

	/**
	 * @param	CouponsToCustomers $couponsToCustomers The couponsToCustomers object to add.
	 */
	protected function doAddCouponsToCustomers($couponsToCustomers)
	{
		$this->collCouponsToCustomerss[]= $couponsToCustomers;
		$couponsToCustomers->setCoupons($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Coupons is new, it will return
	 * an empty collection; or if this Coupons has previously
	 * been saved, it will retrieve related CouponsToCustomerss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Coupons.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CouponsToCustomers[] List of CouponsToCustomers objects
	 */
	public function getCouponsToCustomerssJoinCustomers($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CouponsToCustomersQuery::create(null, $criteria);
		$query->joinWith('Customers', $join_behavior);

		return $this->getCouponsToCustomerss($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->code = null;
		$this->amount = null;
		$this->vat = null;
		$this->currency_id = null;
		$this->uses_pr_coupon = null;
		$this->uses_pr_coustomer = null;
		$this->active_from = null;
		$this->active_to = null;
		$this->created_at = null;
		$this->updated_at = null;
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
			if ($this->collCouponsToCustomerss) {
				foreach ($this->collCouponsToCustomerss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collCouponsToCustomerss instanceof PropelCollection) {
			$this->collCouponsToCustomerss->clearIterator();
		}
		$this->collCouponsToCustomerss = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(CouponsPeer::DEFAULT_STRING_FORMAT);
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     Coupons The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = CouponsPeer::UPDATED_AT;
		return $this;
	}

} // BaseCoupons

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
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\ConsultantNewsletterDrafts;
use Hanzo\Model\ConsultantNewsletterDraftsQuery;
use Hanzo\Model\Consultants;
use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Events;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\GothiaAccounts;
use Hanzo\Model\GothiaAccountsQuery;
use Hanzo\Model\Groups;
use Hanzo\Model\GroupsQuery;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\Wall;
use Hanzo\Model\WallLikes;
use Hanzo\Model\WallLikesQuery;
use Hanzo\Model\WallQuery;

abstract class BaseCustomers extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\CustomersPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CustomersPeer
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
     * The value for the groups_id field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $groups_id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

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
     * The value for the password field.
     * @var        string
     */
    protected $password;

    /**
     * The value for the password_clear field.
     * @var        string
     */
    protected $password_clear;

    /**
     * The value for the discount field.
     * Note: this column has a database default value of: '0.00'
     * @var        string
     */
    protected $discount;

    /**
     * The value for the is_active field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $is_active;

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
     * @var        Groups
     */
    protected $aGroups;

    /**
     * @var        PropelObjectCollection|Addresses[] Collection to store aggregation of Addresses objects.
     */
    protected $collAddressess;
    protected $collAddressessPartial;

    /**
     * @var        PropelObjectCollection|Events[] Collection to store aggregation of Events objects.
     */
    protected $collEventssRelatedByConsultantsId;
    protected $collEventssRelatedByConsultantsIdPartial;

    /**
     * @var        PropelObjectCollection|Events[] Collection to store aggregation of Events objects.
     */
    protected $collEventssRelatedByCustomersId;
    protected $collEventssRelatedByCustomersIdPartial;

    /**
     * @var        PropelObjectCollection|Orders[] Collection to store aggregation of Orders objects.
     */
    protected $collOrderss;
    protected $collOrderssPartial;

    /**
     * @var        PropelObjectCollection|Wall[] Collection to store aggregation of Wall objects.
     */
    protected $collWalls;
    protected $collWallsPartial;

    /**
     * @var        PropelObjectCollection|WallLikes[] Collection to store aggregation of WallLikes objects.
     */
    protected $collWallLikess;
    protected $collWallLikessPartial;

    /**
     * @var        PropelObjectCollection|ConsultantNewsletterDrafts[] Collection to store aggregation of ConsultantNewsletterDrafts objects.
     */
    protected $collConsultantNewsletterDraftss;
    protected $collConsultantNewsletterDraftssPartial;

    /**
     * @var        GothiaAccounts one-to-one related GothiaAccounts object
     */
    protected $singleGothiaAccounts;

    /**
     * @var        Consultants one-to-one related Consultants object
     */
    protected $singleConsultants;

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
    protected $addressessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $eventssRelatedByConsultantsIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $eventssRelatedByCustomersIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $orderssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $wallsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $wallLikessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $consultantNewsletterDraftssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->groups_id = 1;
        $this->discount = '0.00';
        $this->is_active = true;
    }

    /**
     * Initializes internal state of BaseCustomers object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
        EventDispatcherProxy::trigger(array('construct','model.construct'), new ModelEvent($this));
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
     * Get the [groups_id] column value.
     *
     * @return int
     */
    public function getGroupsId()
    {

        return $this->groups_id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getFirstName()
    {

        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     *
     * @return string
     */
    public function getLastName()
    {

        return $this->last_name;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [phone] column value.
     *
     * @return string
     */
    public function getPhone()
    {

        return $this->phone;
    }

    /**
     * Get the [password] column value.
     *
     * @return string
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * Get the [password_clear] column value.
     *
     * @return string
     */
    public function getPasswordClear()
    {

        return $this->password_clear;
    }

    /**
     * Get the [discount] column value.
     *
     * @return string
     */
    public function getDiscount()
    {

        return $this->discount;
    }

    /**
     * Get the [is_active] column value.
     *
     * @return boolean
     */
    public function getIsActive()
    {

        return $this->is_active;
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
     * @return Customers The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CustomersPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [groups_id] column.
     *
     * @param  int $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setGroupsId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->groups_id !== $v) {
            $this->groups_id = $v;
            $this->modifiedColumns[] = CustomersPeer::GROUPS_ID;
        }

        if ($this->aGroups !== null && $this->aGroups->getId() !== $v) {
            $this->aGroups = null;
        }


        return $this;
    } // setGroupsId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = CustomersPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [first_name] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[] = CustomersPeer::FIRST_NAME;
        }


        return $this;
    } // setFirstName()

    /**
     * Set the value of [last_name] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setLastName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[] = CustomersPeer::LAST_NAME;
        }


        return $this;
    } // setLastName()

    /**
     * Set the value of [email] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = CustomersPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [phone] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setPhone($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->phone !== $v) {
            $this->phone = $v;
            $this->modifiedColumns[] = CustomersPeer::PHONE;
        }


        return $this;
    } // setPhone()

    /**
     * Set the value of [password] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[] = CustomersPeer::PASSWORD;
        }


        return $this;
    } // setPassword()

    /**
     * Set the value of [password_clear] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setPasswordClear($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password_clear !== $v) {
            $this->password_clear = $v;
            $this->modifiedColumns[] = CustomersPeer::PASSWORD_CLEAR;
        }


        return $this;
    } // setPasswordClear()

    /**
     * Set the value of [discount] column.
     *
     * @param  string $v new value
     * @return Customers The current object (for fluent API support)
     */
    public function setDiscount($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->discount !== $v) {
            $this->discount = $v;
            $this->modifiedColumns[] = CustomersPeer::DISCOUNT;
        }


        return $this;
    } // setDiscount()

    /**
     * Sets the value of the [is_active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Customers The current object (for fluent API support)
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
            $this->modifiedColumns[] = CustomersPeer::IS_ACTIVE;
        }


        return $this;
    } // setIsActive()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Customers The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = CustomersPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Customers The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = CustomersPeer::UPDATED_AT;
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
            if ($this->groups_id !== 1) {
                return false;
            }

            if ($this->discount !== '0.00') {
                return false;
            }

            if ($this->is_active !== true) {
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
            $this->groups_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->title = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->first_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->last_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->email = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->phone = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->password = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->password_clear = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->discount = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->is_active = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
            $this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 13; // 13 = CustomersPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Customers object", $e);
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

        if ($this->aGroups !== null && $this->groups_id !== $this->aGroups->getId()) {
            $this->aGroups = null;
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
            $con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CustomersPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aGroups = null;
            $this->collAddressess = null;

            $this->collEventssRelatedByConsultantsId = null;

            $this->collEventssRelatedByCustomersId = null;

            $this->collOrderss = null;

            $this->collWalls = null;

            $this->collWallLikess = null;

            $this->collConsultantNewsletterDraftss = null;

            $this->singleGothiaAccounts = null;

            $this->singleConsultants = null;

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
            $con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
            $deleteQuery = CustomersQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                // event behavior
                EventDispatcherProxy::trigger(array('delete.post', 'model.delete.post'), new ModelEvent($this));
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
            $con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // event behavior
            EventDispatcherProxy::trigger('model.save.pre', new ModelEvent($this));
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CustomersPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CustomersPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
                // event behavior
                EventDispatcherProxy::trigger('model.insert.pre', new ModelEvent($this));
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CustomersPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
                // event behavior
                EventDispatcherProxy::trigger(array('update.pre', 'model.update.pre'), new ModelEvent($this));
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                    // event behavior
                    EventDispatcherProxy::trigger('model.insert.post', new ModelEvent($this));
                } else {
                    $this->postUpdate($con);
                    // event behavior
                    EventDispatcherProxy::trigger(array('update.post', 'model.update.post'), new ModelEvent($this));
                }
                $this->postSave($con);
                // event behavior
                EventDispatcherProxy::trigger('model.save.post', new ModelEvent($this));
                CustomersPeer::addInstanceToPool($this);
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

            if ($this->aGroups !== null) {
                if ($this->aGroups->isModified() || $this->aGroups->isNew()) {
                    $affectedRows += $this->aGroups->save($con);
                }
                $this->setGroups($this->aGroups);
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
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->eventssRelatedByConsultantsIdScheduledForDeletion !== null) {
                if (!$this->eventssRelatedByConsultantsIdScheduledForDeletion->isEmpty()) {
                    EventsQuery::create()
                        ->filterByPrimaryKeys($this->eventssRelatedByConsultantsIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->eventssRelatedByConsultantsIdScheduledForDeletion = null;
                }
            }

            if ($this->collEventssRelatedByConsultantsId !== null) {
                foreach ($this->collEventssRelatedByConsultantsId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->eventssRelatedByCustomersIdScheduledForDeletion !== null) {
                if (!$this->eventssRelatedByCustomersIdScheduledForDeletion->isEmpty()) {
                    EventsQuery::create()
                        ->filterByPrimaryKeys($this->eventssRelatedByCustomersIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->eventssRelatedByCustomersIdScheduledForDeletion = null;
                }
            }

            if ($this->collEventssRelatedByCustomersId !== null) {
                foreach ($this->collEventssRelatedByCustomersId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderssScheduledForDeletion !== null) {
                if (!$this->orderssScheduledForDeletion->isEmpty()) {
                    foreach ($this->orderssScheduledForDeletion as $orders) {
                        // need to save related object because we set the relation to null
                        $orders->save($con);
                    }
                    $this->orderssScheduledForDeletion = null;
                }
            }

            if ($this->collOrderss !== null) {
                foreach ($this->collOrderss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->wallsScheduledForDeletion !== null) {
                if (!$this->wallsScheduledForDeletion->isEmpty()) {
                    WallQuery::create()
                        ->filterByPrimaryKeys($this->wallsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->wallsScheduledForDeletion = null;
                }
            }

            if ($this->collWalls !== null) {
                foreach ($this->collWalls as $referrerFK) {
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

            if ($this->consultantNewsletterDraftssScheduledForDeletion !== null) {
                if (!$this->consultantNewsletterDraftssScheduledForDeletion->isEmpty()) {
                    ConsultantNewsletterDraftsQuery::create()
                        ->filterByPrimaryKeys($this->consultantNewsletterDraftssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->consultantNewsletterDraftssScheduledForDeletion = null;
                }
            }

            if ($this->collConsultantNewsletterDraftss !== null) {
                foreach ($this->collConsultantNewsletterDraftss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->singleGothiaAccounts !== null) {
                if (!$this->singleGothiaAccounts->isDeleted() && ($this->singleGothiaAccounts->isNew() || $this->singleGothiaAccounts->isModified())) {
                        $affectedRows += $this->singleGothiaAccounts->save($con);
                }
            }

            if ($this->singleConsultants !== null) {
                if (!$this->singleConsultants->isDeleted() && ($this->singleConsultants->isNew() || $this->singleConsultants->isModified())) {
                        $affectedRows += $this->singleConsultants->save($con);
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

        $this->modifiedColumns[] = CustomersPeer::ID;

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CustomersPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CustomersPeer::GROUPS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`groups_id`';
        }
        if ($this->isColumnModified(CustomersPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(CustomersPeer::FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`first_name`';
        }
        if ($this->isColumnModified(CustomersPeer::LAST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`last_name`';
        }
        if ($this->isColumnModified(CustomersPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '`email`';
        }
        if ($this->isColumnModified(CustomersPeer::PHONE)) {
            $modifiedColumns[':p' . $index++]  = '`phone`';
        }
        if ($this->isColumnModified(CustomersPeer::PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '`password`';
        }
        if ($this->isColumnModified(CustomersPeer::PASSWORD_CLEAR)) {
            $modifiedColumns[':p' . $index++]  = '`password_clear`';
        }
        if ($this->isColumnModified(CustomersPeer::DISCOUNT)) {
            $modifiedColumns[':p' . $index++]  = '`discount`';
        }
        if ($this->isColumnModified(CustomersPeer::IS_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = '`is_active`';
        }
        if ($this->isColumnModified(CustomersPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CustomersPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `customers` (%s) VALUES (%s)',
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
                    case '`groups_id`':
                        $stmt->bindValue($identifier, $this->groups_id, PDO::PARAM_INT);
                        break;
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`first_name`':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case '`last_name`':
                        $stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case '`email`':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '`phone`':
                        $stmt->bindValue($identifier, $this->phone, PDO::PARAM_STR);
                        break;
                    case '`password`':
                        $stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case '`password_clear`':
                        $stmt->bindValue($identifier, $this->password_clear, PDO::PARAM_STR);
                        break;
                    case '`discount`':
                        $stmt->bindValue($identifier, $this->discount, PDO::PARAM_STR);
                        break;
                    case '`is_active`':
                        $stmt->bindValue($identifier, (int) $this->is_active, PDO::PARAM_INT);
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
        if ($pk !== null) {
            $this->setId($pk);
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

            if ($this->aGroups !== null) {
                if (!$this->aGroups->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aGroups->getValidationFailures());
                }
            }


            if (($retval = CustomersPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collAddressess !== null) {
                    foreach ($this->collAddressess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collEventssRelatedByConsultantsId !== null) {
                    foreach ($this->collEventssRelatedByConsultantsId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collEventssRelatedByCustomersId !== null) {
                    foreach ($this->collEventssRelatedByCustomersId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collOrderss !== null) {
                    foreach ($this->collOrderss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collWalls !== null) {
                    foreach ($this->collWalls as $referrerFK) {
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

                if ($this->collConsultantNewsletterDraftss !== null) {
                    foreach ($this->collConsultantNewsletterDraftss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->singleGothiaAccounts !== null) {
                    if (!$this->singleGothiaAccounts->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singleGothiaAccounts->getValidationFailures());
                    }
                }

                if ($this->singleConsultants !== null) {
                    if (!$this->singleConsultants->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singleConsultants->getValidationFailures());
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
        $pos = CustomersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getGroupsId();
                break;
            case 2:
                return $this->getTitle();
                break;
            case 3:
                return $this->getFirstName();
                break;
            case 4:
                return $this->getLastName();
                break;
            case 5:
                return $this->getEmail();
                break;
            case 6:
                return $this->getPhone();
                break;
            case 7:
                return $this->getPassword();
                break;
            case 8:
                return $this->getPasswordClear();
                break;
            case 9:
                return $this->getDiscount();
                break;
            case 10:
                return $this->getIsActive();
                break;
            case 11:
                return $this->getCreatedAt();
                break;
            case 12:
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
        if (isset($alreadyDumpedObjects['Customers'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Customers'][$this->getPrimaryKey()] = true;
        $keys = CustomersPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getGroupsId(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getFirstName(),
            $keys[4] => $this->getLastName(),
            $keys[5] => $this->getEmail(),
            $keys[6] => $this->getPhone(),
            $keys[7] => $this->getPassword(),
            $keys[8] => $this->getPasswordClear(),
            $keys[9] => $this->getDiscount(),
            $keys[10] => $this->getIsActive(),
            $keys[11] => $this->getCreatedAt(),
            $keys[12] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aGroups) {
                $result['Groups'] = $this->aGroups->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collAddressess) {
                $result['Addressess'] = $this->collAddressess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collEventssRelatedByConsultantsId) {
                $result['EventssRelatedByConsultantsId'] = $this->collEventssRelatedByConsultantsId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collEventssRelatedByCustomersId) {
                $result['EventssRelatedByCustomersId'] = $this->collEventssRelatedByCustomersId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderss) {
                $result['Orderss'] = $this->collOrderss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWalls) {
                $result['Walls'] = $this->collWalls->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWallLikess) {
                $result['WallLikess'] = $this->collWallLikess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collConsultantNewsletterDraftss) {
                $result['ConsultantNewsletterDraftss'] = $this->collConsultantNewsletterDraftss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->singleGothiaAccounts) {
                $result['GothiaAccounts'] = $this->singleGothiaAccounts->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->singleConsultants) {
                $result['Consultants'] = $this->singleConsultants->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
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
        $pos = CustomersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setGroupsId($value);
                break;
            case 2:
                $this->setTitle($value);
                break;
            case 3:
                $this->setFirstName($value);
                break;
            case 4:
                $this->setLastName($value);
                break;
            case 5:
                $this->setEmail($value);
                break;
            case 6:
                $this->setPhone($value);
                break;
            case 7:
                $this->setPassword($value);
                break;
            case 8:
                $this->setPasswordClear($value);
                break;
            case 9:
                $this->setDiscount($value);
                break;
            case 10:
                $this->setIsActive($value);
                break;
            case 11:
                $this->setCreatedAt($value);
                break;
            case 12:
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
        $keys = CustomersPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setGroupsId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setFirstName($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setLastName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setEmail($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setPhone($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setPassword($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setPasswordClear($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDiscount($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setIsActive($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setCreatedAt($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setUpdatedAt($arr[$keys[12]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CustomersPeer::DATABASE_NAME);

        if ($this->isColumnModified(CustomersPeer::ID)) $criteria->add(CustomersPeer::ID, $this->id);
        if ($this->isColumnModified(CustomersPeer::GROUPS_ID)) $criteria->add(CustomersPeer::GROUPS_ID, $this->groups_id);
        if ($this->isColumnModified(CustomersPeer::TITLE)) $criteria->add(CustomersPeer::TITLE, $this->title);
        if ($this->isColumnModified(CustomersPeer::FIRST_NAME)) $criteria->add(CustomersPeer::FIRST_NAME, $this->first_name);
        if ($this->isColumnModified(CustomersPeer::LAST_NAME)) $criteria->add(CustomersPeer::LAST_NAME, $this->last_name);
        if ($this->isColumnModified(CustomersPeer::EMAIL)) $criteria->add(CustomersPeer::EMAIL, $this->email);
        if ($this->isColumnModified(CustomersPeer::PHONE)) $criteria->add(CustomersPeer::PHONE, $this->phone);
        if ($this->isColumnModified(CustomersPeer::PASSWORD)) $criteria->add(CustomersPeer::PASSWORD, $this->password);
        if ($this->isColumnModified(CustomersPeer::PASSWORD_CLEAR)) $criteria->add(CustomersPeer::PASSWORD_CLEAR, $this->password_clear);
        if ($this->isColumnModified(CustomersPeer::DISCOUNT)) $criteria->add(CustomersPeer::DISCOUNT, $this->discount);
        if ($this->isColumnModified(CustomersPeer::IS_ACTIVE)) $criteria->add(CustomersPeer::IS_ACTIVE, $this->is_active);
        if ($this->isColumnModified(CustomersPeer::CREATED_AT)) $criteria->add(CustomersPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CustomersPeer::UPDATED_AT)) $criteria->add(CustomersPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CustomersPeer::DATABASE_NAME);
        $criteria->add(CustomersPeer::ID, $this->id);

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
     * @param object $copyObj An object of Customers (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setGroupsId($this->getGroupsId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setLastName($this->getLastName());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setPhone($this->getPhone());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setPasswordClear($this->getPasswordClear());
        $copyObj->setDiscount($this->getDiscount());
        $copyObj->setIsActive($this->getIsActive());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

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

            foreach ($this->getEventssRelatedByConsultantsId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addEventsRelatedByConsultantsId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getEventssRelatedByCustomersId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addEventsRelatedByCustomersId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrders($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWalls() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWall($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWallLikess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWallLikes($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getConsultantNewsletterDraftss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addConsultantNewsletterDrafts($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getGothiaAccounts();
            if ($relObj) {
                $copyObj->setGothiaAccounts($relObj->copy($deepCopy));
            }

            $relObj = $this->getConsultants();
            if ($relObj) {
                $copyObj->setConsultants($relObj->copy($deepCopy));
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
     * @return Customers Clone of current object.
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
     * @return CustomersPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CustomersPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Groups object.
     *
     * @param                  Groups $v
     * @return Customers The current object (for fluent API support)
     * @throws PropelException
     */
    public function setGroups(Groups $v = null)
    {
        if ($v === null) {
            $this->setGroupsId(1);
        } else {
            $this->setGroupsId($v->getId());
        }

        $this->aGroups = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Groups object, it will not be re-added.
        if ($v !== null) {
            $v->addCustomers($this);
        }


        return $this;
    }


    /**
     * Get the associated Groups object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Groups The associated Groups object.
     * @throws PropelException
     */
    public function getGroups(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aGroups === null && ($this->groups_id !== null) && $doQuery) {
            $this->aGroups = GroupsQuery::create()->findPk($this->groups_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aGroups->addCustomerss($this);
             */
        }

        return $this->aGroups;
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
        if ('Addresses' == $relationName) {
            $this->initAddressess();
        }
        if ('EventsRelatedByConsultantsId' == $relationName) {
            $this->initEventssRelatedByConsultantsId();
        }
        if ('EventsRelatedByCustomersId' == $relationName) {
            $this->initEventssRelatedByCustomersId();
        }
        if ('Orders' == $relationName) {
            $this->initOrderss();
        }
        if ('Wall' == $relationName) {
            $this->initWalls();
        }
        if ('WallLikes' == $relationName) {
            $this->initWallLikess();
        }
        if ('ConsultantNewsletterDrafts' == $relationName) {
            $this->initConsultantNewsletterDraftss();
        }
    }

    /**
     * Clears out the collAddressess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
     * @see        addAddressess()
     */
    public function clearAddressess()
    {
        $this->collAddressess = null; // important to set this to null since that means it is uninitialized
        $this->collAddressessPartial = null;

        return $this;
    }

    /**
     * reset is the collAddressess collection loaded partially
     *
     * @return void
     */
    public function resetPartialAddressess($v = true)
    {
        $this->collAddressessPartial = $v;
    }

    /**
     * Initializes the collAddressess collection.
     *
     * By default this just sets the collAddressess collection to an empty array (like clearcollAddressess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
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
     * If this Customers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Addresses[] List of Addresses objects
     * @throws PropelException
     */
    public function getAddressess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAddressessPartial && !$this->isNew();
        if (null === $this->collAddressess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAddressess) {
                // return empty collection
                $this->initAddressess();
            } else {
                $collAddressess = AddressesQuery::create(null, $criteria)
                    ->filterByCustomers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAddressessPartial && count($collAddressess)) {
                      $this->initAddressess(false);

                      foreach ($collAddressess as $obj) {
                        if (false == $this->collAddressess->contains($obj)) {
                          $this->collAddressess->append($obj);
                        }
                      }

                      $this->collAddressessPartial = true;
                    }

                    $collAddressess->getInternalIterator()->rewind();

                    return $collAddressess;
                }

                if ($partial && $this->collAddressess) {
                    foreach ($this->collAddressess as $obj) {
                        if ($obj->isNew()) {
                            $collAddressess[] = $obj;
                        }
                    }
                }

                $this->collAddressess = $collAddressess;
                $this->collAddressessPartial = false;
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
     * @param PropelCollection $addressess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Customers The current object (for fluent API support)
     */
    public function setAddressess(PropelCollection $addressess, PropelPDO $con = null)
    {
        $addressessToDelete = $this->getAddressess(new Criteria(), $con)->diff($addressess);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->addressessScheduledForDeletion = clone $addressessToDelete;

        foreach ($addressessToDelete as $addressesRemoved) {
            $addressesRemoved->setCustomers(null);
        }

        $this->collAddressess = null;
        foreach ($addressess as $addresses) {
            $this->addAddresses($addresses);
        }

        $this->collAddressess = $addressess;
        $this->collAddressessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Addresses objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Addresses objects.
     * @throws PropelException
     */
    public function countAddressess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAddressessPartial && !$this->isNew();
        if (null === $this->collAddressess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAddressess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAddressess());
            }
            $query = AddressesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCustomers($this)
                ->count($con);
        }

        return count($this->collAddressess);
    }

    /**
     * Method called to associate a Addresses object to this object
     * through the Addresses foreign key attribute.
     *
     * @param    Addresses $l Addresses
     * @return Customers The current object (for fluent API support)
     */
    public function addAddresses(Addresses $l)
    {
        if ($this->collAddressess === null) {
            $this->initAddressess();
            $this->collAddressessPartial = true;
        }

        if (!in_array($l, $this->collAddressess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAddresses($l);

            if ($this->addressessScheduledForDeletion and $this->addressessScheduledForDeletion->contains($l)) {
                $this->addressessScheduledForDeletion->remove($this->addressessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Addresses $addresses The addresses object to add.
     */
    protected function doAddAddresses($addresses)
    {
        $this->collAddressess[]= $addresses;
        $addresses->setCustomers($this);
    }

    /**
     * @param	Addresses $addresses The addresses object to remove.
     * @return Customers The current object (for fluent API support)
     */
    public function removeAddresses($addresses)
    {
        if ($this->getAddressess()->contains($addresses)) {
            $this->collAddressess->remove($this->collAddressess->search($addresses));
            if (null === $this->addressessScheduledForDeletion) {
                $this->addressessScheduledForDeletion = clone $this->collAddressess;
                $this->addressessScheduledForDeletion->clear();
            }
            $this->addressessScheduledForDeletion[]= clone $addresses;
            $addresses->setCustomers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Customers is new, it will return
     * an empty collection; or if this Customers has previously
     * been saved, it will retrieve related Addressess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Customers.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Addresses[] List of Addresses objects
     */
    public function getAddressessJoinCountries($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AddressesQuery::create(null, $criteria);
        $query->joinWith('Countries', $join_behavior);

        return $this->getAddressess($query, $con);
    }

    /**
     * Clears out the collEventssRelatedByConsultantsId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
     * @see        addEventssRelatedByConsultantsId()
     */
    public function clearEventssRelatedByConsultantsId()
    {
        $this->collEventssRelatedByConsultantsId = null; // important to set this to null since that means it is uninitialized
        $this->collEventssRelatedByConsultantsIdPartial = null;

        return $this;
    }

    /**
     * reset is the collEventssRelatedByConsultantsId collection loaded partially
     *
     * @return void
     */
    public function resetPartialEventssRelatedByConsultantsId($v = true)
    {
        $this->collEventssRelatedByConsultantsIdPartial = $v;
    }

    /**
     * Initializes the collEventssRelatedByConsultantsId collection.
     *
     * By default this just sets the collEventssRelatedByConsultantsId collection to an empty array (like clearcollEventssRelatedByConsultantsId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initEventssRelatedByConsultantsId($overrideExisting = true)
    {
        if (null !== $this->collEventssRelatedByConsultantsId && !$overrideExisting) {
            return;
        }
        $this->collEventssRelatedByConsultantsId = new PropelObjectCollection();
        $this->collEventssRelatedByConsultantsId->setModel('Events');
    }

    /**
     * Gets an array of Events objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Customers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Events[] List of Events objects
     * @throws PropelException
     */
    public function getEventssRelatedByConsultantsId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collEventssRelatedByConsultantsIdPartial && !$this->isNew();
        if (null === $this->collEventssRelatedByConsultantsId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collEventssRelatedByConsultantsId) {
                // return empty collection
                $this->initEventssRelatedByConsultantsId();
            } else {
                $collEventssRelatedByConsultantsId = EventsQuery::create(null, $criteria)
                    ->filterByCustomersRelatedByConsultantsId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collEventssRelatedByConsultantsIdPartial && count($collEventssRelatedByConsultantsId)) {
                      $this->initEventssRelatedByConsultantsId(false);

                      foreach ($collEventssRelatedByConsultantsId as $obj) {
                        if (false == $this->collEventssRelatedByConsultantsId->contains($obj)) {
                          $this->collEventssRelatedByConsultantsId->append($obj);
                        }
                      }

                      $this->collEventssRelatedByConsultantsIdPartial = true;
                    }

                    $collEventssRelatedByConsultantsId->getInternalIterator()->rewind();

                    return $collEventssRelatedByConsultantsId;
                }

                if ($partial && $this->collEventssRelatedByConsultantsId) {
                    foreach ($this->collEventssRelatedByConsultantsId as $obj) {
                        if ($obj->isNew()) {
                            $collEventssRelatedByConsultantsId[] = $obj;
                        }
                    }
                }

                $this->collEventssRelatedByConsultantsId = $collEventssRelatedByConsultantsId;
                $this->collEventssRelatedByConsultantsIdPartial = false;
            }
        }

        return $this->collEventssRelatedByConsultantsId;
    }

    /**
     * Sets a collection of EventsRelatedByConsultantsId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $eventssRelatedByConsultantsId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Customers The current object (for fluent API support)
     */
    public function setEventssRelatedByConsultantsId(PropelCollection $eventssRelatedByConsultantsId, PropelPDO $con = null)
    {
        $eventssRelatedByConsultantsIdToDelete = $this->getEventssRelatedByConsultantsId(new Criteria(), $con)->diff($eventssRelatedByConsultantsId);


        $this->eventssRelatedByConsultantsIdScheduledForDeletion = $eventssRelatedByConsultantsIdToDelete;

        foreach ($eventssRelatedByConsultantsIdToDelete as $eventsRelatedByConsultantsIdRemoved) {
            $eventsRelatedByConsultantsIdRemoved->setCustomersRelatedByConsultantsId(null);
        }

        $this->collEventssRelatedByConsultantsId = null;
        foreach ($eventssRelatedByConsultantsId as $eventsRelatedByConsultantsId) {
            $this->addEventsRelatedByConsultantsId($eventsRelatedByConsultantsId);
        }

        $this->collEventssRelatedByConsultantsId = $eventssRelatedByConsultantsId;
        $this->collEventssRelatedByConsultantsIdPartial = false;

        return $this;
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
    public function countEventssRelatedByConsultantsId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collEventssRelatedByConsultantsIdPartial && !$this->isNew();
        if (null === $this->collEventssRelatedByConsultantsId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collEventssRelatedByConsultantsId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getEventssRelatedByConsultantsId());
            }
            $query = EventsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCustomersRelatedByConsultantsId($this)
                ->count($con);
        }

        return count($this->collEventssRelatedByConsultantsId);
    }

    /**
     * Method called to associate a Events object to this object
     * through the Events foreign key attribute.
     *
     * @param    Events $l Events
     * @return Customers The current object (for fluent API support)
     */
    public function addEventsRelatedByConsultantsId(Events $l)
    {
        if ($this->collEventssRelatedByConsultantsId === null) {
            $this->initEventssRelatedByConsultantsId();
            $this->collEventssRelatedByConsultantsIdPartial = true;
        }

        if (!in_array($l, $this->collEventssRelatedByConsultantsId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddEventsRelatedByConsultantsId($l);

            if ($this->eventssRelatedByConsultantsIdScheduledForDeletion and $this->eventssRelatedByConsultantsIdScheduledForDeletion->contains($l)) {
                $this->eventssRelatedByConsultantsIdScheduledForDeletion->remove($this->eventssRelatedByConsultantsIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	EventsRelatedByConsultantsId $eventsRelatedByConsultantsId The eventsRelatedByConsultantsId object to add.
     */
    protected function doAddEventsRelatedByConsultantsId($eventsRelatedByConsultantsId)
    {
        $this->collEventssRelatedByConsultantsId[]= $eventsRelatedByConsultantsId;
        $eventsRelatedByConsultantsId->setCustomersRelatedByConsultantsId($this);
    }

    /**
     * @param	EventsRelatedByConsultantsId $eventsRelatedByConsultantsId The eventsRelatedByConsultantsId object to remove.
     * @return Customers The current object (for fluent API support)
     */
    public function removeEventsRelatedByConsultantsId($eventsRelatedByConsultantsId)
    {
        if ($this->getEventssRelatedByConsultantsId()->contains($eventsRelatedByConsultantsId)) {
            $this->collEventssRelatedByConsultantsId->remove($this->collEventssRelatedByConsultantsId->search($eventsRelatedByConsultantsId));
            if (null === $this->eventssRelatedByConsultantsIdScheduledForDeletion) {
                $this->eventssRelatedByConsultantsIdScheduledForDeletion = clone $this->collEventssRelatedByConsultantsId;
                $this->eventssRelatedByConsultantsIdScheduledForDeletion->clear();
            }
            $this->eventssRelatedByConsultantsIdScheduledForDeletion[]= clone $eventsRelatedByConsultantsId;
            $eventsRelatedByConsultantsId->setCustomersRelatedByConsultantsId(null);
        }

        return $this;
    }

    /**
     * Clears out the collEventssRelatedByCustomersId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
     * @see        addEventssRelatedByCustomersId()
     */
    public function clearEventssRelatedByCustomersId()
    {
        $this->collEventssRelatedByCustomersId = null; // important to set this to null since that means it is uninitialized
        $this->collEventssRelatedByCustomersIdPartial = null;

        return $this;
    }

    /**
     * reset is the collEventssRelatedByCustomersId collection loaded partially
     *
     * @return void
     */
    public function resetPartialEventssRelatedByCustomersId($v = true)
    {
        $this->collEventssRelatedByCustomersIdPartial = $v;
    }

    /**
     * Initializes the collEventssRelatedByCustomersId collection.
     *
     * By default this just sets the collEventssRelatedByCustomersId collection to an empty array (like clearcollEventssRelatedByCustomersId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initEventssRelatedByCustomersId($overrideExisting = true)
    {
        if (null !== $this->collEventssRelatedByCustomersId && !$overrideExisting) {
            return;
        }
        $this->collEventssRelatedByCustomersId = new PropelObjectCollection();
        $this->collEventssRelatedByCustomersId->setModel('Events');
    }

    /**
     * Gets an array of Events objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Customers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Events[] List of Events objects
     * @throws PropelException
     */
    public function getEventssRelatedByCustomersId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collEventssRelatedByCustomersIdPartial && !$this->isNew();
        if (null === $this->collEventssRelatedByCustomersId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collEventssRelatedByCustomersId) {
                // return empty collection
                $this->initEventssRelatedByCustomersId();
            } else {
                $collEventssRelatedByCustomersId = EventsQuery::create(null, $criteria)
                    ->filterByCustomersRelatedByCustomersId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collEventssRelatedByCustomersIdPartial && count($collEventssRelatedByCustomersId)) {
                      $this->initEventssRelatedByCustomersId(false);

                      foreach ($collEventssRelatedByCustomersId as $obj) {
                        if (false == $this->collEventssRelatedByCustomersId->contains($obj)) {
                          $this->collEventssRelatedByCustomersId->append($obj);
                        }
                      }

                      $this->collEventssRelatedByCustomersIdPartial = true;
                    }

                    $collEventssRelatedByCustomersId->getInternalIterator()->rewind();

                    return $collEventssRelatedByCustomersId;
                }

                if ($partial && $this->collEventssRelatedByCustomersId) {
                    foreach ($this->collEventssRelatedByCustomersId as $obj) {
                        if ($obj->isNew()) {
                            $collEventssRelatedByCustomersId[] = $obj;
                        }
                    }
                }

                $this->collEventssRelatedByCustomersId = $collEventssRelatedByCustomersId;
                $this->collEventssRelatedByCustomersIdPartial = false;
            }
        }

        return $this->collEventssRelatedByCustomersId;
    }

    /**
     * Sets a collection of EventsRelatedByCustomersId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $eventssRelatedByCustomersId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Customers The current object (for fluent API support)
     */
    public function setEventssRelatedByCustomersId(PropelCollection $eventssRelatedByCustomersId, PropelPDO $con = null)
    {
        $eventssRelatedByCustomersIdToDelete = $this->getEventssRelatedByCustomersId(new Criteria(), $con)->diff($eventssRelatedByCustomersId);


        $this->eventssRelatedByCustomersIdScheduledForDeletion = $eventssRelatedByCustomersIdToDelete;

        foreach ($eventssRelatedByCustomersIdToDelete as $eventsRelatedByCustomersIdRemoved) {
            $eventsRelatedByCustomersIdRemoved->setCustomersRelatedByCustomersId(null);
        }

        $this->collEventssRelatedByCustomersId = null;
        foreach ($eventssRelatedByCustomersId as $eventsRelatedByCustomersId) {
            $this->addEventsRelatedByCustomersId($eventsRelatedByCustomersId);
        }

        $this->collEventssRelatedByCustomersId = $eventssRelatedByCustomersId;
        $this->collEventssRelatedByCustomersIdPartial = false;

        return $this;
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
    public function countEventssRelatedByCustomersId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collEventssRelatedByCustomersIdPartial && !$this->isNew();
        if (null === $this->collEventssRelatedByCustomersId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collEventssRelatedByCustomersId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getEventssRelatedByCustomersId());
            }
            $query = EventsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCustomersRelatedByCustomersId($this)
                ->count($con);
        }

        return count($this->collEventssRelatedByCustomersId);
    }

    /**
     * Method called to associate a Events object to this object
     * through the Events foreign key attribute.
     *
     * @param    Events $l Events
     * @return Customers The current object (for fluent API support)
     */
    public function addEventsRelatedByCustomersId(Events $l)
    {
        if ($this->collEventssRelatedByCustomersId === null) {
            $this->initEventssRelatedByCustomersId();
            $this->collEventssRelatedByCustomersIdPartial = true;
        }

        if (!in_array($l, $this->collEventssRelatedByCustomersId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddEventsRelatedByCustomersId($l);

            if ($this->eventssRelatedByCustomersIdScheduledForDeletion and $this->eventssRelatedByCustomersIdScheduledForDeletion->contains($l)) {
                $this->eventssRelatedByCustomersIdScheduledForDeletion->remove($this->eventssRelatedByCustomersIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	EventsRelatedByCustomersId $eventsRelatedByCustomersId The eventsRelatedByCustomersId object to add.
     */
    protected function doAddEventsRelatedByCustomersId($eventsRelatedByCustomersId)
    {
        $this->collEventssRelatedByCustomersId[]= $eventsRelatedByCustomersId;
        $eventsRelatedByCustomersId->setCustomersRelatedByCustomersId($this);
    }

    /**
     * @param	EventsRelatedByCustomersId $eventsRelatedByCustomersId The eventsRelatedByCustomersId object to remove.
     * @return Customers The current object (for fluent API support)
     */
    public function removeEventsRelatedByCustomersId($eventsRelatedByCustomersId)
    {
        if ($this->getEventssRelatedByCustomersId()->contains($eventsRelatedByCustomersId)) {
            $this->collEventssRelatedByCustomersId->remove($this->collEventssRelatedByCustomersId->search($eventsRelatedByCustomersId));
            if (null === $this->eventssRelatedByCustomersIdScheduledForDeletion) {
                $this->eventssRelatedByCustomersIdScheduledForDeletion = clone $this->collEventssRelatedByCustomersId;
                $this->eventssRelatedByCustomersIdScheduledForDeletion->clear();
            }
            $this->eventssRelatedByCustomersIdScheduledForDeletion[]= clone $eventsRelatedByCustomersId;
            $eventsRelatedByCustomersId->setCustomersRelatedByCustomersId(null);
        }

        return $this;
    }

    /**
     * Clears out the collOrderss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
     * @see        addOrderss()
     */
    public function clearOrderss()
    {
        $this->collOrderss = null; // important to set this to null since that means it is uninitialized
        $this->collOrderssPartial = null;

        return $this;
    }

    /**
     * reset is the collOrderss collection loaded partially
     *
     * @return void
     */
    public function resetPartialOrderss($v = true)
    {
        $this->collOrderssPartial = $v;
    }

    /**
     * Initializes the collOrderss collection.
     *
     * By default this just sets the collOrderss collection to an empty array (like clearcollOrderss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderss($overrideExisting = true)
    {
        if (null !== $this->collOrderss && !$overrideExisting) {
            return;
        }
        $this->collOrderss = new PropelObjectCollection();
        $this->collOrderss->setModel('Orders');
    }

    /**
     * Gets an array of Orders objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Customers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Orders[] List of Orders objects
     * @throws PropelException
     */
    public function getOrderss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collOrderssPartial && !$this->isNew();
        if (null === $this->collOrderss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderss) {
                // return empty collection
                $this->initOrderss();
            } else {
                $collOrderss = OrdersQuery::create(null, $criteria)
                    ->filterByCustomers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collOrderssPartial && count($collOrderss)) {
                      $this->initOrderss(false);

                      foreach ($collOrderss as $obj) {
                        if (false == $this->collOrderss->contains($obj)) {
                          $this->collOrderss->append($obj);
                        }
                      }

                      $this->collOrderssPartial = true;
                    }

                    $collOrderss->getInternalIterator()->rewind();

                    return $collOrderss;
                }

                if ($partial && $this->collOrderss) {
                    foreach ($this->collOrderss as $obj) {
                        if ($obj->isNew()) {
                            $collOrderss[] = $obj;
                        }
                    }
                }

                $this->collOrderss = $collOrderss;
                $this->collOrderssPartial = false;
            }
        }

        return $this->collOrderss;
    }

    /**
     * Sets a collection of Orders objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $orderss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Customers The current object (for fluent API support)
     */
    public function setOrderss(PropelCollection $orderss, PropelPDO $con = null)
    {
        $orderssToDelete = $this->getOrderss(new Criteria(), $con)->diff($orderss);


        $this->orderssScheduledForDeletion = $orderssToDelete;

        foreach ($orderssToDelete as $ordersRemoved) {
            $ordersRemoved->setCustomers(null);
        }

        $this->collOrderss = null;
        foreach ($orderss as $orders) {
            $this->addOrders($orders);
        }

        $this->collOrderss = $orderss;
        $this->collOrderssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Orders objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Orders objects.
     * @throws PropelException
     */
    public function countOrderss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collOrderssPartial && !$this->isNew();
        if (null === $this->collOrderss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderss());
            }
            $query = OrdersQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCustomers($this)
                ->count($con);
        }

        return count($this->collOrderss);
    }

    /**
     * Method called to associate a Orders object to this object
     * through the Orders foreign key attribute.
     *
     * @param    Orders $l Orders
     * @return Customers The current object (for fluent API support)
     */
    public function addOrders(Orders $l)
    {
        if ($this->collOrderss === null) {
            $this->initOrderss();
            $this->collOrderssPartial = true;
        }

        if (!in_array($l, $this->collOrderss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrders($l);

            if ($this->orderssScheduledForDeletion and $this->orderssScheduledForDeletion->contains($l)) {
                $this->orderssScheduledForDeletion->remove($this->orderssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Orders $orders The orders object to add.
     */
    protected function doAddOrders($orders)
    {
        $this->collOrderss[]= $orders;
        $orders->setCustomers($this);
    }

    /**
     * @param	Orders $orders The orders object to remove.
     * @return Customers The current object (for fluent API support)
     */
    public function removeOrders($orders)
    {
        if ($this->getOrderss()->contains($orders)) {
            $this->collOrderss->remove($this->collOrderss->search($orders));
            if (null === $this->orderssScheduledForDeletion) {
                $this->orderssScheduledForDeletion = clone $this->collOrderss;
                $this->orderssScheduledForDeletion->clear();
            }
            $this->orderssScheduledForDeletion[]= $orders;
            $orders->setCustomers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Customers is new, it will return
     * an empty collection; or if this Customers has previously
     * been saved, it will retrieve related Orderss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Customers.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Orders[] List of Orders objects
     */
    public function getOrderssJoinCountriesRelatedByBillingCountriesId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = OrdersQuery::create(null, $criteria);
        $query->joinWith('CountriesRelatedByBillingCountriesId', $join_behavior);

        return $this->getOrderss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Customers is new, it will return
     * an empty collection; or if this Customers has previously
     * been saved, it will retrieve related Orderss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Customers.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Orders[] List of Orders objects
     */
    public function getOrderssJoinCountriesRelatedByDeliveryCountriesId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = OrdersQuery::create(null, $criteria);
        $query->joinWith('CountriesRelatedByDeliveryCountriesId', $join_behavior);

        return $this->getOrderss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Customers is new, it will return
     * an empty collection; or if this Customers has previously
     * been saved, it will retrieve related Orderss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Customers.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Orders[] List of Orders objects
     */
    public function getOrderssJoinEvents($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = OrdersQuery::create(null, $criteria);
        $query->joinWith('Events', $join_behavior);

        return $this->getOrderss($query, $con);
    }

    /**
     * Clears out the collWalls collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
     * @see        addWalls()
     */
    public function clearWalls()
    {
        $this->collWalls = null; // important to set this to null since that means it is uninitialized
        $this->collWallsPartial = null;

        return $this;
    }

    /**
     * reset is the collWalls collection loaded partially
     *
     * @return void
     */
    public function resetPartialWalls($v = true)
    {
        $this->collWallsPartial = $v;
    }

    /**
     * Initializes the collWalls collection.
     *
     * By default this just sets the collWalls collection to an empty array (like clearcollWalls());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWalls($overrideExisting = true)
    {
        if (null !== $this->collWalls && !$overrideExisting) {
            return;
        }
        $this->collWalls = new PropelObjectCollection();
        $this->collWalls->setModel('Wall');
    }

    /**
     * Gets an array of Wall objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Customers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Wall[] List of Wall objects
     * @throws PropelException
     */
    public function getWalls($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWallsPartial && !$this->isNew();
        if (null === $this->collWalls || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWalls) {
                // return empty collection
                $this->initWalls();
            } else {
                $collWalls = WallQuery::create(null, $criteria)
                    ->filterByCustomers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWallsPartial && count($collWalls)) {
                      $this->initWalls(false);

                      foreach ($collWalls as $obj) {
                        if (false == $this->collWalls->contains($obj)) {
                          $this->collWalls->append($obj);
                        }
                      }

                      $this->collWallsPartial = true;
                    }

                    $collWalls->getInternalIterator()->rewind();

                    return $collWalls;
                }

                if ($partial && $this->collWalls) {
                    foreach ($this->collWalls as $obj) {
                        if ($obj->isNew()) {
                            $collWalls[] = $obj;
                        }
                    }
                }

                $this->collWalls = $collWalls;
                $this->collWallsPartial = false;
            }
        }

        return $this->collWalls;
    }

    /**
     * Sets a collection of Wall objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $walls A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Customers The current object (for fluent API support)
     */
    public function setWalls(PropelCollection $walls, PropelPDO $con = null)
    {
        $wallsToDelete = $this->getWalls(new Criteria(), $con)->diff($walls);


        $this->wallsScheduledForDeletion = $wallsToDelete;

        foreach ($wallsToDelete as $wallRemoved) {
            $wallRemoved->setCustomers(null);
        }

        $this->collWalls = null;
        foreach ($walls as $wall) {
            $this->addWall($wall);
        }

        $this->collWalls = $walls;
        $this->collWallsPartial = false;

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
    public function countWalls(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWallsPartial && !$this->isNew();
        if (null === $this->collWalls || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWalls) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWalls());
            }
            $query = WallQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCustomers($this)
                ->count($con);
        }

        return count($this->collWalls);
    }

    /**
     * Method called to associate a Wall object to this object
     * through the Wall foreign key attribute.
     *
     * @param    Wall $l Wall
     * @return Customers The current object (for fluent API support)
     */
    public function addWall(Wall $l)
    {
        if ($this->collWalls === null) {
            $this->initWalls();
            $this->collWallsPartial = true;
        }

        if (!in_array($l, $this->collWalls->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWall($l);

            if ($this->wallsScheduledForDeletion and $this->wallsScheduledForDeletion->contains($l)) {
                $this->wallsScheduledForDeletion->remove($this->wallsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Wall $wall The wall object to add.
     */
    protected function doAddWall($wall)
    {
        $this->collWalls[]= $wall;
        $wall->setCustomers($this);
    }

    /**
     * @param	Wall $wall The wall object to remove.
     * @return Customers The current object (for fluent API support)
     */
    public function removeWall($wall)
    {
        if ($this->getWalls()->contains($wall)) {
            $this->collWalls->remove($this->collWalls->search($wall));
            if (null === $this->wallsScheduledForDeletion) {
                $this->wallsScheduledForDeletion = clone $this->collWalls;
                $this->wallsScheduledForDeletion->clear();
            }
            $this->wallsScheduledForDeletion[]= clone $wall;
            $wall->setCustomers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Customers is new, it will return
     * an empty collection; or if this Customers has previously
     * been saved, it will retrieve related Walls from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Customers.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Wall[] List of Wall objects
     */
    public function getWallsJoinWallRelatedByParentId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WallQuery::create(null, $criteria);
        $query->joinWith('WallRelatedByParentId', $join_behavior);

        return $this->getWalls($query, $con);
    }

    /**
     * Clears out the collWallLikess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
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
     * If this Customers is new, it will return
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
                    ->filterByCustomers($this)
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
     * @return Customers The current object (for fluent API support)
     */
    public function setWallLikess(PropelCollection $wallLikess, PropelPDO $con = null)
    {
        $wallLikessToDelete = $this->getWallLikess(new Criteria(), $con)->diff($wallLikess);


        $this->wallLikessScheduledForDeletion = $wallLikessToDelete;

        foreach ($wallLikessToDelete as $wallLikesRemoved) {
            $wallLikesRemoved->setCustomers(null);
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
                ->filterByCustomers($this)
                ->count($con);
        }

        return count($this->collWallLikess);
    }

    /**
     * Method called to associate a WallLikes object to this object
     * through the WallLikes foreign key attribute.
     *
     * @param    WallLikes $l WallLikes
     * @return Customers The current object (for fluent API support)
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
        $wallLikes->setCustomers($this);
    }

    /**
     * @param	WallLikes $wallLikes The wallLikes object to remove.
     * @return Customers The current object (for fluent API support)
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
            $wallLikes->setCustomers(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Customers is new, it will return
     * an empty collection; or if this Customers has previously
     * been saved, it will retrieve related WallLikess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Customers.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|WallLikes[] List of WallLikes objects
     */
    public function getWallLikessJoinWall($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WallLikesQuery::create(null, $criteria);
        $query->joinWith('Wall', $join_behavior);

        return $this->getWallLikess($query, $con);
    }

    /**
     * Clears out the collConsultantNewsletterDraftss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Customers The current object (for fluent API support)
     * @see        addConsultantNewsletterDraftss()
     */
    public function clearConsultantNewsletterDraftss()
    {
        $this->collConsultantNewsletterDraftss = null; // important to set this to null since that means it is uninitialized
        $this->collConsultantNewsletterDraftssPartial = null;

        return $this;
    }

    /**
     * reset is the collConsultantNewsletterDraftss collection loaded partially
     *
     * @return void
     */
    public function resetPartialConsultantNewsletterDraftss($v = true)
    {
        $this->collConsultantNewsletterDraftssPartial = $v;
    }

    /**
     * Initializes the collConsultantNewsletterDraftss collection.
     *
     * By default this just sets the collConsultantNewsletterDraftss collection to an empty array (like clearcollConsultantNewsletterDraftss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initConsultantNewsletterDraftss($overrideExisting = true)
    {
        if (null !== $this->collConsultantNewsletterDraftss && !$overrideExisting) {
            return;
        }
        $this->collConsultantNewsletterDraftss = new PropelObjectCollection();
        $this->collConsultantNewsletterDraftss->setModel('ConsultantNewsletterDrafts');
    }

    /**
     * Gets an array of ConsultantNewsletterDrafts objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Customers is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ConsultantNewsletterDrafts[] List of ConsultantNewsletterDrafts objects
     * @throws PropelException
     */
    public function getConsultantNewsletterDraftss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collConsultantNewsletterDraftssPartial && !$this->isNew();
        if (null === $this->collConsultantNewsletterDraftss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collConsultantNewsletterDraftss) {
                // return empty collection
                $this->initConsultantNewsletterDraftss();
            } else {
                $collConsultantNewsletterDraftss = ConsultantNewsletterDraftsQuery::create(null, $criteria)
                    ->filterByCustomers($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collConsultantNewsletterDraftssPartial && count($collConsultantNewsletterDraftss)) {
                      $this->initConsultantNewsletterDraftss(false);

                      foreach ($collConsultantNewsletterDraftss as $obj) {
                        if (false == $this->collConsultantNewsletterDraftss->contains($obj)) {
                          $this->collConsultantNewsletterDraftss->append($obj);
                        }
                      }

                      $this->collConsultantNewsletterDraftssPartial = true;
                    }

                    $collConsultantNewsletterDraftss->getInternalIterator()->rewind();

                    return $collConsultantNewsletterDraftss;
                }

                if ($partial && $this->collConsultantNewsletterDraftss) {
                    foreach ($this->collConsultantNewsletterDraftss as $obj) {
                        if ($obj->isNew()) {
                            $collConsultantNewsletterDraftss[] = $obj;
                        }
                    }
                }

                $this->collConsultantNewsletterDraftss = $collConsultantNewsletterDraftss;
                $this->collConsultantNewsletterDraftssPartial = false;
            }
        }

        return $this->collConsultantNewsletterDraftss;
    }

    /**
     * Sets a collection of ConsultantNewsletterDrafts objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $consultantNewsletterDraftss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Customers The current object (for fluent API support)
     */
    public function setConsultantNewsletterDraftss(PropelCollection $consultantNewsletterDraftss, PropelPDO $con = null)
    {
        $consultantNewsletterDraftssToDelete = $this->getConsultantNewsletterDraftss(new Criteria(), $con)->diff($consultantNewsletterDraftss);


        $this->consultantNewsletterDraftssScheduledForDeletion = $consultantNewsletterDraftssToDelete;

        foreach ($consultantNewsletterDraftssToDelete as $consultantNewsletterDraftsRemoved) {
            $consultantNewsletterDraftsRemoved->setCustomers(null);
        }

        $this->collConsultantNewsletterDraftss = null;
        foreach ($consultantNewsletterDraftss as $consultantNewsletterDrafts) {
            $this->addConsultantNewsletterDrafts($consultantNewsletterDrafts);
        }

        $this->collConsultantNewsletterDraftss = $consultantNewsletterDraftss;
        $this->collConsultantNewsletterDraftssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ConsultantNewsletterDrafts objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ConsultantNewsletterDrafts objects.
     * @throws PropelException
     */
    public function countConsultantNewsletterDraftss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collConsultantNewsletterDraftssPartial && !$this->isNew();
        if (null === $this->collConsultantNewsletterDraftss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collConsultantNewsletterDraftss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getConsultantNewsletterDraftss());
            }
            $query = ConsultantNewsletterDraftsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCustomers($this)
                ->count($con);
        }

        return count($this->collConsultantNewsletterDraftss);
    }

    /**
     * Method called to associate a ConsultantNewsletterDrafts object to this object
     * through the ConsultantNewsletterDrafts foreign key attribute.
     *
     * @param    ConsultantNewsletterDrafts $l ConsultantNewsletterDrafts
     * @return Customers The current object (for fluent API support)
     */
    public function addConsultantNewsletterDrafts(ConsultantNewsletterDrafts $l)
    {
        if ($this->collConsultantNewsletterDraftss === null) {
            $this->initConsultantNewsletterDraftss();
            $this->collConsultantNewsletterDraftssPartial = true;
        }

        if (!in_array($l, $this->collConsultantNewsletterDraftss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddConsultantNewsletterDrafts($l);

            if ($this->consultantNewsletterDraftssScheduledForDeletion and $this->consultantNewsletterDraftssScheduledForDeletion->contains($l)) {
                $this->consultantNewsletterDraftssScheduledForDeletion->remove($this->consultantNewsletterDraftssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	ConsultantNewsletterDrafts $consultantNewsletterDrafts The consultantNewsletterDrafts object to add.
     */
    protected function doAddConsultantNewsletterDrafts($consultantNewsletterDrafts)
    {
        $this->collConsultantNewsletterDraftss[]= $consultantNewsletterDrafts;
        $consultantNewsletterDrafts->setCustomers($this);
    }

    /**
     * @param	ConsultantNewsletterDrafts $consultantNewsletterDrafts The consultantNewsletterDrafts object to remove.
     * @return Customers The current object (for fluent API support)
     */
    public function removeConsultantNewsletterDrafts($consultantNewsletterDrafts)
    {
        if ($this->getConsultantNewsletterDraftss()->contains($consultantNewsletterDrafts)) {
            $this->collConsultantNewsletterDraftss->remove($this->collConsultantNewsletterDraftss->search($consultantNewsletterDrafts));
            if (null === $this->consultantNewsletterDraftssScheduledForDeletion) {
                $this->consultantNewsletterDraftssScheduledForDeletion = clone $this->collConsultantNewsletterDraftss;
                $this->consultantNewsletterDraftssScheduledForDeletion->clear();
            }
            $this->consultantNewsletterDraftssScheduledForDeletion[]= clone $consultantNewsletterDrafts;
            $consultantNewsletterDrafts->setCustomers(null);
        }

        return $this;
    }

    /**
     * Gets a single GothiaAccounts object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return GothiaAccounts
     * @throws PropelException
     */
    public function getGothiaAccounts(PropelPDO $con = null)
    {

        if ($this->singleGothiaAccounts === null && !$this->isNew()) {
            $this->singleGothiaAccounts = GothiaAccountsQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleGothiaAccounts;
    }

    /**
     * Sets a single GothiaAccounts object as related to this object by a one-to-one relationship.
     *
     * @param                  GothiaAccounts $v GothiaAccounts
     * @return Customers The current object (for fluent API support)
     * @throws PropelException
     */
    public function setGothiaAccounts(GothiaAccounts $v = null)
    {
        $this->singleGothiaAccounts = $v;

        // Make sure that that the passed-in GothiaAccounts isn't already associated with this object
        if ($v !== null && $v->getCustomers(null, false) === null) {
            $v->setCustomers($this);
        }

        return $this;
    }

    /**
     * Gets a single Consultants object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return Consultants
     * @throws PropelException
     */
    public function getConsultants(PropelPDO $con = null)
    {

        if ($this->singleConsultants === null && !$this->isNew()) {
            $this->singleConsultants = ConsultantsQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleConsultants;
    }

    /**
     * Sets a single Consultants object as related to this object by a one-to-one relationship.
     *
     * @param                  Consultants $v Consultants
     * @return Customers The current object (for fluent API support)
     * @throws PropelException
     */
    public function setConsultants(Consultants $v = null)
    {
        $this->singleConsultants = $v;

        // Make sure that that the passed-in Consultants isn't already associated with this object
        if ($v !== null && $v->getCustomers(null, false) === null) {
            $v->setCustomers($this);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->groups_id = null;
        $this->title = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->email = null;
        $this->phone = null;
        $this->password = null;
        $this->password_clear = null;
        $this->discount = null;
        $this->is_active = null;
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
            if ($this->collAddressess) {
                foreach ($this->collAddressess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collEventssRelatedByConsultantsId) {
                foreach ($this->collEventssRelatedByConsultantsId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collEventssRelatedByCustomersId) {
                foreach ($this->collEventssRelatedByCustomersId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderss) {
                foreach ($this->collOrderss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWalls) {
                foreach ($this->collWalls as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWallLikess) {
                foreach ($this->collWallLikess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collConsultantNewsletterDraftss) {
                foreach ($this->collConsultantNewsletterDraftss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->singleGothiaAccounts) {
                $this->singleGothiaAccounts->clearAllReferences($deep);
            }
            if ($this->singleConsultants) {
                $this->singleConsultants->clearAllReferences($deep);
            }
            if ($this->aGroups instanceof Persistent) {
              $this->aGroups->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collAddressess instanceof PropelCollection) {
            $this->collAddressess->clearIterator();
        }
        $this->collAddressess = null;
        if ($this->collEventssRelatedByConsultantsId instanceof PropelCollection) {
            $this->collEventssRelatedByConsultantsId->clearIterator();
        }
        $this->collEventssRelatedByConsultantsId = null;
        if ($this->collEventssRelatedByCustomersId instanceof PropelCollection) {
            $this->collEventssRelatedByCustomersId->clearIterator();
        }
        $this->collEventssRelatedByCustomersId = null;
        if ($this->collOrderss instanceof PropelCollection) {
            $this->collOrderss->clearIterator();
        }
        $this->collOrderss = null;
        if ($this->collWalls instanceof PropelCollection) {
            $this->collWalls->clearIterator();
        }
        $this->collWalls = null;
        if ($this->collWallLikess instanceof PropelCollection) {
            $this->collWallLikess->clearIterator();
        }
        $this->collWallLikess = null;
        if ($this->collConsultantNewsletterDraftss instanceof PropelCollection) {
            $this->collConsultantNewsletterDraftss->clearIterator();
        }
        $this->collConsultantNewsletterDraftss = null;
        if ($this->singleGothiaAccounts instanceof PropelCollection) {
            $this->singleGothiaAccounts->clearIterator();
        }
        $this->singleGothiaAccounts = null;
        if ($this->singleConsultants instanceof PropelCollection) {
            $this->singleConsultants->clearIterator();
        }
        $this->singleConsultants = null;
        $this->aGroups = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CustomersPeer::DEFAULT_STRING_FORMAT);
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
     * @return     Customers The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = CustomersPeer::UPDATED_AT;

        return $this;
    }

    // event behavior
    public function preCommit(\PropelPDO $con = null){}
    public function preCommitSave(\PropelPDO $con = null){}
    public function preCommitDelete(\PropelPDO $con = null){}
    public function preCommitUpdate(\PropelPDO $con = null){}
    public function preCommitInsert(\PropelPDO $con = null){}
    public function preRollback(\PropelPDO $con = null){}
    public function preRollbackSave(\PropelPDO $con = null){}
    public function preRollbackDelete(\PropelPDO $con = null){}
    public function preRollbackUpdate(\PropelPDO $con = null){}
    public function preRollbackInsert(\PropelPDO $con = null){}

    /**
     * Catches calls to virtual methods
     */
    public function __call($name, $params)
    {

        // delegate behavior

        if (is_callable(array('Hanzo\Model\Consultants', $name))) {
            if (!$delegate = $this->getConsultants()) {
                $delegate = new Consultants();
                $this->setConsultants($delegate);
            }

            return call_user_func_array(array($delegate, $name), $params);
        }

        return parent::__call($name, $params);
    }

}

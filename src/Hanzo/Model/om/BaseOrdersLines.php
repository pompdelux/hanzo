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
use \PropelDateTime;
use \PropelException;
use \PropelPDO;
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;

abstract class BaseOrdersLines extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\OrdersLinesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        OrdersLinesPeer
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
     * The value for the orders_id field.
     * @var        int
     */
    protected $orders_id;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the products_id field.
     * @var        int
     */
    protected $products_id;

    /**
     * The value for the products_sku field.
     * @var        string
     */
    protected $products_sku;

    /**
     * The value for the products_name field.
     * @var        string
     */
    protected $products_name;

    /**
     * The value for the products_color field.
     * @var        string
     */
    protected $products_color;

    /**
     * The value for the products_size field.
     * @var        string
     */
    protected $products_size;

    /**
     * The value for the expected_at field.
     * Note: this column has a database default value of: '1970-01-01'
     * @var        string
     */
    protected $expected_at;

    /**
     * The value for the original_price field.
     * @var        string
     */
    protected $original_price;

    /**
     * The value for the price field.
     * @var        string
     */
    protected $price;

    /**
     * The value for the vat field.
     * Note: this column has a database default value of: '0.00'
     * @var        string
     */
    protected $vat;

    /**
     * The value for the quantity field.
     * @var        int
     */
    protected $quantity;

    /**
     * The value for the unit field.
     * @var        string
     */
    protected $unit;

    /**
     * The value for the is_voucher field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_voucher;

    /**
     * The value for the note field.
     * @var        string
     */
    protected $note;

    /**
     * @var        Orders
     */
    protected $aOrders;

    /**
     * @var        Products
     */
    protected $aProducts;

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
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->expected_at = '1970-01-01';
        $this->vat = '0.00';
        $this->is_voucher = false;
    }

    /**
     * Initializes internal state of BaseOrdersLines object.
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
     * Get the [orders_id] column value.
     *
     * @return int
     */
    public function getOrdersId()
    {

        return $this->orders_id;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [products_id] column value.
     *
     * @return int
     */
    public function getProductsId()
    {

        return $this->products_id;
    }

    /**
     * Get the [products_sku] column value.
     *
     * @return string
     */
    public function getProductsSku()
    {

        return $this->products_sku;
    }

    /**
     * Get the [products_name] column value.
     *
     * @return string
     */
    public function getProductsName()
    {

        return $this->products_name;
    }

    /**
     * Get the [products_color] column value.
     *
     * @return string
     */
    public function getProductsColor()
    {

        return $this->products_color;
    }

    /**
     * Get the [products_size] column value.
     *
     * @return string
     */
    public function getProductsSize()
    {

        return $this->products_size;
    }

    /**
     * Get the [optionally formatted] temporal [expected_at] column value.
     *
     * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
     * option in order to avoid conversions to integers (which are limited in the dates they can express).
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw unix timestamp integer will be returned.
     * @return mixed Formatted date/time value as string or (integer) unix timestamp (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getExpectedAt($format = 'Y-m-d')
    {
        if ($this->expected_at === null) {
            return null;
        }

        if ($this->expected_at === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->expected_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->expected_at, true), $x);
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
     * Get the [original_price] column value.
     *
     * @return string
     */
    public function getOriginalPrice()
    {

        return $this->original_price;
    }

    /**
     * Get the [price] column value.
     *
     * @return string
     */
    public function getPrice()
    {

        return $this->price;
    }

    /**
     * Get the [vat] column value.
     *
     * @return string
     */
    public function getVat()
    {

        return $this->vat;
    }

    /**
     * Get the [quantity] column value.
     *
     * @return int
     */
    public function getQuantity()
    {

        return $this->quantity;
    }

    /**
     * Get the [unit] column value.
     *
     * @return string
     */
    public function getUnit()
    {

        return $this->unit;
    }

    /**
     * Get the [is_voucher] column value.
     *
     * @return boolean
     */
    public function getIsVoucher()
    {

        return $this->is_voucher;
    }

    /**
     * Get the [note] column value.
     *
     * @return string
     */
    public function getNote()
    {

        return $this->note;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [orders_id] column.
     *
     * @param  int $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setOrdersId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->orders_id !== $v) {
            $this->orders_id = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::ORDERS_ID;
        }

        if ($this->aOrders !== null && $this->aOrders->getId() !== $v) {
            $this->aOrders = null;
        }


        return $this;
    } // setOrdersId()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [products_id] column.
     *
     * @param  int $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setProductsId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->products_id !== $v) {
            $this->products_id = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::PRODUCTS_ID;
        }

        if ($this->aProducts !== null && $this->aProducts->getId() !== $v) {
            $this->aProducts = null;
        }


        return $this;
    } // setProductsId()

    /**
     * Set the value of [products_sku] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setProductsSku($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->products_sku !== $v) {
            $this->products_sku = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::PRODUCTS_SKU;
        }


        return $this;
    } // setProductsSku()

    /**
     * Set the value of [products_name] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setProductsName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->products_name !== $v) {
            $this->products_name = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::PRODUCTS_NAME;
        }


        return $this;
    } // setProductsName()

    /**
     * Set the value of [products_color] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setProductsColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->products_color !== $v) {
            $this->products_color = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::PRODUCTS_COLOR;
        }


        return $this;
    } // setProductsColor()

    /**
     * Set the value of [products_size] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setProductsSize($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->products_size !== $v) {
            $this->products_size = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::PRODUCTS_SIZE;
        }


        return $this;
    } // setProductsSize()

    /**
     * Sets the value of [expected_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setExpectedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->expected_at !== null || $dt !== null) {
            $currentDateAsString = ($this->expected_at !== null && $tmpDt = new DateTime($this->expected_at)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ( ($currentDateAsString !== $newDateAsString) // normalized values don't match
                || ($dt->format('Y-m-d') === '1970-01-01') // or the entered value matches the default
                 ) {
                $this->expected_at = $newDateAsString;
                $this->modifiedColumns[] = OrdersLinesPeer::EXPECTED_AT;
            }
        } // if either are not null


        return $this;
    } // setExpectedAt()

    /**
     * Set the value of [original_price] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setOriginalPrice($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->original_price !== $v) {
            $this->original_price = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::ORIGINAL_PRICE;
        }


        return $this;
    } // setOriginalPrice()

    /**
     * Set the value of [price] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setPrice($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->price !== $v) {
            $this->price = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::PRICE;
        }


        return $this;
    } // setPrice()

    /**
     * Set the value of [vat] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setVat($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->vat !== $v) {
            $this->vat = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::VAT;
        }


        return $this;
    } // setVat()

    /**
     * Set the value of [quantity] column.
     *
     * @param  int $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setQuantity($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->quantity !== $v) {
            $this->quantity = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::QUANTITY;
        }


        return $this;
    } // setQuantity()

    /**
     * Set the value of [unit] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setUnit($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->unit !== $v) {
            $this->unit = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::UNIT;
        }


        return $this;
    } // setUnit()

    /**
     * Sets the value of the [is_voucher] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setIsVoucher($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_voucher !== $v) {
            $this->is_voucher = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::IS_VOUCHER;
        }


        return $this;
    } // setIsVoucher()

    /**
     * Set the value of [note] column.
     *
     * @param  string $v new value
     * @return OrdersLines The current object (for fluent API support)
     */
    public function setNote($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->note !== $v) {
            $this->note = $v;
            $this->modifiedColumns[] = OrdersLinesPeer::NOTE;
        }


        return $this;
    } // setNote()

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
            if ($this->expected_at !== '1970-01-01') {
                return false;
            }

            if ($this->vat !== '0.00') {
                return false;
            }

            if ($this->is_voucher !== false) {
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
            $this->orders_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->type = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->products_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->products_sku = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->products_name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->products_color = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->products_size = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->expected_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->original_price = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->price = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->vat = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->quantity = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->unit = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->is_voucher = ($row[$startcol + 14] !== null) ? (boolean) $row[$startcol + 14] : null;
            $this->note = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 16; // 16 = OrdersLinesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating OrdersLines object", $e);
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

        if ($this->aOrders !== null && $this->orders_id !== $this->aOrders->getId()) {
            $this->aOrders = null;
        }
        if ($this->aProducts !== null && $this->products_id !== $this->aProducts->getId()) {
            $this->aProducts = null;
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
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = OrdersLinesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aOrders = null;
            $this->aProducts = null;
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
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = OrdersLinesQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            // event behavior
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
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
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // event behavior
            EventDispatcherProxy::trigger('model.save.pre', new ModelEvent($this));
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // event behavior
                EventDispatcherProxy::trigger('model.insert.pre', new ModelEvent($this));
            } else {
                $ret = $ret && $this->preUpdate($con);
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
                OrdersLinesPeer::addInstanceToPool($this);
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

            if ($this->aOrders !== null) {
                if ($this->aOrders->isModified() || $this->aOrders->isNew()) {
                    $affectedRows += $this->aOrders->save($con);
                }
                $this->setOrders($this->aOrders);
            }

            if ($this->aProducts !== null) {
                if ($this->aProducts->isModified() || $this->aProducts->isNew()) {
                    $affectedRows += $this->aProducts->save($con);
                }
                $this->setProducts($this->aProducts);
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
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = OrdersLinesPeer::ID;

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(OrdersLinesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::ORDERS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`orders_id`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '`type`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`products_id`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_SKU)) {
            $modifiedColumns[':p' . $index++]  = '`products_sku`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`products_name`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_COLOR)) {
            $modifiedColumns[':p' . $index++]  = '`products_color`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_SIZE)) {
            $modifiedColumns[':p' . $index++]  = '`products_size`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::EXPECTED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`expected_at`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::ORIGINAL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`original_price`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`price`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::VAT)) {
            $modifiedColumns[':p' . $index++]  = '`vat`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::QUANTITY)) {
            $modifiedColumns[':p' . $index++]  = '`quantity`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::UNIT)) {
            $modifiedColumns[':p' . $index++]  = '`unit`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::IS_VOUCHER)) {
            $modifiedColumns[':p' . $index++]  = '`is_voucher`';
        }
        if ($this->isColumnModified(OrdersLinesPeer::NOTE)) {
            $modifiedColumns[':p' . $index++]  = '`note`';
        }

        $sql = sprintf(
            'INSERT INTO `orders_lines` (%s) VALUES (%s)',
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
                    case '`orders_id`':
                        $stmt->bindValue($identifier, $this->orders_id, PDO::PARAM_INT);
                        break;
                    case '`type`':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case '`products_id`':
                        $stmt->bindValue($identifier, $this->products_id, PDO::PARAM_INT);
                        break;
                    case '`products_sku`':
                        $stmt->bindValue($identifier, $this->products_sku, PDO::PARAM_STR);
                        break;
                    case '`products_name`':
                        $stmt->bindValue($identifier, $this->products_name, PDO::PARAM_STR);
                        break;
                    case '`products_color`':
                        $stmt->bindValue($identifier, $this->products_color, PDO::PARAM_STR);
                        break;
                    case '`products_size`':
                        $stmt->bindValue($identifier, $this->products_size, PDO::PARAM_STR);
                        break;
                    case '`expected_at`':
                        $stmt->bindValue($identifier, $this->expected_at, PDO::PARAM_STR);
                        break;
                    case '`original_price`':
                        $stmt->bindValue($identifier, $this->original_price, PDO::PARAM_STR);
                        break;
                    case '`price`':
                        $stmt->bindValue($identifier, $this->price, PDO::PARAM_STR);
                        break;
                    case '`vat`':
                        $stmt->bindValue($identifier, $this->vat, PDO::PARAM_STR);
                        break;
                    case '`quantity`':
                        $stmt->bindValue($identifier, $this->quantity, PDO::PARAM_INT);
                        break;
                    case '`unit`':
                        $stmt->bindValue($identifier, $this->unit, PDO::PARAM_STR);
                        break;
                    case '`is_voucher`':
                        $stmt->bindValue($identifier, (int) $this->is_voucher, PDO::PARAM_INT);
                        break;
                    case '`note`':
                        $stmt->bindValue($identifier, $this->note, PDO::PARAM_STR);
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

            if ($this->aOrders !== null) {
                if (!$this->aOrders->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aOrders->getValidationFailures());
                }
            }

            if ($this->aProducts !== null) {
                if (!$this->aProducts->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProducts->getValidationFailures());
                }
            }


            if (($retval = OrdersLinesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
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
        $pos = OrdersLinesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getOrdersId();
                break;
            case 2:
                return $this->getType();
                break;
            case 3:
                return $this->getProductsId();
                break;
            case 4:
                return $this->getProductsSku();
                break;
            case 5:
                return $this->getProductsName();
                break;
            case 6:
                return $this->getProductsColor();
                break;
            case 7:
                return $this->getProductsSize();
                break;
            case 8:
                return $this->getExpectedAt();
                break;
            case 9:
                return $this->getOriginalPrice();
                break;
            case 10:
                return $this->getPrice();
                break;
            case 11:
                return $this->getVat();
                break;
            case 12:
                return $this->getQuantity();
                break;
            case 13:
                return $this->getUnit();
                break;
            case 14:
                return $this->getIsVoucher();
                break;
            case 15:
                return $this->getNote();
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
        if (isset($alreadyDumpedObjects['OrdersLines'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['OrdersLines'][$this->getPrimaryKey()] = true;
        $keys = OrdersLinesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getOrdersId(),
            $keys[2] => $this->getType(),
            $keys[3] => $this->getProductsId(),
            $keys[4] => $this->getProductsSku(),
            $keys[5] => $this->getProductsName(),
            $keys[6] => $this->getProductsColor(),
            $keys[7] => $this->getProductsSize(),
            $keys[8] => $this->getExpectedAt(),
            $keys[9] => $this->getOriginalPrice(),
            $keys[10] => $this->getPrice(),
            $keys[11] => $this->getVat(),
            $keys[12] => $this->getQuantity(),
            $keys[13] => $this->getUnit(),
            $keys[14] => $this->getIsVoucher(),
            $keys[15] => $this->getNote(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aOrders) {
                $result['Orders'] = $this->aOrders->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProducts) {
                $result['Products'] = $this->aProducts->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = OrdersLinesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setOrdersId($value);
                break;
            case 2:
                $this->setType($value);
                break;
            case 3:
                $this->setProductsId($value);
                break;
            case 4:
                $this->setProductsSku($value);
                break;
            case 5:
                $this->setProductsName($value);
                break;
            case 6:
                $this->setProductsColor($value);
                break;
            case 7:
                $this->setProductsSize($value);
                break;
            case 8:
                $this->setExpectedAt($value);
                break;
            case 9:
                $this->setOriginalPrice($value);
                break;
            case 10:
                $this->setPrice($value);
                break;
            case 11:
                $this->setVat($value);
                break;
            case 12:
                $this->setQuantity($value);
                break;
            case 13:
                $this->setUnit($value);
                break;
            case 14:
                $this->setIsVoucher($value);
                break;
            case 15:
                $this->setNote($value);
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
        $keys = OrdersLinesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setOrdersId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setType($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setProductsId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setProductsSku($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setProductsName($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setProductsColor($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setProductsSize($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setExpectedAt($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setOriginalPrice($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setPrice($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setVat($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setQuantity($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setUnit($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setIsVoucher($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setNote($arr[$keys[15]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(OrdersLinesPeer::DATABASE_NAME);

        if ($this->isColumnModified(OrdersLinesPeer::ID)) $criteria->add(OrdersLinesPeer::ID, $this->id);
        if ($this->isColumnModified(OrdersLinesPeer::ORDERS_ID)) $criteria->add(OrdersLinesPeer::ORDERS_ID, $this->orders_id);
        if ($this->isColumnModified(OrdersLinesPeer::TYPE)) $criteria->add(OrdersLinesPeer::TYPE, $this->type);
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_ID)) $criteria->add(OrdersLinesPeer::PRODUCTS_ID, $this->products_id);
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_SKU)) $criteria->add(OrdersLinesPeer::PRODUCTS_SKU, $this->products_sku);
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_NAME)) $criteria->add(OrdersLinesPeer::PRODUCTS_NAME, $this->products_name);
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_COLOR)) $criteria->add(OrdersLinesPeer::PRODUCTS_COLOR, $this->products_color);
        if ($this->isColumnModified(OrdersLinesPeer::PRODUCTS_SIZE)) $criteria->add(OrdersLinesPeer::PRODUCTS_SIZE, $this->products_size);
        if ($this->isColumnModified(OrdersLinesPeer::EXPECTED_AT)) $criteria->add(OrdersLinesPeer::EXPECTED_AT, $this->expected_at);
        if ($this->isColumnModified(OrdersLinesPeer::ORIGINAL_PRICE)) $criteria->add(OrdersLinesPeer::ORIGINAL_PRICE, $this->original_price);
        if ($this->isColumnModified(OrdersLinesPeer::PRICE)) $criteria->add(OrdersLinesPeer::PRICE, $this->price);
        if ($this->isColumnModified(OrdersLinesPeer::VAT)) $criteria->add(OrdersLinesPeer::VAT, $this->vat);
        if ($this->isColumnModified(OrdersLinesPeer::QUANTITY)) $criteria->add(OrdersLinesPeer::QUANTITY, $this->quantity);
        if ($this->isColumnModified(OrdersLinesPeer::UNIT)) $criteria->add(OrdersLinesPeer::UNIT, $this->unit);
        if ($this->isColumnModified(OrdersLinesPeer::IS_VOUCHER)) $criteria->add(OrdersLinesPeer::IS_VOUCHER, $this->is_voucher);
        if ($this->isColumnModified(OrdersLinesPeer::NOTE)) $criteria->add(OrdersLinesPeer::NOTE, $this->note);

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
        $criteria = new Criteria(OrdersLinesPeer::DATABASE_NAME);
        $criteria->add(OrdersLinesPeer::ID, $this->id);

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
     * @param object $copyObj An object of OrdersLines (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setOrdersId($this->getOrdersId());
        $copyObj->setType($this->getType());
        $copyObj->setProductsId($this->getProductsId());
        $copyObj->setProductsSku($this->getProductsSku());
        $copyObj->setProductsName($this->getProductsName());
        $copyObj->setProductsColor($this->getProductsColor());
        $copyObj->setProductsSize($this->getProductsSize());
        $copyObj->setExpectedAt($this->getExpectedAt());
        $copyObj->setOriginalPrice($this->getOriginalPrice());
        $copyObj->setPrice($this->getPrice());
        $copyObj->setVat($this->getVat());
        $copyObj->setQuantity($this->getQuantity());
        $copyObj->setUnit($this->getUnit());
        $copyObj->setIsVoucher($this->getIsVoucher());
        $copyObj->setNote($this->getNote());

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
     * @return OrdersLines Clone of current object.
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
     * @return OrdersLinesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new OrdersLinesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Orders object.
     *
     * @param                  Orders $v
     * @return OrdersLines The current object (for fluent API support)
     * @throws PropelException
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
            $v->addOrdersLines($this);
        }


        return $this;
    }


    /**
     * Get the associated Orders object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Orders The associated Orders object.
     * @throws PropelException
     */
    public function getOrders(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aOrders === null && ($this->orders_id !== null) && $doQuery) {
            $this->aOrders = OrdersQuery::create()->findPk($this->orders_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aOrders->addOrdersLiness($this);
             */
        }

        return $this->aOrders;
    }

    /**
     * Declares an association between this object and a Products object.
     *
     * @param                  Products $v
     * @return OrdersLines The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProducts(Products $v = null)
    {
        if ($v === null) {
            $this->setProductsId(NULL);
        } else {
            $this->setProductsId($v->getId());
        }

        $this->aProducts = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Products object, it will not be re-added.
        if ($v !== null) {
            $v->addOrdersLines($this);
        }


        return $this;
    }


    /**
     * Get the associated Products object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Products The associated Products object.
     * @throws PropelException
     */
    public function getProducts(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aProducts === null && ($this->products_id !== null) && $doQuery) {
            $this->aProducts = ProductsQuery::create()->findPk($this->products_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProducts->addOrdersLiness($this);
             */
        }

        return $this->aProducts;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->orders_id = null;
        $this->type = null;
        $this->products_id = null;
        $this->products_sku = null;
        $this->products_name = null;
        $this->products_color = null;
        $this->products_size = null;
        $this->expected_at = null;
        $this->original_price = null;
        $this->price = null;
        $this->vat = null;
        $this->quantity = null;
        $this->unit = null;
        $this->is_voucher = null;
        $this->note = null;
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
            if ($this->aOrders instanceof Persistent) {
              $this->aOrders->clearAllReferences($deep);
            }
            if ($this->aProducts instanceof Persistent) {
              $this->aProducts->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aOrders = null;
        $this->aProducts = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(OrdersLinesPeer::DEFAULT_STRING_FORMAT);
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

}

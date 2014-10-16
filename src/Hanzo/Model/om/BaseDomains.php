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
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Model\Domains;
use Hanzo\Model\DomainsPeer;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\DomainsSettings;
use Hanzo\Model\DomainsSettingsQuery;
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsQuantityDiscount;
use Hanzo\Model\ProductsQuantityDiscountQuery;

abstract class BaseDomains extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\DomainsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DomainsPeer
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
     * The value for the domain_name field.
     * @var        string
     */
    protected $domain_name;

    /**
     * The value for the domain_key field.
     * @var        string
     */
    protected $domain_key;

    /**
     * @var        PropelObjectCollection|DomainsSettings[] Collection to store aggregation of DomainsSettings objects.
     */
    protected $collDomainsSettingss;
    protected $collDomainsSettingssPartial;

    /**
     * @var        PropelObjectCollection|ProductsDomainsPrices[] Collection to store aggregation of ProductsDomainsPrices objects.
     */
    protected $collProductsDomainsPricess;
    protected $collProductsDomainsPricessPartial;

    /**
     * @var        PropelObjectCollection|ProductsQuantityDiscount[] Collection to store aggregation of ProductsQuantityDiscount objects.
     */
    protected $collProductsQuantityDiscounts;
    protected $collProductsQuantityDiscountsPartial;

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
    protected $domainsSettingssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsDomainsPricessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsQuantityDiscountsScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    public function __construct(){
        parent::__construct();
        EventDispatcherProxy::trigger(array('construct','model.construct'), new ModelEvent($this));
    }

    /**
     * Get the [domain_name] column value.
     *
     * @return string
     */
    public function getDomainName()
    {

        return $this->domain_name;
    }

    /**
     * Get the [domain_key] column value.
     *
     * @return string
     */
    public function getDomainKey()
    {

        return $this->domain_key;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Domains The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = DomainsPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [domain_name] column.
     *
     * @param  string $v new value
     * @return Domains The current object (for fluent API support)
     */
    public function setDomainName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->domain_name !== $v) {
            $this->domain_name = $v;
            $this->modifiedColumns[] = DomainsPeer::DOMAIN_NAME;
        }


        return $this;
    } // setDomainName()

    /**
     * Set the value of [domain_key] column.
     *
     * @param  string $v new value
     * @return Domains The current object (for fluent API support)
     */
    public function setDomainKey($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->domain_key !== $v) {
            $this->domain_key = $v;
            $this->modifiedColumns[] = DomainsPeer::DOMAIN_KEY;
        }


        return $this;
    } // setDomainKey()

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
            $this->domain_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->domain_key = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 3; // 3 = DomainsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Domains object", $e);
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
            $con = Propel::getConnection(DomainsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = DomainsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collDomainsSettingss = null;

            $this->collProductsDomainsPricess = null;

            $this->collProductsQuantityDiscounts = null;

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
            $con = Propel::getConnection(DomainsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
            $deleteQuery = DomainsQuery::create()
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
            $con = Propel::getConnection(DomainsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                DomainsPeer::addInstanceToPool($this);
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

            if ($this->domainsSettingssScheduledForDeletion !== null) {
                if (!$this->domainsSettingssScheduledForDeletion->isEmpty()) {
                    DomainsSettingsQuery::create()
                        ->filterByPrimaryKeys($this->domainsSettingssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->domainsSettingssScheduledForDeletion = null;
                }
            }

            if ($this->collDomainsSettingss !== null) {
                foreach ($this->collDomainsSettingss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsDomainsPricessScheduledForDeletion !== null) {
                if (!$this->productsDomainsPricessScheduledForDeletion->isEmpty()) {
                    ProductsDomainsPricesQuery::create()
                        ->filterByPrimaryKeys($this->productsDomainsPricessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsDomainsPricessScheduledForDeletion = null;
                }
            }

            if ($this->collProductsDomainsPricess !== null) {
                foreach ($this->collProductsDomainsPricess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsQuantityDiscountsScheduledForDeletion !== null) {
                if (!$this->productsQuantityDiscountsScheduledForDeletion->isEmpty()) {
                    ProductsQuantityDiscountQuery::create()
                        ->filterByPrimaryKeys($this->productsQuantityDiscountsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsQuantityDiscountsScheduledForDeletion = null;
                }
            }

            if ($this->collProductsQuantityDiscounts !== null) {
                foreach ($this->collProductsQuantityDiscounts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
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

        $this->modifiedColumns[] = DomainsPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DomainsPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DomainsPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(DomainsPeer::DOMAIN_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`domain_name`';
        }
        if ($this->isColumnModified(DomainsPeer::DOMAIN_KEY)) {
            $modifiedColumns[':p' . $index++]  = '`domain_key`';
        }

        $sql = sprintf(
            'INSERT INTO `domains` (%s) VALUES (%s)',
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
                    case '`domain_name`':
                        $stmt->bindValue($identifier, $this->domain_name, PDO::PARAM_STR);
                        break;
                    case '`domain_key`':
                        $stmt->bindValue($identifier, $this->domain_key, PDO::PARAM_STR);
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


            if (($retval = DomainsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collDomainsSettingss !== null) {
                    foreach ($this->collDomainsSettingss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductsDomainsPricess !== null) {
                    foreach ($this->collProductsDomainsPricess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductsQuantityDiscounts !== null) {
                    foreach ($this->collProductsQuantityDiscounts as $referrerFK) {
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
        $pos = DomainsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDomainName();
                break;
            case 2:
                return $this->getDomainKey();
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
        if (isset($alreadyDumpedObjects['Domains'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Domains'][$this->getPrimaryKey()] = true;
        $keys = DomainsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDomainName(),
            $keys[2] => $this->getDomainKey(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collDomainsSettingss) {
                $result['DomainsSettingss'] = $this->collDomainsSettingss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsDomainsPricess) {
                $result['ProductsDomainsPricess'] = $this->collProductsDomainsPricess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsQuantityDiscounts) {
                $result['ProductsQuantityDiscounts'] = $this->collProductsQuantityDiscounts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = DomainsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDomainName($value);
                break;
            case 2:
                $this->setDomainKey($value);
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
        $keys = DomainsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDomainName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDomainKey($arr[$keys[2]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DomainsPeer::DATABASE_NAME);

        if ($this->isColumnModified(DomainsPeer::ID)) $criteria->add(DomainsPeer::ID, $this->id);
        if ($this->isColumnModified(DomainsPeer::DOMAIN_NAME)) $criteria->add(DomainsPeer::DOMAIN_NAME, $this->domain_name);
        if ($this->isColumnModified(DomainsPeer::DOMAIN_KEY)) $criteria->add(DomainsPeer::DOMAIN_KEY, $this->domain_key);

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
        $criteria = new Criteria(DomainsPeer::DATABASE_NAME);
        $criteria->add(DomainsPeer::ID, $this->id);

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
     * @param object $copyObj An object of Domains (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDomainName($this->getDomainName());
        $copyObj->setDomainKey($this->getDomainKey());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getDomainsSettingss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDomainsSettings($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsDomainsPricess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsDomainsPrices($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsQuantityDiscounts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsQuantityDiscount($relObj->copy($deepCopy));
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
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Domains Clone of current object.
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
     * @return DomainsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DomainsPeer();
        }

        return self::$peer;
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
        if ('DomainsSettings' == $relationName) {
            $this->initDomainsSettingss();
        }
        if ('ProductsDomainsPrices' == $relationName) {
            $this->initProductsDomainsPricess();
        }
        if ('ProductsQuantityDiscount' == $relationName) {
            $this->initProductsQuantityDiscounts();
        }
    }

    /**
     * Clears out the collDomainsSettingss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Domains The current object (for fluent API support)
     * @see        addDomainsSettingss()
     */
    public function clearDomainsSettingss()
    {
        $this->collDomainsSettingss = null; // important to set this to null since that means it is uninitialized
        $this->collDomainsSettingssPartial = null;

        return $this;
    }

    /**
     * reset is the collDomainsSettingss collection loaded partially
     *
     * @return void
     */
    public function resetPartialDomainsSettingss($v = true)
    {
        $this->collDomainsSettingssPartial = $v;
    }

    /**
     * Initializes the collDomainsSettingss collection.
     *
     * By default this just sets the collDomainsSettingss collection to an empty array (like clearcollDomainsSettingss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDomainsSettingss($overrideExisting = true)
    {
        if (null !== $this->collDomainsSettingss && !$overrideExisting) {
            return;
        }
        $this->collDomainsSettingss = new PropelObjectCollection();
        $this->collDomainsSettingss->setModel('DomainsSettings');
    }

    /**
     * Gets an array of DomainsSettings objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Domains is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|DomainsSettings[] List of DomainsSettings objects
     * @throws PropelException
     */
    public function getDomainsSettingss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collDomainsSettingssPartial && !$this->isNew();
        if (null === $this->collDomainsSettingss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDomainsSettingss) {
                // return empty collection
                $this->initDomainsSettingss();
            } else {
                $collDomainsSettingss = DomainsSettingsQuery::create(null, $criteria)
                    ->filterByDomains($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collDomainsSettingssPartial && count($collDomainsSettingss)) {
                      $this->initDomainsSettingss(false);

                      foreach ($collDomainsSettingss as $obj) {
                        if (false == $this->collDomainsSettingss->contains($obj)) {
                          $this->collDomainsSettingss->append($obj);
                        }
                      }

                      $this->collDomainsSettingssPartial = true;
                    }

                    $collDomainsSettingss->getInternalIterator()->rewind();

                    return $collDomainsSettingss;
                }

                if ($partial && $this->collDomainsSettingss) {
                    foreach ($this->collDomainsSettingss as $obj) {
                        if ($obj->isNew()) {
                            $collDomainsSettingss[] = $obj;
                        }
                    }
                }

                $this->collDomainsSettingss = $collDomainsSettingss;
                $this->collDomainsSettingssPartial = false;
            }
        }

        return $this->collDomainsSettingss;
    }

    /**
     * Sets a collection of DomainsSettings objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $domainsSettingss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Domains The current object (for fluent API support)
     */
    public function setDomainsSettingss(PropelCollection $domainsSettingss, PropelPDO $con = null)
    {
        $domainsSettingssToDelete = $this->getDomainsSettingss(new Criteria(), $con)->diff($domainsSettingss);


        $this->domainsSettingssScheduledForDeletion = $domainsSettingssToDelete;

        foreach ($domainsSettingssToDelete as $domainsSettingsRemoved) {
            $domainsSettingsRemoved->setDomains(null);
        }

        $this->collDomainsSettingss = null;
        foreach ($domainsSettingss as $domainsSettings) {
            $this->addDomainsSettings($domainsSettings);
        }

        $this->collDomainsSettingss = $domainsSettingss;
        $this->collDomainsSettingssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DomainsSettings objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related DomainsSettings objects.
     * @throws PropelException
     */
    public function countDomainsSettingss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collDomainsSettingssPartial && !$this->isNew();
        if (null === $this->collDomainsSettingss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDomainsSettingss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDomainsSettingss());
            }
            $query = DomainsSettingsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDomains($this)
                ->count($con);
        }

        return count($this->collDomainsSettingss);
    }

    /**
     * Method called to associate a DomainsSettings object to this object
     * through the DomainsSettings foreign key attribute.
     *
     * @param    DomainsSettings $l DomainsSettings
     * @return Domains The current object (for fluent API support)
     */
    public function addDomainsSettings(DomainsSettings $l)
    {
        if ($this->collDomainsSettingss === null) {
            $this->initDomainsSettingss();
            $this->collDomainsSettingssPartial = true;
        }

        if (!in_array($l, $this->collDomainsSettingss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDomainsSettings($l);

            if ($this->domainsSettingssScheduledForDeletion and $this->domainsSettingssScheduledForDeletion->contains($l)) {
                $this->domainsSettingssScheduledForDeletion->remove($this->domainsSettingssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	DomainsSettings $domainsSettings The domainsSettings object to add.
     */
    protected function doAddDomainsSettings($domainsSettings)
    {
        $this->collDomainsSettingss[]= $domainsSettings;
        $domainsSettings->setDomains($this);
    }

    /**
     * @param	DomainsSettings $domainsSettings The domainsSettings object to remove.
     * @return Domains The current object (for fluent API support)
     */
    public function removeDomainsSettings($domainsSettings)
    {
        if ($this->getDomainsSettingss()->contains($domainsSettings)) {
            $this->collDomainsSettingss->remove($this->collDomainsSettingss->search($domainsSettings));
            if (null === $this->domainsSettingssScheduledForDeletion) {
                $this->domainsSettingssScheduledForDeletion = clone $this->collDomainsSettingss;
                $this->domainsSettingssScheduledForDeletion->clear();
            }
            $this->domainsSettingssScheduledForDeletion[]= clone $domainsSettings;
            $domainsSettings->setDomains(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductsDomainsPricess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Domains The current object (for fluent API support)
     * @see        addProductsDomainsPricess()
     */
    public function clearProductsDomainsPricess()
    {
        $this->collProductsDomainsPricess = null; // important to set this to null since that means it is uninitialized
        $this->collProductsDomainsPricessPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsDomainsPricess collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsDomainsPricess($v = true)
    {
        $this->collProductsDomainsPricessPartial = $v;
    }

    /**
     * Initializes the collProductsDomainsPricess collection.
     *
     * By default this just sets the collProductsDomainsPricess collection to an empty array (like clearcollProductsDomainsPricess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsDomainsPricess($overrideExisting = true)
    {
        if (null !== $this->collProductsDomainsPricess && !$overrideExisting) {
            return;
        }
        $this->collProductsDomainsPricess = new PropelObjectCollection();
        $this->collProductsDomainsPricess->setModel('ProductsDomainsPrices');
    }

    /**
     * Gets an array of ProductsDomainsPrices objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Domains is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsDomainsPrices[] List of ProductsDomainsPrices objects
     * @throws PropelException
     */
    public function getProductsDomainsPricess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsDomainsPricessPartial && !$this->isNew();
        if (null === $this->collProductsDomainsPricess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsDomainsPricess) {
                // return empty collection
                $this->initProductsDomainsPricess();
            } else {
                $collProductsDomainsPricess = ProductsDomainsPricesQuery::create(null, $criteria)
                    ->filterByDomains($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsDomainsPricessPartial && count($collProductsDomainsPricess)) {
                      $this->initProductsDomainsPricess(false);

                      foreach ($collProductsDomainsPricess as $obj) {
                        if (false == $this->collProductsDomainsPricess->contains($obj)) {
                          $this->collProductsDomainsPricess->append($obj);
                        }
                      }

                      $this->collProductsDomainsPricessPartial = true;
                    }

                    $collProductsDomainsPricess->getInternalIterator()->rewind();

                    return $collProductsDomainsPricess;
                }

                if ($partial && $this->collProductsDomainsPricess) {
                    foreach ($this->collProductsDomainsPricess as $obj) {
                        if ($obj->isNew()) {
                            $collProductsDomainsPricess[] = $obj;
                        }
                    }
                }

                $this->collProductsDomainsPricess = $collProductsDomainsPricess;
                $this->collProductsDomainsPricessPartial = false;
            }
        }

        return $this->collProductsDomainsPricess;
    }

    /**
     * Sets a collection of ProductsDomainsPrices objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsDomainsPricess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Domains The current object (for fluent API support)
     */
    public function setProductsDomainsPricess(PropelCollection $productsDomainsPricess, PropelPDO $con = null)
    {
        $productsDomainsPricessToDelete = $this->getProductsDomainsPricess(new Criteria(), $con)->diff($productsDomainsPricess);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->productsDomainsPricessScheduledForDeletion = clone $productsDomainsPricessToDelete;

        foreach ($productsDomainsPricessToDelete as $productsDomainsPricesRemoved) {
            $productsDomainsPricesRemoved->setDomains(null);
        }

        $this->collProductsDomainsPricess = null;
        foreach ($productsDomainsPricess as $productsDomainsPrices) {
            $this->addProductsDomainsPrices($productsDomainsPrices);
        }

        $this->collProductsDomainsPricess = $productsDomainsPricess;
        $this->collProductsDomainsPricessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsDomainsPrices objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsDomainsPrices objects.
     * @throws PropelException
     */
    public function countProductsDomainsPricess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsDomainsPricessPartial && !$this->isNew();
        if (null === $this->collProductsDomainsPricess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsDomainsPricess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsDomainsPricess());
            }
            $query = ProductsDomainsPricesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDomains($this)
                ->count($con);
        }

        return count($this->collProductsDomainsPricess);
    }

    /**
     * Method called to associate a ProductsDomainsPrices object to this object
     * through the ProductsDomainsPrices foreign key attribute.
     *
     * @param    ProductsDomainsPrices $l ProductsDomainsPrices
     * @return Domains The current object (for fluent API support)
     */
    public function addProductsDomainsPrices(ProductsDomainsPrices $l)
    {
        if ($this->collProductsDomainsPricess === null) {
            $this->initProductsDomainsPricess();
            $this->collProductsDomainsPricessPartial = true;
        }

        if (!in_array($l, $this->collProductsDomainsPricess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsDomainsPrices($l);

            if ($this->productsDomainsPricessScheduledForDeletion and $this->productsDomainsPricessScheduledForDeletion->contains($l)) {
                $this->productsDomainsPricessScheduledForDeletion->remove($this->productsDomainsPricessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	ProductsDomainsPrices $productsDomainsPrices The productsDomainsPrices object to add.
     */
    protected function doAddProductsDomainsPrices($productsDomainsPrices)
    {
        $this->collProductsDomainsPricess[]= $productsDomainsPrices;
        $productsDomainsPrices->setDomains($this);
    }

    /**
     * @param	ProductsDomainsPrices $productsDomainsPrices The productsDomainsPrices object to remove.
     * @return Domains The current object (for fluent API support)
     */
    public function removeProductsDomainsPrices($productsDomainsPrices)
    {
        if ($this->getProductsDomainsPricess()->contains($productsDomainsPrices)) {
            $this->collProductsDomainsPricess->remove($this->collProductsDomainsPricess->search($productsDomainsPrices));
            if (null === $this->productsDomainsPricessScheduledForDeletion) {
                $this->productsDomainsPricessScheduledForDeletion = clone $this->collProductsDomainsPricess;
                $this->productsDomainsPricessScheduledForDeletion->clear();
            }
            $this->productsDomainsPricessScheduledForDeletion[]= clone $productsDomainsPrices;
            $productsDomainsPrices->setDomains(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Domains is new, it will return
     * an empty collection; or if this Domains has previously
     * been saved, it will retrieve related ProductsDomainsPricess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Domains.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsDomainsPrices[] List of ProductsDomainsPrices objects
     */
    public function getProductsDomainsPricessJoinProducts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsDomainsPricesQuery::create(null, $criteria);
        $query->joinWith('Products', $join_behavior);

        return $this->getProductsDomainsPricess($query, $con);
    }

    /**
     * Clears out the collProductsQuantityDiscounts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Domains The current object (for fluent API support)
     * @see        addProductsQuantityDiscounts()
     */
    public function clearProductsQuantityDiscounts()
    {
        $this->collProductsQuantityDiscounts = null; // important to set this to null since that means it is uninitialized
        $this->collProductsQuantityDiscountsPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsQuantityDiscounts collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsQuantityDiscounts($v = true)
    {
        $this->collProductsQuantityDiscountsPartial = $v;
    }

    /**
     * Initializes the collProductsQuantityDiscounts collection.
     *
     * By default this just sets the collProductsQuantityDiscounts collection to an empty array (like clearcollProductsQuantityDiscounts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsQuantityDiscounts($overrideExisting = true)
    {
        if (null !== $this->collProductsQuantityDiscounts && !$overrideExisting) {
            return;
        }
        $this->collProductsQuantityDiscounts = new PropelObjectCollection();
        $this->collProductsQuantityDiscounts->setModel('ProductsQuantityDiscount');
    }

    /**
     * Gets an array of ProductsQuantityDiscount objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Domains is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsQuantityDiscount[] List of ProductsQuantityDiscount objects
     * @throws PropelException
     */
    public function getProductsQuantityDiscounts($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsQuantityDiscountsPartial && !$this->isNew();
        if (null === $this->collProductsQuantityDiscounts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsQuantityDiscounts) {
                // return empty collection
                $this->initProductsQuantityDiscounts();
            } else {
                $collProductsQuantityDiscounts = ProductsQuantityDiscountQuery::create(null, $criteria)
                    ->filterByDomains($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsQuantityDiscountsPartial && count($collProductsQuantityDiscounts)) {
                      $this->initProductsQuantityDiscounts(false);

                      foreach ($collProductsQuantityDiscounts as $obj) {
                        if (false == $this->collProductsQuantityDiscounts->contains($obj)) {
                          $this->collProductsQuantityDiscounts->append($obj);
                        }
                      }

                      $this->collProductsQuantityDiscountsPartial = true;
                    }

                    $collProductsQuantityDiscounts->getInternalIterator()->rewind();

                    return $collProductsQuantityDiscounts;
                }

                if ($partial && $this->collProductsQuantityDiscounts) {
                    foreach ($this->collProductsQuantityDiscounts as $obj) {
                        if ($obj->isNew()) {
                            $collProductsQuantityDiscounts[] = $obj;
                        }
                    }
                }

                $this->collProductsQuantityDiscounts = $collProductsQuantityDiscounts;
                $this->collProductsQuantityDiscountsPartial = false;
            }
        }

        return $this->collProductsQuantityDiscounts;
    }

    /**
     * Sets a collection of ProductsQuantityDiscount objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsQuantityDiscounts A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Domains The current object (for fluent API support)
     */
    public function setProductsQuantityDiscounts(PropelCollection $productsQuantityDiscounts, PropelPDO $con = null)
    {
        $productsQuantityDiscountsToDelete = $this->getProductsQuantityDiscounts(new Criteria(), $con)->diff($productsQuantityDiscounts);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->productsQuantityDiscountsScheduledForDeletion = clone $productsQuantityDiscountsToDelete;

        foreach ($productsQuantityDiscountsToDelete as $productsQuantityDiscountRemoved) {
            $productsQuantityDiscountRemoved->setDomains(null);
        }

        $this->collProductsQuantityDiscounts = null;
        foreach ($productsQuantityDiscounts as $productsQuantityDiscount) {
            $this->addProductsQuantityDiscount($productsQuantityDiscount);
        }

        $this->collProductsQuantityDiscounts = $productsQuantityDiscounts;
        $this->collProductsQuantityDiscountsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsQuantityDiscount objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsQuantityDiscount objects.
     * @throws PropelException
     */
    public function countProductsQuantityDiscounts(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsQuantityDiscountsPartial && !$this->isNew();
        if (null === $this->collProductsQuantityDiscounts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsQuantityDiscounts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsQuantityDiscounts());
            }
            $query = ProductsQuantityDiscountQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDomains($this)
                ->count($con);
        }

        return count($this->collProductsQuantityDiscounts);
    }

    /**
     * Method called to associate a ProductsQuantityDiscount object to this object
     * through the ProductsQuantityDiscount foreign key attribute.
     *
     * @param    ProductsQuantityDiscount $l ProductsQuantityDiscount
     * @return Domains The current object (for fluent API support)
     */
    public function addProductsQuantityDiscount(ProductsQuantityDiscount $l)
    {
        if ($this->collProductsQuantityDiscounts === null) {
            $this->initProductsQuantityDiscounts();
            $this->collProductsQuantityDiscountsPartial = true;
        }

        if (!in_array($l, $this->collProductsQuantityDiscounts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsQuantityDiscount($l);

            if ($this->productsQuantityDiscountsScheduledForDeletion and $this->productsQuantityDiscountsScheduledForDeletion->contains($l)) {
                $this->productsQuantityDiscountsScheduledForDeletion->remove($this->productsQuantityDiscountsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	ProductsQuantityDiscount $productsQuantityDiscount The productsQuantityDiscount object to add.
     */
    protected function doAddProductsQuantityDiscount($productsQuantityDiscount)
    {
        $this->collProductsQuantityDiscounts[]= $productsQuantityDiscount;
        $productsQuantityDiscount->setDomains($this);
    }

    /**
     * @param	ProductsQuantityDiscount $productsQuantityDiscount The productsQuantityDiscount object to remove.
     * @return Domains The current object (for fluent API support)
     */
    public function removeProductsQuantityDiscount($productsQuantityDiscount)
    {
        if ($this->getProductsQuantityDiscounts()->contains($productsQuantityDiscount)) {
            $this->collProductsQuantityDiscounts->remove($this->collProductsQuantityDiscounts->search($productsQuantityDiscount));
            if (null === $this->productsQuantityDiscountsScheduledForDeletion) {
                $this->productsQuantityDiscountsScheduledForDeletion = clone $this->collProductsQuantityDiscounts;
                $this->productsQuantityDiscountsScheduledForDeletion->clear();
            }
            $this->productsQuantityDiscountsScheduledForDeletion[]= clone $productsQuantityDiscount;
            $productsQuantityDiscount->setDomains(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Domains is new, it will return
     * an empty collection; or if this Domains has previously
     * been saved, it will retrieve related ProductsQuantityDiscounts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Domains.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsQuantityDiscount[] List of ProductsQuantityDiscount objects
     */
    public function getProductsQuantityDiscountsJoinProducts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsQuantityDiscountQuery::create(null, $criteria);
        $query->joinWith('Products', $join_behavior);

        return $this->getProductsQuantityDiscounts($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->domain_name = null;
        $this->domain_key = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
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
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collDomainsSettingss) {
                foreach ($this->collDomainsSettingss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsDomainsPricess) {
                foreach ($this->collProductsDomainsPricess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsQuantityDiscounts) {
                foreach ($this->collProductsQuantityDiscounts as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collDomainsSettingss instanceof PropelCollection) {
            $this->collDomainsSettingss->clearIterator();
        }
        $this->collDomainsSettingss = null;
        if ($this->collProductsDomainsPricess instanceof PropelCollection) {
            $this->collProductsDomainsPricess->clearIterator();
        }
        $this->collProductsDomainsPricess = null;
        if ($this->collProductsQuantityDiscounts instanceof PropelCollection) {
            $this->collProductsQuantityDiscounts->clearIterator();
        }
        $this->collProductsQuantityDiscounts = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DomainsPeer::DEFAULT_STRING_FORMAT);
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

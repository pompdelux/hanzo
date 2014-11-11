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
use Hanzo\Model\Products;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesPeer;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsQuery;

abstract class BaseProductsImages extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\ProductsImagesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProductsImagesPeer
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
     * The value for the products_id field.
     * @var        int
     */
    protected $products_id;

    /**
     * The value for the image field.
     * @var        string
     */
    protected $image;

    /**
     * The value for the color field.
     * @var        string
     */
    protected $color;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * @var        Products
     */
    protected $aProducts;

    /**
     * @var        PropelObjectCollection|ProductsImagesCategoriesSort[] Collection to store aggregation of ProductsImagesCategoriesSort objects.
     */
    protected $collProductsImagesCategoriesSorts;
    protected $collProductsImagesCategoriesSortsPartial;

    /**
     * @var        PropelObjectCollection|ProductsImagesProductReferences[] Collection to store aggregation of ProductsImagesProductReferences objects.
     */
    protected $collProductsImagesProductReferencess;
    protected $collProductsImagesProductReferencessPartial;

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
    protected $productsImagesCategoriesSortsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsImagesProductReferencessScheduledForDeletion = null;

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
     * Get the [products_id] column value.
     *
     * @return int
     */
    public function getProductsId()
    {

        return $this->products_id;
    }

    /**
     * Get the [image] column value.
     *
     * @return string
     */
    public function getImage()
    {

        return $this->image;
    }

    /**
     * Get the [color] column value.
     *
     * @return string
     */
    public function getColor()
    {

        return $this->color;
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
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ProductsImagesPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [products_id] column.
     *
     * @param  int $v new value
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setProductsId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->products_id !== $v) {
            $this->products_id = $v;
            $this->modifiedColumns[] = ProductsImagesPeer::PRODUCTS_ID;
        }

        if ($this->aProducts !== null && $this->aProducts->getId() !== $v) {
            $this->aProducts = null;
        }


        return $this;
    } // setProductsId()

    /**
     * Set the value of [image] column.
     *
     * @param  string $v new value
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setImage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->image !== $v) {
            $this->image = $v;
            $this->modifiedColumns[] = ProductsImagesPeer::IMAGE;
        }


        return $this;
    } // setImage()

    /**
     * Set the value of [color] column.
     *
     * @param  string $v new value
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[] = ProductsImagesPeer::COLOR;
        }


        return $this;
    } // setColor()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = ProductsImagesPeer::TYPE;
        }


        return $this;
    } // setType()

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
            $this->products_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->image = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->color = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = ProductsImagesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating ProductsImages object", $e);
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
            $con = Propel::getConnection(ProductsImagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ProductsImagesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aProducts = null;
            $this->collProductsImagesCategoriesSorts = null;

            $this->collProductsImagesProductReferencess = null;

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
            $con = Propel::getConnection(ProductsImagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
            $deleteQuery = ProductsImagesQuery::create()
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
            $con = Propel::getConnection(ProductsImagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                ProductsImagesPeer::addInstanceToPool($this);
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

            if ($this->productsImagesCategoriesSortsScheduledForDeletion !== null) {
                if (!$this->productsImagesCategoriesSortsScheduledForDeletion->isEmpty()) {
                    ProductsImagesCategoriesSortQuery::create()
                        ->filterByPrimaryKeys($this->productsImagesCategoriesSortsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsImagesCategoriesSortsScheduledForDeletion = null;
                }
            }

            if ($this->collProductsImagesCategoriesSorts !== null) {
                foreach ($this->collProductsImagesCategoriesSorts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsImagesProductReferencessScheduledForDeletion !== null) {
                if (!$this->productsImagesProductReferencessScheduledForDeletion->isEmpty()) {
                    ProductsImagesProductReferencesQuery::create()
                        ->filterByPrimaryKeys($this->productsImagesProductReferencessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsImagesProductReferencessScheduledForDeletion = null;
                }
            }

            if ($this->collProductsImagesProductReferencess !== null) {
                foreach ($this->collProductsImagesProductReferencess as $referrerFK) {
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

        $this->modifiedColumns[] = ProductsImagesPeer::ID;

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProductsImagesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(ProductsImagesPeer::PRODUCTS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`products_id`';
        }
        if ($this->isColumnModified(ProductsImagesPeer::IMAGE)) {
            $modifiedColumns[':p' . $index++]  = '`image`';
        }
        if ($this->isColumnModified(ProductsImagesPeer::COLOR)) {
            $modifiedColumns[':p' . $index++]  = '`color`';
        }
        if ($this->isColumnModified(ProductsImagesPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '`type`';
        }

        $sql = sprintf(
            'INSERT INTO `products_images` (%s) VALUES (%s)',
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
                    case '`products_id`':
                        $stmt->bindValue($identifier, $this->products_id, PDO::PARAM_INT);
                        break;
                    case '`image`':
                        $stmt->bindValue($identifier, $this->image, PDO::PARAM_STR);
                        break;
                    case '`color`':
                        $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
                        break;
                    case '`type`':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
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

            if ($this->aProducts !== null) {
                if (!$this->aProducts->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProducts->getValidationFailures());
                }
            }


            if (($retval = ProductsImagesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collProductsImagesCategoriesSorts !== null) {
                    foreach ($this->collProductsImagesCategoriesSorts as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductsImagesProductReferencess !== null) {
                    foreach ($this->collProductsImagesProductReferencess as $referrerFK) {
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
        $pos = ProductsImagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getProductsId();
                break;
            case 2:
                return $this->getImage();
                break;
            case 3:
                return $this->getColor();
                break;
            case 4:
                return $this->getType();
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
        if (isset($alreadyDumpedObjects['ProductsImages'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ProductsImages'][$this->getPrimaryKey()] = true;
        $keys = ProductsImagesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getProductsId(),
            $keys[2] => $this->getImage(),
            $keys[3] => $this->getColor(),
            $keys[4] => $this->getType(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aProducts) {
                $result['Products'] = $this->aProducts->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collProductsImagesCategoriesSorts) {
                $result['ProductsImagesCategoriesSorts'] = $this->collProductsImagesCategoriesSorts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsImagesProductReferencess) {
                $result['ProductsImagesProductReferencess'] = $this->collProductsImagesProductReferencess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ProductsImagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setProductsId($value);
                break;
            case 2:
                $this->setImage($value);
                break;
            case 3:
                $this->setColor($value);
                break;
            case 4:
                $this->setType($value);
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
        $keys = ProductsImagesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setProductsId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setImage($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setColor($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setType($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProductsImagesPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProductsImagesPeer::ID)) $criteria->add(ProductsImagesPeer::ID, $this->id);
        if ($this->isColumnModified(ProductsImagesPeer::PRODUCTS_ID)) $criteria->add(ProductsImagesPeer::PRODUCTS_ID, $this->products_id);
        if ($this->isColumnModified(ProductsImagesPeer::IMAGE)) $criteria->add(ProductsImagesPeer::IMAGE, $this->image);
        if ($this->isColumnModified(ProductsImagesPeer::COLOR)) $criteria->add(ProductsImagesPeer::COLOR, $this->color);
        if ($this->isColumnModified(ProductsImagesPeer::TYPE)) $criteria->add(ProductsImagesPeer::TYPE, $this->type);

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
        $criteria = new Criteria(ProductsImagesPeer::DATABASE_NAME);
        $criteria->add(ProductsImagesPeer::ID, $this->id);

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
     * @param object $copyObj An object of ProductsImages (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setProductsId($this->getProductsId());
        $copyObj->setImage($this->getImage());
        $copyObj->setColor($this->getColor());
        $copyObj->setType($this->getType());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getProductsImagesCategoriesSorts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsImagesCategoriesSort($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsImagesProductReferencess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsImagesProductReferences($relObj->copy($deepCopy));
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
     * @return ProductsImages Clone of current object.
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
     * @return ProductsImagesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProductsImagesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Products object.
     *
     * @param                  Products $v
     * @return ProductsImages The current object (for fluent API support)
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
            $v->addProductsImages($this);
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
                $this->aProducts->addProductsImagess($this);
             */
        }

        return $this->aProducts;
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
        if ('ProductsImagesCategoriesSort' == $relationName) {
            $this->initProductsImagesCategoriesSorts();
        }
        if ('ProductsImagesProductReferences' == $relationName) {
            $this->initProductsImagesProductReferencess();
        }
    }

    /**
     * Clears out the collProductsImagesCategoriesSorts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return ProductsImages The current object (for fluent API support)
     * @see        addProductsImagesCategoriesSorts()
     */
    public function clearProductsImagesCategoriesSorts()
    {
        $this->collProductsImagesCategoriesSorts = null; // important to set this to null since that means it is uninitialized
        $this->collProductsImagesCategoriesSortsPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsImagesCategoriesSorts collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsImagesCategoriesSorts($v = true)
    {
        $this->collProductsImagesCategoriesSortsPartial = $v;
    }

    /**
     * Initializes the collProductsImagesCategoriesSorts collection.
     *
     * By default this just sets the collProductsImagesCategoriesSorts collection to an empty array (like clearcollProductsImagesCategoriesSorts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsImagesCategoriesSorts($overrideExisting = true)
    {
        if (null !== $this->collProductsImagesCategoriesSorts && !$overrideExisting) {
            return;
        }
        $this->collProductsImagesCategoriesSorts = new PropelObjectCollection();
        $this->collProductsImagesCategoriesSorts->setModel('ProductsImagesCategoriesSort');
    }

    /**
     * Gets an array of ProductsImagesCategoriesSort objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ProductsImages is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsImagesCategoriesSort[] List of ProductsImagesCategoriesSort objects
     * @throws PropelException
     */
    public function getProductsImagesCategoriesSorts($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsImagesCategoriesSortsPartial && !$this->isNew();
        if (null === $this->collProductsImagesCategoriesSorts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsImagesCategoriesSorts) {
                // return empty collection
                $this->initProductsImagesCategoriesSorts();
            } else {
                $collProductsImagesCategoriesSorts = ProductsImagesCategoriesSortQuery::create(null, $criteria)
                    ->filterByProductsImages($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsImagesCategoriesSortsPartial && count($collProductsImagesCategoriesSorts)) {
                      $this->initProductsImagesCategoriesSorts(false);

                      foreach ($collProductsImagesCategoriesSorts as $obj) {
                        if (false == $this->collProductsImagesCategoriesSorts->contains($obj)) {
                          $this->collProductsImagesCategoriesSorts->append($obj);
                        }
                      }

                      $this->collProductsImagesCategoriesSortsPartial = true;
                    }

                    $collProductsImagesCategoriesSorts->getInternalIterator()->rewind();

                    return $collProductsImagesCategoriesSorts;
                }

                if ($partial && $this->collProductsImagesCategoriesSorts) {
                    foreach ($this->collProductsImagesCategoriesSorts as $obj) {
                        if ($obj->isNew()) {
                            $collProductsImagesCategoriesSorts[] = $obj;
                        }
                    }
                }

                $this->collProductsImagesCategoriesSorts = $collProductsImagesCategoriesSorts;
                $this->collProductsImagesCategoriesSortsPartial = false;
            }
        }

        return $this->collProductsImagesCategoriesSorts;
    }

    /**
     * Sets a collection of ProductsImagesCategoriesSort objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsImagesCategoriesSorts A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setProductsImagesCategoriesSorts(PropelCollection $productsImagesCategoriesSorts, PropelPDO $con = null)
    {
        $productsImagesCategoriesSortsToDelete = $this->getProductsImagesCategoriesSorts(new Criteria(), $con)->diff($productsImagesCategoriesSorts);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->productsImagesCategoriesSortsScheduledForDeletion = clone $productsImagesCategoriesSortsToDelete;

        foreach ($productsImagesCategoriesSortsToDelete as $productsImagesCategoriesSortRemoved) {
            $productsImagesCategoriesSortRemoved->setProductsImages(null);
        }

        $this->collProductsImagesCategoriesSorts = null;
        foreach ($productsImagesCategoriesSorts as $productsImagesCategoriesSort) {
            $this->addProductsImagesCategoriesSort($productsImagesCategoriesSort);
        }

        $this->collProductsImagesCategoriesSorts = $productsImagesCategoriesSorts;
        $this->collProductsImagesCategoriesSortsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsImagesCategoriesSort objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsImagesCategoriesSort objects.
     * @throws PropelException
     */
    public function countProductsImagesCategoriesSorts(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsImagesCategoriesSortsPartial && !$this->isNew();
        if (null === $this->collProductsImagesCategoriesSorts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsImagesCategoriesSorts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsImagesCategoriesSorts());
            }
            $query = ProductsImagesCategoriesSortQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsImages($this)
                ->count($con);
        }

        return count($this->collProductsImagesCategoriesSorts);
    }

    /**
     * Method called to associate a ProductsImagesCategoriesSort object to this object
     * through the ProductsImagesCategoriesSort foreign key attribute.
     *
     * @param    ProductsImagesCategoriesSort $l ProductsImagesCategoriesSort
     * @return ProductsImages The current object (for fluent API support)
     */
    public function addProductsImagesCategoriesSort(ProductsImagesCategoriesSort $l)
    {
        if ($this->collProductsImagesCategoriesSorts === null) {
            $this->initProductsImagesCategoriesSorts();
            $this->collProductsImagesCategoriesSortsPartial = true;
        }

        if (!in_array($l, $this->collProductsImagesCategoriesSorts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsImagesCategoriesSort($l);

            if ($this->productsImagesCategoriesSortsScheduledForDeletion and $this->productsImagesCategoriesSortsScheduledForDeletion->contains($l)) {
                $this->productsImagesCategoriesSortsScheduledForDeletion->remove($this->productsImagesCategoriesSortsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	ProductsImagesCategoriesSort $productsImagesCategoriesSort The productsImagesCategoriesSort object to add.
     */
    protected function doAddProductsImagesCategoriesSort($productsImagesCategoriesSort)
    {
        $this->collProductsImagesCategoriesSorts[]= $productsImagesCategoriesSort;
        $productsImagesCategoriesSort->setProductsImages($this);
    }

    /**
     * @param	ProductsImagesCategoriesSort $productsImagesCategoriesSort The productsImagesCategoriesSort object to remove.
     * @return ProductsImages The current object (for fluent API support)
     */
    public function removeProductsImagesCategoriesSort($productsImagesCategoriesSort)
    {
        if ($this->getProductsImagesCategoriesSorts()->contains($productsImagesCategoriesSort)) {
            $this->collProductsImagesCategoriesSorts->remove($this->collProductsImagesCategoriesSorts->search($productsImagesCategoriesSort));
            if (null === $this->productsImagesCategoriesSortsScheduledForDeletion) {
                $this->productsImagesCategoriesSortsScheduledForDeletion = clone $this->collProductsImagesCategoriesSorts;
                $this->productsImagesCategoriesSortsScheduledForDeletion->clear();
            }
            $this->productsImagesCategoriesSortsScheduledForDeletion[]= clone $productsImagesCategoriesSort;
            $productsImagesCategoriesSort->setProductsImages(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ProductsImages is new, it will return
     * an empty collection; or if this ProductsImages has previously
     * been saved, it will retrieve related ProductsImagesCategoriesSorts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ProductsImages.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsImagesCategoriesSort[] List of ProductsImagesCategoriesSort objects
     */
    public function getProductsImagesCategoriesSortsJoinProducts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsImagesCategoriesSortQuery::create(null, $criteria);
        $query->joinWith('Products', $join_behavior);

        return $this->getProductsImagesCategoriesSorts($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ProductsImages is new, it will return
     * an empty collection; or if this ProductsImages has previously
     * been saved, it will retrieve related ProductsImagesCategoriesSorts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ProductsImages.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsImagesCategoriesSort[] List of ProductsImagesCategoriesSort objects
     */
    public function getProductsImagesCategoriesSortsJoinCategories($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsImagesCategoriesSortQuery::create(null, $criteria);
        $query->joinWith('Categories', $join_behavior);

        return $this->getProductsImagesCategoriesSorts($query, $con);
    }

    /**
     * Clears out the collProductsImagesProductReferencess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return ProductsImages The current object (for fluent API support)
     * @see        addProductsImagesProductReferencess()
     */
    public function clearProductsImagesProductReferencess()
    {
        $this->collProductsImagesProductReferencess = null; // important to set this to null since that means it is uninitialized
        $this->collProductsImagesProductReferencessPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsImagesProductReferencess collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsImagesProductReferencess($v = true)
    {
        $this->collProductsImagesProductReferencessPartial = $v;
    }

    /**
     * Initializes the collProductsImagesProductReferencess collection.
     *
     * By default this just sets the collProductsImagesProductReferencess collection to an empty array (like clearcollProductsImagesProductReferencess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsImagesProductReferencess($overrideExisting = true)
    {
        if (null !== $this->collProductsImagesProductReferencess && !$overrideExisting) {
            return;
        }
        $this->collProductsImagesProductReferencess = new PropelObjectCollection();
        $this->collProductsImagesProductReferencess->setModel('ProductsImagesProductReferences');
    }

    /**
     * Gets an array of ProductsImagesProductReferences objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ProductsImages is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsImagesProductReferences[] List of ProductsImagesProductReferences objects
     * @throws PropelException
     */
    public function getProductsImagesProductReferencess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsImagesProductReferencessPartial && !$this->isNew();
        if (null === $this->collProductsImagesProductReferencess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsImagesProductReferencess) {
                // return empty collection
                $this->initProductsImagesProductReferencess();
            } else {
                $collProductsImagesProductReferencess = ProductsImagesProductReferencesQuery::create(null, $criteria)
                    ->filterByProductsImages($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsImagesProductReferencessPartial && count($collProductsImagesProductReferencess)) {
                      $this->initProductsImagesProductReferencess(false);

                      foreach ($collProductsImagesProductReferencess as $obj) {
                        if (false == $this->collProductsImagesProductReferencess->contains($obj)) {
                          $this->collProductsImagesProductReferencess->append($obj);
                        }
                      }

                      $this->collProductsImagesProductReferencessPartial = true;
                    }

                    $collProductsImagesProductReferencess->getInternalIterator()->rewind();

                    return $collProductsImagesProductReferencess;
                }

                if ($partial && $this->collProductsImagesProductReferencess) {
                    foreach ($this->collProductsImagesProductReferencess as $obj) {
                        if ($obj->isNew()) {
                            $collProductsImagesProductReferencess[] = $obj;
                        }
                    }
                }

                $this->collProductsImagesProductReferencess = $collProductsImagesProductReferencess;
                $this->collProductsImagesProductReferencessPartial = false;
            }
        }

        return $this->collProductsImagesProductReferencess;
    }

    /**
     * Sets a collection of ProductsImagesProductReferences objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsImagesProductReferencess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return ProductsImages The current object (for fluent API support)
     */
    public function setProductsImagesProductReferencess(PropelCollection $productsImagesProductReferencess, PropelPDO $con = null)
    {
        $productsImagesProductReferencessToDelete = $this->getProductsImagesProductReferencess(new Criteria(), $con)->diff($productsImagesProductReferencess);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->productsImagesProductReferencessScheduledForDeletion = clone $productsImagesProductReferencessToDelete;

        foreach ($productsImagesProductReferencessToDelete as $productsImagesProductReferencesRemoved) {
            $productsImagesProductReferencesRemoved->setProductsImages(null);
        }

        $this->collProductsImagesProductReferencess = null;
        foreach ($productsImagesProductReferencess as $productsImagesProductReferences) {
            $this->addProductsImagesProductReferences($productsImagesProductReferences);
        }

        $this->collProductsImagesProductReferencess = $productsImagesProductReferencess;
        $this->collProductsImagesProductReferencessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsImagesProductReferences objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsImagesProductReferences objects.
     * @throws PropelException
     */
    public function countProductsImagesProductReferencess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsImagesProductReferencessPartial && !$this->isNew();
        if (null === $this->collProductsImagesProductReferencess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsImagesProductReferencess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsImagesProductReferencess());
            }
            $query = ProductsImagesProductReferencesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsImages($this)
                ->count($con);
        }

        return count($this->collProductsImagesProductReferencess);
    }

    /**
     * Method called to associate a ProductsImagesProductReferences object to this object
     * through the ProductsImagesProductReferences foreign key attribute.
     *
     * @param    ProductsImagesProductReferences $l ProductsImagesProductReferences
     * @return ProductsImages The current object (for fluent API support)
     */
    public function addProductsImagesProductReferences(ProductsImagesProductReferences $l)
    {
        if ($this->collProductsImagesProductReferencess === null) {
            $this->initProductsImagesProductReferencess();
            $this->collProductsImagesProductReferencessPartial = true;
        }

        if (!in_array($l, $this->collProductsImagesProductReferencess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsImagesProductReferences($l);

            if ($this->productsImagesProductReferencessScheduledForDeletion and $this->productsImagesProductReferencessScheduledForDeletion->contains($l)) {
                $this->productsImagesProductReferencessScheduledForDeletion->remove($this->productsImagesProductReferencessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	ProductsImagesProductReferences $productsImagesProductReferences The productsImagesProductReferences object to add.
     */
    protected function doAddProductsImagesProductReferences($productsImagesProductReferences)
    {
        $this->collProductsImagesProductReferencess[]= $productsImagesProductReferences;
        $productsImagesProductReferences->setProductsImages($this);
    }

    /**
     * @param	ProductsImagesProductReferences $productsImagesProductReferences The productsImagesProductReferences object to remove.
     * @return ProductsImages The current object (for fluent API support)
     */
    public function removeProductsImagesProductReferences($productsImagesProductReferences)
    {
        if ($this->getProductsImagesProductReferencess()->contains($productsImagesProductReferences)) {
            $this->collProductsImagesProductReferencess->remove($this->collProductsImagesProductReferencess->search($productsImagesProductReferences));
            if (null === $this->productsImagesProductReferencessScheduledForDeletion) {
                $this->productsImagesProductReferencessScheduledForDeletion = clone $this->collProductsImagesProductReferencess;
                $this->productsImagesProductReferencessScheduledForDeletion->clear();
            }
            $this->productsImagesProductReferencessScheduledForDeletion[]= clone $productsImagesProductReferences;
            $productsImagesProductReferences->setProductsImages(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ProductsImages is new, it will return
     * an empty collection; or if this ProductsImages has previously
     * been saved, it will retrieve related ProductsImagesProductReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ProductsImages.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsImagesProductReferences[] List of ProductsImagesProductReferences objects
     */
    public function getProductsImagesProductReferencessJoinProducts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsImagesProductReferencesQuery::create(null, $criteria);
        $query->joinWith('Products', $join_behavior);

        return $this->getProductsImagesProductReferencess($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->products_id = null;
        $this->image = null;
        $this->color = null;
        $this->type = null;
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
            if ($this->collProductsImagesCategoriesSorts) {
                foreach ($this->collProductsImagesCategoriesSorts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsImagesProductReferencess) {
                foreach ($this->collProductsImagesProductReferencess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aProducts instanceof Persistent) {
              $this->aProducts->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collProductsImagesCategoriesSorts instanceof PropelCollection) {
            $this->collProductsImagesCategoriesSorts->clearIterator();
        }
        $this->collProductsImagesCategoriesSorts = null;
        if ($this->collProductsImagesProductReferencess instanceof PropelCollection) {
            $this->collProductsImagesProductReferencess->clearIterator();
        }
        $this->collProductsImagesProductReferencess = null;
        $this->aProducts = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProductsImagesPeer::DEFAULT_STRING_FORMAT);
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

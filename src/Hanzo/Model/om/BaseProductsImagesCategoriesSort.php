<?php

namespace Hanzo\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelException;
use \PropelPDO;
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortPeer;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsQuery;

abstract class BaseProductsImagesCategoriesSort extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\ProductsImagesCategoriesSortPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProductsImagesCategoriesSortPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the products_id field.
     * @var        int
     */
    protected $products_id;

    /**
     * The value for the categories_id field.
     * @var        int
     */
    protected $categories_id;

    /**
     * The value for the products_images_id field.
     * @var        int
     */
    protected $products_images_id;

    /**
     * The value for the sort field.
     * @var        int
     */
    protected $sort;

    /**
     * @var        Products
     */
    protected $aProducts;

    /**
     * @var        ProductsImages
     */
    protected $aProductsImages;

    /**
     * @var        Categories
     */
    protected $aCategories;

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
     * Get the [products_id] column value.
     *
     * @return int
     */
    public function getProductsId()
    {

        return $this->products_id;
    }

    public function __construct(){
        parent::__construct();
        EventDispatcherProxy::trigger(array('construct','model.construct'), new ModelEvent($this));
    }

    /**
     * Get the [categories_id] column value.
     *
     * @return int
     */
    public function getCategoriesId()
    {

        return $this->categories_id;
    }

    /**
     * Get the [products_images_id] column value.
     *
     * @return int
     */
    public function getProductsImagesId()
    {

        return $this->products_images_id;
    }

    /**
     * Get the [sort] column value.
     *
     * @return int
     */
    public function getSort()
    {

        return $this->sort;
    }

    /**
     * Set the value of [products_id] column.
     *
     * @param  int $v new value
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
     */
    public function setProductsId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->products_id !== $v) {
            $this->products_id = $v;
            $this->modifiedColumns[] = ProductsImagesCategoriesSortPeer::PRODUCTS_ID;
        }

        if ($this->aProducts !== null && $this->aProducts->getId() !== $v) {
            $this->aProducts = null;
        }


        return $this;
    } // setProductsId()

    /**
     * Set the value of [categories_id] column.
     *
     * @param  int $v new value
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
     */
    public function setCategoriesId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->categories_id !== $v) {
            $this->categories_id = $v;
            $this->modifiedColumns[] = ProductsImagesCategoriesSortPeer::CATEGORIES_ID;
        }

        if ($this->aCategories !== null && $this->aCategories->getId() !== $v) {
            $this->aCategories = null;
        }


        return $this;
    } // setCategoriesId()

    /**
     * Set the value of [products_images_id] column.
     *
     * @param  int $v new value
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
     */
    public function setProductsImagesId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->products_images_id !== $v) {
            $this->products_images_id = $v;
            $this->modifiedColumns[] = ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID;
        }

        if ($this->aProductsImages !== null && $this->aProductsImages->getId() !== $v) {
            $this->aProductsImages = null;
        }


        return $this;
    } // setProductsImagesId()

    /**
     * Set the value of [sort] column.
     *
     * @param  int $v new value
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
     */
    public function setSort($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sort !== $v) {
            $this->sort = $v;
            $this->modifiedColumns[] = ProductsImagesCategoriesSortPeer::SORT;
        }


        return $this;
    } // setSort()

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

            $this->products_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->categories_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->products_images_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->sort = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 4; // 4 = ProductsImagesCategoriesSortPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating ProductsImagesCategoriesSort object", $e);
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
        if ($this->aCategories !== null && $this->categories_id !== $this->aCategories->getId()) {
            $this->aCategories = null;
        }
        if ($this->aProductsImages !== null && $this->products_images_id !== $this->aProductsImages->getId()) {
            $this->aProductsImages = null;
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
            $con = Propel::getConnection(ProductsImagesCategoriesSortPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ProductsImagesCategoriesSortPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aProducts = null;
            $this->aProductsImages = null;
            $this->aCategories = null;
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
            $con = Propel::getConnection(ProductsImagesCategoriesSortPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
            $deleteQuery = ProductsImagesCategoriesSortQuery::create()
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
            $con = Propel::getConnection(ProductsImagesCategoriesSortPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                ProductsImagesCategoriesSortPeer::addInstanceToPool($this);
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

            if ($this->aProductsImages !== null) {
                if ($this->aProductsImages->isModified() || $this->aProductsImages->isNew()) {
                    $affectedRows += $this->aProductsImages->save($con);
                }
                $this->setProductsImages($this->aProductsImages);
            }

            if ($this->aCategories !== null) {
                if ($this->aCategories->isModified() || $this->aCategories->isNew()) {
                    $affectedRows += $this->aCategories->save($con);
                }
                $this->setCategories($this->aCategories);
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::PRODUCTS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`products_id`';
        }
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::CATEGORIES_ID)) {
            $modifiedColumns[':p' . $index++]  = '`categories_id`';
        }
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID)) {
            $modifiedColumns[':p' . $index++]  = '`products_images_id`';
        }
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::SORT)) {
            $modifiedColumns[':p' . $index++]  = '`sort`';
        }

        $sql = sprintf(
            'INSERT INTO `products_images_categories_sort` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`products_id`':
                        $stmt->bindValue($identifier, $this->products_id, PDO::PARAM_INT);
                        break;
                    case '`categories_id`':
                        $stmt->bindValue($identifier, $this->categories_id, PDO::PARAM_INT);
                        break;
                    case '`products_images_id`':
                        $stmt->bindValue($identifier, $this->products_images_id, PDO::PARAM_INT);
                        break;
                    case '`sort`':
                        $stmt->bindValue($identifier, $this->sort, PDO::PARAM_INT);
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

            if ($this->aProductsImages !== null) {
                if (!$this->aProductsImages->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProductsImages->getValidationFailures());
                }
            }

            if ($this->aCategories !== null) {
                if (!$this->aCategories->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCategories->getValidationFailures());
                }
            }


            if (($retval = ProductsImagesCategoriesSortPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ProductsImagesCategoriesSortPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getProductsId();
                break;
            case 1:
                return $this->getCategoriesId();
                break;
            case 2:
                return $this->getProductsImagesId();
                break;
            case 3:
                return $this->getSort();
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
        if (isset($alreadyDumpedObjects['ProductsImagesCategoriesSort'][serialize($this->getPrimaryKey())])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ProductsImagesCategoriesSort'][serialize($this->getPrimaryKey())] = true;
        $keys = ProductsImagesCategoriesSortPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getProductsId(),
            $keys[1] => $this->getCategoriesId(),
            $keys[2] => $this->getProductsImagesId(),
            $keys[3] => $this->getSort(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aProducts) {
                $result['Products'] = $this->aProducts->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProductsImages) {
                $result['ProductsImages'] = $this->aProductsImages->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCategories) {
                $result['Categories'] = $this->aCategories->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = ProductsImagesCategoriesSortPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setProductsId($value);
                break;
            case 1:
                $this->setCategoriesId($value);
                break;
            case 2:
                $this->setProductsImagesId($value);
                break;
            case 3:
                $this->setSort($value);
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
        $keys = ProductsImagesCategoriesSortPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setProductsId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCategoriesId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setProductsImagesId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSort($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProductsImagesCategoriesSortPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::PRODUCTS_ID)) $criteria->add(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $this->products_id);
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::CATEGORIES_ID)) $criteria->add(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $this->categories_id);
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID)) $criteria->add(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $this->products_images_id);
        if ($this->isColumnModified(ProductsImagesCategoriesSortPeer::SORT)) $criteria->add(ProductsImagesCategoriesSortPeer::SORT, $this->sort);

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
        $criteria = new Criteria(ProductsImagesCategoriesSortPeer::DATABASE_NAME);
        $criteria->add(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $this->products_id);
        $criteria->add(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $this->categories_id);
        $criteria->add(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $this->products_images_id);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return array
     */
    public function getPrimaryKey()
    {
        $pks = array();
        $pks[0] = $this->getProductsId();
        $pks[1] = $this->getCategoriesId();
        $pks[2] = $this->getProductsImagesId();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param array $keys The elements of the composite key (order must match the order in XML file).
     * @return void
     */
    public function setPrimaryKey($keys)
    {
        $this->setProductsId($keys[0]);
        $this->setCategoriesId($keys[1]);
        $this->setProductsImagesId($keys[2]);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return (null === $this->getProductsId()) && (null === $this->getCategoriesId()) && (null === $this->getProductsImagesId());
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of ProductsImagesCategoriesSort (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setProductsId($this->getProductsId());
        $copyObj->setCategoriesId($this->getCategoriesId());
        $copyObj->setProductsImagesId($this->getProductsImagesId());
        $copyObj->setSort($this->getSort());

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
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return ProductsImagesCategoriesSort Clone of current object.
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
     * @return ProductsImagesCategoriesSortPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProductsImagesCategoriesSortPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Products object.
     *
     * @param                  Products $v
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
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
            $v->addProductsImagesCategoriesSort($this);
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
                $this->aProducts->addProductsImagesCategoriesSorts($this);
             */
        }

        return $this->aProducts;
    }

    /**
     * Declares an association between this object and a ProductsImages object.
     *
     * @param                  ProductsImages $v
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProductsImages(ProductsImages $v = null)
    {
        if ($v === null) {
            $this->setProductsImagesId(NULL);
        } else {
            $this->setProductsImagesId($v->getId());
        }

        $this->aProductsImages = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ProductsImages object, it will not be re-added.
        if ($v !== null) {
            $v->addProductsImagesCategoriesSort($this);
        }


        return $this;
    }


    /**
     * Get the associated ProductsImages object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return ProductsImages The associated ProductsImages object.
     * @throws PropelException
     */
    public function getProductsImages(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aProductsImages === null && ($this->products_images_id !== null) && $doQuery) {
            $this->aProductsImages = ProductsImagesQuery::create()->findPk($this->products_images_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProductsImages->addProductsImagesCategoriesSorts($this);
             */
        }

        return $this->aProductsImages;
    }

    /**
     * Declares an association between this object and a Categories object.
     *
     * @param                  Categories $v
     * @return ProductsImagesCategoriesSort The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCategories(Categories $v = null)
    {
        if ($v === null) {
            $this->setCategoriesId(NULL);
        } else {
            $this->setCategoriesId($v->getId());
        }

        $this->aCategories = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Categories object, it will not be re-added.
        if ($v !== null) {
            $v->addProductsImagesCategoriesSort($this);
        }


        return $this;
    }


    /**
     * Get the associated Categories object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Categories The associated Categories object.
     * @throws PropelException
     */
    public function getCategories(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCategories === null && ($this->categories_id !== null) && $doQuery) {
            $this->aCategories = CategoriesQuery::create()->findPk($this->categories_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCategories->addProductsImagesCategoriesSorts($this);
             */
        }

        return $this->aCategories;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->products_id = null;
        $this->categories_id = null;
        $this->products_images_id = null;
        $this->sort = null;
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
            if ($this->aProducts instanceof Persistent) {
              $this->aProducts->clearAllReferences($deep);
            }
            if ($this->aProductsImages instanceof Persistent) {
              $this->aProductsImages->clearAllReferences($deep);
            }
            if ($this->aCategories instanceof Persistent) {
              $this->aCategories->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aProducts = null;
        $this->aProductsImages = null;
        $this->aCategories = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProductsImagesCategoriesSortPeer::DEFAULT_STRING_FORMAT);
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

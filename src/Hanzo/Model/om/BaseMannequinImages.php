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
use Hanzo\Model\MannequinImages;
use Hanzo\Model\MannequinImagesPeer;
use Hanzo\Model\MannequinImagesQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuery;

abstract class BaseMannequinImages extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\MannequinImagesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        MannequinImagesPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the master field.
     * @var        string
     */
    protected $master;

    /**
     * The value for the color field.
     * @var        string
     */
    protected $color;

    /**
     * The value for the layer field.
     * @var        int
     */
    protected $layer;

    /**
     * The value for the image field.
     * @var        string
     */
    protected $image;

    /**
     * The value for the icon field.
     * @var        string
     */
    protected $icon;

    /**
     * The value for the weight field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $weight;

    /**
     * The value for the is_main field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_main;

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
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->weight = 0;
        $this->is_main = false;
    }

    /**
     * Initializes internal state of BaseMannequinImages object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [master] column value.
     *
     * @return string
     */
    public function getMaster()
    {
        return $this->master;
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
     * Get the [layer] column value.
     *
     * @return int
     */
    public function getLayer()
    {
        return $this->layer;
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
     * Get the [icon] column value.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Get the [weight] column value.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Get the [is_main] column value.
     *
     * @return boolean
     */
    public function getIsMain()
    {
        return $this->is_main;
    }

    /**
     * Set the value of [master] column.
     *
     * @param string $v new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setMaster($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->master !== $v) {
            $this->master = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::MASTER;
        }

        if ($this->aProducts !== null && $this->aProducts->getSku() !== $v) {
            $this->aProducts = null;
        }


        return $this;
    } // setMaster()

    /**
     * Set the value of [color] column.
     *
     * @param string $v new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::COLOR;
        }


        return $this;
    } // setColor()

    /**
     * Set the value of [layer] column.
     *
     * @param int $v new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setLayer($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->layer !== $v) {
            $this->layer = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::LAYER;
        }


        return $this;
    } // setLayer()

    /**
     * Set the value of [image] column.
     *
     * @param string $v new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setImage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->image !== $v) {
            $this->image = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::IMAGE;
        }


        return $this;
    } // setImage()

    /**
     * Set the value of [icon] column.
     *
     * @param string $v new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setIcon($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->icon !== $v) {
            $this->icon = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::ICON;
        }


        return $this;
    } // setIcon()

    /**
     * Set the value of [weight] column.
     *
     * @param int $v new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setWeight($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->weight !== $v) {
            $this->weight = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::WEIGHT;
        }


        return $this;
    } // setWeight()

    /**
     * Sets the value of the [is_main] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return MannequinImages The current object (for fluent API support)
     */
    public function setIsMain($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_main !== $v) {
            $this->is_main = $v;
            $this->modifiedColumns[] = MannequinImagesPeer::IS_MAIN;
        }


        return $this;
    } // setIsMain()

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
            if ($this->weight !== 0) {
                return false;
            }

            if ($this->is_main !== false) {
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
     * @param int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->master = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
            $this->color = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->layer = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->image = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->icon = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->weight = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->is_main = ($row[$startcol + 6] !== null) ? (boolean) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = MannequinImagesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating MannequinImages object", $e);
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

        if ($this->aProducts !== null && $this->master !== $this->aProducts->getSku()) {
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
            $con = Propel::getConnection(MannequinImagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = MannequinImagesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

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
            $con = Propel::getConnection(MannequinImagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = MannequinImagesQuery::create()
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
            $con = Propel::getConnection(MannequinImagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                MannequinImagesPeer::addInstanceToPool($this);
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
            // were passed to this object by their coresponding set
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
        if ($this->isColumnModified(MannequinImagesPeer::MASTER)) {
            $modifiedColumns[':p' . $index++]  = '`MASTER`';
        }
        if ($this->isColumnModified(MannequinImagesPeer::COLOR)) {
            $modifiedColumns[':p' . $index++]  = '`COLOR`';
        }
        if ($this->isColumnModified(MannequinImagesPeer::LAYER)) {
            $modifiedColumns[':p' . $index++]  = '`LAYER`';
        }
        if ($this->isColumnModified(MannequinImagesPeer::IMAGE)) {
            $modifiedColumns[':p' . $index++]  = '`IMAGE`';
        }
        if ($this->isColumnModified(MannequinImagesPeer::ICON)) {
            $modifiedColumns[':p' . $index++]  = '`ICON`';
        }
        if ($this->isColumnModified(MannequinImagesPeer::WEIGHT)) {
            $modifiedColumns[':p' . $index++]  = '`WEIGHT`';
        }
        if ($this->isColumnModified(MannequinImagesPeer::IS_MAIN)) {
            $modifiedColumns[':p' . $index++]  = '`IS_MAIN`';
        }

        $sql = sprintf(
            'INSERT INTO `mannequin_images` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`MASTER`':
                        $stmt->bindValue($identifier, $this->master, PDO::PARAM_STR);
                        break;
                    case '`COLOR`':
                        $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
                        break;
                    case '`LAYER`':
                        $stmt->bindValue($identifier, $this->layer, PDO::PARAM_INT);
                        break;
                    case '`IMAGE`':
                        $stmt->bindValue($identifier, $this->image, PDO::PARAM_STR);
                        break;
                    case '`ICON`':
                        $stmt->bindValue($identifier, $this->icon, PDO::PARAM_STR);
                        break;
                    case '`WEIGHT`':
                        $stmt->bindValue($identifier, $this->weight, PDO::PARAM_INT);
                        break;
                    case '`IS_MAIN`':
                        $stmt->bindValue($identifier, (int) $this->is_main, PDO::PARAM_INT);
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
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
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

            if ($this->aProducts !== null) {
                if (!$this->aProducts->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProducts->getValidationFailures());
                }
            }


            if (($retval = MannequinImagesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = MannequinImagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getMaster();
                break;
            case 1:
                return $this->getColor();
                break;
            case 2:
                return $this->getLayer();
                break;
            case 3:
                return $this->getImage();
                break;
            case 4:
                return $this->getIcon();
                break;
            case 5:
                return $this->getWeight();
                break;
            case 6:
                return $this->getIsMain();
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
        if (isset($alreadyDumpedObjects['MannequinImages'][serialize($this->getPrimaryKey())])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['MannequinImages'][serialize($this->getPrimaryKey())] = true;
        $keys = MannequinImagesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getMaster(),
            $keys[1] => $this->getColor(),
            $keys[2] => $this->getLayer(),
            $keys[3] => $this->getImage(),
            $keys[4] => $this->getIcon(),
            $keys[5] => $this->getWeight(),
            $keys[6] => $this->getIsMain(),
        );
        if ($includeForeignObjects) {
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
        $pos = MannequinImagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setMaster($value);
                break;
            case 1:
                $this->setColor($value);
                break;
            case 2:
                $this->setLayer($value);
                break;
            case 3:
                $this->setImage($value);
                break;
            case 4:
                $this->setIcon($value);
                break;
            case 5:
                $this->setWeight($value);
                break;
            case 6:
                $this->setIsMain($value);
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
        $keys = MannequinImagesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setMaster($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setColor($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setLayer($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setImage($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setIcon($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setWeight($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setIsMain($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(MannequinImagesPeer::DATABASE_NAME);

        if ($this->isColumnModified(MannequinImagesPeer::MASTER)) $criteria->add(MannequinImagesPeer::MASTER, $this->master);
        if ($this->isColumnModified(MannequinImagesPeer::COLOR)) $criteria->add(MannequinImagesPeer::COLOR, $this->color);
        if ($this->isColumnModified(MannequinImagesPeer::LAYER)) $criteria->add(MannequinImagesPeer::LAYER, $this->layer);
        if ($this->isColumnModified(MannequinImagesPeer::IMAGE)) $criteria->add(MannequinImagesPeer::IMAGE, $this->image);
        if ($this->isColumnModified(MannequinImagesPeer::ICON)) $criteria->add(MannequinImagesPeer::ICON, $this->icon);
        if ($this->isColumnModified(MannequinImagesPeer::WEIGHT)) $criteria->add(MannequinImagesPeer::WEIGHT, $this->weight);
        if ($this->isColumnModified(MannequinImagesPeer::IS_MAIN)) $criteria->add(MannequinImagesPeer::IS_MAIN, $this->is_main);

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
        $criteria = new Criteria(MannequinImagesPeer::DATABASE_NAME);
        $criteria->add(MannequinImagesPeer::MASTER, $this->master);
        $criteria->add(MannequinImagesPeer::COLOR, $this->color);

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
        $pks[0] = $this->getMaster();
        $pks[1] = $this->getColor();

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
        $this->setMaster($keys[0]);
        $this->setColor($keys[1]);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return (null === $this->getMaster()) && (null === $this->getColor());
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of MannequinImages (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setMaster($this->getMaster());
        $copyObj->setColor($this->getColor());
        $copyObj->setLayer($this->getLayer());
        $copyObj->setImage($this->getImage());
        $copyObj->setIcon($this->getIcon());
        $copyObj->setWeight($this->getWeight());
        $copyObj->setIsMain($this->getIsMain());

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
     * @return MannequinImages Clone of current object.
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
     * @return MannequinImagesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new MannequinImagesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Products object.
     *
     * @param             Products $v
     * @return MannequinImages The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProducts(Products $v = null)
    {
        if ($v === null) {
            $this->setMaster(NULL);
        } else {
            $this->setMaster($v->getSku());
        }

        $this->aProducts = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Products object, it will not be re-added.
        if ($v !== null) {
            $v->addMannequinImages($this);
        }


        return $this;
    }


    /**
     * Get the associated Products object
     *
     * @param PropelPDO $con Optional Connection object.
     * @return Products The associated Products object.
     * @throws PropelException
     */
    public function getProducts(PropelPDO $con = null)
    {
        if ($this->aProducts === null && (($this->master !== "" && $this->master !== null))) {
            $this->aProducts = ProductsQuery::create()
                ->filterByMannequinImages($this) // here
                ->findOne($con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProducts->addMannequinImagess($this);
             */
        }

        return $this->aProducts;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->master = null;
        $this->color = null;
        $this->layer = null;
        $this->image = null;
        $this->icon = null;
        $this->weight = null;
        $this->is_main = null;
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
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
        } // if ($deep)

        $this->aProducts = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(MannequinImagesPeer::DEFAULT_STRING_FORMAT);
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

}

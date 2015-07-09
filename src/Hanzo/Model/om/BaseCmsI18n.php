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
use Hanzo\Model\Cms;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nPeer;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\CmsQuery;

abstract class BaseCmsI18n extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\CmsI18nPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CmsI18nPeer
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
     * The value for the locale field.
     * Note: this column has a database default value of: 'da_DK'
     * @var        string
     */
    protected $locale;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the path field.
     * @var        string
     */
    protected $path;

    /**
     * The value for the old_path field.
     * @var        string
     */
    protected $old_path;

    /**
     * The value for the content field.
     * @var        string
     */
    protected $content;

    /**
     * The value for the settings field.
     * @var        string
     */
    protected $settings;

    /**
     * The value for the is_restricted field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_restricted;

    /**
     * The value for the is_active field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $is_active;

    /**
     * The value for the on_mobile field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $on_mobile;

    /**
     * The value for the only_mobile field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $only_mobile;

    /**
     * The value for the meta_title field.
     * @var        string
     */
    protected $meta_title;

    /**
     * The value for the meta_description field.
     * @var        string
     */
    protected $meta_description;

    /**
     * @var        Cms
     */
    protected $aCms;

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
        $this->locale = 'da_DK';
        $this->is_restricted = false;
        $this->is_active = true;
        $this->on_mobile = true;
        $this->only_mobile = false;
    }

    /**
     * Initializes internal state of BaseCmsI18n object.
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
     * Get the [locale] column value.
     *
     * @return string
     */
    public function getLocale()
    {

        return $this->locale;
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
     * Get the [path] column value.
     *
     * @return string
     */
    public function getPath()
    {

        return $this->path;
    }

    /**
     * Get the [old_path] column value.
     *
     * @return string
     */
    public function getOldPath()
    {

        return $this->old_path;
    }

    /**
     * Get the [content] column value.
     *
     * @return string
     */
    public function getContent()
    {

        return $this->content;
    }

    /**
     * Get the [settings] column value.
     *
     * @return string
     */
    public function getSettings()
    {

        return $this->settings;
    }

    /**
     * Get the [is_restricted] column value.
     *
     * @return boolean
     */
    public function getIsRestricted()
    {

        return $this->is_restricted;
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
     * Get the [on_mobile] column value.
     *
     * @return boolean
     */
    public function getOnMobile()
    {

        return $this->on_mobile;
    }

    /**
     * Get the [only_mobile] column value.
     *
     * @return boolean
     */
    public function getOnlyMobile()
    {

        return $this->only_mobile;
    }

    /**
     * Get the [meta_title] column value.
     *
     * @return string
     */
    public function getMetaTitle()
    {

        return $this->meta_title;
    }

    /**
     * Get the [meta_description] column value.
     *
     * @return string
     */
    public function getMetaDescription()
    {

        return $this->meta_description;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CmsI18nPeer::ID;
        }

        if ($this->aCms !== null && $this->aCms->getId() !== $v) {
            $this->aCms = null;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [locale] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setLocale($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->locale !== $v) {
            $this->locale = $v;
            $this->modifiedColumns[] = CmsI18nPeer::LOCALE;
        }


        return $this;
    } // setLocale()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = CmsI18nPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [path] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setPath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->path !== $v) {
            $this->path = $v;
            $this->modifiedColumns[] = CmsI18nPeer::PATH;
        }


        return $this;
    } // setPath()

    /**
     * Set the value of [old_path] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setOldPath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->old_path !== $v) {
            $this->old_path = $v;
            $this->modifiedColumns[] = CmsI18nPeer::OLD_PATH;
        }


        return $this;
    } // setOldPath()

    /**
     * Set the value of [content] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setContent($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->content !== $v) {
            $this->content = $v;
            $this->modifiedColumns[] = CmsI18nPeer::CONTENT;
        }


        return $this;
    } // setContent()

    /**
     * Set the value of [settings] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setSettings($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->settings !== $v) {
            $this->settings = $v;
            $this->modifiedColumns[] = CmsI18nPeer::SETTINGS;
        }


        return $this;
    } // setSettings()

    /**
     * Sets the value of the [is_restricted] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setIsRestricted($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_restricted !== $v) {
            $this->is_restricted = $v;
            $this->modifiedColumns[] = CmsI18nPeer::IS_RESTRICTED;
        }


        return $this;
    } // setIsRestricted()

    /**
     * Sets the value of the [is_active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CmsI18n The current object (for fluent API support)
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
            $this->modifiedColumns[] = CmsI18nPeer::IS_ACTIVE;
        }


        return $this;
    } // setIsActive()

    /**
     * Sets the value of the [on_mobile] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setOnMobile($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->on_mobile !== $v) {
            $this->on_mobile = $v;
            $this->modifiedColumns[] = CmsI18nPeer::ON_MOBILE;
        }


        return $this;
    } // setOnMobile()

    /**
     * Sets the value of the [only_mobile] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setOnlyMobile($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->only_mobile !== $v) {
            $this->only_mobile = $v;
            $this->modifiedColumns[] = CmsI18nPeer::ONLY_MOBILE;
        }


        return $this;
    } // setOnlyMobile()

    /**
     * Set the value of [meta_title] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setMetaTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->meta_title !== $v) {
            $this->meta_title = $v;
            $this->modifiedColumns[] = CmsI18nPeer::META_TITLE;
        }


        return $this;
    } // setMetaTitle()

    /**
     * Set the value of [meta_description] column.
     *
     * @param  string $v new value
     * @return CmsI18n The current object (for fluent API support)
     */
    public function setMetaDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->meta_description !== $v) {
            $this->meta_description = $v;
            $this->modifiedColumns[] = CmsI18nPeer::META_DESCRIPTION;
        }


        return $this;
    } // setMetaDescription()

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
            if ($this->locale !== 'da_DK') {
                return false;
            }

            if ($this->is_restricted !== false) {
                return false;
            }

            if ($this->is_active !== true) {
                return false;
            }

            if ($this->on_mobile !== true) {
                return false;
            }

            if ($this->only_mobile !== false) {
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
            $this->locale = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->title = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->path = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->old_path = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->content = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->settings = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->is_restricted = ($row[$startcol + 7] !== null) ? (boolean) $row[$startcol + 7] : null;
            $this->is_active = ($row[$startcol + 8] !== null) ? (boolean) $row[$startcol + 8] : null;
            $this->on_mobile = ($row[$startcol + 9] !== null) ? (boolean) $row[$startcol + 9] : null;
            $this->only_mobile = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
            $this->meta_title = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->meta_description = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 13; // 13 = CmsI18nPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CmsI18n object", $e);
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

        if ($this->aCms !== null && $this->id !== $this->aCms->getId()) {
            $this->aCms = null;
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
            $con = Propel::getConnection(CmsI18nPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CmsI18nPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCms = null;
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
            $con = Propel::getConnection(CmsI18nPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
            $deleteQuery = CmsI18nQuery::create()
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
            $con = Propel::getConnection(CmsI18nPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CmsI18nPeer::addInstanceToPool($this);
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

            if ($this->aCms !== null) {
                if ($this->aCms->isModified() || $this->aCms->isNew()) {
                    $affectedRows += $this->aCms->save($con);
                }
                $this->setCms($this->aCms);
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
        if ($this->isColumnModified(CmsI18nPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CmsI18nPeer::LOCALE)) {
            $modifiedColumns[':p' . $index++]  = '`locale`';
        }
        if ($this->isColumnModified(CmsI18nPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(CmsI18nPeer::PATH)) {
            $modifiedColumns[':p' . $index++]  = '`path`';
        }
        if ($this->isColumnModified(CmsI18nPeer::OLD_PATH)) {
            $modifiedColumns[':p' . $index++]  = '`old_path`';
        }
        if ($this->isColumnModified(CmsI18nPeer::CONTENT)) {
            $modifiedColumns[':p' . $index++]  = '`content`';
        }
        if ($this->isColumnModified(CmsI18nPeer::SETTINGS)) {
            $modifiedColumns[':p' . $index++]  = '`settings`';
        }
        if ($this->isColumnModified(CmsI18nPeer::IS_RESTRICTED)) {
            $modifiedColumns[':p' . $index++]  = '`is_restricted`';
        }
        if ($this->isColumnModified(CmsI18nPeer::IS_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = '`is_active`';
        }
        if ($this->isColumnModified(CmsI18nPeer::ON_MOBILE)) {
            $modifiedColumns[':p' . $index++]  = '`on_mobile`';
        }
        if ($this->isColumnModified(CmsI18nPeer::ONLY_MOBILE)) {
            $modifiedColumns[':p' . $index++]  = '`only_mobile`';
        }
        if ($this->isColumnModified(CmsI18nPeer::META_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`meta_title`';
        }
        if ($this->isColumnModified(CmsI18nPeer::META_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`meta_description`';
        }

        $sql = sprintf(
            'INSERT INTO `cms_i18n` (%s) VALUES (%s)',
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
                    case '`locale`':
                        $stmt->bindValue($identifier, $this->locale, PDO::PARAM_STR);
                        break;
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`path`':
                        $stmt->bindValue($identifier, $this->path, PDO::PARAM_STR);
                        break;
                    case '`old_path`':
                        $stmt->bindValue($identifier, $this->old_path, PDO::PARAM_STR);
                        break;
                    case '`content`':
                        $stmt->bindValue($identifier, $this->content, PDO::PARAM_STR);
                        break;
                    case '`settings`':
                        $stmt->bindValue($identifier, $this->settings, PDO::PARAM_STR);
                        break;
                    case '`is_restricted`':
                        $stmt->bindValue($identifier, (int) $this->is_restricted, PDO::PARAM_INT);
                        break;
                    case '`is_active`':
                        $stmt->bindValue($identifier, (int) $this->is_active, PDO::PARAM_INT);
                        break;
                    case '`on_mobile`':
                        $stmt->bindValue($identifier, (int) $this->on_mobile, PDO::PARAM_INT);
                        break;
                    case '`only_mobile`':
                        $stmt->bindValue($identifier, (int) $this->only_mobile, PDO::PARAM_INT);
                        break;
                    case '`meta_title`':
                        $stmt->bindValue($identifier, $this->meta_title, PDO::PARAM_STR);
                        break;
                    case '`meta_description`':
                        $stmt->bindValue($identifier, $this->meta_description, PDO::PARAM_STR);
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

            if ($this->aCms !== null) {
                if (!$this->aCms->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCms->getValidationFailures());
                }
            }


            if (($retval = CmsI18nPeer::doValidate($this, $columns)) !== true) {
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
        $pos = CmsI18nPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLocale();
                break;
            case 2:
                return $this->getTitle();
                break;
            case 3:
                return $this->getPath();
                break;
            case 4:
                return $this->getOldPath();
                break;
            case 5:
                return $this->getContent();
                break;
            case 6:
                return $this->getSettings();
                break;
            case 7:
                return $this->getIsRestricted();
                break;
            case 8:
                return $this->getIsActive();
                break;
            case 9:
                return $this->getOnMobile();
                break;
            case 10:
                return $this->getOnlyMobile();
                break;
            case 11:
                return $this->getMetaTitle();
                break;
            case 12:
                return $this->getMetaDescription();
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
        if (isset($alreadyDumpedObjects['CmsI18n'][serialize($this->getPrimaryKey())])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CmsI18n'][serialize($this->getPrimaryKey())] = true;
        $keys = CmsI18nPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getLocale(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getPath(),
            $keys[4] => $this->getOldPath(),
            $keys[5] => $this->getContent(),
            $keys[6] => $this->getSettings(),
            $keys[7] => $this->getIsRestricted(),
            $keys[8] => $this->getIsActive(),
            $keys[9] => $this->getOnMobile(),
            $keys[10] => $this->getOnlyMobile(),
            $keys[11] => $this->getMetaTitle(),
            $keys[12] => $this->getMetaDescription(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCms) {
                $result['Cms'] = $this->aCms->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = CmsI18nPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setLocale($value);
                break;
            case 2:
                $this->setTitle($value);
                break;
            case 3:
                $this->setPath($value);
                break;
            case 4:
                $this->setOldPath($value);
                break;
            case 5:
                $this->setContent($value);
                break;
            case 6:
                $this->setSettings($value);
                break;
            case 7:
                $this->setIsRestricted($value);
                break;
            case 8:
                $this->setIsActive($value);
                break;
            case 9:
                $this->setOnMobile($value);
                break;
            case 10:
                $this->setOnlyMobile($value);
                break;
            case 11:
                $this->setMetaTitle($value);
                break;
            case 12:
                $this->setMetaDescription($value);
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
        $keys = CmsI18nPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setLocale($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPath($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setOldPath($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setContent($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setSettings($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setIsRestricted($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setIsActive($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setOnMobile($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setOnlyMobile($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setMetaTitle($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setMetaDescription($arr[$keys[12]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CmsI18nPeer::DATABASE_NAME);

        if ($this->isColumnModified(CmsI18nPeer::ID)) $criteria->add(CmsI18nPeer::ID, $this->id);
        if ($this->isColumnModified(CmsI18nPeer::LOCALE)) $criteria->add(CmsI18nPeer::LOCALE, $this->locale);
        if ($this->isColumnModified(CmsI18nPeer::TITLE)) $criteria->add(CmsI18nPeer::TITLE, $this->title);
        if ($this->isColumnModified(CmsI18nPeer::PATH)) $criteria->add(CmsI18nPeer::PATH, $this->path);
        if ($this->isColumnModified(CmsI18nPeer::OLD_PATH)) $criteria->add(CmsI18nPeer::OLD_PATH, $this->old_path);
        if ($this->isColumnModified(CmsI18nPeer::CONTENT)) $criteria->add(CmsI18nPeer::CONTENT, $this->content);
        if ($this->isColumnModified(CmsI18nPeer::SETTINGS)) $criteria->add(CmsI18nPeer::SETTINGS, $this->settings);
        if ($this->isColumnModified(CmsI18nPeer::IS_RESTRICTED)) $criteria->add(CmsI18nPeer::IS_RESTRICTED, $this->is_restricted);
        if ($this->isColumnModified(CmsI18nPeer::IS_ACTIVE)) $criteria->add(CmsI18nPeer::IS_ACTIVE, $this->is_active);
        if ($this->isColumnModified(CmsI18nPeer::ON_MOBILE)) $criteria->add(CmsI18nPeer::ON_MOBILE, $this->on_mobile);
        if ($this->isColumnModified(CmsI18nPeer::ONLY_MOBILE)) $criteria->add(CmsI18nPeer::ONLY_MOBILE, $this->only_mobile);
        if ($this->isColumnModified(CmsI18nPeer::META_TITLE)) $criteria->add(CmsI18nPeer::META_TITLE, $this->meta_title);
        if ($this->isColumnModified(CmsI18nPeer::META_DESCRIPTION)) $criteria->add(CmsI18nPeer::META_DESCRIPTION, $this->meta_description);

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
        $criteria = new Criteria(CmsI18nPeer::DATABASE_NAME);
        $criteria->add(CmsI18nPeer::ID, $this->id);
        $criteria->add(CmsI18nPeer::LOCALE, $this->locale);

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
        $pks[0] = $this->getId();
        $pks[1] = $this->getLocale();

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
        $this->setId($keys[0]);
        $this->setLocale($keys[1]);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return (null === $this->getId()) && (null === $this->getLocale());
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of CmsI18n (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setId($this->getId());
        $copyObj->setLocale($this->getLocale());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setPath($this->getPath());
        $copyObj->setOldPath($this->getOldPath());
        $copyObj->setContent($this->getContent());
        $copyObj->setSettings($this->getSettings());
        $copyObj->setIsRestricted($this->getIsRestricted());
        $copyObj->setIsActive($this->getIsActive());
        $copyObj->setOnMobile($this->getOnMobile());
        $copyObj->setOnlyMobile($this->getOnlyMobile());
        $copyObj->setMetaTitle($this->getMetaTitle());
        $copyObj->setMetaDescription($this->getMetaDescription());

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
     * @return CmsI18n Clone of current object.
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
     * @return CmsI18nPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CmsI18nPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Cms object.
     *
     * @param                  Cms $v
     * @return CmsI18n The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCms(Cms $v = null)
    {
        if ($v === null) {
            $this->setId(NULL);
        } else {
            $this->setId($v->getId());
        }

        $this->aCms = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Cms object, it will not be re-added.
        if ($v !== null) {
            $v->addCmsI18n($this);
        }


        return $this;
    }


    /**
     * Get the associated Cms object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Cms The associated Cms object.
     * @throws PropelException
     */
    public function getCms(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCms === null && ($this->id !== null) && $doQuery) {
            $this->aCms = CmsQuery::create()->findPk($this->id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCms->addCmsI18ns($this);
             */
        }

        return $this->aCms;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->locale = null;
        $this->title = null;
        $this->path = null;
        $this->old_path = null;
        $this->content = null;
        $this->settings = null;
        $this->is_restricted = null;
        $this->is_active = null;
        $this->on_mobile = null;
        $this->only_mobile = null;
        $this->meta_title = null;
        $this->meta_description = null;
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
            if ($this->aCms instanceof Persistent) {
              $this->aCms->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aCms = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string The value of the 'title' column
     */
    public function __toString()
    {
        return (string) $this->getTitle();
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

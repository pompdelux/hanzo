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
use Hanzo\Model\Cms;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsRevision;
use Hanzo\Model\CmsRevisionQuery;
use Hanzo\Model\CmsThread;
use Hanzo\Model\CmsThreadQuery;

abstract class BaseCms extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\CmsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CmsPeer
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
     * The value for the parent_id field.
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the cms_thread_id field.
     * @var        int
     */
    protected $cms_thread_id;

    /**
     * The value for the sort field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $sort;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 'cms'
     * @var        string
     */
    protected $type;

    /**
     * The value for the updated_by field.
     * @var        string
     */
    protected $updated_by;

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
     * @var        CmsThread
     */
    protected $aCmsThread;

    /**
     * @var        Cms
     */
    protected $aCmsRelatedByParentId;

    /**
     * @var        PropelObjectCollection|Cms[] Collection to store aggregation of Cms objects.
     */
    protected $collCmssRelatedById;
    protected $collCmssRelatedByIdPartial;

    /**
     * @var        PropelObjectCollection|CmsRevision[] Collection to store aggregation of CmsRevision objects.
     */
    protected $collCmsRevisions;
    protected $collCmsRevisionsPartial;

    /**
     * @var        PropelObjectCollection|CmsI18n[] Collection to store aggregation of CmsI18n objects.
     */
    protected $collCmsI18ns;
    protected $collCmsI18nsPartial;

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

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'da_DK';

    /**
     * Current translation objects
     * @var        array[CmsI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $cmssRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $cmsRevisionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $cmsI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->sort = 1;
        $this->type = 'cms';
    }

    /**
     * Initializes internal state of BaseCms object.
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
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {

        return $this->parent_id;
    }

    /**
     * Get the [cms_thread_id] column value.
     *
     * @return int
     */
    public function getCmsThreadId()
    {

        return $this->cms_thread_id;
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
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [updated_by] column value.
     *
     * @return string
     */
    public function getUpdatedBy()
    {

        return $this->updated_by;
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
     * @return Cms The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CmsPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param  int $v new value
     * @return Cms The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[] = CmsPeer::PARENT_ID;
        }

        if ($this->aCmsRelatedByParentId !== null && $this->aCmsRelatedByParentId->getId() !== $v) {
            $this->aCmsRelatedByParentId = null;
        }


        return $this;
    } // setParentId()

    /**
     * Set the value of [cms_thread_id] column.
     *
     * @param  int $v new value
     * @return Cms The current object (for fluent API support)
     */
    public function setCmsThreadId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->cms_thread_id !== $v) {
            $this->cms_thread_id = $v;
            $this->modifiedColumns[] = CmsPeer::CMS_THREAD_ID;
        }

        if ($this->aCmsThread !== null && $this->aCmsThread->getId() !== $v) {
            $this->aCmsThread = null;
        }


        return $this;
    } // setCmsThreadId()

    /**
     * Set the value of [sort] column.
     *
     * @param  int $v new value
     * @return Cms The current object (for fluent API support)
     */
    public function setSort($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sort !== $v) {
            $this->sort = $v;
            $this->modifiedColumns[] = CmsPeer::SORT;
        }


        return $this;
    } // setSort()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return Cms The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = CmsPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [updated_by] column.
     *
     * @param  string $v new value
     * @return Cms The current object (for fluent API support)
     */
    public function setUpdatedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->updated_by !== $v) {
            $this->updated_by = $v;
            $this->modifiedColumns[] = CmsPeer::UPDATED_BY;
        }


        return $this;
    } // setUpdatedBy()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Cms The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = CmsPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Cms The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = CmsPeer::UPDATED_AT;
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
            if ($this->sort !== 1) {
                return false;
            }

            if ($this->type !== 'cms') {
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
            $this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->cms_thread_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->sort = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->updated_by = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->created_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->updated_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = CmsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Cms object", $e);
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

        if ($this->aCmsRelatedByParentId !== null && $this->parent_id !== $this->aCmsRelatedByParentId->getId()) {
            $this->aCmsRelatedByParentId = null;
        }
        if ($this->aCmsThread !== null && $this->cms_thread_id !== $this->aCmsThread->getId()) {
            $this->aCmsThread = null;
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
            $con = Propel::getConnection(CmsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CmsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCmsThread = null;
            $this->aCmsRelatedByParentId = null;
            $this->collCmssRelatedById = null;

            $this->collCmsRevisions = null;

            $this->collCmsI18ns = null;

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
            $con = Propel::getConnection(CmsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CmsQuery::create()
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
            $con = Propel::getConnection(CmsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CmsPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CmsPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CmsPeer::UPDATED_AT)) {
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
                CmsPeer::addInstanceToPool($this);
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

            if ($this->aCmsThread !== null) {
                if ($this->aCmsThread->isModified() || $this->aCmsThread->isNew()) {
                    $affectedRows += $this->aCmsThread->save($con);
                }
                $this->setCmsThread($this->aCmsThread);
            }

            if ($this->aCmsRelatedByParentId !== null) {
                if ($this->aCmsRelatedByParentId->isModified() || $this->aCmsRelatedByParentId->isNew()) {
                    $affectedRows += $this->aCmsRelatedByParentId->save($con);
                }
                $this->setCmsRelatedByParentId($this->aCmsRelatedByParentId);
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

            if ($this->cmssRelatedByIdScheduledForDeletion !== null) {
                if (!$this->cmssRelatedByIdScheduledForDeletion->isEmpty()) {
                    CmsQuery::create()
                        ->filterByPrimaryKeys($this->cmssRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cmssRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collCmssRelatedById !== null) {
                foreach ($this->collCmssRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->cmsRevisionsScheduledForDeletion !== null) {
                if (!$this->cmsRevisionsScheduledForDeletion->isEmpty()) {
                    CmsRevisionQuery::create()
                        ->filterByPrimaryKeys($this->cmsRevisionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cmsRevisionsScheduledForDeletion = null;
                }
            }

            if ($this->collCmsRevisions !== null) {
                foreach ($this->collCmsRevisions as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->cmsI18nsScheduledForDeletion !== null) {
                if (!$this->cmsI18nsScheduledForDeletion->isEmpty()) {
                    CmsI18nQuery::create()
                        ->filterByPrimaryKeys($this->cmsI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cmsI18nsScheduledForDeletion = null;
                }
            }

            if ($this->collCmsI18ns !== null) {
                foreach ($this->collCmsI18ns as $referrerFK) {
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

        $this->modifiedColumns[] = CmsPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CmsPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CmsPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CmsPeer::PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`parent_id`';
        }
        if ($this->isColumnModified(CmsPeer::CMS_THREAD_ID)) {
            $modifiedColumns[':p' . $index++]  = '`cms_thread_id`';
        }
        if ($this->isColumnModified(CmsPeer::SORT)) {
            $modifiedColumns[':p' . $index++]  = '`sort`';
        }
        if ($this->isColumnModified(CmsPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '`type`';
        }
        if ($this->isColumnModified(CmsPeer::UPDATED_BY)) {
            $modifiedColumns[':p' . $index++]  = '`updated_by`';
        }
        if ($this->isColumnModified(CmsPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CmsPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `cms` (%s) VALUES (%s)',
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
                    case '`parent_id`':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case '`cms_thread_id`':
                        $stmt->bindValue($identifier, $this->cms_thread_id, PDO::PARAM_INT);
                        break;
                    case '`sort`':
                        $stmt->bindValue($identifier, $this->sort, PDO::PARAM_INT);
                        break;
                    case '`type`':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case '`updated_by`':
                        $stmt->bindValue($identifier, $this->updated_by, PDO::PARAM_STR);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCmsThread !== null) {
                if (!$this->aCmsThread->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCmsThread->getValidationFailures());
                }
            }

            if ($this->aCmsRelatedByParentId !== null) {
                if (!$this->aCmsRelatedByParentId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCmsRelatedByParentId->getValidationFailures());
                }
            }


            if (($retval = CmsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCmssRelatedById !== null) {
                    foreach ($this->collCmssRelatedById as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCmsRevisions !== null) {
                    foreach ($this->collCmsRevisions as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCmsI18ns !== null) {
                    foreach ($this->collCmsI18ns as $referrerFK) {
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
        $pos = CmsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getParentId();
                break;
            case 2:
                return $this->getCmsThreadId();
                break;
            case 3:
                return $this->getSort();
                break;
            case 4:
                return $this->getType();
                break;
            case 5:
                return $this->getUpdatedBy();
                break;
            case 6:
                return $this->getCreatedAt();
                break;
            case 7:
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
        if (isset($alreadyDumpedObjects['Cms'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Cms'][$this->getPrimaryKey()] = true;
        $keys = CmsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getParentId(),
            $keys[2] => $this->getCmsThreadId(),
            $keys[3] => $this->getSort(),
            $keys[4] => $this->getType(),
            $keys[5] => $this->getUpdatedBy(),
            $keys[6] => $this->getCreatedAt(),
            $keys[7] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCmsThread) {
                $result['CmsThread'] = $this->aCmsThread->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCmsRelatedByParentId) {
                $result['CmsRelatedByParentId'] = $this->aCmsRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCmssRelatedById) {
                $result['CmssRelatedById'] = $this->collCmssRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCmsRevisions) {
                $result['CmsRevisions'] = $this->collCmsRevisions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCmsI18ns) {
                $result['CmsI18ns'] = $this->collCmsI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CmsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setParentId($value);
                break;
            case 2:
                $this->setCmsThreadId($value);
                break;
            case 3:
                $this->setSort($value);
                break;
            case 4:
                $this->setType($value);
                break;
            case 5:
                $this->setUpdatedBy($value);
                break;
            case 6:
                $this->setCreatedAt($value);
                break;
            case 7:
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
        $keys = CmsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCmsThreadId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSort($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setType($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUpdatedBy($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedAt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setUpdatedAt($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CmsPeer::DATABASE_NAME);

        if ($this->isColumnModified(CmsPeer::ID)) $criteria->add(CmsPeer::ID, $this->id);
        if ($this->isColumnModified(CmsPeer::PARENT_ID)) $criteria->add(CmsPeer::PARENT_ID, $this->parent_id);
        if ($this->isColumnModified(CmsPeer::CMS_THREAD_ID)) $criteria->add(CmsPeer::CMS_THREAD_ID, $this->cms_thread_id);
        if ($this->isColumnModified(CmsPeer::SORT)) $criteria->add(CmsPeer::SORT, $this->sort);
        if ($this->isColumnModified(CmsPeer::TYPE)) $criteria->add(CmsPeer::TYPE, $this->type);
        if ($this->isColumnModified(CmsPeer::UPDATED_BY)) $criteria->add(CmsPeer::UPDATED_BY, $this->updated_by);
        if ($this->isColumnModified(CmsPeer::CREATED_AT)) $criteria->add(CmsPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CmsPeer::UPDATED_AT)) $criteria->add(CmsPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CmsPeer::DATABASE_NAME);
        $criteria->add(CmsPeer::ID, $this->id);

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
     * @param object $copyObj An object of Cms (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentId($this->getParentId());
        $copyObj->setCmsThreadId($this->getCmsThreadId());
        $copyObj->setSort($this->getSort());
        $copyObj->setType($this->getType());
        $copyObj->setUpdatedBy($this->getUpdatedBy());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCmssRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCmsRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCmsRevisions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCmsRevision($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCmsI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCmsI18n($relObj->copy($deepCopy));
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
     * @return Cms Clone of current object.
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
     * @return CmsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CmsPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CmsThread object.
     *
     * @param                  CmsThread $v
     * @return Cms The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCmsThread(CmsThread $v = null)
    {
        if ($v === null) {
            $this->setCmsThreadId(NULL);
        } else {
            $this->setCmsThreadId($v->getId());
        }

        $this->aCmsThread = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CmsThread object, it will not be re-added.
        if ($v !== null) {
            $v->addCms($this);
        }


        return $this;
    }


    /**
     * Get the associated CmsThread object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CmsThread The associated CmsThread object.
     * @throws PropelException
     */
    public function getCmsThread(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCmsThread === null && ($this->cms_thread_id !== null) && $doQuery) {
            $this->aCmsThread = CmsThreadQuery::create()->findPk($this->cms_thread_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCmsThread->addCmss($this);
             */
        }

        return $this->aCmsThread;
    }

    /**
     * Declares an association between this object and a Cms object.
     *
     * @param                  Cms $v
     * @return Cms The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCmsRelatedByParentId(Cms $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aCmsRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Cms object, it will not be re-added.
        if ($v !== null) {
            $v->addCmsRelatedById($this);
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
    public function getCmsRelatedByParentId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCmsRelatedByParentId === null && ($this->parent_id !== null) && $doQuery) {
            $this->aCmsRelatedByParentId = CmsQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCmsRelatedByParentId->addCmssRelatedById($this);
             */
        }

        return $this->aCmsRelatedByParentId;
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
        if ('CmsRelatedById' == $relationName) {
            $this->initCmssRelatedById();
        }
        if ('CmsRevision' == $relationName) {
            $this->initCmsRevisions();
        }
        if ('CmsI18n' == $relationName) {
            $this->initCmsI18ns();
        }
    }

    /**
     * Clears out the collCmssRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Cms The current object (for fluent API support)
     * @see        addCmssRelatedById()
     */
    public function clearCmssRelatedById()
    {
        $this->collCmssRelatedById = null; // important to set this to null since that means it is uninitialized
        $this->collCmssRelatedByIdPartial = null;

        return $this;
    }

    /**
     * reset is the collCmssRelatedById collection loaded partially
     *
     * @return void
     */
    public function resetPartialCmssRelatedById($v = true)
    {
        $this->collCmssRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collCmssRelatedById collection.
     *
     * By default this just sets the collCmssRelatedById collection to an empty array (like clearcollCmssRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCmssRelatedById($overrideExisting = true)
    {
        if (null !== $this->collCmssRelatedById && !$overrideExisting) {
            return;
        }
        $this->collCmssRelatedById = new PropelObjectCollection();
        $this->collCmssRelatedById->setModel('Cms');
    }

    /**
     * Gets an array of Cms objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Cms is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Cms[] List of Cms objects
     * @throws PropelException
     */
    public function getCmssRelatedById($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCmssRelatedByIdPartial && !$this->isNew();
        if (null === $this->collCmssRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCmssRelatedById) {
                // return empty collection
                $this->initCmssRelatedById();
            } else {
                $collCmssRelatedById = CmsQuery::create(null, $criteria)
                    ->filterByCmsRelatedByParentId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCmssRelatedByIdPartial && count($collCmssRelatedById)) {
                      $this->initCmssRelatedById(false);

                      foreach ($collCmssRelatedById as $obj) {
                        if (false == $this->collCmssRelatedById->contains($obj)) {
                          $this->collCmssRelatedById->append($obj);
                        }
                      }

                      $this->collCmssRelatedByIdPartial = true;
                    }

                    $collCmssRelatedById->getInternalIterator()->rewind();

                    return $collCmssRelatedById;
                }

                if ($partial && $this->collCmssRelatedById) {
                    foreach ($this->collCmssRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collCmssRelatedById[] = $obj;
                        }
                    }
                }

                $this->collCmssRelatedById = $collCmssRelatedById;
                $this->collCmssRelatedByIdPartial = false;
            }
        }

        return $this->collCmssRelatedById;
    }

    /**
     * Sets a collection of CmsRelatedById objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $cmssRelatedById A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Cms The current object (for fluent API support)
     */
    public function setCmssRelatedById(PropelCollection $cmssRelatedById, PropelPDO $con = null)
    {
        $cmssRelatedByIdToDelete = $this->getCmssRelatedById(new Criteria(), $con)->diff($cmssRelatedById);


        $this->cmssRelatedByIdScheduledForDeletion = $cmssRelatedByIdToDelete;

        foreach ($cmssRelatedByIdToDelete as $cmsRelatedByIdRemoved) {
            $cmsRelatedByIdRemoved->setCmsRelatedByParentId(null);
        }

        $this->collCmssRelatedById = null;
        foreach ($cmssRelatedById as $cmsRelatedById) {
            $this->addCmsRelatedById($cmsRelatedById);
        }

        $this->collCmssRelatedById = $cmssRelatedById;
        $this->collCmssRelatedByIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Cms objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Cms objects.
     * @throws PropelException
     */
    public function countCmssRelatedById(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCmssRelatedByIdPartial && !$this->isNew();
        if (null === $this->collCmssRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCmssRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCmssRelatedById());
            }
            $query = CmsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCmsRelatedByParentId($this)
                ->count($con);
        }

        return count($this->collCmssRelatedById);
    }

    /**
     * Method called to associate a Cms object to this object
     * through the Cms foreign key attribute.
     *
     * @param    Cms $l Cms
     * @return Cms The current object (for fluent API support)
     */
    public function addCmsRelatedById(Cms $l)
    {
        if ($this->collCmssRelatedById === null) {
            $this->initCmssRelatedById();
            $this->collCmssRelatedByIdPartial = true;
        }

        if (!in_array($l, $this->collCmssRelatedById->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCmsRelatedById($l);

            if ($this->cmssRelatedByIdScheduledForDeletion and $this->cmssRelatedByIdScheduledForDeletion->contains($l)) {
                $this->cmssRelatedByIdScheduledForDeletion->remove($this->cmssRelatedByIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CmsRelatedById $cmsRelatedById The cmsRelatedById object to add.
     */
    protected function doAddCmsRelatedById($cmsRelatedById)
    {
        $this->collCmssRelatedById[]= $cmsRelatedById;
        $cmsRelatedById->setCmsRelatedByParentId($this);
    }

    /**
     * @param	CmsRelatedById $cmsRelatedById The cmsRelatedById object to remove.
     * @return Cms The current object (for fluent API support)
     */
    public function removeCmsRelatedById($cmsRelatedById)
    {
        if ($this->getCmssRelatedById()->contains($cmsRelatedById)) {
            $this->collCmssRelatedById->remove($this->collCmssRelatedById->search($cmsRelatedById));
            if (null === $this->cmssRelatedByIdScheduledForDeletion) {
                $this->cmssRelatedByIdScheduledForDeletion = clone $this->collCmssRelatedById;
                $this->cmssRelatedByIdScheduledForDeletion->clear();
            }
            $this->cmssRelatedByIdScheduledForDeletion[]= $cmsRelatedById;
            $cmsRelatedById->setCmsRelatedByParentId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Cms is new, it will return
     * an empty collection; or if this Cms has previously
     * been saved, it will retrieve related CmssRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Cms.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Cms[] List of Cms objects
     */
    public function getCmssRelatedByIdJoinCmsThread($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CmsQuery::create(null, $criteria);
        $query->joinWith('CmsThread', $join_behavior);

        return $this->getCmssRelatedById($query, $con);
    }

    /**
     * Clears out the collCmsRevisions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Cms The current object (for fluent API support)
     * @see        addCmsRevisions()
     */
    public function clearCmsRevisions()
    {
        $this->collCmsRevisions = null; // important to set this to null since that means it is uninitialized
        $this->collCmsRevisionsPartial = null;

        return $this;
    }

    /**
     * reset is the collCmsRevisions collection loaded partially
     *
     * @return void
     */
    public function resetPartialCmsRevisions($v = true)
    {
        $this->collCmsRevisionsPartial = $v;
    }

    /**
     * Initializes the collCmsRevisions collection.
     *
     * By default this just sets the collCmsRevisions collection to an empty array (like clearcollCmsRevisions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCmsRevisions($overrideExisting = true)
    {
        if (null !== $this->collCmsRevisions && !$overrideExisting) {
            return;
        }
        $this->collCmsRevisions = new PropelObjectCollection();
        $this->collCmsRevisions->setModel('CmsRevision');
    }

    /**
     * Gets an array of CmsRevision objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Cms is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CmsRevision[] List of CmsRevision objects
     * @throws PropelException
     */
    public function getCmsRevisions($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCmsRevisionsPartial && !$this->isNew();
        if (null === $this->collCmsRevisions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCmsRevisions) {
                // return empty collection
                $this->initCmsRevisions();
            } else {
                $collCmsRevisions = CmsRevisionQuery::create(null, $criteria)
                    ->filterByCms($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCmsRevisionsPartial && count($collCmsRevisions)) {
                      $this->initCmsRevisions(false);

                      foreach ($collCmsRevisions as $obj) {
                        if (false == $this->collCmsRevisions->contains($obj)) {
                          $this->collCmsRevisions->append($obj);
                        }
                      }

                      $this->collCmsRevisionsPartial = true;
                    }

                    $collCmsRevisions->getInternalIterator()->rewind();

                    return $collCmsRevisions;
                }

                if ($partial && $this->collCmsRevisions) {
                    foreach ($this->collCmsRevisions as $obj) {
                        if ($obj->isNew()) {
                            $collCmsRevisions[] = $obj;
                        }
                    }
                }

                $this->collCmsRevisions = $collCmsRevisions;
                $this->collCmsRevisionsPartial = false;
            }
        }

        return $this->collCmsRevisions;
    }

    /**
     * Sets a collection of CmsRevision objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $cmsRevisions A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Cms The current object (for fluent API support)
     */
    public function setCmsRevisions(PropelCollection $cmsRevisions, PropelPDO $con = null)
    {
        $cmsRevisionsToDelete = $this->getCmsRevisions(new Criteria(), $con)->diff($cmsRevisions);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->cmsRevisionsScheduledForDeletion = clone $cmsRevisionsToDelete;

        foreach ($cmsRevisionsToDelete as $cmsRevisionRemoved) {
            $cmsRevisionRemoved->setCms(null);
        }

        $this->collCmsRevisions = null;
        foreach ($cmsRevisions as $cmsRevision) {
            $this->addCmsRevision($cmsRevision);
        }

        $this->collCmsRevisions = $cmsRevisions;
        $this->collCmsRevisionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CmsRevision objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CmsRevision objects.
     * @throws PropelException
     */
    public function countCmsRevisions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCmsRevisionsPartial && !$this->isNew();
        if (null === $this->collCmsRevisions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCmsRevisions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCmsRevisions());
            }
            $query = CmsRevisionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCms($this)
                ->count($con);
        }

        return count($this->collCmsRevisions);
    }

    /**
     * Method called to associate a CmsRevision object to this object
     * through the CmsRevision foreign key attribute.
     *
     * @param    CmsRevision $l CmsRevision
     * @return Cms The current object (for fluent API support)
     */
    public function addCmsRevision(CmsRevision $l)
    {
        if ($this->collCmsRevisions === null) {
            $this->initCmsRevisions();
            $this->collCmsRevisionsPartial = true;
        }

        if (!in_array($l, $this->collCmsRevisions->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCmsRevision($l);

            if ($this->cmsRevisionsScheduledForDeletion and $this->cmsRevisionsScheduledForDeletion->contains($l)) {
                $this->cmsRevisionsScheduledForDeletion->remove($this->cmsRevisionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CmsRevision $cmsRevision The cmsRevision object to add.
     */
    protected function doAddCmsRevision($cmsRevision)
    {
        $this->collCmsRevisions[]= $cmsRevision;
        $cmsRevision->setCms($this);
    }

    /**
     * @param	CmsRevision $cmsRevision The cmsRevision object to remove.
     * @return Cms The current object (for fluent API support)
     */
    public function removeCmsRevision($cmsRevision)
    {
        if ($this->getCmsRevisions()->contains($cmsRevision)) {
            $this->collCmsRevisions->remove($this->collCmsRevisions->search($cmsRevision));
            if (null === $this->cmsRevisionsScheduledForDeletion) {
                $this->cmsRevisionsScheduledForDeletion = clone $this->collCmsRevisions;
                $this->cmsRevisionsScheduledForDeletion->clear();
            }
            $this->cmsRevisionsScheduledForDeletion[]= clone $cmsRevision;
            $cmsRevision->setCms(null);
        }

        return $this;
    }

    /**
     * Clears out the collCmsI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Cms The current object (for fluent API support)
     * @see        addCmsI18ns()
     */
    public function clearCmsI18ns()
    {
        $this->collCmsI18ns = null; // important to set this to null since that means it is uninitialized
        $this->collCmsI18nsPartial = null;

        return $this;
    }

    /**
     * reset is the collCmsI18ns collection loaded partially
     *
     * @return void
     */
    public function resetPartialCmsI18ns($v = true)
    {
        $this->collCmsI18nsPartial = $v;
    }

    /**
     * Initializes the collCmsI18ns collection.
     *
     * By default this just sets the collCmsI18ns collection to an empty array (like clearcollCmsI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCmsI18ns($overrideExisting = true)
    {
        if (null !== $this->collCmsI18ns && !$overrideExisting) {
            return;
        }
        $this->collCmsI18ns = new PropelObjectCollection();
        $this->collCmsI18ns->setModel('CmsI18n');
    }

    /**
     * Gets an array of CmsI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Cms is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CmsI18n[] List of CmsI18n objects
     * @throws PropelException
     */
    public function getCmsI18ns($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCmsI18nsPartial && !$this->isNew();
        if (null === $this->collCmsI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCmsI18ns) {
                // return empty collection
                $this->initCmsI18ns();
            } else {
                $collCmsI18ns = CmsI18nQuery::create(null, $criteria)
                    ->filterByCms($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCmsI18nsPartial && count($collCmsI18ns)) {
                      $this->initCmsI18ns(false);

                      foreach ($collCmsI18ns as $obj) {
                        if (false == $this->collCmsI18ns->contains($obj)) {
                          $this->collCmsI18ns->append($obj);
                        }
                      }

                      $this->collCmsI18nsPartial = true;
                    }

                    $collCmsI18ns->getInternalIterator()->rewind();

                    return $collCmsI18ns;
                }

                if ($partial && $this->collCmsI18ns) {
                    foreach ($this->collCmsI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collCmsI18ns[] = $obj;
                        }
                    }
                }

                $this->collCmsI18ns = $collCmsI18ns;
                $this->collCmsI18nsPartial = false;
            }
        }

        return $this->collCmsI18ns;
    }

    /**
     * Sets a collection of CmsI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $cmsI18ns A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Cms The current object (for fluent API support)
     */
    public function setCmsI18ns(PropelCollection $cmsI18ns, PropelPDO $con = null)
    {
        $cmsI18nsToDelete = $this->getCmsI18ns(new Criteria(), $con)->diff($cmsI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->cmsI18nsScheduledForDeletion = clone $cmsI18nsToDelete;

        foreach ($cmsI18nsToDelete as $cmsI18nRemoved) {
            $cmsI18nRemoved->setCms(null);
        }

        $this->collCmsI18ns = null;
        foreach ($cmsI18ns as $cmsI18n) {
            $this->addCmsI18n($cmsI18n);
        }

        $this->collCmsI18ns = $cmsI18ns;
        $this->collCmsI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CmsI18n objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CmsI18n objects.
     * @throws PropelException
     */
    public function countCmsI18ns(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCmsI18nsPartial && !$this->isNew();
        if (null === $this->collCmsI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCmsI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCmsI18ns());
            }
            $query = CmsI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCms($this)
                ->count($con);
        }

        return count($this->collCmsI18ns);
    }

    /**
     * Method called to associate a CmsI18n object to this object
     * through the CmsI18n foreign key attribute.
     *
     * @param    CmsI18n $l CmsI18n
     * @return Cms The current object (for fluent API support)
     */
    public function addCmsI18n(CmsI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collCmsI18ns === null) {
            $this->initCmsI18ns();
            $this->collCmsI18nsPartial = true;
        }

        if (!in_array($l, $this->collCmsI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCmsI18n($l);

            if ($this->cmsI18nsScheduledForDeletion and $this->cmsI18nsScheduledForDeletion->contains($l)) {
                $this->cmsI18nsScheduledForDeletion->remove($this->cmsI18nsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CmsI18n $cmsI18n The cmsI18n object to add.
     */
    protected function doAddCmsI18n($cmsI18n)
    {
        $this->collCmsI18ns[]= $cmsI18n;
        $cmsI18n->setCms($this);
    }

    /**
     * @param	CmsI18n $cmsI18n The cmsI18n object to remove.
     * @return Cms The current object (for fluent API support)
     */
    public function removeCmsI18n($cmsI18n)
    {
        if ($this->getCmsI18ns()->contains($cmsI18n)) {
            $this->collCmsI18ns->remove($this->collCmsI18ns->search($cmsI18n));
            if (null === $this->cmsI18nsScheduledForDeletion) {
                $this->cmsI18nsScheduledForDeletion = clone $this->collCmsI18ns;
                $this->cmsI18nsScheduledForDeletion->clear();
            }
            $this->cmsI18nsScheduledForDeletion[]= clone $cmsI18n;
            $cmsI18n->setCms(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->parent_id = null;
        $this->cms_thread_id = null;
        $this->sort = null;
        $this->type = null;
        $this->updated_by = null;
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
            if ($this->collCmssRelatedById) {
                foreach ($this->collCmssRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCmsRevisions) {
                foreach ($this->collCmsRevisions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCmsI18ns) {
                foreach ($this->collCmsI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCmsThread instanceof Persistent) {
              $this->aCmsThread->clearAllReferences($deep);
            }
            if ($this->aCmsRelatedByParentId instanceof Persistent) {
              $this->aCmsRelatedByParentId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'da_DK';
        $this->currentTranslations = null;

        if ($this->collCmssRelatedById instanceof PropelCollection) {
            $this->collCmssRelatedById->clearIterator();
        }
        $this->collCmssRelatedById = null;
        if ($this->collCmsRevisions instanceof PropelCollection) {
            $this->collCmsRevisions->clearIterator();
        }
        $this->collCmsRevisions = null;
        if ($this->collCmsI18ns instanceof PropelCollection) {
            $this->collCmsI18ns->clearIterator();
        }
        $this->collCmsI18ns = null;
        $this->aCmsThread = null;
        $this->aCmsRelatedByParentId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CmsPeer::DEFAULT_STRING_FORMAT);
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

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    Cms The current object (for fluent API support)
     */
    public function setLocale($locale = 'da_DK')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     PropelPDO $con an optional connection object
     *
     * @return CmsI18n */
    public function getTranslation($locale = 'da_DK', PropelPDO $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collCmsI18ns) {
                foreach ($this->collCmsI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new CmsI18n();
                $translation->setLocale($locale);
            } else {
                $translation = CmsI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addCmsI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     PropelPDO $con an optional connection object
     *
     * @return    Cms The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'da_DK', PropelPDO $con = null)
    {
        if (!$this->isNew()) {
            CmsI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collCmsI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collCmsI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     PropelPDO $con an optional connection object
     *
     * @return CmsI18n */
    public function getCurrentTranslation(PropelPDO $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param  string $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [path] column value.
         *
         * @return string
         */
        public function getPath()
        {
        return $this->getCurrentTranslation()->getPath();
    }


        /**
         * Set the value of [path] column.
         *
         * @param  string $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setPath($v)
        {    $this->getCurrentTranslation()->setPath($v);

        return $this;
    }


        /**
         * Get the [old_path] column value.
         *
         * @return string
         */
        public function getOldPath()
        {
        return $this->getCurrentTranslation()->getOldPath();
    }


        /**
         * Set the value of [old_path] column.
         *
         * @param  string $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setOldPath($v)
        {    $this->getCurrentTranslation()->setOldPath($v);

        return $this;
    }


        /**
         * Get the [content] column value.
         *
         * @return string
         */
        public function getContent()
        {
        return $this->getCurrentTranslation()->getContent();
    }


        /**
         * Set the value of [content] column.
         *
         * @param  string $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setContent($v)
        {    $this->getCurrentTranslation()->setContent($v);

        return $this;
    }


        /**
         * Get the [settings] column value.
         *
         * @return string
         */
        public function getSettings()
        {
        return $this->getCurrentTranslation()->getSettings();
    }


        /**
         * Set the value of [settings] column.
         *
         * @param  string $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setSettings($v)
        {    $this->getCurrentTranslation()->setSettings($v);

        return $this;
    }


        /**
         * Get the [is_restricted] column value.
         *
         * @return boolean
         */
        public function getIsRestricted()
        {
        return $this->getCurrentTranslation()->getIsRestricted();
    }


        /**
         * Set the value of [is_restricted] column.
         *
         * @param  boolean $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setIsRestricted($v)
        {    $this->getCurrentTranslation()->setIsRestricted($v);

        return $this;
    }


        /**
         * Get the [is_active] column value.
         *
         * @return boolean
         */
        public function getIsActive()
        {
        return $this->getCurrentTranslation()->getIsActive();
    }


        /**
         * Set the value of [is_active] column.
         *
         * @param  boolean $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setIsActive($v)
        {    $this->getCurrentTranslation()->setIsActive($v);

        return $this;
    }


        /**
         * Get the [on_mobile] column value.
         *
         * @return boolean
         */
        public function getOnMobile()
        {
        return $this->getCurrentTranslation()->getOnMobile();
    }


        /**
         * Set the value of [on_mobile] column.
         *
         * @param  boolean $v new value
         * @return CmsI18n The current object (for fluent API support)
         */
        public function setOnMobile($v)
        {    $this->getCurrentTranslation()->setOnMobile($v);

        return $this;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     Cms The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = CmsPeer::UPDATED_AT;

        return $this;
    }

}

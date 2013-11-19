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
use Hanzo\Model\MannequinImages;
use Hanzo\Model\MannequinImagesQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\ProductsQuantityDiscount;
use Hanzo\Model\ProductsQuantityDiscountQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\ProductsWashingInstructionsQuery;
use Hanzo\Model\RelatedProducts;
use Hanzo\Model\RelatedProductsQuery;
use Hanzo\Model\SearchProductsTags;
use Hanzo\Model\SearchProductsTagsQuery;

abstract class BaseProducts extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Hanzo\\Model\\ProductsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProductsPeer
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
     * The value for the sku field.
     * @var        string
     */
    protected $sku;

    /**
     * The value for the master field.
     * @var        string
     */
    protected $master;

    /**
     * The value for the size field.
     * @var        string
     */
    protected $size;

    /**
     * The value for the color field.
     * @var        string
     */
    protected $color;

    /**
     * The value for the unit field.
     * @var        string
     */
    protected $unit;

    /**
     * The value for the washing field.
     * @var        int
     */
    protected $washing;

    /**
     * The value for the has_video field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $has_video;

    /**
     * The value for the is_out_of_stock field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_out_of_stock;

    /**
     * The value for the is_active field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $is_active;

    /**
     * The value for the is_voucher field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_voucher;

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
     * @var        Products
     */
    protected $aProductsRelatedByMaster;

    /**
     * @var        ProductsWashingInstructions
     */
    protected $aProductsWashingInstructions;

    /**
     * @var        PropelObjectCollection|MannequinImages[] Collection to store aggregation of MannequinImages objects.
     */
    protected $collMannequinImagess;
    protected $collMannequinImagessPartial;

    /**
     * @var        PropelObjectCollection|Products[] Collection to store aggregation of Products objects.
     */
    protected $collProductssRelatedBySku;
    protected $collProductssRelatedBySkuPartial;

    /**
     * @var        PropelObjectCollection|ProductsDomainsPrices[] Collection to store aggregation of ProductsDomainsPrices objects.
     */
    protected $collProductsDomainsPricess;
    protected $collProductsDomainsPricessPartial;

    /**
     * @var        PropelObjectCollection|ProductsImages[] Collection to store aggregation of ProductsImages objects.
     */
    protected $collProductsImagess;
    protected $collProductsImagessPartial;

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
     * @var        PropelObjectCollection|ProductsQuantityDiscount[] Collection to store aggregation of ProductsQuantityDiscount objects.
     */
    protected $collProductsQuantityDiscounts;
    protected $collProductsQuantityDiscountsPartial;

    /**
     * @var        PropelObjectCollection|ProductsStock[] Collection to store aggregation of ProductsStock objects.
     */
    protected $collProductsStocks;
    protected $collProductsStocksPartial;

    /**
     * @var        PropelObjectCollection|ProductsToCategories[] Collection to store aggregation of ProductsToCategories objects.
     */
    protected $collProductsToCategoriess;
    protected $collProductsToCategoriessPartial;

    /**
     * @var        PropelObjectCollection|OrdersLines[] Collection to store aggregation of OrdersLines objects.
     */
    protected $collOrdersLiness;
    protected $collOrdersLinessPartial;

    /**
     * @var        PropelObjectCollection|RelatedProducts[] Collection to store aggregation of RelatedProducts objects.
     */
    protected $collRelatedProductssRelatedByMaster;
    protected $collRelatedProductssRelatedByMasterPartial;

    /**
     * @var        PropelObjectCollection|RelatedProducts[] Collection to store aggregation of RelatedProducts objects.
     */
    protected $collRelatedProductssRelatedBySku;
    protected $collRelatedProductssRelatedBySkuPartial;

    /**
     * @var        PropelObjectCollection|SearchProductsTags[] Collection to store aggregation of SearchProductsTags objects.
     */
    protected $collSearchProductsTagssRelatedByMasterProductsId;
    protected $collSearchProductsTagssRelatedByMasterProductsIdPartial;

    /**
     * @var        PropelObjectCollection|SearchProductsTags[] Collection to store aggregation of SearchProductsTags objects.
     */
    protected $collSearchProductsTagssRelatedByProductsId;
    protected $collSearchProductsTagssRelatedByProductsIdPartial;

    /**
     * @var        PropelObjectCollection|ProductsI18n[] Collection to store aggregation of ProductsI18n objects.
     */
    protected $collProductsI18ns;
    protected $collProductsI18nsPartial;

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
     * @var        array[ProductsI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $mannequinImagessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productssRelatedBySkuScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsDomainsPricessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsImagessScheduledForDeletion = null;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsQuantityDiscountsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsStocksScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsToCategoriessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ordersLinessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $relatedProductssRelatedByMasterScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $relatedProductssRelatedBySkuScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $searchProductsTagssRelatedByProductsIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $productsI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->has_video = true;
        $this->is_out_of_stock = false;
        $this->is_active = true;
        $this->is_voucher = false;
    }

    /**
     * Initializes internal state of BaseProducts object.
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
     * Get the [sku] column value.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
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
     * Get the [size] column value.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
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
     * Get the [unit] column value.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Get the [washing] column value.
     *
     * @return int
     */
    public function getWashing()
    {
        return $this->washing;
    }

    /**
     * Get the [has_video] column value.
     *
     * @return boolean
     */
    public function getHasVideo()
    {
        return $this->has_video;
    }

    /**
     * Get the [is_out_of_stock] column value.
     *
     * @return boolean
     */
    public function getIsOutOfStock()
    {
        return $this->is_out_of_stock;
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
     * Get the [is_voucher] column value.
     *
     * @return boolean
     */
    public function getIsVoucher()
    {
        return $this->is_voucher;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
     * option in order to avoid converstions to integers (which are limited in the dates they can express).
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
     * option in order to avoid converstions to integers (which are limited in the dates they can express).
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
     * @param int $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ProductsPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [sku] column.
     *
     * @param string $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setSku($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->sku !== $v) {
            $this->sku = $v;
            $this->modifiedColumns[] = ProductsPeer::SKU;
        }


        return $this;
    } // setSku()

    /**
     * Set the value of [master] column.
     *
     * @param string $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setMaster($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->master !== $v) {
            $this->master = $v;
            $this->modifiedColumns[] = ProductsPeer::MASTER;
        }

        if ($this->aProductsRelatedByMaster !== null && $this->aProductsRelatedByMaster->getSku() !== $v) {
            $this->aProductsRelatedByMaster = null;
        }


        return $this;
    } // setMaster()

    /**
     * Set the value of [size] column.
     *
     * @param string $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setSize($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->size !== $v) {
            $this->size = $v;
            $this->modifiedColumns[] = ProductsPeer::SIZE;
        }


        return $this;
    } // setSize()

    /**
     * Set the value of [color] column.
     *
     * @param string $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setColor($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[] = ProductsPeer::COLOR;
        }


        return $this;
    } // setColor()

    /**
     * Set the value of [unit] column.
     *
     * @param string $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setUnit($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->unit !== $v) {
            $this->unit = $v;
            $this->modifiedColumns[] = ProductsPeer::UNIT;
        }


        return $this;
    } // setUnit()

    /**
     * Set the value of [washing] column.
     *
     * @param int $v new value
     * @return Products The current object (for fluent API support)
     */
    public function setWashing($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->washing !== $v) {
            $this->washing = $v;
            $this->modifiedColumns[] = ProductsPeer::WASHING;
        }

        if ($this->aProductsWashingInstructions !== null && $this->aProductsWashingInstructions->getCode() !== $v) {
            $this->aProductsWashingInstructions = null;
        }


        return $this;
    } // setWashing()

    /**
     * Sets the value of the [has_video] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Products The current object (for fluent API support)
     */
    public function setHasVideo($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->has_video !== $v) {
            $this->has_video = $v;
            $this->modifiedColumns[] = ProductsPeer::HAS_VIDEO;
        }


        return $this;
    } // setHasVideo()

    /**
     * Sets the value of the [is_out_of_stock] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Products The current object (for fluent API support)
     */
    public function setIsOutOfStock($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_out_of_stock !== $v) {
            $this->is_out_of_stock = $v;
            $this->modifiedColumns[] = ProductsPeer::IS_OUT_OF_STOCK;
        }


        return $this;
    } // setIsOutOfStock()

    /**
     * Sets the value of the [is_active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Products The current object (for fluent API support)
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
            $this->modifiedColumns[] = ProductsPeer::IS_ACTIVE;
        }


        return $this;
    } // setIsActive()

    /**
     * Sets the value of the [is_voucher] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Products The current object (for fluent API support)
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
            $this->modifiedColumns[] = ProductsPeer::IS_VOUCHER;
        }


        return $this;
    } // setIsVoucher()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Products The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = ProductsPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Products The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = ProductsPeer::UPDATED_AT;
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
            if ($this->has_video !== true) {
                return false;
            }

            if ($this->is_out_of_stock !== false) {
                return false;
            }

            if ($this->is_active !== true) {
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
     * @param int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->sku = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->master = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->size = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->color = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->unit = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->washing = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->has_video = ($row[$startcol + 7] !== null) ? (boolean) $row[$startcol + 7] : null;
            $this->is_out_of_stock = ($row[$startcol + 8] !== null) ? (boolean) $row[$startcol + 8] : null;
            $this->is_active = ($row[$startcol + 9] !== null) ? (boolean) $row[$startcol + 9] : null;
            $this->is_voucher = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
            $this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 13; // 13 = ProductsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Products object", $e);
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

        if ($this->aProductsRelatedByMaster !== null && $this->master !== $this->aProductsRelatedByMaster->getSku()) {
            $this->aProductsRelatedByMaster = null;
        }
        if ($this->aProductsWashingInstructions !== null && $this->washing !== $this->aProductsWashingInstructions->getCode()) {
            $this->aProductsWashingInstructions = null;
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
            $con = Propel::getConnection(ProductsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ProductsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aProductsRelatedByMaster = null;
            $this->aProductsWashingInstructions = null;
            $this->collMannequinImagess = null;

            $this->collProductssRelatedBySku = null;

            $this->collProductsDomainsPricess = null;

            $this->collProductsImagess = null;

            $this->collProductsImagesCategoriesSorts = null;

            $this->collProductsImagesProductReferencess = null;

            $this->collProductsQuantityDiscounts = null;

            $this->collProductsStocks = null;

            $this->collProductsToCategoriess = null;

            $this->collOrdersLiness = null;

            $this->collRelatedProductssRelatedByMaster = null;

            $this->collRelatedProductssRelatedBySku = null;

            $this->collSearchProductsTagssRelatedByMasterProductsId = null;

            $this->collSearchProductsTagssRelatedByProductsId = null;

            $this->collProductsI18ns = null;

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
            $con = Propel::getConnection(ProductsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ProductsQuery::create()
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
            $con = Propel::getConnection(ProductsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(ProductsPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ProductsPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ProductsPeer::UPDATED_AT)) {
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
                ProductsPeer::addInstanceToPool($this);
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

            if ($this->aProductsRelatedByMaster !== null) {
                if ($this->aProductsRelatedByMaster->isModified() || $this->aProductsRelatedByMaster->isNew()) {
                    $affectedRows += $this->aProductsRelatedByMaster->save($con);
                }
                $this->setProductsRelatedByMaster($this->aProductsRelatedByMaster);
            }

            if ($this->aProductsWashingInstructions !== null) {
                if ($this->aProductsWashingInstructions->isModified() || $this->aProductsWashingInstructions->isNew()) {
                    $affectedRows += $this->aProductsWashingInstructions->save($con);
                }
                $this->setProductsWashingInstructions($this->aProductsWashingInstructions);
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

            if ($this->mannequinImagessScheduledForDeletion !== null) {
                if (!$this->mannequinImagessScheduledForDeletion->isEmpty()) {
                    MannequinImagesQuery::create()
                        ->filterByPrimaryKeys($this->mannequinImagessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->mannequinImagessScheduledForDeletion = null;
                }
            }

            if ($this->collMannequinImagess !== null) {
                foreach ($this->collMannequinImagess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productssRelatedBySkuScheduledForDeletion !== null) {
                if (!$this->productssRelatedBySkuScheduledForDeletion->isEmpty()) {
                    foreach ($this->productssRelatedBySkuScheduledForDeletion as $productsRelatedBySku) {
                        // need to save related object because we set the relation to null
                        $productsRelatedBySku->save($con);
                    }
                    $this->productssRelatedBySkuScheduledForDeletion = null;
                }
            }

            if ($this->collProductssRelatedBySku !== null) {
                foreach ($this->collProductssRelatedBySku as $referrerFK) {
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

            if ($this->productsImagessScheduledForDeletion !== null) {
                if (!$this->productsImagessScheduledForDeletion->isEmpty()) {
                    ProductsImagesQuery::create()
                        ->filterByPrimaryKeys($this->productsImagessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsImagessScheduledForDeletion = null;
                }
            }

            if ($this->collProductsImagess !== null) {
                foreach ($this->collProductsImagess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

            if ($this->productsStocksScheduledForDeletion !== null) {
                if (!$this->productsStocksScheduledForDeletion->isEmpty()) {
                    ProductsStockQuery::create()
                        ->filterByPrimaryKeys($this->productsStocksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsStocksScheduledForDeletion = null;
                }
            }

            if ($this->collProductsStocks !== null) {
                foreach ($this->collProductsStocks as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsToCategoriessScheduledForDeletion !== null) {
                if (!$this->productsToCategoriessScheduledForDeletion->isEmpty()) {
                    ProductsToCategoriesQuery::create()
                        ->filterByPrimaryKeys($this->productsToCategoriessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsToCategoriessScheduledForDeletion = null;
                }
            }

            if ($this->collProductsToCategoriess !== null) {
                foreach ($this->collProductsToCategoriess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ordersLinessScheduledForDeletion !== null) {
                if (!$this->ordersLinessScheduledForDeletion->isEmpty()) {
                    foreach ($this->ordersLinessScheduledForDeletion as $ordersLines) {
                        // need to save related object because we set the relation to null
                        $ordersLines->save($con);
                    }
                    $this->ordersLinessScheduledForDeletion = null;
                }
            }

            if ($this->collOrdersLiness !== null) {
                foreach ($this->collOrdersLiness as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->relatedProductssRelatedByMasterScheduledForDeletion !== null) {
                if (!$this->relatedProductssRelatedByMasterScheduledForDeletion->isEmpty()) {
                    RelatedProductsQuery::create()
                        ->filterByPrimaryKeys($this->relatedProductssRelatedByMasterScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->relatedProductssRelatedByMasterScheduledForDeletion = null;
                }
            }

            if ($this->collRelatedProductssRelatedByMaster !== null) {
                foreach ($this->collRelatedProductssRelatedByMaster as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->relatedProductssRelatedBySkuScheduledForDeletion !== null) {
                if (!$this->relatedProductssRelatedBySkuScheduledForDeletion->isEmpty()) {
                    RelatedProductsQuery::create()
                        ->filterByPrimaryKeys($this->relatedProductssRelatedBySkuScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->relatedProductssRelatedBySkuScheduledForDeletion = null;
                }
            }

            if ($this->collRelatedProductssRelatedBySku !== null) {
                foreach ($this->collRelatedProductssRelatedBySku as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion !== null) {
                if (!$this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion->isEmpty()) {
                    SearchProductsTagsQuery::create()
                        ->filterByPrimaryKeys($this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion = null;
                }
            }

            if ($this->collSearchProductsTagssRelatedByMasterProductsId !== null) {
                foreach ($this->collSearchProductsTagssRelatedByMasterProductsId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->searchProductsTagssRelatedByProductsIdScheduledForDeletion !== null) {
                if (!$this->searchProductsTagssRelatedByProductsIdScheduledForDeletion->isEmpty()) {
                    SearchProductsTagsQuery::create()
                        ->filterByPrimaryKeys($this->searchProductsTagssRelatedByProductsIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->searchProductsTagssRelatedByProductsIdScheduledForDeletion = null;
                }
            }

            if ($this->collSearchProductsTagssRelatedByProductsId !== null) {
                foreach ($this->collSearchProductsTagssRelatedByProductsId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsI18nsScheduledForDeletion !== null) {
                if (!$this->productsI18nsScheduledForDeletion->isEmpty()) {
                    ProductsI18nQuery::create()
                        ->filterByPrimaryKeys($this->productsI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productsI18nsScheduledForDeletion = null;
                }
            }

            if ($this->collProductsI18ns !== null) {
                foreach ($this->collProductsI18ns as $referrerFK) {
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

        $this->modifiedColumns[] = ProductsPeer::ID;

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProductsPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(ProductsPeer::SKU)) {
            $modifiedColumns[':p' . $index++]  = '`sku`';
        }
        if ($this->isColumnModified(ProductsPeer::MASTER)) {
            $modifiedColumns[':p' . $index++]  = '`master`';
        }
        if ($this->isColumnModified(ProductsPeer::SIZE)) {
            $modifiedColumns[':p' . $index++]  = '`size`';
        }
        if ($this->isColumnModified(ProductsPeer::COLOR)) {
            $modifiedColumns[':p' . $index++]  = '`color`';
        }
        if ($this->isColumnModified(ProductsPeer::UNIT)) {
            $modifiedColumns[':p' . $index++]  = '`unit`';
        }
        if ($this->isColumnModified(ProductsPeer::WASHING)) {
            $modifiedColumns[':p' . $index++]  = '`washing`';
        }
        if ($this->isColumnModified(ProductsPeer::HAS_VIDEO)) {
            $modifiedColumns[':p' . $index++]  = '`has_video`';
        }
        if ($this->isColumnModified(ProductsPeer::IS_OUT_OF_STOCK)) {
            $modifiedColumns[':p' . $index++]  = '`is_out_of_stock`';
        }
        if ($this->isColumnModified(ProductsPeer::IS_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = '`is_active`';
        }
        if ($this->isColumnModified(ProductsPeer::IS_VOUCHER)) {
            $modifiedColumns[':p' . $index++]  = '`is_voucher`';
        }
        if ($this->isColumnModified(ProductsPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(ProductsPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `products` (%s) VALUES (%s)',
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
                    case '`sku`':
                        $stmt->bindValue($identifier, $this->sku, PDO::PARAM_STR);
                        break;
                    case '`master`':
                        $stmt->bindValue($identifier, $this->master, PDO::PARAM_STR);
                        break;
                    case '`size`':
                        $stmt->bindValue($identifier, $this->size, PDO::PARAM_STR);
                        break;
                    case '`color`':
                        $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
                        break;
                    case '`unit`':
                        $stmt->bindValue($identifier, $this->unit, PDO::PARAM_STR);
                        break;
                    case '`washing`':
                        $stmt->bindValue($identifier, $this->washing, PDO::PARAM_INT);
                        break;
                    case '`has_video`':
                        $stmt->bindValue($identifier, (int) $this->has_video, PDO::PARAM_INT);
                        break;
                    case '`is_out_of_stock`':
                        $stmt->bindValue($identifier, (int) $this->is_out_of_stock, PDO::PARAM_INT);
                        break;
                    case '`is_active`':
                        $stmt->bindValue($identifier, (int) $this->is_active, PDO::PARAM_INT);
                        break;
                    case '`is_voucher`':
                        $stmt->bindValue($identifier, (int) $this->is_voucher, PDO::PARAM_INT);
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

            if ($this->aProductsRelatedByMaster !== null) {
                if (!$this->aProductsRelatedByMaster->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProductsRelatedByMaster->getValidationFailures());
                }
            }

            if ($this->aProductsWashingInstructions !== null) {
                if (!$this->aProductsWashingInstructions->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProductsWashingInstructions->getValidationFailures());
                }
            }


            if (($retval = ProductsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collMannequinImagess !== null) {
                    foreach ($this->collMannequinImagess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductssRelatedBySku !== null) {
                    foreach ($this->collProductssRelatedBySku as $referrerFK) {
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

                if ($this->collProductsImagess !== null) {
                    foreach ($this->collProductsImagess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
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

                if ($this->collProductsQuantityDiscounts !== null) {
                    foreach ($this->collProductsQuantityDiscounts as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductsStocks !== null) {
                    foreach ($this->collProductsStocks as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductsToCategoriess !== null) {
                    foreach ($this->collProductsToCategoriess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collOrdersLiness !== null) {
                    foreach ($this->collOrdersLiness as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collRelatedProductssRelatedByMaster !== null) {
                    foreach ($this->collRelatedProductssRelatedByMaster as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collRelatedProductssRelatedBySku !== null) {
                    foreach ($this->collRelatedProductssRelatedBySku as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collSearchProductsTagssRelatedByMasterProductsId !== null) {
                    foreach ($this->collSearchProductsTagssRelatedByMasterProductsId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collSearchProductsTagssRelatedByProductsId !== null) {
                    foreach ($this->collSearchProductsTagssRelatedByProductsId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProductsI18ns !== null) {
                    foreach ($this->collProductsI18ns as $referrerFK) {
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
        $pos = ProductsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSku();
                break;
            case 2:
                return $this->getMaster();
                break;
            case 3:
                return $this->getSize();
                break;
            case 4:
                return $this->getColor();
                break;
            case 5:
                return $this->getUnit();
                break;
            case 6:
                return $this->getWashing();
                break;
            case 7:
                return $this->getHasVideo();
                break;
            case 8:
                return $this->getIsOutOfStock();
                break;
            case 9:
                return $this->getIsActive();
                break;
            case 10:
                return $this->getIsVoucher();
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
        if (isset($alreadyDumpedObjects['Products'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Products'][$this->getPrimaryKey()] = true;
        $keys = ProductsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getSku(),
            $keys[2] => $this->getMaster(),
            $keys[3] => $this->getSize(),
            $keys[4] => $this->getColor(),
            $keys[5] => $this->getUnit(),
            $keys[6] => $this->getWashing(),
            $keys[7] => $this->getHasVideo(),
            $keys[8] => $this->getIsOutOfStock(),
            $keys[9] => $this->getIsActive(),
            $keys[10] => $this->getIsVoucher(),
            $keys[11] => $this->getCreatedAt(),
            $keys[12] => $this->getUpdatedAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aProductsRelatedByMaster) {
                $result['ProductsRelatedByMaster'] = $this->aProductsRelatedByMaster->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProductsWashingInstructions) {
                $result['ProductsWashingInstructions'] = $this->aProductsWashingInstructions->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collMannequinImagess) {
                $result['MannequinImagess'] = $this->collMannequinImagess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductssRelatedBySku) {
                $result['ProductssRelatedBySku'] = $this->collProductssRelatedBySku->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsDomainsPricess) {
                $result['ProductsDomainsPricess'] = $this->collProductsDomainsPricess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsImagess) {
                $result['ProductsImagess'] = $this->collProductsImagess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsImagesCategoriesSorts) {
                $result['ProductsImagesCategoriesSorts'] = $this->collProductsImagesCategoriesSorts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsImagesProductReferencess) {
                $result['ProductsImagesProductReferencess'] = $this->collProductsImagesProductReferencess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsQuantityDiscounts) {
                $result['ProductsQuantityDiscounts'] = $this->collProductsQuantityDiscounts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsStocks) {
                $result['ProductsStocks'] = $this->collProductsStocks->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsToCategoriess) {
                $result['ProductsToCategoriess'] = $this->collProductsToCategoriess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrdersLiness) {
                $result['OrdersLiness'] = $this->collOrdersLiness->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRelatedProductssRelatedByMaster) {
                $result['RelatedProductssRelatedByMaster'] = $this->collRelatedProductssRelatedByMaster->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRelatedProductssRelatedBySku) {
                $result['RelatedProductssRelatedBySku'] = $this->collRelatedProductssRelatedBySku->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSearchProductsTagssRelatedByMasterProductsId) {
                $result['SearchProductsTagssRelatedByMasterProductsId'] = $this->collSearchProductsTagssRelatedByMasterProductsId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSearchProductsTagssRelatedByProductsId) {
                $result['SearchProductsTagssRelatedByProductsId'] = $this->collSearchProductsTagssRelatedByProductsId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsI18ns) {
                $result['ProductsI18ns'] = $this->collProductsI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ProductsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setSku($value);
                break;
            case 2:
                $this->setMaster($value);
                break;
            case 3:
                $this->setSize($value);
                break;
            case 4:
                $this->setColor($value);
                break;
            case 5:
                $this->setUnit($value);
                break;
            case 6:
                $this->setWashing($value);
                break;
            case 7:
                $this->setHasVideo($value);
                break;
            case 8:
                $this->setIsOutOfStock($value);
                break;
            case 9:
                $this->setIsActive($value);
                break;
            case 10:
                $this->setIsVoucher($value);
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
        $keys = ProductsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setSku($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setMaster($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSize($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setColor($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUnit($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setWashing($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setHasVideo($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setIsOutOfStock($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setIsActive($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setIsVoucher($arr[$keys[10]]);
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
        $criteria = new Criteria(ProductsPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProductsPeer::ID)) $criteria->add(ProductsPeer::ID, $this->id);
        if ($this->isColumnModified(ProductsPeer::SKU)) $criteria->add(ProductsPeer::SKU, $this->sku);
        if ($this->isColumnModified(ProductsPeer::MASTER)) $criteria->add(ProductsPeer::MASTER, $this->master);
        if ($this->isColumnModified(ProductsPeer::SIZE)) $criteria->add(ProductsPeer::SIZE, $this->size);
        if ($this->isColumnModified(ProductsPeer::COLOR)) $criteria->add(ProductsPeer::COLOR, $this->color);
        if ($this->isColumnModified(ProductsPeer::UNIT)) $criteria->add(ProductsPeer::UNIT, $this->unit);
        if ($this->isColumnModified(ProductsPeer::WASHING)) $criteria->add(ProductsPeer::WASHING, $this->washing);
        if ($this->isColumnModified(ProductsPeer::HAS_VIDEO)) $criteria->add(ProductsPeer::HAS_VIDEO, $this->has_video);
        if ($this->isColumnModified(ProductsPeer::IS_OUT_OF_STOCK)) $criteria->add(ProductsPeer::IS_OUT_OF_STOCK, $this->is_out_of_stock);
        if ($this->isColumnModified(ProductsPeer::IS_ACTIVE)) $criteria->add(ProductsPeer::IS_ACTIVE, $this->is_active);
        if ($this->isColumnModified(ProductsPeer::IS_VOUCHER)) $criteria->add(ProductsPeer::IS_VOUCHER, $this->is_voucher);
        if ($this->isColumnModified(ProductsPeer::CREATED_AT)) $criteria->add(ProductsPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(ProductsPeer::UPDATED_AT)) $criteria->add(ProductsPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(ProductsPeer::DATABASE_NAME);
        $criteria->add(ProductsPeer::ID, $this->id);

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
     * @param object $copyObj An object of Products (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSku($this->getSku());
        $copyObj->setMaster($this->getMaster());
        $copyObj->setSize($this->getSize());
        $copyObj->setColor($this->getColor());
        $copyObj->setUnit($this->getUnit());
        $copyObj->setWashing($this->getWashing());
        $copyObj->setHasVideo($this->getHasVideo());
        $copyObj->setIsOutOfStock($this->getIsOutOfStock());
        $copyObj->setIsActive($this->getIsActive());
        $copyObj->setIsVoucher($this->getIsVoucher());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getMannequinImagess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMannequinImages($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductssRelatedBySku() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsRelatedBySku($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsDomainsPricess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsDomainsPrices($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsImagess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsImages($relObj->copy($deepCopy));
                }
            }

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

            foreach ($this->getProductsQuantityDiscounts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsQuantityDiscount($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsStocks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsStock($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsToCategoriess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsToCategories($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrdersLiness() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrdersLines($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRelatedProductssRelatedByMaster() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRelatedProductsRelatedByMaster($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRelatedProductssRelatedBySku() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRelatedProductsRelatedBySku($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSearchProductsTagssRelatedByMasterProductsId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSearchProductsTagsRelatedByMasterProductsId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSearchProductsTagssRelatedByProductsId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSearchProductsTagsRelatedByProductsId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductsI18n($relObj->copy($deepCopy));
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
     * @return Products Clone of current object.
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
     * @return ProductsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProductsPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Products object.
     *
     * @param             Products $v
     * @return Products The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProductsRelatedByMaster(Products $v = null)
    {
        if ($v === null) {
            $this->setMaster(NULL);
        } else {
            $this->setMaster($v->getSku());
        }

        $this->aProductsRelatedByMaster = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Products object, it will not be re-added.
        if ($v !== null) {
            $v->addProductsRelatedBySku($this);
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
    public function getProductsRelatedByMaster(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aProductsRelatedByMaster === null && (($this->master !== "" && $this->master !== null)) && $doQuery) {
            $this->aProductsRelatedByMaster = ProductsQuery::create()
                ->filterByProductsRelatedBySku($this) // here
                ->findOne($con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProductsRelatedByMaster->addProductssRelatedBySku($this);
             */
        }

        return $this->aProductsRelatedByMaster;
    }

    /**
     * Declares an association between this object and a ProductsWashingInstructions object.
     *
     * @param             ProductsWashingInstructions $v
     * @return Products The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProductsWashingInstructions(ProductsWashingInstructions $v = null)
    {
        if ($v === null) {
            $this->setWashing(NULL);
        } else {
            $this->setWashing($v->getCode());
        }

        $this->aProductsWashingInstructions = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ProductsWashingInstructions object, it will not be re-added.
        if ($v !== null) {
            $v->addProducts($this);
        }


        return $this;
    }


    /**
     * Get the associated ProductsWashingInstructions object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return ProductsWashingInstructions The associated ProductsWashingInstructions object.
     * @throws PropelException
     */
    public function getProductsWashingInstructions(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aProductsWashingInstructions === null && ($this->washing !== null) && $doQuery) {
            $this->aProductsWashingInstructions = ProductsWashingInstructionsQuery::create()
                ->filterByProducts($this) // here
                ->findOne($con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProductsWashingInstructions->addProductss($this);
             */
        }

        return $this->aProductsWashingInstructions;
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
        if ('MannequinImages' == $relationName) {
            $this->initMannequinImagess();
        }
        if ('ProductsRelatedBySku' == $relationName) {
            $this->initProductssRelatedBySku();
        }
        if ('ProductsDomainsPrices' == $relationName) {
            $this->initProductsDomainsPricess();
        }
        if ('ProductsImages' == $relationName) {
            $this->initProductsImagess();
        }
        if ('ProductsImagesCategoriesSort' == $relationName) {
            $this->initProductsImagesCategoriesSorts();
        }
        if ('ProductsImagesProductReferences' == $relationName) {
            $this->initProductsImagesProductReferencess();
        }
        if ('ProductsQuantityDiscount' == $relationName) {
            $this->initProductsQuantityDiscounts();
        }
        if ('ProductsStock' == $relationName) {
            $this->initProductsStocks();
        }
        if ('ProductsToCategories' == $relationName) {
            $this->initProductsToCategoriess();
        }
        if ('OrdersLines' == $relationName) {
            $this->initOrdersLiness();
        }
        if ('RelatedProductsRelatedByMaster' == $relationName) {
            $this->initRelatedProductssRelatedByMaster();
        }
        if ('RelatedProductsRelatedBySku' == $relationName) {
            $this->initRelatedProductssRelatedBySku();
        }
        if ('SearchProductsTagsRelatedByMasterProductsId' == $relationName) {
            $this->initSearchProductsTagssRelatedByMasterProductsId();
        }
        if ('SearchProductsTagsRelatedByProductsId' == $relationName) {
            $this->initSearchProductsTagssRelatedByProductsId();
        }
        if ('ProductsI18n' == $relationName) {
            $this->initProductsI18ns();
        }
    }

    /**
     * Clears out the collMannequinImagess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addMannequinImagess()
     */
    public function clearMannequinImagess()
    {
        $this->collMannequinImagess = null; // important to set this to null since that means it is uninitialized
        $this->collMannequinImagessPartial = null;

        return $this;
    }

    /**
     * reset is the collMannequinImagess collection loaded partially
     *
     * @return void
     */
    public function resetPartialMannequinImagess($v = true)
    {
        $this->collMannequinImagessPartial = $v;
    }

    /**
     * Initializes the collMannequinImagess collection.
     *
     * By default this just sets the collMannequinImagess collection to an empty array (like clearcollMannequinImagess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMannequinImagess($overrideExisting = true)
    {
        if (null !== $this->collMannequinImagess && !$overrideExisting) {
            return;
        }
        $this->collMannequinImagess = new PropelObjectCollection();
        $this->collMannequinImagess->setModel('MannequinImages');
    }

    /**
     * Gets an array of MannequinImages objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|MannequinImages[] List of MannequinImages objects
     * @throws PropelException
     */
    public function getMannequinImagess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collMannequinImagessPartial && !$this->isNew();
        if (null === $this->collMannequinImagess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMannequinImagess) {
                // return empty collection
                $this->initMannequinImagess();
            } else {
                $collMannequinImagess = MannequinImagesQuery::create(null, $criteria)
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collMannequinImagessPartial && count($collMannequinImagess)) {
                      $this->initMannequinImagess(false);

                      foreach($collMannequinImagess as $obj) {
                        if (false == $this->collMannequinImagess->contains($obj)) {
                          $this->collMannequinImagess->append($obj);
                        }
                      }

                      $this->collMannequinImagessPartial = true;
                    }

                    $collMannequinImagess->getInternalIterator()->rewind();
                    return $collMannequinImagess;
                }

                if($partial && $this->collMannequinImagess) {
                    foreach($this->collMannequinImagess as $obj) {
                        if($obj->isNew()) {
                            $collMannequinImagess[] = $obj;
                        }
                    }
                }

                $this->collMannequinImagess = $collMannequinImagess;
                $this->collMannequinImagessPartial = false;
            }
        }

        return $this->collMannequinImagess;
    }

    /**
     * Sets a collection of MannequinImages objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $mannequinImagess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setMannequinImagess(PropelCollection $mannequinImagess, PropelPDO $con = null)
    {
        $mannequinImagessToDelete = $this->getMannequinImagess(new Criteria(), $con)->diff($mannequinImagess);

        $this->mannequinImagessScheduledForDeletion = unserialize(serialize($mannequinImagessToDelete));

        foreach ($mannequinImagessToDelete as $mannequinImagesRemoved) {
            $mannequinImagesRemoved->setProducts(null);
        }

        $this->collMannequinImagess = null;
        foreach ($mannequinImagess as $mannequinImages) {
            $this->addMannequinImages($mannequinImages);
        }

        $this->collMannequinImagess = $mannequinImagess;
        $this->collMannequinImagessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MannequinImages objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related MannequinImages objects.
     * @throws PropelException
     */
    public function countMannequinImagess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collMannequinImagessPartial && !$this->isNew();
        if (null === $this->collMannequinImagess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMannequinImagess) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getMannequinImagess());
            }
            $query = MannequinImagesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collMannequinImagess);
    }

    /**
     * Method called to associate a MannequinImages object to this object
     * through the MannequinImages foreign key attribute.
     *
     * @param    MannequinImages $l MannequinImages
     * @return Products The current object (for fluent API support)
     */
    public function addMannequinImages(MannequinImages $l)
    {
        if ($this->collMannequinImagess === null) {
            $this->initMannequinImagess();
            $this->collMannequinImagessPartial = true;
        }
        if (!in_array($l, $this->collMannequinImagess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMannequinImages($l);
        }

        return $this;
    }

    /**
     * @param	MannequinImages $mannequinImages The mannequinImages object to add.
     */
    protected function doAddMannequinImages($mannequinImages)
    {
        $this->collMannequinImagess[]= $mannequinImages;
        $mannequinImages->setProducts($this);
    }

    /**
     * @param	MannequinImages $mannequinImages The mannequinImages object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeMannequinImages($mannequinImages)
    {
        if ($this->getMannequinImagess()->contains($mannequinImages)) {
            $this->collMannequinImagess->remove($this->collMannequinImagess->search($mannequinImages));
            if (null === $this->mannequinImagessScheduledForDeletion) {
                $this->mannequinImagessScheduledForDeletion = clone $this->collMannequinImagess;
                $this->mannequinImagessScheduledForDeletion->clear();
            }
            $this->mannequinImagessScheduledForDeletion[]= clone $mannequinImages;
            $mannequinImages->setProducts(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductssRelatedBySku collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addProductssRelatedBySku()
     */
    public function clearProductssRelatedBySku()
    {
        $this->collProductssRelatedBySku = null; // important to set this to null since that means it is uninitialized
        $this->collProductssRelatedBySkuPartial = null;

        return $this;
    }

    /**
     * reset is the collProductssRelatedBySku collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductssRelatedBySku($v = true)
    {
        $this->collProductssRelatedBySkuPartial = $v;
    }

    /**
     * Initializes the collProductssRelatedBySku collection.
     *
     * By default this just sets the collProductssRelatedBySku collection to an empty array (like clearcollProductssRelatedBySku());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductssRelatedBySku($overrideExisting = true)
    {
        if (null !== $this->collProductssRelatedBySku && !$overrideExisting) {
            return;
        }
        $this->collProductssRelatedBySku = new PropelObjectCollection();
        $this->collProductssRelatedBySku->setModel('Products');
    }

    /**
     * Gets an array of Products objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Products[] List of Products objects
     * @throws PropelException
     */
    public function getProductssRelatedBySku($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductssRelatedBySkuPartial && !$this->isNew();
        if (null === $this->collProductssRelatedBySku || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductssRelatedBySku) {
                // return empty collection
                $this->initProductssRelatedBySku();
            } else {
                $collProductssRelatedBySku = ProductsQuery::create(null, $criteria)
                    ->filterByProductsRelatedByMaster($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductssRelatedBySkuPartial && count($collProductssRelatedBySku)) {
                      $this->initProductssRelatedBySku(false);

                      foreach($collProductssRelatedBySku as $obj) {
                        if (false == $this->collProductssRelatedBySku->contains($obj)) {
                          $this->collProductssRelatedBySku->append($obj);
                        }
                      }

                      $this->collProductssRelatedBySkuPartial = true;
                    }

                    $collProductssRelatedBySku->getInternalIterator()->rewind();
                    return $collProductssRelatedBySku;
                }

                if($partial && $this->collProductssRelatedBySku) {
                    foreach($this->collProductssRelatedBySku as $obj) {
                        if($obj->isNew()) {
                            $collProductssRelatedBySku[] = $obj;
                        }
                    }
                }

                $this->collProductssRelatedBySku = $collProductssRelatedBySku;
                $this->collProductssRelatedBySkuPartial = false;
            }
        }

        return $this->collProductssRelatedBySku;
    }

    /**
     * Sets a collection of ProductsRelatedBySku objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productssRelatedBySku A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setProductssRelatedBySku(PropelCollection $productssRelatedBySku, PropelPDO $con = null)
    {
        $productssRelatedBySkuToDelete = $this->getProductssRelatedBySku(new Criteria(), $con)->diff($productssRelatedBySku);

        $this->productssRelatedBySkuScheduledForDeletion = unserialize(serialize($productssRelatedBySkuToDelete));

        foreach ($productssRelatedBySkuToDelete as $productsRelatedBySkuRemoved) {
            $productsRelatedBySkuRemoved->setProductsRelatedByMaster(null);
        }

        $this->collProductssRelatedBySku = null;
        foreach ($productssRelatedBySku as $productsRelatedBySku) {
            $this->addProductsRelatedBySku($productsRelatedBySku);
        }

        $this->collProductssRelatedBySku = $productssRelatedBySku;
        $this->collProductssRelatedBySkuPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Products objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Products objects.
     * @throws PropelException
     */
    public function countProductssRelatedBySku(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductssRelatedBySkuPartial && !$this->isNew();
        if (null === $this->collProductssRelatedBySku || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductssRelatedBySku) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProductssRelatedBySku());
            }
            $query = ProductsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsRelatedByMaster($this)
                ->count($con);
        }

        return count($this->collProductssRelatedBySku);
    }

    /**
     * Method called to associate a Products object to this object
     * through the Products foreign key attribute.
     *
     * @param    Products $l Products
     * @return Products The current object (for fluent API support)
     */
    public function addProductsRelatedBySku(Products $l)
    {
        if ($this->collProductssRelatedBySku === null) {
            $this->initProductssRelatedBySku();
            $this->collProductssRelatedBySkuPartial = true;
        }
        if (!in_array($l, $this->collProductssRelatedBySku->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsRelatedBySku($l);
        }

        return $this;
    }

    /**
     * @param	ProductsRelatedBySku $productsRelatedBySku The productsRelatedBySku object to add.
     */
    protected function doAddProductsRelatedBySku($productsRelatedBySku)
    {
        $this->collProductssRelatedBySku[]= $productsRelatedBySku;
        $productsRelatedBySku->setProductsRelatedByMaster($this);
    }

    /**
     * @param	ProductsRelatedBySku $productsRelatedBySku The productsRelatedBySku object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeProductsRelatedBySku($productsRelatedBySku)
    {
        if ($this->getProductssRelatedBySku()->contains($productsRelatedBySku)) {
            $this->collProductssRelatedBySku->remove($this->collProductssRelatedBySku->search($productsRelatedBySku));
            if (null === $this->productssRelatedBySkuScheduledForDeletion) {
                $this->productssRelatedBySkuScheduledForDeletion = clone $this->collProductssRelatedBySku;
                $this->productssRelatedBySkuScheduledForDeletion->clear();
            }
            $this->productssRelatedBySkuScheduledForDeletion[]= $productsRelatedBySku;
            $productsRelatedBySku->setProductsRelatedByMaster(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductssRelatedBySku from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Products[] List of Products objects
     */
    public function getProductssRelatedBySkuJoinProductsWashingInstructions($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsQuery::create(null, $criteria);
        $query->joinWith('ProductsWashingInstructions', $join_behavior);

        return $this->getProductssRelatedBySku($query, $con);
    }

    /**
     * Clears out the collProductsDomainsPricess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
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
     * If this Products is new, it will return
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
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsDomainsPricessPartial && count($collProductsDomainsPricess)) {
                      $this->initProductsDomainsPricess(false);

                      foreach($collProductsDomainsPricess as $obj) {
                        if (false == $this->collProductsDomainsPricess->contains($obj)) {
                          $this->collProductsDomainsPricess->append($obj);
                        }
                      }

                      $this->collProductsDomainsPricessPartial = true;
                    }

                    $collProductsDomainsPricess->getInternalIterator()->rewind();
                    return $collProductsDomainsPricess;
                }

                if($partial && $this->collProductsDomainsPricess) {
                    foreach($this->collProductsDomainsPricess as $obj) {
                        if($obj->isNew()) {
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
     * @return Products The current object (for fluent API support)
     */
    public function setProductsDomainsPricess(PropelCollection $productsDomainsPricess, PropelPDO $con = null)
    {
        $productsDomainsPricessToDelete = $this->getProductsDomainsPricess(new Criteria(), $con)->diff($productsDomainsPricess);

        $this->productsDomainsPricessScheduledForDeletion = unserialize(serialize($productsDomainsPricessToDelete));

        foreach ($productsDomainsPricessToDelete as $productsDomainsPricesRemoved) {
            $productsDomainsPricesRemoved->setProducts(null);
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

            if($partial && !$criteria) {
                return count($this->getProductsDomainsPricess());
            }
            $query = ProductsDomainsPricesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsDomainsPricess);
    }

    /**
     * Method called to associate a ProductsDomainsPrices object to this object
     * through the ProductsDomainsPrices foreign key attribute.
     *
     * @param    ProductsDomainsPrices $l ProductsDomainsPrices
     * @return Products The current object (for fluent API support)
     */
    public function addProductsDomainsPrices(ProductsDomainsPrices $l)
    {
        if ($this->collProductsDomainsPricess === null) {
            $this->initProductsDomainsPricess();
            $this->collProductsDomainsPricessPartial = true;
        }
        if (!in_array($l, $this->collProductsDomainsPricess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsDomainsPrices($l);
        }

        return $this;
    }

    /**
     * @param	ProductsDomainsPrices $productsDomainsPrices The productsDomainsPrices object to add.
     */
    protected function doAddProductsDomainsPrices($productsDomainsPrices)
    {
        $this->collProductsDomainsPricess[]= $productsDomainsPrices;
        $productsDomainsPrices->setProducts($this);
    }

    /**
     * @param	ProductsDomainsPrices $productsDomainsPrices The productsDomainsPrices object to remove.
     * @return Products The current object (for fluent API support)
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
            $productsDomainsPrices->setProducts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductsDomainsPricess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsDomainsPrices[] List of ProductsDomainsPrices objects
     */
    public function getProductsDomainsPricessJoinDomains($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsDomainsPricesQuery::create(null, $criteria);
        $query->joinWith('Domains', $join_behavior);

        return $this->getProductsDomainsPricess($query, $con);
    }

    /**
     * Clears out the collProductsImagess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addProductsImagess()
     */
    public function clearProductsImagess()
    {
        $this->collProductsImagess = null; // important to set this to null since that means it is uninitialized
        $this->collProductsImagessPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsImagess collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsImagess($v = true)
    {
        $this->collProductsImagessPartial = $v;
    }

    /**
     * Initializes the collProductsImagess collection.
     *
     * By default this just sets the collProductsImagess collection to an empty array (like clearcollProductsImagess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsImagess($overrideExisting = true)
    {
        if (null !== $this->collProductsImagess && !$overrideExisting) {
            return;
        }
        $this->collProductsImagess = new PropelObjectCollection();
        $this->collProductsImagess->setModel('ProductsImages');
    }

    /**
     * Gets an array of ProductsImages objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsImages[] List of ProductsImages objects
     * @throws PropelException
     */
    public function getProductsImagess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsImagessPartial && !$this->isNew();
        if (null === $this->collProductsImagess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsImagess) {
                // return empty collection
                $this->initProductsImagess();
            } else {
                $collProductsImagess = ProductsImagesQuery::create(null, $criteria)
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsImagessPartial && count($collProductsImagess)) {
                      $this->initProductsImagess(false);

                      foreach($collProductsImagess as $obj) {
                        if (false == $this->collProductsImagess->contains($obj)) {
                          $this->collProductsImagess->append($obj);
                        }
                      }

                      $this->collProductsImagessPartial = true;
                    }

                    $collProductsImagess->getInternalIterator()->rewind();
                    return $collProductsImagess;
                }

                if($partial && $this->collProductsImagess) {
                    foreach($this->collProductsImagess as $obj) {
                        if($obj->isNew()) {
                            $collProductsImagess[] = $obj;
                        }
                    }
                }

                $this->collProductsImagess = $collProductsImagess;
                $this->collProductsImagessPartial = false;
            }
        }

        return $this->collProductsImagess;
    }

    /**
     * Sets a collection of ProductsImages objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsImagess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setProductsImagess(PropelCollection $productsImagess, PropelPDO $con = null)
    {
        $productsImagessToDelete = $this->getProductsImagess(new Criteria(), $con)->diff($productsImagess);

        $this->productsImagessScheduledForDeletion = unserialize(serialize($productsImagessToDelete));

        foreach ($productsImagessToDelete as $productsImagesRemoved) {
            $productsImagesRemoved->setProducts(null);
        }

        $this->collProductsImagess = null;
        foreach ($productsImagess as $productsImages) {
            $this->addProductsImages($productsImages);
        }

        $this->collProductsImagess = $productsImagess;
        $this->collProductsImagessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsImages objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsImages objects.
     * @throws PropelException
     */
    public function countProductsImagess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsImagessPartial && !$this->isNew();
        if (null === $this->collProductsImagess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsImagess) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProductsImagess());
            }
            $query = ProductsImagesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsImagess);
    }

    /**
     * Method called to associate a ProductsImages object to this object
     * through the ProductsImages foreign key attribute.
     *
     * @param    ProductsImages $l ProductsImages
     * @return Products The current object (for fluent API support)
     */
    public function addProductsImages(ProductsImages $l)
    {
        if ($this->collProductsImagess === null) {
            $this->initProductsImagess();
            $this->collProductsImagessPartial = true;
        }
        if (!in_array($l, $this->collProductsImagess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsImages($l);
        }

        return $this;
    }

    /**
     * @param	ProductsImages $productsImages The productsImages object to add.
     */
    protected function doAddProductsImages($productsImages)
    {
        $this->collProductsImagess[]= $productsImages;
        $productsImages->setProducts($this);
    }

    /**
     * @param	ProductsImages $productsImages The productsImages object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeProductsImages($productsImages)
    {
        if ($this->getProductsImagess()->contains($productsImages)) {
            $this->collProductsImagess->remove($this->collProductsImagess->search($productsImages));
            if (null === $this->productsImagessScheduledForDeletion) {
                $this->productsImagessScheduledForDeletion = clone $this->collProductsImagess;
                $this->productsImagessScheduledForDeletion->clear();
            }
            $this->productsImagessScheduledForDeletion[]= clone $productsImages;
            $productsImages->setProducts(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductsImagesCategoriesSorts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
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
     * If this Products is new, it will return
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
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsImagesCategoriesSortsPartial && count($collProductsImagesCategoriesSorts)) {
                      $this->initProductsImagesCategoriesSorts(false);

                      foreach($collProductsImagesCategoriesSorts as $obj) {
                        if (false == $this->collProductsImagesCategoriesSorts->contains($obj)) {
                          $this->collProductsImagesCategoriesSorts->append($obj);
                        }
                      }

                      $this->collProductsImagesCategoriesSortsPartial = true;
                    }

                    $collProductsImagesCategoriesSorts->getInternalIterator()->rewind();
                    return $collProductsImagesCategoriesSorts;
                }

                if($partial && $this->collProductsImagesCategoriesSorts) {
                    foreach($this->collProductsImagesCategoriesSorts as $obj) {
                        if($obj->isNew()) {
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
     * @return Products The current object (for fluent API support)
     */
    public function setProductsImagesCategoriesSorts(PropelCollection $productsImagesCategoriesSorts, PropelPDO $con = null)
    {
        $productsImagesCategoriesSortsToDelete = $this->getProductsImagesCategoriesSorts(new Criteria(), $con)->diff($productsImagesCategoriesSorts);

        $this->productsImagesCategoriesSortsScheduledForDeletion = unserialize(serialize($productsImagesCategoriesSortsToDelete));

        foreach ($productsImagesCategoriesSortsToDelete as $productsImagesCategoriesSortRemoved) {
            $productsImagesCategoriesSortRemoved->setProducts(null);
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

            if($partial && !$criteria) {
                return count($this->getProductsImagesCategoriesSorts());
            }
            $query = ProductsImagesCategoriesSortQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsImagesCategoriesSorts);
    }

    /**
     * Method called to associate a ProductsImagesCategoriesSort object to this object
     * through the ProductsImagesCategoriesSort foreign key attribute.
     *
     * @param    ProductsImagesCategoriesSort $l ProductsImagesCategoriesSort
     * @return Products The current object (for fluent API support)
     */
    public function addProductsImagesCategoriesSort(ProductsImagesCategoriesSort $l)
    {
        if ($this->collProductsImagesCategoriesSorts === null) {
            $this->initProductsImagesCategoriesSorts();
            $this->collProductsImagesCategoriesSortsPartial = true;
        }
        if (!in_array($l, $this->collProductsImagesCategoriesSorts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsImagesCategoriesSort($l);
        }

        return $this;
    }

    /**
     * @param	ProductsImagesCategoriesSort $productsImagesCategoriesSort The productsImagesCategoriesSort object to add.
     */
    protected function doAddProductsImagesCategoriesSort($productsImagesCategoriesSort)
    {
        $this->collProductsImagesCategoriesSorts[]= $productsImagesCategoriesSort;
        $productsImagesCategoriesSort->setProducts($this);
    }

    /**
     * @param	ProductsImagesCategoriesSort $productsImagesCategoriesSort The productsImagesCategoriesSort object to remove.
     * @return Products The current object (for fluent API support)
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
            $productsImagesCategoriesSort->setProducts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductsImagesCategoriesSorts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsImagesCategoriesSort[] List of ProductsImagesCategoriesSort objects
     */
    public function getProductsImagesCategoriesSortsJoinProductsImages($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsImagesCategoriesSortQuery::create(null, $criteria);
        $query->joinWith('ProductsImages', $join_behavior);

        return $this->getProductsImagesCategoriesSorts($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductsImagesCategoriesSorts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
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
     * @return Products The current object (for fluent API support)
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
     * If this Products is new, it will return
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
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsImagesProductReferencessPartial && count($collProductsImagesProductReferencess)) {
                      $this->initProductsImagesProductReferencess(false);

                      foreach($collProductsImagesProductReferencess as $obj) {
                        if (false == $this->collProductsImagesProductReferencess->contains($obj)) {
                          $this->collProductsImagesProductReferencess->append($obj);
                        }
                      }

                      $this->collProductsImagesProductReferencessPartial = true;
                    }

                    $collProductsImagesProductReferencess->getInternalIterator()->rewind();
                    return $collProductsImagesProductReferencess;
                }

                if($partial && $this->collProductsImagesProductReferencess) {
                    foreach($this->collProductsImagesProductReferencess as $obj) {
                        if($obj->isNew()) {
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
     * @return Products The current object (for fluent API support)
     */
    public function setProductsImagesProductReferencess(PropelCollection $productsImagesProductReferencess, PropelPDO $con = null)
    {
        $productsImagesProductReferencessToDelete = $this->getProductsImagesProductReferencess(new Criteria(), $con)->diff($productsImagesProductReferencess);

        $this->productsImagesProductReferencessScheduledForDeletion = unserialize(serialize($productsImagesProductReferencessToDelete));

        foreach ($productsImagesProductReferencessToDelete as $productsImagesProductReferencesRemoved) {
            $productsImagesProductReferencesRemoved->setProducts(null);
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

            if($partial && !$criteria) {
                return count($this->getProductsImagesProductReferencess());
            }
            $query = ProductsImagesProductReferencesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsImagesProductReferencess);
    }

    /**
     * Method called to associate a ProductsImagesProductReferences object to this object
     * through the ProductsImagesProductReferences foreign key attribute.
     *
     * @param    ProductsImagesProductReferences $l ProductsImagesProductReferences
     * @return Products The current object (for fluent API support)
     */
    public function addProductsImagesProductReferences(ProductsImagesProductReferences $l)
    {
        if ($this->collProductsImagesProductReferencess === null) {
            $this->initProductsImagesProductReferencess();
            $this->collProductsImagesProductReferencessPartial = true;
        }
        if (!in_array($l, $this->collProductsImagesProductReferencess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsImagesProductReferences($l);
        }

        return $this;
    }

    /**
     * @param	ProductsImagesProductReferences $productsImagesProductReferences The productsImagesProductReferences object to add.
     */
    protected function doAddProductsImagesProductReferences($productsImagesProductReferences)
    {
        $this->collProductsImagesProductReferencess[]= $productsImagesProductReferences;
        $productsImagesProductReferences->setProducts($this);
    }

    /**
     * @param	ProductsImagesProductReferences $productsImagesProductReferences The productsImagesProductReferences object to remove.
     * @return Products The current object (for fluent API support)
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
            $productsImagesProductReferences->setProducts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductsImagesProductReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsImagesProductReferences[] List of ProductsImagesProductReferences objects
     */
    public function getProductsImagesProductReferencessJoinProductsImages($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsImagesProductReferencesQuery::create(null, $criteria);
        $query->joinWith('ProductsImages', $join_behavior);

        return $this->getProductsImagesProductReferencess($query, $con);
    }

    /**
     * Clears out the collProductsQuantityDiscounts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
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
     * If this Products is new, it will return
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
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsQuantityDiscountsPartial && count($collProductsQuantityDiscounts)) {
                      $this->initProductsQuantityDiscounts(false);

                      foreach($collProductsQuantityDiscounts as $obj) {
                        if (false == $this->collProductsQuantityDiscounts->contains($obj)) {
                          $this->collProductsQuantityDiscounts->append($obj);
                        }
                      }

                      $this->collProductsQuantityDiscountsPartial = true;
                    }

                    $collProductsQuantityDiscounts->getInternalIterator()->rewind();
                    return $collProductsQuantityDiscounts;
                }

                if($partial && $this->collProductsQuantityDiscounts) {
                    foreach($this->collProductsQuantityDiscounts as $obj) {
                        if($obj->isNew()) {
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
     * @return Products The current object (for fluent API support)
     */
    public function setProductsQuantityDiscounts(PropelCollection $productsQuantityDiscounts, PropelPDO $con = null)
    {
        $productsQuantityDiscountsToDelete = $this->getProductsQuantityDiscounts(new Criteria(), $con)->diff($productsQuantityDiscounts);

        $this->productsQuantityDiscountsScheduledForDeletion = unserialize(serialize($productsQuantityDiscountsToDelete));

        foreach ($productsQuantityDiscountsToDelete as $productsQuantityDiscountRemoved) {
            $productsQuantityDiscountRemoved->setProducts(null);
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

            if($partial && !$criteria) {
                return count($this->getProductsQuantityDiscounts());
            }
            $query = ProductsQuantityDiscountQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsQuantityDiscounts);
    }

    /**
     * Method called to associate a ProductsQuantityDiscount object to this object
     * through the ProductsQuantityDiscount foreign key attribute.
     *
     * @param    ProductsQuantityDiscount $l ProductsQuantityDiscount
     * @return Products The current object (for fluent API support)
     */
    public function addProductsQuantityDiscount(ProductsQuantityDiscount $l)
    {
        if ($this->collProductsQuantityDiscounts === null) {
            $this->initProductsQuantityDiscounts();
            $this->collProductsQuantityDiscountsPartial = true;
        }
        if (!in_array($l, $this->collProductsQuantityDiscounts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsQuantityDiscount($l);
        }

        return $this;
    }

    /**
     * @param	ProductsQuantityDiscount $productsQuantityDiscount The productsQuantityDiscount object to add.
     */
    protected function doAddProductsQuantityDiscount($productsQuantityDiscount)
    {
        $this->collProductsQuantityDiscounts[]= $productsQuantityDiscount;
        $productsQuantityDiscount->setProducts($this);
    }

    /**
     * @param	ProductsQuantityDiscount $productsQuantityDiscount The productsQuantityDiscount object to remove.
     * @return Products The current object (for fluent API support)
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
            $productsQuantityDiscount->setProducts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductsQuantityDiscounts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsQuantityDiscount[] List of ProductsQuantityDiscount objects
     */
    public function getProductsQuantityDiscountsJoinDomains($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsQuantityDiscountQuery::create(null, $criteria);
        $query->joinWith('Domains', $join_behavior);

        return $this->getProductsQuantityDiscounts($query, $con);
    }

    /**
     * Clears out the collProductsStocks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addProductsStocks()
     */
    public function clearProductsStocks()
    {
        $this->collProductsStocks = null; // important to set this to null since that means it is uninitialized
        $this->collProductsStocksPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsStocks collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsStocks($v = true)
    {
        $this->collProductsStocksPartial = $v;
    }

    /**
     * Initializes the collProductsStocks collection.
     *
     * By default this just sets the collProductsStocks collection to an empty array (like clearcollProductsStocks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsStocks($overrideExisting = true)
    {
        if (null !== $this->collProductsStocks && !$overrideExisting) {
            return;
        }
        $this->collProductsStocks = new PropelObjectCollection();
        $this->collProductsStocks->setModel('ProductsStock');
    }

    /**
     * Gets an array of ProductsStock objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsStock[] List of ProductsStock objects
     * @throws PropelException
     */
    public function getProductsStocks($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsStocksPartial && !$this->isNew();
        if (null === $this->collProductsStocks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsStocks) {
                // return empty collection
                $this->initProductsStocks();
            } else {
                $collProductsStocks = ProductsStockQuery::create(null, $criteria)
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsStocksPartial && count($collProductsStocks)) {
                      $this->initProductsStocks(false);

                      foreach($collProductsStocks as $obj) {
                        if (false == $this->collProductsStocks->contains($obj)) {
                          $this->collProductsStocks->append($obj);
                        }
                      }

                      $this->collProductsStocksPartial = true;
                    }

                    $collProductsStocks->getInternalIterator()->rewind();
                    return $collProductsStocks;
                }

                if($partial && $this->collProductsStocks) {
                    foreach($this->collProductsStocks as $obj) {
                        if($obj->isNew()) {
                            $collProductsStocks[] = $obj;
                        }
                    }
                }

                $this->collProductsStocks = $collProductsStocks;
                $this->collProductsStocksPartial = false;
            }
        }

        return $this->collProductsStocks;
    }

    /**
     * Sets a collection of ProductsStock objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsStocks A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setProductsStocks(PropelCollection $productsStocks, PropelPDO $con = null)
    {
        $productsStocksToDelete = $this->getProductsStocks(new Criteria(), $con)->diff($productsStocks);

        $this->productsStocksScheduledForDeletion = unserialize(serialize($productsStocksToDelete));

        foreach ($productsStocksToDelete as $productsStockRemoved) {
            $productsStockRemoved->setProducts(null);
        }

        $this->collProductsStocks = null;
        foreach ($productsStocks as $productsStock) {
            $this->addProductsStock($productsStock);
        }

        $this->collProductsStocks = $productsStocks;
        $this->collProductsStocksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsStock objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsStock objects.
     * @throws PropelException
     */
    public function countProductsStocks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsStocksPartial && !$this->isNew();
        if (null === $this->collProductsStocks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsStocks) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProductsStocks());
            }
            $query = ProductsStockQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsStocks);
    }

    /**
     * Method called to associate a ProductsStock object to this object
     * through the ProductsStock foreign key attribute.
     *
     * @param    ProductsStock $l ProductsStock
     * @return Products The current object (for fluent API support)
     */
    public function addProductsStock(ProductsStock $l)
    {
        if ($this->collProductsStocks === null) {
            $this->initProductsStocks();
            $this->collProductsStocksPartial = true;
        }
        if (!in_array($l, $this->collProductsStocks->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsStock($l);
        }

        return $this;
    }

    /**
     * @param	ProductsStock $productsStock The productsStock object to add.
     */
    protected function doAddProductsStock($productsStock)
    {
        $this->collProductsStocks[]= $productsStock;
        $productsStock->setProducts($this);
    }

    /**
     * @param	ProductsStock $productsStock The productsStock object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeProductsStock($productsStock)
    {
        if ($this->getProductsStocks()->contains($productsStock)) {
            $this->collProductsStocks->remove($this->collProductsStocks->search($productsStock));
            if (null === $this->productsStocksScheduledForDeletion) {
                $this->productsStocksScheduledForDeletion = clone $this->collProductsStocks;
                $this->productsStocksScheduledForDeletion->clear();
            }
            $this->productsStocksScheduledForDeletion[]= clone $productsStock;
            $productsStock->setProducts(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductsToCategoriess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addProductsToCategoriess()
     */
    public function clearProductsToCategoriess()
    {
        $this->collProductsToCategoriess = null; // important to set this to null since that means it is uninitialized
        $this->collProductsToCategoriessPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsToCategoriess collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsToCategoriess($v = true)
    {
        $this->collProductsToCategoriessPartial = $v;
    }

    /**
     * Initializes the collProductsToCategoriess collection.
     *
     * By default this just sets the collProductsToCategoriess collection to an empty array (like clearcollProductsToCategoriess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsToCategoriess($overrideExisting = true)
    {
        if (null !== $this->collProductsToCategoriess && !$overrideExisting) {
            return;
        }
        $this->collProductsToCategoriess = new PropelObjectCollection();
        $this->collProductsToCategoriess->setModel('ProductsToCategories');
    }

    /**
     * Gets an array of ProductsToCategories objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsToCategories[] List of ProductsToCategories objects
     * @throws PropelException
     */
    public function getProductsToCategoriess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsToCategoriessPartial && !$this->isNew();
        if (null === $this->collProductsToCategoriess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsToCategoriess) {
                // return empty collection
                $this->initProductsToCategoriess();
            } else {
                $collProductsToCategoriess = ProductsToCategoriesQuery::create(null, $criteria)
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsToCategoriessPartial && count($collProductsToCategoriess)) {
                      $this->initProductsToCategoriess(false);

                      foreach($collProductsToCategoriess as $obj) {
                        if (false == $this->collProductsToCategoriess->contains($obj)) {
                          $this->collProductsToCategoriess->append($obj);
                        }
                      }

                      $this->collProductsToCategoriessPartial = true;
                    }

                    $collProductsToCategoriess->getInternalIterator()->rewind();
                    return $collProductsToCategoriess;
                }

                if($partial && $this->collProductsToCategoriess) {
                    foreach($this->collProductsToCategoriess as $obj) {
                        if($obj->isNew()) {
                            $collProductsToCategoriess[] = $obj;
                        }
                    }
                }

                $this->collProductsToCategoriess = $collProductsToCategoriess;
                $this->collProductsToCategoriessPartial = false;
            }
        }

        return $this->collProductsToCategoriess;
    }

    /**
     * Sets a collection of ProductsToCategories objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsToCategoriess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setProductsToCategoriess(PropelCollection $productsToCategoriess, PropelPDO $con = null)
    {
        $productsToCategoriessToDelete = $this->getProductsToCategoriess(new Criteria(), $con)->diff($productsToCategoriess);

        $this->productsToCategoriessScheduledForDeletion = unserialize(serialize($productsToCategoriessToDelete));

        foreach ($productsToCategoriessToDelete as $productsToCategoriesRemoved) {
            $productsToCategoriesRemoved->setProducts(null);
        }

        $this->collProductsToCategoriess = null;
        foreach ($productsToCategoriess as $productsToCategories) {
            $this->addProductsToCategories($productsToCategories);
        }

        $this->collProductsToCategoriess = $productsToCategoriess;
        $this->collProductsToCategoriessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsToCategories objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsToCategories objects.
     * @throws PropelException
     */
    public function countProductsToCategoriess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsToCategoriessPartial && !$this->isNew();
        if (null === $this->collProductsToCategoriess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsToCategoriess) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProductsToCategoriess());
            }
            $query = ProductsToCategoriesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsToCategoriess);
    }

    /**
     * Method called to associate a ProductsToCategories object to this object
     * through the ProductsToCategories foreign key attribute.
     *
     * @param    ProductsToCategories $l ProductsToCategories
     * @return Products The current object (for fluent API support)
     */
    public function addProductsToCategories(ProductsToCategories $l)
    {
        if ($this->collProductsToCategoriess === null) {
            $this->initProductsToCategoriess();
            $this->collProductsToCategoriessPartial = true;
        }
        if (!in_array($l, $this->collProductsToCategoriess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsToCategories($l);
        }

        return $this;
    }

    /**
     * @param	ProductsToCategories $productsToCategories The productsToCategories object to add.
     */
    protected function doAddProductsToCategories($productsToCategories)
    {
        $this->collProductsToCategoriess[]= $productsToCategories;
        $productsToCategories->setProducts($this);
    }

    /**
     * @param	ProductsToCategories $productsToCategories The productsToCategories object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeProductsToCategories($productsToCategories)
    {
        if ($this->getProductsToCategoriess()->contains($productsToCategories)) {
            $this->collProductsToCategoriess->remove($this->collProductsToCategoriess->search($productsToCategories));
            if (null === $this->productsToCategoriessScheduledForDeletion) {
                $this->productsToCategoriessScheduledForDeletion = clone $this->collProductsToCategoriess;
                $this->productsToCategoriessScheduledForDeletion->clear();
            }
            $this->productsToCategoriessScheduledForDeletion[]= clone $productsToCategories;
            $productsToCategories->setProducts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related ProductsToCategoriess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProductsToCategories[] List of ProductsToCategories objects
     */
    public function getProductsToCategoriessJoinCategories($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProductsToCategoriesQuery::create(null, $criteria);
        $query->joinWith('Categories', $join_behavior);

        return $this->getProductsToCategoriess($query, $con);
    }

    /**
     * Clears out the collOrdersLiness collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addOrdersLiness()
     */
    public function clearOrdersLiness()
    {
        $this->collOrdersLiness = null; // important to set this to null since that means it is uninitialized
        $this->collOrdersLinessPartial = null;

        return $this;
    }

    /**
     * reset is the collOrdersLiness collection loaded partially
     *
     * @return void
     */
    public function resetPartialOrdersLiness($v = true)
    {
        $this->collOrdersLinessPartial = $v;
    }

    /**
     * Initializes the collOrdersLiness collection.
     *
     * By default this just sets the collOrdersLiness collection to an empty array (like clearcollOrdersLiness());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrdersLiness($overrideExisting = true)
    {
        if (null !== $this->collOrdersLiness && !$overrideExisting) {
            return;
        }
        $this->collOrdersLiness = new PropelObjectCollection();
        $this->collOrdersLiness->setModel('OrdersLines');
    }

    /**
     * Gets an array of OrdersLines objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|OrdersLines[] List of OrdersLines objects
     * @throws PropelException
     */
    public function getOrdersLiness($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collOrdersLinessPartial && !$this->isNew();
        if (null === $this->collOrdersLiness || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrdersLiness) {
                // return empty collection
                $this->initOrdersLiness();
            } else {
                $collOrdersLiness = OrdersLinesQuery::create(null, $criteria)
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collOrdersLinessPartial && count($collOrdersLiness)) {
                      $this->initOrdersLiness(false);

                      foreach($collOrdersLiness as $obj) {
                        if (false == $this->collOrdersLiness->contains($obj)) {
                          $this->collOrdersLiness->append($obj);
                        }
                      }

                      $this->collOrdersLinessPartial = true;
                    }

                    $collOrdersLiness->getInternalIterator()->rewind();
                    return $collOrdersLiness;
                }

                if($partial && $this->collOrdersLiness) {
                    foreach($this->collOrdersLiness as $obj) {
                        if($obj->isNew()) {
                            $collOrdersLiness[] = $obj;
                        }
                    }
                }

                $this->collOrdersLiness = $collOrdersLiness;
                $this->collOrdersLinessPartial = false;
            }
        }

        return $this->collOrdersLiness;
    }

    /**
     * Sets a collection of OrdersLines objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ordersLiness A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setOrdersLiness(PropelCollection $ordersLiness, PropelPDO $con = null)
    {
        $ordersLinessToDelete = $this->getOrdersLiness(new Criteria(), $con)->diff($ordersLiness);

        $this->ordersLinessScheduledForDeletion = unserialize(serialize($ordersLinessToDelete));

        foreach ($ordersLinessToDelete as $ordersLinesRemoved) {
            $ordersLinesRemoved->setProducts(null);
        }

        $this->collOrdersLiness = null;
        foreach ($ordersLiness as $ordersLines) {
            $this->addOrdersLines($ordersLines);
        }

        $this->collOrdersLiness = $ordersLiness;
        $this->collOrdersLinessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrdersLines objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related OrdersLines objects.
     * @throws PropelException
     */
    public function countOrdersLiness(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collOrdersLinessPartial && !$this->isNew();
        if (null === $this->collOrdersLiness || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrdersLiness) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getOrdersLiness());
            }
            $query = OrdersLinesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collOrdersLiness);
    }

    /**
     * Method called to associate a OrdersLines object to this object
     * through the OrdersLines foreign key attribute.
     *
     * @param    OrdersLines $l OrdersLines
     * @return Products The current object (for fluent API support)
     */
    public function addOrdersLines(OrdersLines $l)
    {
        if ($this->collOrdersLiness === null) {
            $this->initOrdersLiness();
            $this->collOrdersLinessPartial = true;
        }
        if (!in_array($l, $this->collOrdersLiness->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrdersLines($l);
        }

        return $this;
    }

    /**
     * @param	OrdersLines $ordersLines The ordersLines object to add.
     */
    protected function doAddOrdersLines($ordersLines)
    {
        $this->collOrdersLiness[]= $ordersLines;
        $ordersLines->setProducts($this);
    }

    /**
     * @param	OrdersLines $ordersLines The ordersLines object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeOrdersLines($ordersLines)
    {
        if ($this->getOrdersLiness()->contains($ordersLines)) {
            $this->collOrdersLiness->remove($this->collOrdersLiness->search($ordersLines));
            if (null === $this->ordersLinessScheduledForDeletion) {
                $this->ordersLinessScheduledForDeletion = clone $this->collOrdersLiness;
                $this->ordersLinessScheduledForDeletion->clear();
            }
            $this->ordersLinessScheduledForDeletion[]= $ordersLines;
            $ordersLines->setProducts(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Products is new, it will return
     * an empty collection; or if this Products has previously
     * been saved, it will retrieve related OrdersLiness from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Products.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|OrdersLines[] List of OrdersLines objects
     */
    public function getOrdersLinessJoinOrders($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = OrdersLinesQuery::create(null, $criteria);
        $query->joinWith('Orders', $join_behavior);

        return $this->getOrdersLiness($query, $con);
    }

    /**
     * Clears out the collRelatedProductssRelatedByMaster collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addRelatedProductssRelatedByMaster()
     */
    public function clearRelatedProductssRelatedByMaster()
    {
        $this->collRelatedProductssRelatedByMaster = null; // important to set this to null since that means it is uninitialized
        $this->collRelatedProductssRelatedByMasterPartial = null;

        return $this;
    }

    /**
     * reset is the collRelatedProductssRelatedByMaster collection loaded partially
     *
     * @return void
     */
    public function resetPartialRelatedProductssRelatedByMaster($v = true)
    {
        $this->collRelatedProductssRelatedByMasterPartial = $v;
    }

    /**
     * Initializes the collRelatedProductssRelatedByMaster collection.
     *
     * By default this just sets the collRelatedProductssRelatedByMaster collection to an empty array (like clearcollRelatedProductssRelatedByMaster());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRelatedProductssRelatedByMaster($overrideExisting = true)
    {
        if (null !== $this->collRelatedProductssRelatedByMaster && !$overrideExisting) {
            return;
        }
        $this->collRelatedProductssRelatedByMaster = new PropelObjectCollection();
        $this->collRelatedProductssRelatedByMaster->setModel('RelatedProducts');
    }

    /**
     * Gets an array of RelatedProducts objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|RelatedProducts[] List of RelatedProducts objects
     * @throws PropelException
     */
    public function getRelatedProductssRelatedByMaster($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collRelatedProductssRelatedByMasterPartial && !$this->isNew();
        if (null === $this->collRelatedProductssRelatedByMaster || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRelatedProductssRelatedByMaster) {
                // return empty collection
                $this->initRelatedProductssRelatedByMaster();
            } else {
                $collRelatedProductssRelatedByMaster = RelatedProductsQuery::create(null, $criteria)
                    ->filterByProductsRelatedByMaster($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collRelatedProductssRelatedByMasterPartial && count($collRelatedProductssRelatedByMaster)) {
                      $this->initRelatedProductssRelatedByMaster(false);

                      foreach($collRelatedProductssRelatedByMaster as $obj) {
                        if (false == $this->collRelatedProductssRelatedByMaster->contains($obj)) {
                          $this->collRelatedProductssRelatedByMaster->append($obj);
                        }
                      }

                      $this->collRelatedProductssRelatedByMasterPartial = true;
                    }

                    $collRelatedProductssRelatedByMaster->getInternalIterator()->rewind();
                    return $collRelatedProductssRelatedByMaster;
                }

                if($partial && $this->collRelatedProductssRelatedByMaster) {
                    foreach($this->collRelatedProductssRelatedByMaster as $obj) {
                        if($obj->isNew()) {
                            $collRelatedProductssRelatedByMaster[] = $obj;
                        }
                    }
                }

                $this->collRelatedProductssRelatedByMaster = $collRelatedProductssRelatedByMaster;
                $this->collRelatedProductssRelatedByMasterPartial = false;
            }
        }

        return $this->collRelatedProductssRelatedByMaster;
    }

    /**
     * Sets a collection of RelatedProductsRelatedByMaster objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $relatedProductssRelatedByMaster A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setRelatedProductssRelatedByMaster(PropelCollection $relatedProductssRelatedByMaster, PropelPDO $con = null)
    {
        $relatedProductssRelatedByMasterToDelete = $this->getRelatedProductssRelatedByMaster(new Criteria(), $con)->diff($relatedProductssRelatedByMaster);

        $this->relatedProductssRelatedByMasterScheduledForDeletion = unserialize(serialize($relatedProductssRelatedByMasterToDelete));

        foreach ($relatedProductssRelatedByMasterToDelete as $relatedProductsRelatedByMasterRemoved) {
            $relatedProductsRelatedByMasterRemoved->setProductsRelatedByMaster(null);
        }

        $this->collRelatedProductssRelatedByMaster = null;
        foreach ($relatedProductssRelatedByMaster as $relatedProductsRelatedByMaster) {
            $this->addRelatedProductsRelatedByMaster($relatedProductsRelatedByMaster);
        }

        $this->collRelatedProductssRelatedByMaster = $relatedProductssRelatedByMaster;
        $this->collRelatedProductssRelatedByMasterPartial = false;

        return $this;
    }

    /**
     * Returns the number of related RelatedProducts objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related RelatedProducts objects.
     * @throws PropelException
     */
    public function countRelatedProductssRelatedByMaster(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collRelatedProductssRelatedByMasterPartial && !$this->isNew();
        if (null === $this->collRelatedProductssRelatedByMaster || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRelatedProductssRelatedByMaster) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getRelatedProductssRelatedByMaster());
            }
            $query = RelatedProductsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsRelatedByMaster($this)
                ->count($con);
        }

        return count($this->collRelatedProductssRelatedByMaster);
    }

    /**
     * Method called to associate a RelatedProducts object to this object
     * through the RelatedProducts foreign key attribute.
     *
     * @param    RelatedProducts $l RelatedProducts
     * @return Products The current object (for fluent API support)
     */
    public function addRelatedProductsRelatedByMaster(RelatedProducts $l)
    {
        if ($this->collRelatedProductssRelatedByMaster === null) {
            $this->initRelatedProductssRelatedByMaster();
            $this->collRelatedProductssRelatedByMasterPartial = true;
        }
        if (!in_array($l, $this->collRelatedProductssRelatedByMaster->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddRelatedProductsRelatedByMaster($l);
        }

        return $this;
    }

    /**
     * @param	RelatedProductsRelatedByMaster $relatedProductsRelatedByMaster The relatedProductsRelatedByMaster object to add.
     */
    protected function doAddRelatedProductsRelatedByMaster($relatedProductsRelatedByMaster)
    {
        $this->collRelatedProductssRelatedByMaster[]= $relatedProductsRelatedByMaster;
        $relatedProductsRelatedByMaster->setProductsRelatedByMaster($this);
    }

    /**
     * @param	RelatedProductsRelatedByMaster $relatedProductsRelatedByMaster The relatedProductsRelatedByMaster object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeRelatedProductsRelatedByMaster($relatedProductsRelatedByMaster)
    {
        if ($this->getRelatedProductssRelatedByMaster()->contains($relatedProductsRelatedByMaster)) {
            $this->collRelatedProductssRelatedByMaster->remove($this->collRelatedProductssRelatedByMaster->search($relatedProductsRelatedByMaster));
            if (null === $this->relatedProductssRelatedByMasterScheduledForDeletion) {
                $this->relatedProductssRelatedByMasterScheduledForDeletion = clone $this->collRelatedProductssRelatedByMaster;
                $this->relatedProductssRelatedByMasterScheduledForDeletion->clear();
            }
            $this->relatedProductssRelatedByMasterScheduledForDeletion[]= clone $relatedProductsRelatedByMaster;
            $relatedProductsRelatedByMaster->setProductsRelatedByMaster(null);
        }

        return $this;
    }

    /**
     * Clears out the collRelatedProductssRelatedBySku collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addRelatedProductssRelatedBySku()
     */
    public function clearRelatedProductssRelatedBySku()
    {
        $this->collRelatedProductssRelatedBySku = null; // important to set this to null since that means it is uninitialized
        $this->collRelatedProductssRelatedBySkuPartial = null;

        return $this;
    }

    /**
     * reset is the collRelatedProductssRelatedBySku collection loaded partially
     *
     * @return void
     */
    public function resetPartialRelatedProductssRelatedBySku($v = true)
    {
        $this->collRelatedProductssRelatedBySkuPartial = $v;
    }

    /**
     * Initializes the collRelatedProductssRelatedBySku collection.
     *
     * By default this just sets the collRelatedProductssRelatedBySku collection to an empty array (like clearcollRelatedProductssRelatedBySku());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRelatedProductssRelatedBySku($overrideExisting = true)
    {
        if (null !== $this->collRelatedProductssRelatedBySku && !$overrideExisting) {
            return;
        }
        $this->collRelatedProductssRelatedBySku = new PropelObjectCollection();
        $this->collRelatedProductssRelatedBySku->setModel('RelatedProducts');
    }

    /**
     * Gets an array of RelatedProducts objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|RelatedProducts[] List of RelatedProducts objects
     * @throws PropelException
     */
    public function getRelatedProductssRelatedBySku($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collRelatedProductssRelatedBySkuPartial && !$this->isNew();
        if (null === $this->collRelatedProductssRelatedBySku || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRelatedProductssRelatedBySku) {
                // return empty collection
                $this->initRelatedProductssRelatedBySku();
            } else {
                $collRelatedProductssRelatedBySku = RelatedProductsQuery::create(null, $criteria)
                    ->filterByProductsRelatedBySku($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collRelatedProductssRelatedBySkuPartial && count($collRelatedProductssRelatedBySku)) {
                      $this->initRelatedProductssRelatedBySku(false);

                      foreach($collRelatedProductssRelatedBySku as $obj) {
                        if (false == $this->collRelatedProductssRelatedBySku->contains($obj)) {
                          $this->collRelatedProductssRelatedBySku->append($obj);
                        }
                      }

                      $this->collRelatedProductssRelatedBySkuPartial = true;
                    }

                    $collRelatedProductssRelatedBySku->getInternalIterator()->rewind();
                    return $collRelatedProductssRelatedBySku;
                }

                if($partial && $this->collRelatedProductssRelatedBySku) {
                    foreach($this->collRelatedProductssRelatedBySku as $obj) {
                        if($obj->isNew()) {
                            $collRelatedProductssRelatedBySku[] = $obj;
                        }
                    }
                }

                $this->collRelatedProductssRelatedBySku = $collRelatedProductssRelatedBySku;
                $this->collRelatedProductssRelatedBySkuPartial = false;
            }
        }

        return $this->collRelatedProductssRelatedBySku;
    }

    /**
     * Sets a collection of RelatedProductsRelatedBySku objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $relatedProductssRelatedBySku A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setRelatedProductssRelatedBySku(PropelCollection $relatedProductssRelatedBySku, PropelPDO $con = null)
    {
        $relatedProductssRelatedBySkuToDelete = $this->getRelatedProductssRelatedBySku(new Criteria(), $con)->diff($relatedProductssRelatedBySku);

        $this->relatedProductssRelatedBySkuScheduledForDeletion = unserialize(serialize($relatedProductssRelatedBySkuToDelete));

        foreach ($relatedProductssRelatedBySkuToDelete as $relatedProductsRelatedBySkuRemoved) {
            $relatedProductsRelatedBySkuRemoved->setProductsRelatedBySku(null);
        }

        $this->collRelatedProductssRelatedBySku = null;
        foreach ($relatedProductssRelatedBySku as $relatedProductsRelatedBySku) {
            $this->addRelatedProductsRelatedBySku($relatedProductsRelatedBySku);
        }

        $this->collRelatedProductssRelatedBySku = $relatedProductssRelatedBySku;
        $this->collRelatedProductssRelatedBySkuPartial = false;

        return $this;
    }

    /**
     * Returns the number of related RelatedProducts objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related RelatedProducts objects.
     * @throws PropelException
     */
    public function countRelatedProductssRelatedBySku(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collRelatedProductssRelatedBySkuPartial && !$this->isNew();
        if (null === $this->collRelatedProductssRelatedBySku || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRelatedProductssRelatedBySku) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getRelatedProductssRelatedBySku());
            }
            $query = RelatedProductsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsRelatedBySku($this)
                ->count($con);
        }

        return count($this->collRelatedProductssRelatedBySku);
    }

    /**
     * Method called to associate a RelatedProducts object to this object
     * through the RelatedProducts foreign key attribute.
     *
     * @param    RelatedProducts $l RelatedProducts
     * @return Products The current object (for fluent API support)
     */
    public function addRelatedProductsRelatedBySku(RelatedProducts $l)
    {
        if ($this->collRelatedProductssRelatedBySku === null) {
            $this->initRelatedProductssRelatedBySku();
            $this->collRelatedProductssRelatedBySkuPartial = true;
        }
        if (!in_array($l, $this->collRelatedProductssRelatedBySku->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddRelatedProductsRelatedBySku($l);
        }

        return $this;
    }

    /**
     * @param	RelatedProductsRelatedBySku $relatedProductsRelatedBySku The relatedProductsRelatedBySku object to add.
     */
    protected function doAddRelatedProductsRelatedBySku($relatedProductsRelatedBySku)
    {
        $this->collRelatedProductssRelatedBySku[]= $relatedProductsRelatedBySku;
        $relatedProductsRelatedBySku->setProductsRelatedBySku($this);
    }

    /**
     * @param	RelatedProductsRelatedBySku $relatedProductsRelatedBySku The relatedProductsRelatedBySku object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeRelatedProductsRelatedBySku($relatedProductsRelatedBySku)
    {
        if ($this->getRelatedProductssRelatedBySku()->contains($relatedProductsRelatedBySku)) {
            $this->collRelatedProductssRelatedBySku->remove($this->collRelatedProductssRelatedBySku->search($relatedProductsRelatedBySku));
            if (null === $this->relatedProductssRelatedBySkuScheduledForDeletion) {
                $this->relatedProductssRelatedBySkuScheduledForDeletion = clone $this->collRelatedProductssRelatedBySku;
                $this->relatedProductssRelatedBySkuScheduledForDeletion->clear();
            }
            $this->relatedProductssRelatedBySkuScheduledForDeletion[]= clone $relatedProductsRelatedBySku;
            $relatedProductsRelatedBySku->setProductsRelatedBySku(null);
        }

        return $this;
    }

    /**
     * Clears out the collSearchProductsTagssRelatedByMasterProductsId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addSearchProductsTagssRelatedByMasterProductsId()
     */
    public function clearSearchProductsTagssRelatedByMasterProductsId()
    {
        $this->collSearchProductsTagssRelatedByMasterProductsId = null; // important to set this to null since that means it is uninitialized
        $this->collSearchProductsTagssRelatedByMasterProductsIdPartial = null;

        return $this;
    }

    /**
     * reset is the collSearchProductsTagssRelatedByMasterProductsId collection loaded partially
     *
     * @return void
     */
    public function resetPartialSearchProductsTagssRelatedByMasterProductsId($v = true)
    {
        $this->collSearchProductsTagssRelatedByMasterProductsIdPartial = $v;
    }

    /**
     * Initializes the collSearchProductsTagssRelatedByMasterProductsId collection.
     *
     * By default this just sets the collSearchProductsTagssRelatedByMasterProductsId collection to an empty array (like clearcollSearchProductsTagssRelatedByMasterProductsId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSearchProductsTagssRelatedByMasterProductsId($overrideExisting = true)
    {
        if (null !== $this->collSearchProductsTagssRelatedByMasterProductsId && !$overrideExisting) {
            return;
        }
        $this->collSearchProductsTagssRelatedByMasterProductsId = new PropelObjectCollection();
        $this->collSearchProductsTagssRelatedByMasterProductsId->setModel('SearchProductsTags');
    }

    /**
     * Gets an array of SearchProductsTags objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|SearchProductsTags[] List of SearchProductsTags objects
     * @throws PropelException
     */
    public function getSearchProductsTagssRelatedByMasterProductsId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collSearchProductsTagssRelatedByMasterProductsIdPartial && !$this->isNew();
        if (null === $this->collSearchProductsTagssRelatedByMasterProductsId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSearchProductsTagssRelatedByMasterProductsId) {
                // return empty collection
                $this->initSearchProductsTagssRelatedByMasterProductsId();
            } else {
                $collSearchProductsTagssRelatedByMasterProductsId = SearchProductsTagsQuery::create(null, $criteria)
                    ->filterByProductsRelatedByMasterProductsId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collSearchProductsTagssRelatedByMasterProductsIdPartial && count($collSearchProductsTagssRelatedByMasterProductsId)) {
                      $this->initSearchProductsTagssRelatedByMasterProductsId(false);

                      foreach($collSearchProductsTagssRelatedByMasterProductsId as $obj) {
                        if (false == $this->collSearchProductsTagssRelatedByMasterProductsId->contains($obj)) {
                          $this->collSearchProductsTagssRelatedByMasterProductsId->append($obj);
                        }
                      }

                      $this->collSearchProductsTagssRelatedByMasterProductsIdPartial = true;
                    }

                    $collSearchProductsTagssRelatedByMasterProductsId->getInternalIterator()->rewind();
                    return $collSearchProductsTagssRelatedByMasterProductsId;
                }

                if($partial && $this->collSearchProductsTagssRelatedByMasterProductsId) {
                    foreach($this->collSearchProductsTagssRelatedByMasterProductsId as $obj) {
                        if($obj->isNew()) {
                            $collSearchProductsTagssRelatedByMasterProductsId[] = $obj;
                        }
                    }
                }

                $this->collSearchProductsTagssRelatedByMasterProductsId = $collSearchProductsTagssRelatedByMasterProductsId;
                $this->collSearchProductsTagssRelatedByMasterProductsIdPartial = false;
            }
        }

        return $this->collSearchProductsTagssRelatedByMasterProductsId;
    }

    /**
     * Sets a collection of SearchProductsTagsRelatedByMasterProductsId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $searchProductsTagssRelatedByMasterProductsId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setSearchProductsTagssRelatedByMasterProductsId(PropelCollection $searchProductsTagssRelatedByMasterProductsId, PropelPDO $con = null)
    {
        $searchProductsTagssRelatedByMasterProductsIdToDelete = $this->getSearchProductsTagssRelatedByMasterProductsId(new Criteria(), $con)->diff($searchProductsTagssRelatedByMasterProductsId);

        $this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion = unserialize(serialize($searchProductsTagssRelatedByMasterProductsIdToDelete));

        foreach ($searchProductsTagssRelatedByMasterProductsIdToDelete as $searchProductsTagsRelatedByMasterProductsIdRemoved) {
            $searchProductsTagsRelatedByMasterProductsIdRemoved->setProductsRelatedByMasterProductsId(null);
        }

        $this->collSearchProductsTagssRelatedByMasterProductsId = null;
        foreach ($searchProductsTagssRelatedByMasterProductsId as $searchProductsTagsRelatedByMasterProductsId) {
            $this->addSearchProductsTagsRelatedByMasterProductsId($searchProductsTagsRelatedByMasterProductsId);
        }

        $this->collSearchProductsTagssRelatedByMasterProductsId = $searchProductsTagssRelatedByMasterProductsId;
        $this->collSearchProductsTagssRelatedByMasterProductsIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SearchProductsTags objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related SearchProductsTags objects.
     * @throws PropelException
     */
    public function countSearchProductsTagssRelatedByMasterProductsId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collSearchProductsTagssRelatedByMasterProductsIdPartial && !$this->isNew();
        if (null === $this->collSearchProductsTagssRelatedByMasterProductsId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSearchProductsTagssRelatedByMasterProductsId) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getSearchProductsTagssRelatedByMasterProductsId());
            }
            $query = SearchProductsTagsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsRelatedByMasterProductsId($this)
                ->count($con);
        }

        return count($this->collSearchProductsTagssRelatedByMasterProductsId);
    }

    /**
     * Method called to associate a SearchProductsTags object to this object
     * through the SearchProductsTags foreign key attribute.
     *
     * @param    SearchProductsTags $l SearchProductsTags
     * @return Products The current object (for fluent API support)
     */
    public function addSearchProductsTagsRelatedByMasterProductsId(SearchProductsTags $l)
    {
        if ($this->collSearchProductsTagssRelatedByMasterProductsId === null) {
            $this->initSearchProductsTagssRelatedByMasterProductsId();
            $this->collSearchProductsTagssRelatedByMasterProductsIdPartial = true;
        }
        if (!in_array($l, $this->collSearchProductsTagssRelatedByMasterProductsId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSearchProductsTagsRelatedByMasterProductsId($l);
        }

        return $this;
    }

    /**
     * @param	SearchProductsTagsRelatedByMasterProductsId $searchProductsTagsRelatedByMasterProductsId The searchProductsTagsRelatedByMasterProductsId object to add.
     */
    protected function doAddSearchProductsTagsRelatedByMasterProductsId($searchProductsTagsRelatedByMasterProductsId)
    {
        $this->collSearchProductsTagssRelatedByMasterProductsId[]= $searchProductsTagsRelatedByMasterProductsId;
        $searchProductsTagsRelatedByMasterProductsId->setProductsRelatedByMasterProductsId($this);
    }

    /**
     * @param	SearchProductsTagsRelatedByMasterProductsId $searchProductsTagsRelatedByMasterProductsId The searchProductsTagsRelatedByMasterProductsId object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeSearchProductsTagsRelatedByMasterProductsId($searchProductsTagsRelatedByMasterProductsId)
    {
        if ($this->getSearchProductsTagssRelatedByMasterProductsId()->contains($searchProductsTagsRelatedByMasterProductsId)) {
            $this->collSearchProductsTagssRelatedByMasterProductsId->remove($this->collSearchProductsTagssRelatedByMasterProductsId->search($searchProductsTagsRelatedByMasterProductsId));
            if (null === $this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion) {
                $this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion = clone $this->collSearchProductsTagssRelatedByMasterProductsId;
                $this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion->clear();
            }
            $this->searchProductsTagssRelatedByMasterProductsIdScheduledForDeletion[]= clone $searchProductsTagsRelatedByMasterProductsId;
            $searchProductsTagsRelatedByMasterProductsId->setProductsRelatedByMasterProductsId(null);
        }

        return $this;
    }

    /**
     * Clears out the collSearchProductsTagssRelatedByProductsId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addSearchProductsTagssRelatedByProductsId()
     */
    public function clearSearchProductsTagssRelatedByProductsId()
    {
        $this->collSearchProductsTagssRelatedByProductsId = null; // important to set this to null since that means it is uninitialized
        $this->collSearchProductsTagssRelatedByProductsIdPartial = null;

        return $this;
    }

    /**
     * reset is the collSearchProductsTagssRelatedByProductsId collection loaded partially
     *
     * @return void
     */
    public function resetPartialSearchProductsTagssRelatedByProductsId($v = true)
    {
        $this->collSearchProductsTagssRelatedByProductsIdPartial = $v;
    }

    /**
     * Initializes the collSearchProductsTagssRelatedByProductsId collection.
     *
     * By default this just sets the collSearchProductsTagssRelatedByProductsId collection to an empty array (like clearcollSearchProductsTagssRelatedByProductsId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSearchProductsTagssRelatedByProductsId($overrideExisting = true)
    {
        if (null !== $this->collSearchProductsTagssRelatedByProductsId && !$overrideExisting) {
            return;
        }
        $this->collSearchProductsTagssRelatedByProductsId = new PropelObjectCollection();
        $this->collSearchProductsTagssRelatedByProductsId->setModel('SearchProductsTags');
    }

    /**
     * Gets an array of SearchProductsTags objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|SearchProductsTags[] List of SearchProductsTags objects
     * @throws PropelException
     */
    public function getSearchProductsTagssRelatedByProductsId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collSearchProductsTagssRelatedByProductsIdPartial && !$this->isNew();
        if (null === $this->collSearchProductsTagssRelatedByProductsId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSearchProductsTagssRelatedByProductsId) {
                // return empty collection
                $this->initSearchProductsTagssRelatedByProductsId();
            } else {
                $collSearchProductsTagssRelatedByProductsId = SearchProductsTagsQuery::create(null, $criteria)
                    ->filterByProductsRelatedByProductsId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collSearchProductsTagssRelatedByProductsIdPartial && count($collSearchProductsTagssRelatedByProductsId)) {
                      $this->initSearchProductsTagssRelatedByProductsId(false);

                      foreach($collSearchProductsTagssRelatedByProductsId as $obj) {
                        if (false == $this->collSearchProductsTagssRelatedByProductsId->contains($obj)) {
                          $this->collSearchProductsTagssRelatedByProductsId->append($obj);
                        }
                      }

                      $this->collSearchProductsTagssRelatedByProductsIdPartial = true;
                    }

                    $collSearchProductsTagssRelatedByProductsId->getInternalIterator()->rewind();
                    return $collSearchProductsTagssRelatedByProductsId;
                }

                if($partial && $this->collSearchProductsTagssRelatedByProductsId) {
                    foreach($this->collSearchProductsTagssRelatedByProductsId as $obj) {
                        if($obj->isNew()) {
                            $collSearchProductsTagssRelatedByProductsId[] = $obj;
                        }
                    }
                }

                $this->collSearchProductsTagssRelatedByProductsId = $collSearchProductsTagssRelatedByProductsId;
                $this->collSearchProductsTagssRelatedByProductsIdPartial = false;
            }
        }

        return $this->collSearchProductsTagssRelatedByProductsId;
    }

    /**
     * Sets a collection of SearchProductsTagsRelatedByProductsId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $searchProductsTagssRelatedByProductsId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setSearchProductsTagssRelatedByProductsId(PropelCollection $searchProductsTagssRelatedByProductsId, PropelPDO $con = null)
    {
        $searchProductsTagssRelatedByProductsIdToDelete = $this->getSearchProductsTagssRelatedByProductsId(new Criteria(), $con)->diff($searchProductsTagssRelatedByProductsId);

        $this->searchProductsTagssRelatedByProductsIdScheduledForDeletion = unserialize(serialize($searchProductsTagssRelatedByProductsIdToDelete));

        foreach ($searchProductsTagssRelatedByProductsIdToDelete as $searchProductsTagsRelatedByProductsIdRemoved) {
            $searchProductsTagsRelatedByProductsIdRemoved->setProductsRelatedByProductsId(null);
        }

        $this->collSearchProductsTagssRelatedByProductsId = null;
        foreach ($searchProductsTagssRelatedByProductsId as $searchProductsTagsRelatedByProductsId) {
            $this->addSearchProductsTagsRelatedByProductsId($searchProductsTagsRelatedByProductsId);
        }

        $this->collSearchProductsTagssRelatedByProductsId = $searchProductsTagssRelatedByProductsId;
        $this->collSearchProductsTagssRelatedByProductsIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SearchProductsTags objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related SearchProductsTags objects.
     * @throws PropelException
     */
    public function countSearchProductsTagssRelatedByProductsId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collSearchProductsTagssRelatedByProductsIdPartial && !$this->isNew();
        if (null === $this->collSearchProductsTagssRelatedByProductsId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSearchProductsTagssRelatedByProductsId) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getSearchProductsTagssRelatedByProductsId());
            }
            $query = SearchProductsTagsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductsRelatedByProductsId($this)
                ->count($con);
        }

        return count($this->collSearchProductsTagssRelatedByProductsId);
    }

    /**
     * Method called to associate a SearchProductsTags object to this object
     * through the SearchProductsTags foreign key attribute.
     *
     * @param    SearchProductsTags $l SearchProductsTags
     * @return Products The current object (for fluent API support)
     */
    public function addSearchProductsTagsRelatedByProductsId(SearchProductsTags $l)
    {
        if ($this->collSearchProductsTagssRelatedByProductsId === null) {
            $this->initSearchProductsTagssRelatedByProductsId();
            $this->collSearchProductsTagssRelatedByProductsIdPartial = true;
        }
        if (!in_array($l, $this->collSearchProductsTagssRelatedByProductsId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSearchProductsTagsRelatedByProductsId($l);
        }

        return $this;
    }

    /**
     * @param	SearchProductsTagsRelatedByProductsId $searchProductsTagsRelatedByProductsId The searchProductsTagsRelatedByProductsId object to add.
     */
    protected function doAddSearchProductsTagsRelatedByProductsId($searchProductsTagsRelatedByProductsId)
    {
        $this->collSearchProductsTagssRelatedByProductsId[]= $searchProductsTagsRelatedByProductsId;
        $searchProductsTagsRelatedByProductsId->setProductsRelatedByProductsId($this);
    }

    /**
     * @param	SearchProductsTagsRelatedByProductsId $searchProductsTagsRelatedByProductsId The searchProductsTagsRelatedByProductsId object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeSearchProductsTagsRelatedByProductsId($searchProductsTagsRelatedByProductsId)
    {
        if ($this->getSearchProductsTagssRelatedByProductsId()->contains($searchProductsTagsRelatedByProductsId)) {
            $this->collSearchProductsTagssRelatedByProductsId->remove($this->collSearchProductsTagssRelatedByProductsId->search($searchProductsTagsRelatedByProductsId));
            if (null === $this->searchProductsTagssRelatedByProductsIdScheduledForDeletion) {
                $this->searchProductsTagssRelatedByProductsIdScheduledForDeletion = clone $this->collSearchProductsTagssRelatedByProductsId;
                $this->searchProductsTagssRelatedByProductsIdScheduledForDeletion->clear();
            }
            $this->searchProductsTagssRelatedByProductsIdScheduledForDeletion[]= clone $searchProductsTagsRelatedByProductsId;
            $searchProductsTagsRelatedByProductsId->setProductsRelatedByProductsId(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductsI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Products The current object (for fluent API support)
     * @see        addProductsI18ns()
     */
    public function clearProductsI18ns()
    {
        $this->collProductsI18ns = null; // important to set this to null since that means it is uninitialized
        $this->collProductsI18nsPartial = null;

        return $this;
    }

    /**
     * reset is the collProductsI18ns collection loaded partially
     *
     * @return void
     */
    public function resetPartialProductsI18ns($v = true)
    {
        $this->collProductsI18nsPartial = $v;
    }

    /**
     * Initializes the collProductsI18ns collection.
     *
     * By default this just sets the collProductsI18ns collection to an empty array (like clearcollProductsI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsI18ns($overrideExisting = true)
    {
        if (null !== $this->collProductsI18ns && !$overrideExisting) {
            return;
        }
        $this->collProductsI18ns = new PropelObjectCollection();
        $this->collProductsI18ns->setModel('ProductsI18n');
    }

    /**
     * Gets an array of ProductsI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Products is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProductsI18n[] List of ProductsI18n objects
     * @throws PropelException
     */
    public function getProductsI18ns($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProductsI18nsPartial && !$this->isNew();
        if (null === $this->collProductsI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsI18ns) {
                // return empty collection
                $this->initProductsI18ns();
            } else {
                $collProductsI18ns = ProductsI18nQuery::create(null, $criteria)
                    ->filterByProducts($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProductsI18nsPartial && count($collProductsI18ns)) {
                      $this->initProductsI18ns(false);

                      foreach($collProductsI18ns as $obj) {
                        if (false == $this->collProductsI18ns->contains($obj)) {
                          $this->collProductsI18ns->append($obj);
                        }
                      }

                      $this->collProductsI18nsPartial = true;
                    }

                    $collProductsI18ns->getInternalIterator()->rewind();
                    return $collProductsI18ns;
                }

                if($partial && $this->collProductsI18ns) {
                    foreach($this->collProductsI18ns as $obj) {
                        if($obj->isNew()) {
                            $collProductsI18ns[] = $obj;
                        }
                    }
                }

                $this->collProductsI18ns = $collProductsI18ns;
                $this->collProductsI18nsPartial = false;
            }
        }

        return $this->collProductsI18ns;
    }

    /**
     * Sets a collection of ProductsI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $productsI18ns A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Products The current object (for fluent API support)
     */
    public function setProductsI18ns(PropelCollection $productsI18ns, PropelPDO $con = null)
    {
        $productsI18nsToDelete = $this->getProductsI18ns(new Criteria(), $con)->diff($productsI18ns);

        $this->productsI18nsScheduledForDeletion = unserialize(serialize($productsI18nsToDelete));

        foreach ($productsI18nsToDelete as $productsI18nRemoved) {
            $productsI18nRemoved->setProducts(null);
        }

        $this->collProductsI18ns = null;
        foreach ($productsI18ns as $productsI18n) {
            $this->addProductsI18n($productsI18n);
        }

        $this->collProductsI18ns = $productsI18ns;
        $this->collProductsI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductsI18n objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProductsI18n objects.
     * @throws PropelException
     */
    public function countProductsI18ns(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProductsI18nsPartial && !$this->isNew();
        if (null === $this->collProductsI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsI18ns) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProductsI18ns());
            }
            $query = ProductsI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProducts($this)
                ->count($con);
        }

        return count($this->collProductsI18ns);
    }

    /**
     * Method called to associate a ProductsI18n object to this object
     * through the ProductsI18n foreign key attribute.
     *
     * @param    ProductsI18n $l ProductsI18n
     * @return Products The current object (for fluent API support)
     */
    public function addProductsI18n(ProductsI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collProductsI18ns === null) {
            $this->initProductsI18ns();
            $this->collProductsI18nsPartial = true;
        }
        if (!in_array($l, $this->collProductsI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductsI18n($l);
        }

        return $this;
    }

    /**
     * @param	ProductsI18n $productsI18n The productsI18n object to add.
     */
    protected function doAddProductsI18n($productsI18n)
    {
        $this->collProductsI18ns[]= $productsI18n;
        $productsI18n->setProducts($this);
    }

    /**
     * @param	ProductsI18n $productsI18n The productsI18n object to remove.
     * @return Products The current object (for fluent API support)
     */
    public function removeProductsI18n($productsI18n)
    {
        if ($this->getProductsI18ns()->contains($productsI18n)) {
            $this->collProductsI18ns->remove($this->collProductsI18ns->search($productsI18n));
            if (null === $this->productsI18nsScheduledForDeletion) {
                $this->productsI18nsScheduledForDeletion = clone $this->collProductsI18ns;
                $this->productsI18nsScheduledForDeletion->clear();
            }
            $this->productsI18nsScheduledForDeletion[]= clone $productsI18n;
            $productsI18n->setProducts(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->sku = null;
        $this->master = null;
        $this->size = null;
        $this->color = null;
        $this->unit = null;
        $this->washing = null;
        $this->has_video = null;
        $this->is_out_of_stock = null;
        $this->is_active = null;
        $this->is_voucher = null;
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
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collMannequinImagess) {
                foreach ($this->collMannequinImagess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductssRelatedBySku) {
                foreach ($this->collProductssRelatedBySku as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsDomainsPricess) {
                foreach ($this->collProductsDomainsPricess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsImagess) {
                foreach ($this->collProductsImagess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
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
            if ($this->collProductsQuantityDiscounts) {
                foreach ($this->collProductsQuantityDiscounts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsStocks) {
                foreach ($this->collProductsStocks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsToCategoriess) {
                foreach ($this->collProductsToCategoriess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrdersLiness) {
                foreach ($this->collOrdersLiness as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRelatedProductssRelatedByMaster) {
                foreach ($this->collRelatedProductssRelatedByMaster as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRelatedProductssRelatedBySku) {
                foreach ($this->collRelatedProductssRelatedBySku as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSearchProductsTagssRelatedByMasterProductsId) {
                foreach ($this->collSearchProductsTagssRelatedByMasterProductsId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSearchProductsTagssRelatedByProductsId) {
                foreach ($this->collSearchProductsTagssRelatedByProductsId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsI18ns) {
                foreach ($this->collProductsI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aProductsRelatedByMaster instanceof Persistent) {
              $this->aProductsRelatedByMaster->clearAllReferences($deep);
            }
            if ($this->aProductsWashingInstructions instanceof Persistent) {
              $this->aProductsWashingInstructions->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'da_DK';
        $this->currentTranslations = null;

        if ($this->collMannequinImagess instanceof PropelCollection) {
            $this->collMannequinImagess->clearIterator();
        }
        $this->collMannequinImagess = null;
        if ($this->collProductssRelatedBySku instanceof PropelCollection) {
            $this->collProductssRelatedBySku->clearIterator();
        }
        $this->collProductssRelatedBySku = null;
        if ($this->collProductsDomainsPricess instanceof PropelCollection) {
            $this->collProductsDomainsPricess->clearIterator();
        }
        $this->collProductsDomainsPricess = null;
        if ($this->collProductsImagess instanceof PropelCollection) {
            $this->collProductsImagess->clearIterator();
        }
        $this->collProductsImagess = null;
        if ($this->collProductsImagesCategoriesSorts instanceof PropelCollection) {
            $this->collProductsImagesCategoriesSorts->clearIterator();
        }
        $this->collProductsImagesCategoriesSorts = null;
        if ($this->collProductsImagesProductReferencess instanceof PropelCollection) {
            $this->collProductsImagesProductReferencess->clearIterator();
        }
        $this->collProductsImagesProductReferencess = null;
        if ($this->collProductsQuantityDiscounts instanceof PropelCollection) {
            $this->collProductsQuantityDiscounts->clearIterator();
        }
        $this->collProductsQuantityDiscounts = null;
        if ($this->collProductsStocks instanceof PropelCollection) {
            $this->collProductsStocks->clearIterator();
        }
        $this->collProductsStocks = null;
        if ($this->collProductsToCategoriess instanceof PropelCollection) {
            $this->collProductsToCategoriess->clearIterator();
        }
        $this->collProductsToCategoriess = null;
        if ($this->collOrdersLiness instanceof PropelCollection) {
            $this->collOrdersLiness->clearIterator();
        }
        $this->collOrdersLiness = null;
        if ($this->collRelatedProductssRelatedByMaster instanceof PropelCollection) {
            $this->collRelatedProductssRelatedByMaster->clearIterator();
        }
        $this->collRelatedProductssRelatedByMaster = null;
        if ($this->collRelatedProductssRelatedBySku instanceof PropelCollection) {
            $this->collRelatedProductssRelatedBySku->clearIterator();
        }
        $this->collRelatedProductssRelatedBySku = null;
        if ($this->collSearchProductsTagssRelatedByMasterProductsId instanceof PropelCollection) {
            $this->collSearchProductsTagssRelatedByMasterProductsId->clearIterator();
        }
        $this->collSearchProductsTagssRelatedByMasterProductsId = null;
        if ($this->collSearchProductsTagssRelatedByProductsId instanceof PropelCollection) {
            $this->collSearchProductsTagssRelatedByProductsId->clearIterator();
        }
        $this->collSearchProductsTagssRelatedByProductsId = null;
        if ($this->collProductsI18ns instanceof PropelCollection) {
            $this->collProductsI18ns->clearIterator();
        }
        $this->collProductsI18ns = null;
        $this->aProductsRelatedByMaster = null;
        $this->aProductsWashingInstructions = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProductsPeer::DEFAULT_STRING_FORMAT);
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
     * @return    Products The current object (for fluent API support)
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
     * @return ProductsI18n */
    public function getTranslation($locale = 'da_DK', PropelPDO $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collProductsI18ns) {
                foreach ($this->collProductsI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ProductsI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ProductsI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addProductsI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     PropelPDO $con an optional connection object
     *
     * @return    Products The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'da_DK', PropelPDO $con = null)
    {
        if (!$this->isNew()) {
            ProductsI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collProductsI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collProductsI18ns[$key]);
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
     * @return ProductsI18n */
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
         * @param string $v new value
         * @return ProductsI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

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
         * @param string $v new value
         * @return ProductsI18n The current object (for fluent API support)
         */
        public function setContent($v)
        {    $this->getCurrentTranslation()->setContent($v);

        return $this;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     Products The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = ProductsPeer::UPDATED_AT;

        return $this;
    }

}

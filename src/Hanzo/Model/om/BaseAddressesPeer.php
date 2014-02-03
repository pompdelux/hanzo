<?php

namespace Hanzo\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\map\AddressesTableMap;

abstract class BaseAddressesPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'addresses';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\Addresses';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\AddressesTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 18;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 18;

    /** the column name for the customers_id field */
    const CUSTOMERS_ID = 'addresses.customers_id';

    /** the column name for the type field */
    const TYPE = 'addresses.type';

    /** the column name for the title field */
    const TITLE = 'addresses.title';

    /** the column name for the first_name field */
    const FIRST_NAME = 'addresses.first_name';

    /** the column name for the last_name field */
    const LAST_NAME = 'addresses.last_name';

    /** the column name for the address_line_1 field */
    const ADDRESS_LINE_1 = 'addresses.address_line_1';

    /** the column name for the address_line_2 field */
    const ADDRESS_LINE_2 = 'addresses.address_line_2';

    /** the column name for the postal_code field */
    const POSTAL_CODE = 'addresses.postal_code';

    /** the column name for the city field */
    const CITY = 'addresses.city';

    /** the column name for the country field */
    const COUNTRY = 'addresses.country';

    /** the column name for the countries_id field */
    const COUNTRIES_ID = 'addresses.countries_id';

    /** the column name for the state_province field */
    const STATE_PROVINCE = 'addresses.state_province';

    /** the column name for the company_name field */
    const COMPANY_NAME = 'addresses.company_name';

    /** the column name for the external_address_id field */
    const EXTERNAL_ADDRESS_ID = 'addresses.external_address_id';

    /** the column name for the latitude field */
    const LATITUDE = 'addresses.latitude';

    /** the column name for the longitude field */
    const LONGITUDE = 'addresses.longitude';

    /** the column name for the created_at field */
    const CREATED_AT = 'addresses.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'addresses.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Addresses objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Addresses[]
     */
    public static $instances = array();


    // geocodable behavior
    /**
     * Kilometers unit
     */
    const KILOMETERS_UNIT = 1.609344;
    /**
     * Miles unit
     */
    const MILES_UNIT = 1.1515;
    /**
     * Nautical miles unit
     */
    const NAUTICAL_MILES_UNIT = 0.8684;

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. AddressesPeer::$fieldNames[AddressesPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('CustomersId', 'Type', 'Title', 'FirstName', 'LastName', 'AddressLine1', 'AddressLine2', 'PostalCode', 'City', 'Country', 'CountriesId', 'StateProvince', 'CompanyName', 'ExternalAddressId', 'Latitude', 'Longitude', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('customersId', 'type', 'title', 'firstName', 'lastName', 'addressLine1', 'addressLine2', 'postalCode', 'city', 'country', 'countriesId', 'stateProvince', 'companyName', 'externalAddressId', 'latitude', 'longitude', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (AddressesPeer::CUSTOMERS_ID, AddressesPeer::TYPE, AddressesPeer::TITLE, AddressesPeer::FIRST_NAME, AddressesPeer::LAST_NAME, AddressesPeer::ADDRESS_LINE_1, AddressesPeer::ADDRESS_LINE_2, AddressesPeer::POSTAL_CODE, AddressesPeer::CITY, AddressesPeer::COUNTRY, AddressesPeer::COUNTRIES_ID, AddressesPeer::STATE_PROVINCE, AddressesPeer::COMPANY_NAME, AddressesPeer::EXTERNAL_ADDRESS_ID, AddressesPeer::LATITUDE, AddressesPeer::LONGITUDE, AddressesPeer::CREATED_AT, AddressesPeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('CUSTOMERS_ID', 'TYPE', 'TITLE', 'FIRST_NAME', 'LAST_NAME', 'ADDRESS_LINE_1', 'ADDRESS_LINE_2', 'POSTAL_CODE', 'CITY', 'COUNTRY', 'COUNTRIES_ID', 'STATE_PROVINCE', 'COMPANY_NAME', 'EXTERNAL_ADDRESS_ID', 'LATITUDE', 'LONGITUDE', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('customers_id', 'type', 'title', 'first_name', 'last_name', 'address_line_1', 'address_line_2', 'postal_code', 'city', 'country', 'countries_id', 'state_province', 'company_name', 'external_address_id', 'latitude', 'longitude', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. AddressesPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('CustomersId' => 0, 'Type' => 1, 'Title' => 2, 'FirstName' => 3, 'LastName' => 4, 'AddressLine1' => 5, 'AddressLine2' => 6, 'PostalCode' => 7, 'City' => 8, 'Country' => 9, 'CountriesId' => 10, 'StateProvince' => 11, 'CompanyName' => 12, 'ExternalAddressId' => 13, 'Latitude' => 14, 'Longitude' => 15, 'CreatedAt' => 16, 'UpdatedAt' => 17, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('customersId' => 0, 'type' => 1, 'title' => 2, 'firstName' => 3, 'lastName' => 4, 'addressLine1' => 5, 'addressLine2' => 6, 'postalCode' => 7, 'city' => 8, 'country' => 9, 'countriesId' => 10, 'stateProvince' => 11, 'companyName' => 12, 'externalAddressId' => 13, 'latitude' => 14, 'longitude' => 15, 'createdAt' => 16, 'updatedAt' => 17, ),
        BasePeer::TYPE_COLNAME => array (AddressesPeer::CUSTOMERS_ID => 0, AddressesPeer::TYPE => 1, AddressesPeer::TITLE => 2, AddressesPeer::FIRST_NAME => 3, AddressesPeer::LAST_NAME => 4, AddressesPeer::ADDRESS_LINE_1 => 5, AddressesPeer::ADDRESS_LINE_2 => 6, AddressesPeer::POSTAL_CODE => 7, AddressesPeer::CITY => 8, AddressesPeer::COUNTRY => 9, AddressesPeer::COUNTRIES_ID => 10, AddressesPeer::STATE_PROVINCE => 11, AddressesPeer::COMPANY_NAME => 12, AddressesPeer::EXTERNAL_ADDRESS_ID => 13, AddressesPeer::LATITUDE => 14, AddressesPeer::LONGITUDE => 15, AddressesPeer::CREATED_AT => 16, AddressesPeer::UPDATED_AT => 17, ),
        BasePeer::TYPE_RAW_COLNAME => array ('CUSTOMERS_ID' => 0, 'TYPE' => 1, 'TITLE' => 2, 'FIRST_NAME' => 3, 'LAST_NAME' => 4, 'ADDRESS_LINE_1' => 5, 'ADDRESS_LINE_2' => 6, 'POSTAL_CODE' => 7, 'CITY' => 8, 'COUNTRY' => 9, 'COUNTRIES_ID' => 10, 'STATE_PROVINCE' => 11, 'COMPANY_NAME' => 12, 'EXTERNAL_ADDRESS_ID' => 13, 'LATITUDE' => 14, 'LONGITUDE' => 15, 'CREATED_AT' => 16, 'UPDATED_AT' => 17, ),
        BasePeer::TYPE_FIELDNAME => array ('customers_id' => 0, 'type' => 1, 'title' => 2, 'first_name' => 3, 'last_name' => 4, 'address_line_1' => 5, 'address_line_2' => 6, 'postal_code' => 7, 'city' => 8, 'country' => 9, 'countries_id' => 10, 'state_province' => 11, 'company_name' => 12, 'external_address_id' => 13, 'latitude' => 14, 'longitude' => 15, 'created_at' => 16, 'updated_at' => 17, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = AddressesPeer::getFieldNames($toType);
        $key = isset(AddressesPeer::$fieldKeys[$fromType][$name]) ? AddressesPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(AddressesPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, AddressesPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return AddressesPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. AddressesPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AddressesPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(AddressesPeer::CUSTOMERS_ID);
            $criteria->addSelectColumn(AddressesPeer::TYPE);
            $criteria->addSelectColumn(AddressesPeer::TITLE);
            $criteria->addSelectColumn(AddressesPeer::FIRST_NAME);
            $criteria->addSelectColumn(AddressesPeer::LAST_NAME);
            $criteria->addSelectColumn(AddressesPeer::ADDRESS_LINE_1);
            $criteria->addSelectColumn(AddressesPeer::ADDRESS_LINE_2);
            $criteria->addSelectColumn(AddressesPeer::POSTAL_CODE);
            $criteria->addSelectColumn(AddressesPeer::CITY);
            $criteria->addSelectColumn(AddressesPeer::COUNTRY);
            $criteria->addSelectColumn(AddressesPeer::COUNTRIES_ID);
            $criteria->addSelectColumn(AddressesPeer::STATE_PROVINCE);
            $criteria->addSelectColumn(AddressesPeer::COMPANY_NAME);
            $criteria->addSelectColumn(AddressesPeer::EXTERNAL_ADDRESS_ID);
            $criteria->addSelectColumn(AddressesPeer::LATITUDE);
            $criteria->addSelectColumn(AddressesPeer::LONGITUDE);
            $criteria->addSelectColumn(AddressesPeer::CREATED_AT);
            $criteria->addSelectColumn(AddressesPeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.customers_id');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.last_name');
            $criteria->addSelectColumn($alias . '.address_line_1');
            $criteria->addSelectColumn($alias . '.address_line_2');
            $criteria->addSelectColumn($alias . '.postal_code');
            $criteria->addSelectColumn($alias . '.city');
            $criteria->addSelectColumn($alias . '.country');
            $criteria->addSelectColumn($alias . '.countries_id');
            $criteria->addSelectColumn($alias . '.state_province');
            $criteria->addSelectColumn($alias . '.company_name');
            $criteria->addSelectColumn($alias . '.external_address_id');
            $criteria->addSelectColumn($alias . '.latitude');
            $criteria->addSelectColumn($alias . '.longitude');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AddressesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(AddressesPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Addresses
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AddressesPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return AddressesPeer::populateObjects(AddressesPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            AddressesPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Addresses $obj A Addresses object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = serialize(array((string) $obj->getCustomersId(), (string) $obj->getType()));
            } // if key === null
            AddressesPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Addresses object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Addresses) {
                $key = serialize(array((string) $value->getCustomersId(), (string) $value->getType()));
            } elseif (is_array($value) && count($value) === 2) {
                // assume we've been passed a primary key
                $key = serialize(array((string) $value[0], (string) $value[1]));
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Addresses object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(AddressesPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Addresses Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(AddressesPeer::$instances[$key])) {
                return AddressesPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (AddressesPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        AddressesPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to addresses
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null && $row[$startcol + 1] === null) {
            return null;
        }

        return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1]));
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return array((int) $row[$startcol], (string) $row[$startcol + 1]);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = AddressesPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = AddressesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = AddressesPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                AddressesPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Addresses object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = AddressesPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = AddressesPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + AddressesPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = AddressesPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            AddressesPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Customers table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCustomers(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AddressesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AddressesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Countries table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCountries(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AddressesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AddressesPeer::COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Addresses objects pre-filled with their Customers objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Addresses objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCustomers(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AddressesPeer::DATABASE_NAME);
        }

        AddressesPeer::addSelectColumns($criteria);
        $startcol = AddressesPeer::NUM_HYDRATE_COLUMNS;
        CustomersPeer::addSelectColumns($criteria);

        $criteria->addJoin(AddressesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AddressesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AddressesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AddressesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AddressesPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CustomersPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CustomersPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CustomersPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Addresses) to $obj2 (Customers)
                $obj2->addAddresses($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Addresses objects pre-filled with their Countries objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Addresses objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCountries(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AddressesPeer::DATABASE_NAME);
        }

        AddressesPeer::addSelectColumns($criteria);
        $startcol = AddressesPeer::NUM_HYDRATE_COLUMNS;
        CountriesPeer::addSelectColumns($criteria);

        $criteria->addJoin(AddressesPeer::COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AddressesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AddressesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AddressesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AddressesPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CountriesPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CountriesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CountriesPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Addresses) to $obj2 (Countries)
                $obj2->addAddresses($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AddressesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AddressesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(AddressesPeer::COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of Addresses objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Addresses objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AddressesPeer::DATABASE_NAME);
        }

        AddressesPeer::addSelectColumns($criteria);
        $startcol2 = AddressesPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AddressesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(AddressesPeer::COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AddressesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AddressesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AddressesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AddressesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Customers rows

            $key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CustomersPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CustomersPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CustomersPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Addresses) to the collection in $obj2 (Customers)
                $obj2->addAddresses($obj1);
            } // if joined row not null

            // Add objects for joined Countries rows

            $key3 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CountriesPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CountriesPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CountriesPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (Addresses) to the collection in $obj3 (Countries)
                $obj3->addAddresses($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Customers table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCustomers(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AddressesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AddressesPeer::COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Countries table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCountries(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AddressesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AddressesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Addresses objects pre-filled with all related objects except Customers.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Addresses objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCustomers(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AddressesPeer::DATABASE_NAME);
        }

        AddressesPeer::addSelectColumns($criteria);
        $startcol2 = AddressesPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AddressesPeer::COUNTRIES_ID, CountriesPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AddressesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AddressesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AddressesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AddressesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Countries rows

                $key2 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CountriesPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CountriesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CountriesPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Addresses) to the collection in $obj2 (Countries)
                $obj2->addAddresses($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Addresses objects pre-filled with all related objects except Countries.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Addresses objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCountries(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AddressesPeer::DATABASE_NAME);
        }

        AddressesPeer::addSelectColumns($criteria);
        $startcol2 = AddressesPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AddressesPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AddressesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AddressesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AddressesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AddressesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Customers rows

                $key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CustomersPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CustomersPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CustomersPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Addresses) to the collection in $obj2 (Customers)
                $obj2->addAddresses($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(AddressesPeer::DATABASE_NAME)->getTable(AddressesPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseAddressesPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseAddressesPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\AddressesTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return AddressesPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Addresses or Criteria object.
     *
     * @param      mixed $values Criteria or Addresses object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Addresses object
        }


        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Addresses or Criteria object.
     *
     * @param      mixed $values Criteria or Addresses object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(AddressesPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(AddressesPeer::CUSTOMERS_ID);
            $value = $criteria->remove(AddressesPeer::CUSTOMERS_ID);
            if ($value) {
                $selectCriteria->add(AddressesPeer::CUSTOMERS_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);
            }

            $comparison = $criteria->getComparison(AddressesPeer::TYPE);
            $value = $criteria->remove(AddressesPeer::TYPE);
            if ($value) {
                $selectCriteria->add(AddressesPeer::TYPE, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(AddressesPeer::TABLE_NAME);
            }

        } else { // $values is Addresses object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the addresses table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(AddressesPeer::TABLE_NAME, $con, AddressesPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AddressesPeer::clearInstancePool();
            AddressesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Addresses or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Addresses object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            AddressesPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Addresses) { // it's a model object
            // invalidate the cache for this single object
            AddressesPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(AddressesPeer::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(AddressesPeer::CUSTOMERS_ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(AddressesPeer::TYPE, $value[1]));
                $criteria->addOr($criterion);
                // we can invalidate the cache for this single PK
                AddressesPeer::removeInstanceFromPool($value);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(AddressesPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            AddressesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Addresses object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Addresses $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AddressesPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AddressesPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(AddressesPeer::DATABASE_NAME, AddressesPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param   int $customers_id
     * @param   string $type
     * @param      PropelPDO $con
     * @return Addresses
     */
    public static function retrieveByPK($customers_id, $type, PropelPDO $con = null) {
        $_instancePoolKey = serialize(array((string) $customers_id, (string) $type));
         if (null !== ($obj = AddressesPeer::getInstanceFromPool($_instancePoolKey))) {
             return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $criteria = new Criteria(AddressesPeer::DATABASE_NAME);
        $criteria->add(AddressesPeer::CUSTOMERS_ID, $customers_id);
        $criteria->add(AddressesPeer::TYPE, $type);
        $v = AddressesPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
} // BaseAddressesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAddressesPeer::buildTableMap();


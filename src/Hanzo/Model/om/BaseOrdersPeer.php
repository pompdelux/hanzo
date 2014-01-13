<?php

namespace Hanzo\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Hanzo\Model\CountriesPeer;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\EventsPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributesPeer;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersStateLogPeer;
use Hanzo\Model\OrdersSyncLogPeer;
use Hanzo\Model\OrdersToCouponsPeer;
use Hanzo\Model\OrdersVersionsPeer;
use Hanzo\Model\map\OrdersTableMap;

abstract class BaseOrdersPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'orders';

    /** the related Propel class for this table */
    const OM_CLASS = 'Hanzo\\Model\\Orders';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Hanzo\\Model\\map\\OrdersTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 43;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 43;

    /** the column name for the id field */
    const ID = 'orders.id';

    /** the column name for the version_id field */
    const VERSION_ID = 'orders.version_id';

    /** the column name for the session_id field */
    const SESSION_ID = 'orders.session_id';

    /** the column name for the payment_gateway_id field */
    const PAYMENT_GATEWAY_ID = 'orders.payment_gateway_id';

    /** the column name for the state field */
    const STATE = 'orders.state';

    /** the column name for the in_edit field */
    const IN_EDIT = 'orders.in_edit';

    /** the column name for the customers_id field */
    const CUSTOMERS_ID = 'orders.customers_id';

    /** the column name for the first_name field */
    const FIRST_NAME = 'orders.first_name';

    /** the column name for the last_name field */
    const LAST_NAME = 'orders.last_name';

    /** the column name for the email field */
    const EMAIL = 'orders.email';

    /** the column name for the phone field */
    const PHONE = 'orders.phone';

    /** the column name for the languages_id field */
    const LANGUAGES_ID = 'orders.languages_id';

    /** the column name for the currency_code field */
    const CURRENCY_CODE = 'orders.currency_code';

    /** the column name for the billing_title field */
    const BILLING_TITLE = 'orders.billing_title';

    /** the column name for the billing_first_name field */
    const BILLING_FIRST_NAME = 'orders.billing_first_name';

    /** the column name for the billing_last_name field */
    const BILLING_LAST_NAME = 'orders.billing_last_name';

    /** the column name for the billing_address_line_1 field */
    const BILLING_ADDRESS_LINE_1 = 'orders.billing_address_line_1';

    /** the column name for the billing_address_line_2 field */
    const BILLING_ADDRESS_LINE_2 = 'orders.billing_address_line_2';

    /** the column name for the billing_postal_code field */
    const BILLING_POSTAL_CODE = 'orders.billing_postal_code';

    /** the column name for the billing_city field */
    const BILLING_CITY = 'orders.billing_city';

    /** the column name for the billing_country field */
    const BILLING_COUNTRY = 'orders.billing_country';

    /** the column name for the billing_countries_id field */
    const BILLING_COUNTRIES_ID = 'orders.billing_countries_id';

    /** the column name for the billing_state_province field */
    const BILLING_STATE_PROVINCE = 'orders.billing_state_province';

    /** the column name for the billing_company_name field */
    const BILLING_COMPANY_NAME = 'orders.billing_company_name';

    /** the column name for the billing_method field */
    const BILLING_METHOD = 'orders.billing_method';

    /** the column name for the billing_external_address_id field */
    const BILLING_EXTERNAL_ADDRESS_ID = 'orders.billing_external_address_id';

    /** the column name for the delivery_title field */
    const DELIVERY_TITLE = 'orders.delivery_title';

    /** the column name for the delivery_first_name field */
    const DELIVERY_FIRST_NAME = 'orders.delivery_first_name';

    /** the column name for the delivery_last_name field */
    const DELIVERY_LAST_NAME = 'orders.delivery_last_name';

    /** the column name for the delivery_address_line_1 field */
    const DELIVERY_ADDRESS_LINE_1 = 'orders.delivery_address_line_1';

    /** the column name for the delivery_address_line_2 field */
    const DELIVERY_ADDRESS_LINE_2 = 'orders.delivery_address_line_2';

    /** the column name for the delivery_postal_code field */
    const DELIVERY_POSTAL_CODE = 'orders.delivery_postal_code';

    /** the column name for the delivery_city field */
    const DELIVERY_CITY = 'orders.delivery_city';

    /** the column name for the delivery_country field */
    const DELIVERY_COUNTRY = 'orders.delivery_country';

    /** the column name for the delivery_countries_id field */
    const DELIVERY_COUNTRIES_ID = 'orders.delivery_countries_id';

    /** the column name for the delivery_state_province field */
    const DELIVERY_STATE_PROVINCE = 'orders.delivery_state_province';

    /** the column name for the delivery_company_name field */
    const DELIVERY_COMPANY_NAME = 'orders.delivery_company_name';

    /** the column name for the delivery_method field */
    const DELIVERY_METHOD = 'orders.delivery_method';

    /** the column name for the delivery_external_address_id field */
    const DELIVERY_EXTERNAL_ADDRESS_ID = 'orders.delivery_external_address_id';

    /** the column name for the events_id field */
    const EVENTS_ID = 'orders.events_id';

    /** the column name for the finished_at field */
    const FINISHED_AT = 'orders.finished_at';

    /** the column name for the created_at field */
    const CREATED_AT = 'orders.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'orders.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Orders objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Orders[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. OrdersPeer::$fieldNames[OrdersPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'VersionId', 'SessionId', 'PaymentGatewayId', 'State', 'InEdit', 'CustomersId', 'FirstName', 'LastName', 'Email', 'Phone', 'LanguagesId', 'CurrencyCode', 'BillingTitle', 'BillingFirstName', 'BillingLastName', 'BillingAddressLine1', 'BillingAddressLine2', 'BillingPostalCode', 'BillingCity', 'BillingCountry', 'BillingCountriesId', 'BillingStateProvince', 'BillingCompanyName', 'BillingMethod', 'BillingExternalAddressId', 'DeliveryTitle', 'DeliveryFirstName', 'DeliveryLastName', 'DeliveryAddressLine1', 'DeliveryAddressLine2', 'DeliveryPostalCode', 'DeliveryCity', 'DeliveryCountry', 'DeliveryCountriesId', 'DeliveryStateProvince', 'DeliveryCompanyName', 'DeliveryMethod', 'DeliveryExternalAddressId', 'EventsId', 'FinishedAt', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'versionId', 'sessionId', 'paymentGatewayId', 'state', 'inEdit', 'customersId', 'firstName', 'lastName', 'email', 'phone', 'languagesId', 'currencyCode', 'billingTitle', 'billingFirstName', 'billingLastName', 'billingAddressLine1', 'billingAddressLine2', 'billingPostalCode', 'billingCity', 'billingCountry', 'billingCountriesId', 'billingStateProvince', 'billingCompanyName', 'billingMethod', 'billingExternalAddressId', 'deliveryTitle', 'deliveryFirstName', 'deliveryLastName', 'deliveryAddressLine1', 'deliveryAddressLine2', 'deliveryPostalCode', 'deliveryCity', 'deliveryCountry', 'deliveryCountriesId', 'deliveryStateProvince', 'deliveryCompanyName', 'deliveryMethod', 'deliveryExternalAddressId', 'eventsId', 'finishedAt', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (OrdersPeer::ID, OrdersPeer::VERSION_ID, OrdersPeer::SESSION_ID, OrdersPeer::PAYMENT_GATEWAY_ID, OrdersPeer::STATE, OrdersPeer::IN_EDIT, OrdersPeer::CUSTOMERS_ID, OrdersPeer::FIRST_NAME, OrdersPeer::LAST_NAME, OrdersPeer::EMAIL, OrdersPeer::PHONE, OrdersPeer::LANGUAGES_ID, OrdersPeer::CURRENCY_CODE, OrdersPeer::BILLING_TITLE, OrdersPeer::BILLING_FIRST_NAME, OrdersPeer::BILLING_LAST_NAME, OrdersPeer::BILLING_ADDRESS_LINE_1, OrdersPeer::BILLING_ADDRESS_LINE_2, OrdersPeer::BILLING_POSTAL_CODE, OrdersPeer::BILLING_CITY, OrdersPeer::BILLING_COUNTRY, OrdersPeer::BILLING_COUNTRIES_ID, OrdersPeer::BILLING_STATE_PROVINCE, OrdersPeer::BILLING_COMPANY_NAME, OrdersPeer::BILLING_METHOD, OrdersPeer::BILLING_EXTERNAL_ADDRESS_ID, OrdersPeer::DELIVERY_TITLE, OrdersPeer::DELIVERY_FIRST_NAME, OrdersPeer::DELIVERY_LAST_NAME, OrdersPeer::DELIVERY_ADDRESS_LINE_1, OrdersPeer::DELIVERY_ADDRESS_LINE_2, OrdersPeer::DELIVERY_POSTAL_CODE, OrdersPeer::DELIVERY_CITY, OrdersPeer::DELIVERY_COUNTRY, OrdersPeer::DELIVERY_COUNTRIES_ID, OrdersPeer::DELIVERY_STATE_PROVINCE, OrdersPeer::DELIVERY_COMPANY_NAME, OrdersPeer::DELIVERY_METHOD, OrdersPeer::DELIVERY_EXTERNAL_ADDRESS_ID, OrdersPeer::EVENTS_ID, OrdersPeer::FINISHED_AT, OrdersPeer::CREATED_AT, OrdersPeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'VERSION_ID', 'SESSION_ID', 'PAYMENT_GATEWAY_ID', 'STATE', 'IN_EDIT', 'CUSTOMERS_ID', 'FIRST_NAME', 'LAST_NAME', 'EMAIL', 'PHONE', 'LANGUAGES_ID', 'CURRENCY_CODE', 'BILLING_TITLE', 'BILLING_FIRST_NAME', 'BILLING_LAST_NAME', 'BILLING_ADDRESS_LINE_1', 'BILLING_ADDRESS_LINE_2', 'BILLING_POSTAL_CODE', 'BILLING_CITY', 'BILLING_COUNTRY', 'BILLING_COUNTRIES_ID', 'BILLING_STATE_PROVINCE', 'BILLING_COMPANY_NAME', 'BILLING_METHOD', 'BILLING_EXTERNAL_ADDRESS_ID', 'DELIVERY_TITLE', 'DELIVERY_FIRST_NAME', 'DELIVERY_LAST_NAME', 'DELIVERY_ADDRESS_LINE_1', 'DELIVERY_ADDRESS_LINE_2', 'DELIVERY_POSTAL_CODE', 'DELIVERY_CITY', 'DELIVERY_COUNTRY', 'DELIVERY_COUNTRIES_ID', 'DELIVERY_STATE_PROVINCE', 'DELIVERY_COMPANY_NAME', 'DELIVERY_METHOD', 'DELIVERY_EXTERNAL_ADDRESS_ID', 'EVENTS_ID', 'FINISHED_AT', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'version_id', 'session_id', 'payment_gateway_id', 'state', 'in_edit', 'customers_id', 'first_name', 'last_name', 'email', 'phone', 'languages_id', 'currency_code', 'billing_title', 'billing_first_name', 'billing_last_name', 'billing_address_line_1', 'billing_address_line_2', 'billing_postal_code', 'billing_city', 'billing_country', 'billing_countries_id', 'billing_state_province', 'billing_company_name', 'billing_method', 'billing_external_address_id', 'delivery_title', 'delivery_first_name', 'delivery_last_name', 'delivery_address_line_1', 'delivery_address_line_2', 'delivery_postal_code', 'delivery_city', 'delivery_country', 'delivery_countries_id', 'delivery_state_province', 'delivery_company_name', 'delivery_method', 'delivery_external_address_id', 'events_id', 'finished_at', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. OrdersPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'VersionId' => 1, 'SessionId' => 2, 'PaymentGatewayId' => 3, 'State' => 4, 'InEdit' => 5, 'CustomersId' => 6, 'FirstName' => 7, 'LastName' => 8, 'Email' => 9, 'Phone' => 10, 'LanguagesId' => 11, 'CurrencyCode' => 12, 'BillingTitle' => 13, 'BillingFirstName' => 14, 'BillingLastName' => 15, 'BillingAddressLine1' => 16, 'BillingAddressLine2' => 17, 'BillingPostalCode' => 18, 'BillingCity' => 19, 'BillingCountry' => 20, 'BillingCountriesId' => 21, 'BillingStateProvince' => 22, 'BillingCompanyName' => 23, 'BillingMethod' => 24, 'BillingExternalAddressId' => 25, 'DeliveryTitle' => 26, 'DeliveryFirstName' => 27, 'DeliveryLastName' => 28, 'DeliveryAddressLine1' => 29, 'DeliveryAddressLine2' => 30, 'DeliveryPostalCode' => 31, 'DeliveryCity' => 32, 'DeliveryCountry' => 33, 'DeliveryCountriesId' => 34, 'DeliveryStateProvince' => 35, 'DeliveryCompanyName' => 36, 'DeliveryMethod' => 37, 'DeliveryExternalAddressId' => 38, 'EventsId' => 39, 'FinishedAt' => 40, 'CreatedAt' => 41, 'UpdatedAt' => 42, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'versionId' => 1, 'sessionId' => 2, 'paymentGatewayId' => 3, 'state' => 4, 'inEdit' => 5, 'customersId' => 6, 'firstName' => 7, 'lastName' => 8, 'email' => 9, 'phone' => 10, 'languagesId' => 11, 'currencyCode' => 12, 'billingTitle' => 13, 'billingFirstName' => 14, 'billingLastName' => 15, 'billingAddressLine1' => 16, 'billingAddressLine2' => 17, 'billingPostalCode' => 18, 'billingCity' => 19, 'billingCountry' => 20, 'billingCountriesId' => 21, 'billingStateProvince' => 22, 'billingCompanyName' => 23, 'billingMethod' => 24, 'billingExternalAddressId' => 25, 'deliveryTitle' => 26, 'deliveryFirstName' => 27, 'deliveryLastName' => 28, 'deliveryAddressLine1' => 29, 'deliveryAddressLine2' => 30, 'deliveryPostalCode' => 31, 'deliveryCity' => 32, 'deliveryCountry' => 33, 'deliveryCountriesId' => 34, 'deliveryStateProvince' => 35, 'deliveryCompanyName' => 36, 'deliveryMethod' => 37, 'deliveryExternalAddressId' => 38, 'eventsId' => 39, 'finishedAt' => 40, 'createdAt' => 41, 'updatedAt' => 42, ),
        BasePeer::TYPE_COLNAME => array (OrdersPeer::ID => 0, OrdersPeer::VERSION_ID => 1, OrdersPeer::SESSION_ID => 2, OrdersPeer::PAYMENT_GATEWAY_ID => 3, OrdersPeer::STATE => 4, OrdersPeer::IN_EDIT => 5, OrdersPeer::CUSTOMERS_ID => 6, OrdersPeer::FIRST_NAME => 7, OrdersPeer::LAST_NAME => 8, OrdersPeer::EMAIL => 9, OrdersPeer::PHONE => 10, OrdersPeer::LANGUAGES_ID => 11, OrdersPeer::CURRENCY_CODE => 12, OrdersPeer::BILLING_TITLE => 13, OrdersPeer::BILLING_FIRST_NAME => 14, OrdersPeer::BILLING_LAST_NAME => 15, OrdersPeer::BILLING_ADDRESS_LINE_1 => 16, OrdersPeer::BILLING_ADDRESS_LINE_2 => 17, OrdersPeer::BILLING_POSTAL_CODE => 18, OrdersPeer::BILLING_CITY => 19, OrdersPeer::BILLING_COUNTRY => 20, OrdersPeer::BILLING_COUNTRIES_ID => 21, OrdersPeer::BILLING_STATE_PROVINCE => 22, OrdersPeer::BILLING_COMPANY_NAME => 23, OrdersPeer::BILLING_METHOD => 24, OrdersPeer::BILLING_EXTERNAL_ADDRESS_ID => 25, OrdersPeer::DELIVERY_TITLE => 26, OrdersPeer::DELIVERY_FIRST_NAME => 27, OrdersPeer::DELIVERY_LAST_NAME => 28, OrdersPeer::DELIVERY_ADDRESS_LINE_1 => 29, OrdersPeer::DELIVERY_ADDRESS_LINE_2 => 30, OrdersPeer::DELIVERY_POSTAL_CODE => 31, OrdersPeer::DELIVERY_CITY => 32, OrdersPeer::DELIVERY_COUNTRY => 33, OrdersPeer::DELIVERY_COUNTRIES_ID => 34, OrdersPeer::DELIVERY_STATE_PROVINCE => 35, OrdersPeer::DELIVERY_COMPANY_NAME => 36, OrdersPeer::DELIVERY_METHOD => 37, OrdersPeer::DELIVERY_EXTERNAL_ADDRESS_ID => 38, OrdersPeer::EVENTS_ID => 39, OrdersPeer::FINISHED_AT => 40, OrdersPeer::CREATED_AT => 41, OrdersPeer::UPDATED_AT => 42, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'VERSION_ID' => 1, 'SESSION_ID' => 2, 'PAYMENT_GATEWAY_ID' => 3, 'STATE' => 4, 'IN_EDIT' => 5, 'CUSTOMERS_ID' => 6, 'FIRST_NAME' => 7, 'LAST_NAME' => 8, 'EMAIL' => 9, 'PHONE' => 10, 'LANGUAGES_ID' => 11, 'CURRENCY_CODE' => 12, 'BILLING_TITLE' => 13, 'BILLING_FIRST_NAME' => 14, 'BILLING_LAST_NAME' => 15, 'BILLING_ADDRESS_LINE_1' => 16, 'BILLING_ADDRESS_LINE_2' => 17, 'BILLING_POSTAL_CODE' => 18, 'BILLING_CITY' => 19, 'BILLING_COUNTRY' => 20, 'BILLING_COUNTRIES_ID' => 21, 'BILLING_STATE_PROVINCE' => 22, 'BILLING_COMPANY_NAME' => 23, 'BILLING_METHOD' => 24, 'BILLING_EXTERNAL_ADDRESS_ID' => 25, 'DELIVERY_TITLE' => 26, 'DELIVERY_FIRST_NAME' => 27, 'DELIVERY_LAST_NAME' => 28, 'DELIVERY_ADDRESS_LINE_1' => 29, 'DELIVERY_ADDRESS_LINE_2' => 30, 'DELIVERY_POSTAL_CODE' => 31, 'DELIVERY_CITY' => 32, 'DELIVERY_COUNTRY' => 33, 'DELIVERY_COUNTRIES_ID' => 34, 'DELIVERY_STATE_PROVINCE' => 35, 'DELIVERY_COMPANY_NAME' => 36, 'DELIVERY_METHOD' => 37, 'DELIVERY_EXTERNAL_ADDRESS_ID' => 38, 'EVENTS_ID' => 39, 'FINISHED_AT' => 40, 'CREATED_AT' => 41, 'UPDATED_AT' => 42, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'version_id' => 1, 'session_id' => 2, 'payment_gateway_id' => 3, 'state' => 4, 'in_edit' => 5, 'customers_id' => 6, 'first_name' => 7, 'last_name' => 8, 'email' => 9, 'phone' => 10, 'languages_id' => 11, 'currency_code' => 12, 'billing_title' => 13, 'billing_first_name' => 14, 'billing_last_name' => 15, 'billing_address_line_1' => 16, 'billing_address_line_2' => 17, 'billing_postal_code' => 18, 'billing_city' => 19, 'billing_country' => 20, 'billing_countries_id' => 21, 'billing_state_province' => 22, 'billing_company_name' => 23, 'billing_method' => 24, 'billing_external_address_id' => 25, 'delivery_title' => 26, 'delivery_first_name' => 27, 'delivery_last_name' => 28, 'delivery_address_line_1' => 29, 'delivery_address_line_2' => 30, 'delivery_postal_code' => 31, 'delivery_city' => 32, 'delivery_country' => 33, 'delivery_countries_id' => 34, 'delivery_state_province' => 35, 'delivery_company_name' => 36, 'delivery_method' => 37, 'delivery_external_address_id' => 38, 'events_id' => 39, 'finished_at' => 40, 'created_at' => 41, 'updated_at' => 42, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, )
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
        $toNames = OrdersPeer::getFieldNames($toType);
        $key = isset(OrdersPeer::$fieldKeys[$fromType][$name]) ? OrdersPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(OrdersPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, OrdersPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return OrdersPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. OrdersPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(OrdersPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(OrdersPeer::ID);
            $criteria->addSelectColumn(OrdersPeer::VERSION_ID);
            $criteria->addSelectColumn(OrdersPeer::SESSION_ID);
            $criteria->addSelectColumn(OrdersPeer::PAYMENT_GATEWAY_ID);
            $criteria->addSelectColumn(OrdersPeer::STATE);
            $criteria->addSelectColumn(OrdersPeer::IN_EDIT);
            $criteria->addSelectColumn(OrdersPeer::CUSTOMERS_ID);
            $criteria->addSelectColumn(OrdersPeer::FIRST_NAME);
            $criteria->addSelectColumn(OrdersPeer::LAST_NAME);
            $criteria->addSelectColumn(OrdersPeer::EMAIL);
            $criteria->addSelectColumn(OrdersPeer::PHONE);
            $criteria->addSelectColumn(OrdersPeer::LANGUAGES_ID);
            $criteria->addSelectColumn(OrdersPeer::CURRENCY_CODE);
            $criteria->addSelectColumn(OrdersPeer::BILLING_TITLE);
            $criteria->addSelectColumn(OrdersPeer::BILLING_FIRST_NAME);
            $criteria->addSelectColumn(OrdersPeer::BILLING_LAST_NAME);
            $criteria->addSelectColumn(OrdersPeer::BILLING_ADDRESS_LINE_1);
            $criteria->addSelectColumn(OrdersPeer::BILLING_ADDRESS_LINE_2);
            $criteria->addSelectColumn(OrdersPeer::BILLING_POSTAL_CODE);
            $criteria->addSelectColumn(OrdersPeer::BILLING_CITY);
            $criteria->addSelectColumn(OrdersPeer::BILLING_COUNTRY);
            $criteria->addSelectColumn(OrdersPeer::BILLING_COUNTRIES_ID);
            $criteria->addSelectColumn(OrdersPeer::BILLING_STATE_PROVINCE);
            $criteria->addSelectColumn(OrdersPeer::BILLING_COMPANY_NAME);
            $criteria->addSelectColumn(OrdersPeer::BILLING_METHOD);
            $criteria->addSelectColumn(OrdersPeer::BILLING_EXTERNAL_ADDRESS_ID);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_TITLE);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_FIRST_NAME);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_LAST_NAME);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_ADDRESS_LINE_1);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_ADDRESS_LINE_2);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_POSTAL_CODE);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_CITY);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_COUNTRY);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_COUNTRIES_ID);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_STATE_PROVINCE);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_COMPANY_NAME);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_METHOD);
            $criteria->addSelectColumn(OrdersPeer::DELIVERY_EXTERNAL_ADDRESS_ID);
            $criteria->addSelectColumn(OrdersPeer::EVENTS_ID);
            $criteria->addSelectColumn(OrdersPeer::FINISHED_AT);
            $criteria->addSelectColumn(OrdersPeer::CREATED_AT);
            $criteria->addSelectColumn(OrdersPeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.version_id');
            $criteria->addSelectColumn($alias . '.session_id');
            $criteria->addSelectColumn($alias . '.payment_gateway_id');
            $criteria->addSelectColumn($alias . '.state');
            $criteria->addSelectColumn($alias . '.in_edit');
            $criteria->addSelectColumn($alias . '.customers_id');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.last_name');
            $criteria->addSelectColumn($alias . '.email');
            $criteria->addSelectColumn($alias . '.phone');
            $criteria->addSelectColumn($alias . '.languages_id');
            $criteria->addSelectColumn($alias . '.currency_code');
            $criteria->addSelectColumn($alias . '.billing_title');
            $criteria->addSelectColumn($alias . '.billing_first_name');
            $criteria->addSelectColumn($alias . '.billing_last_name');
            $criteria->addSelectColumn($alias . '.billing_address_line_1');
            $criteria->addSelectColumn($alias . '.billing_address_line_2');
            $criteria->addSelectColumn($alias . '.billing_postal_code');
            $criteria->addSelectColumn($alias . '.billing_city');
            $criteria->addSelectColumn($alias . '.billing_country');
            $criteria->addSelectColumn($alias . '.billing_countries_id');
            $criteria->addSelectColumn($alias . '.billing_state_province');
            $criteria->addSelectColumn($alias . '.billing_company_name');
            $criteria->addSelectColumn($alias . '.billing_method');
            $criteria->addSelectColumn($alias . '.billing_external_address_id');
            $criteria->addSelectColumn($alias . '.delivery_title');
            $criteria->addSelectColumn($alias . '.delivery_first_name');
            $criteria->addSelectColumn($alias . '.delivery_last_name');
            $criteria->addSelectColumn($alias . '.delivery_address_line_1');
            $criteria->addSelectColumn($alias . '.delivery_address_line_2');
            $criteria->addSelectColumn($alias . '.delivery_postal_code');
            $criteria->addSelectColumn($alias . '.delivery_city');
            $criteria->addSelectColumn($alias . '.delivery_country');
            $criteria->addSelectColumn($alias . '.delivery_countries_id');
            $criteria->addSelectColumn($alias . '.delivery_state_province');
            $criteria->addSelectColumn($alias . '.delivery_company_name');
            $criteria->addSelectColumn($alias . '.delivery_method');
            $criteria->addSelectColumn($alias . '.delivery_external_address_id');
            $criteria->addSelectColumn($alias . '.events_id');
            $criteria->addSelectColumn($alias . '.finished_at');
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
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(OrdersPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return Orders
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = OrdersPeer::doSelect($critcopy, $con);
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
        return OrdersPeer::populateObjects(OrdersPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            OrdersPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

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
     * @param Orders $obj A Orders object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            OrdersPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Orders object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Orders) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Orders object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(OrdersPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Orders Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(OrdersPeer::$instances[$key])) {
                return OrdersPeer::$instances[$key];
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
        foreach (OrdersPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        OrdersPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to orders
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in OrdersToCouponsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersToCouponsPeer::clearInstancePool();
        // Invalidate objects in OrdersAttributesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersAttributesPeer::clearInstancePool();
        // Invalidate objects in OrdersLinesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersLinesPeer::clearInstancePool();
        // Invalidate objects in OrdersStateLogPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersStateLogPeer::clearInstancePool();
        // Invalidate objects in OrdersSyncLogPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersSyncLogPeer::clearInstancePool();
        // Invalidate objects in OrdersVersionsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrdersVersionsPeer::clearInstancePool();
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
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
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

        return (int) $row[$startcol];
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
        $cls = OrdersPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = OrdersPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrdersPeer::addInstanceToPool($obj, $key);
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
     * @return array (Orders object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = OrdersPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = OrdersPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + OrdersPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrdersPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            OrdersPeer::addInstanceToPool($obj, $key);
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
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CountriesRelatedByBillingCountriesId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCountriesRelatedByBillingCountriesId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CountriesRelatedByDeliveryCountriesId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCountriesRelatedByDeliveryCountriesId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related Events table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinEvents(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Selects a collection of Orders objects pre-filled with their Customers objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCustomers(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol = OrdersPeer::NUM_HYDRATE_COLUMNS;
        CustomersPeer::addSelectColumns($criteria);

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to $obj2 (Customers)
                $obj2->addOrders($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Orders objects pre-filled with their Countries objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCountriesRelatedByBillingCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol = OrdersPeer::NUM_HYDRATE_COLUMNS;
        CountriesPeer::addSelectColumns($criteria);

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to $obj2 (Countries)
                $obj2->addOrdersRelatedByBillingCountriesId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Orders objects pre-filled with their Countries objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCountriesRelatedByDeliveryCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol = OrdersPeer::NUM_HYDRATE_COLUMNS;
        CountriesPeer::addSelectColumns($criteria);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to $obj2 (Countries)
                $obj2->addOrdersRelatedByDeliveryCountriesId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Orders objects pre-filled with their Events objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinEvents(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol = OrdersPeer::NUM_HYDRATE_COLUMNS;
        EventsPeer::addSelectColumns($criteria);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = EventsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = EventsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    EventsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Orders) to $obj2 (Events)
                $obj2->addOrders($obj1);

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
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Selects a collection of Orders objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        EventsPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + EventsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to the collection in $obj2 (Customers)
                $obj2->addOrders($obj1);
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

                // Add the $obj1 (Orders) to the collection in $obj3 (Countries)
                $obj3->addOrdersRelatedByBillingCountriesId($obj1);
            } // if joined row not null

            // Add objects for joined Countries rows

            $key4 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = CountriesPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = CountriesPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CountriesPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (Orders) to the collection in $obj4 (Countries)
                $obj4->addOrdersRelatedByDeliveryCountriesId($obj1);
            } // if joined row not null

            // Add objects for joined Events rows

            $key5 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol5);
            if ($key5 !== null) {
                $obj5 = EventsPeer::getInstanceFromPool($key5);
                if (!$obj5) {

                    $cls = EventsPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    EventsPeer::addInstanceToPool($obj5, $key5);
                } // if obj5 loaded

                // Add the $obj1 (Orders) to the collection in $obj5 (Events)
                $obj5->addOrders($obj1);
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
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CountriesRelatedByBillingCountriesId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCountriesRelatedByBillingCountriesId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CountriesRelatedByDeliveryCountriesId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCountriesRelatedByDeliveryCountriesId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related Events table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptEvents(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            OrdersPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

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
     * Selects a collection of Orders objects pre-filled with all related objects except Customers.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
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
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        EventsPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + EventsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to the collection in $obj2 (Countries)
                $obj2->addOrdersRelatedByBillingCountriesId($obj1);

            } // if joined row is not null

                // Add objects for joined Countries rows

                $key3 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CountriesPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CountriesPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CountriesPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Orders) to the collection in $obj3 (Countries)
                $obj3->addOrdersRelatedByDeliveryCountriesId($obj1);

            } // if joined row is not null

                // Add objects for joined Events rows

                $key4 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = EventsPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = EventsPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    EventsPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Orders) to the collection in $obj4 (Events)
                $obj4->addOrders($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Orders objects pre-filled with all related objects except CountriesRelatedByBillingCountriesId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCountriesRelatedByBillingCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        EventsPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + EventsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to the collection in $obj2 (Customers)
                $obj2->addOrders($obj1);

            } // if joined row is not null

                // Add objects for joined Events rows

                $key3 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = EventsPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = EventsPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    EventsPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Orders) to the collection in $obj3 (Events)
                $obj3->addOrders($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Orders objects pre-filled with all related objects except CountriesRelatedByDeliveryCountriesId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCountriesRelatedByDeliveryCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        EventsPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + EventsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::EVENTS_ID, EventsPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to the collection in $obj2 (Customers)
                $obj2->addOrders($obj1);

            } // if joined row is not null

                // Add objects for joined Events rows

                $key3 = EventsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = EventsPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = EventsPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    EventsPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Orders) to the collection in $obj3 (Events)
                $obj3->addOrders($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Orders objects pre-filled with all related objects except Events.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Orders objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptEvents(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(OrdersPeer::DATABASE_NAME);
        }

        OrdersPeer::addSelectColumns($criteria);
        $startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

        CustomersPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        CountriesPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CountriesPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(OrdersPeer::CUSTOMERS_ID, CustomersPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::BILLING_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);

        $criteria->addJoin(OrdersPeer::DELIVERY_COUNTRIES_ID, CountriesPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = OrdersPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = OrdersPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = OrdersPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                OrdersPeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (Orders) to the collection in $obj2 (Customers)
                $obj2->addOrders($obj1);

            } // if joined row is not null

                // Add objects for joined Countries rows

                $key3 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CountriesPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CountriesPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CountriesPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Orders) to the collection in $obj3 (Countries)
                $obj3->addOrdersRelatedByBillingCountriesId($obj1);

            } // if joined row is not null

                // Add objects for joined Countries rows

                $key4 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = CountriesPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = CountriesPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CountriesPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Orders) to the collection in $obj4 (Countries)
                $obj4->addOrdersRelatedByDeliveryCountriesId($obj1);

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
        return Propel::getDatabaseMap(OrdersPeer::DATABASE_NAME)->getTable(OrdersPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseOrdersPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseOrdersPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Hanzo\Model\map\OrdersTableMap());
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
        return OrdersPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Orders or Criteria object.
     *
     * @param      mixed $values Criteria or Orders object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Orders object
        }


        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a Orders or Criteria object.
     *
     * @param      mixed $values Criteria or Orders object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(OrdersPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(OrdersPeer::ID);
            $value = $criteria->remove(OrdersPeer::ID);
            if ($value) {
                $selectCriteria->add(OrdersPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(OrdersPeer::TABLE_NAME);
            }

        } else { // $values is Orders object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the orders table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(OrdersPeer::TABLE_NAME, $con, OrdersPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OrdersPeer::clearInstancePool();
            OrdersPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Orders or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Orders object or primary key or array of primary keys
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
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            OrdersPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Orders) { // it's a model object
            // invalidate the cache for this single object
            OrdersPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrdersPeer::DATABASE_NAME);
            $criteria->add(OrdersPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                OrdersPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(OrdersPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            OrdersPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Orders object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Orders $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(OrdersPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(OrdersPeer::TABLE_NAME);

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

        return BasePeer::doValidate(OrdersPeer::DATABASE_NAME, OrdersPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Orders
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = OrdersPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(OrdersPeer::DATABASE_NAME);
        $criteria->add(OrdersPeer::ID, $pk);

        $v = OrdersPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Orders[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(OrdersPeer::DATABASE_NAME);
            $criteria->add(OrdersPeer::ID, $pks, Criteria::IN);
            $objs = OrdersPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseOrdersPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseOrdersPeer::buildTableMap();


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
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributesPeer;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersStateLogPeer;
use Hanzo\Model\OrdersSyncLogPeer;
use Hanzo\Model\OrdersVersionsPeer;
use Hanzo\Model\map\OrdersTableMap;

/**
 * Base static class for performing query and update operations on the 'orders' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseOrdersPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'default';

	/** the table name for this class */
	const TABLE_NAME = 'orders';

	/** the related Propel class for this table */
	const OM_CLASS = 'Hanzo\\Model\\Orders';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'src.Hanzo.Model.Orders';

	/** the related TableMap class for this table */
	const TM_CLASS = 'OrdersTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 38;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 38;

	/** the column name for the ID field */
	const ID = 'orders.ID';

	/** the column name for the VERSION_ID field */
	const VERSION_ID = 'orders.VERSION_ID';

	/** the column name for the SESSION_ID field */
	const SESSION_ID = 'orders.SESSION_ID';

	/** the column name for the PAYMENT_GATEWAY_ID field */
	const PAYMENT_GATEWAY_ID = 'orders.PAYMENT_GATEWAY_ID';

	/** the column name for the STATE field */
	const STATE = 'orders.STATE';

	/** the column name for the IN_EDIT field */
	const IN_EDIT = 'orders.IN_EDIT';

	/** the column name for the CUSTOMERS_ID field */
	const CUSTOMERS_ID = 'orders.CUSTOMERS_ID';

	/** the column name for the FIRST_NAME field */
	const FIRST_NAME = 'orders.FIRST_NAME';

	/** the column name for the LAST_NAME field */
	const LAST_NAME = 'orders.LAST_NAME';

	/** the column name for the EMAIL field */
	const EMAIL = 'orders.EMAIL';

	/** the column name for the PHONE field */
	const PHONE = 'orders.PHONE';

	/** the column name for the LANGUAGES_ID field */
	const LANGUAGES_ID = 'orders.LANGUAGES_ID';

	/** the column name for the CURRENCY_ID field */
	const CURRENCY_ID = 'orders.CURRENCY_ID';

	/** the column name for the BILLING_FIRST_NAME field */
	const BILLING_FIRST_NAME = 'orders.BILLING_FIRST_NAME';

	/** the column name for the BILLING_LAST_NAME field */
	const BILLING_LAST_NAME = 'orders.BILLING_LAST_NAME';

	/** the column name for the BILLING_ADDRESS_LINE_1 field */
	const BILLING_ADDRESS_LINE_1 = 'orders.BILLING_ADDRESS_LINE_1';

	/** the column name for the BILLING_ADDRESS_LINE_2 field */
	const BILLING_ADDRESS_LINE_2 = 'orders.BILLING_ADDRESS_LINE_2';

	/** the column name for the BILLING_POSTAL_CODE field */
	const BILLING_POSTAL_CODE = 'orders.BILLING_POSTAL_CODE';

	/** the column name for the BILLING_CITY field */
	const BILLING_CITY = 'orders.BILLING_CITY';

	/** the column name for the BILLING_COUNTRY field */
	const BILLING_COUNTRY = 'orders.BILLING_COUNTRY';

	/** the column name for the BILLING_COUNTRIES_ID field */
	const BILLING_COUNTRIES_ID = 'orders.BILLING_COUNTRIES_ID';

	/** the column name for the BILLING_STATE_PROVINCE field */
	const BILLING_STATE_PROVINCE = 'orders.BILLING_STATE_PROVINCE';

	/** the column name for the BILLING_COMPANY_NAME field */
	const BILLING_COMPANY_NAME = 'orders.BILLING_COMPANY_NAME';

	/** the column name for the BILLING_METHOD field */
	const BILLING_METHOD = 'orders.BILLING_METHOD';

	/** the column name for the DELIVERY_FIRST_NAME field */
	const DELIVERY_FIRST_NAME = 'orders.DELIVERY_FIRST_NAME';

	/** the column name for the DELIVERY_LAST_NAME field */
	const DELIVERY_LAST_NAME = 'orders.DELIVERY_LAST_NAME';

	/** the column name for the DELIVERY_ADDRESS_LINE_1 field */
	const DELIVERY_ADDRESS_LINE_1 = 'orders.DELIVERY_ADDRESS_LINE_1';

	/** the column name for the DELIVERY_ADDRESS_LINE_2 field */
	const DELIVERY_ADDRESS_LINE_2 = 'orders.DELIVERY_ADDRESS_LINE_2';

	/** the column name for the DELIVERY_POSTAL_CODE field */
	const DELIVERY_POSTAL_CODE = 'orders.DELIVERY_POSTAL_CODE';

	/** the column name for the DELIVERY_CITY field */
	const DELIVERY_CITY = 'orders.DELIVERY_CITY';

	/** the column name for the DELIVERY_COUNTRY field */
	const DELIVERY_COUNTRY = 'orders.DELIVERY_COUNTRY';

	/** the column name for the DELIVERY_COUNTRIES_ID field */
	const DELIVERY_COUNTRIES_ID = 'orders.DELIVERY_COUNTRIES_ID';

	/** the column name for the DELIVERY_STATE_PROVINCE field */
	const DELIVERY_STATE_PROVINCE = 'orders.DELIVERY_STATE_PROVINCE';

	/** the column name for the DELIVERY_COMPANY_NAME field */
	const DELIVERY_COMPANY_NAME = 'orders.DELIVERY_COMPANY_NAME';

	/** the column name for the DELIVERY_METHOD field */
	const DELIVERY_METHOD = 'orders.DELIVERY_METHOD';

	/** the column name for the FINISHED_AT field */
	const FINISHED_AT = 'orders.FINISHED_AT';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'orders.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'orders.UPDATED_AT';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';

	/**
	 * An identiy map to hold any loaded instances of Orders objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array Orders[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'VersionId', 'SessionId', 'PaymentGatewayId', 'State', 'InEdit', 'CustomersId', 'FirstName', 'LastName', 'Email', 'Phone', 'LanguagesId', 'CurrencyId', 'BillingFirstName', 'BillingLastName', 'BillingAddressLine1', 'BillingAddressLine2', 'BillingPostalCode', 'BillingCity', 'BillingCountry', 'BillingCountriesId', 'BillingStateProvince', 'BillingCompanyName', 'BillingMethod', 'DeliveryFirstName', 'DeliveryLastName', 'DeliveryAddressLine1', 'DeliveryAddressLine2', 'DeliveryPostalCode', 'DeliveryCity', 'DeliveryCountry', 'DeliveryCountriesId', 'DeliveryStateProvince', 'DeliveryCompanyName', 'DeliveryMethod', 'FinishedAt', 'CreatedAt', 'UpdatedAt', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'versionId', 'sessionId', 'paymentGatewayId', 'state', 'inEdit', 'customersId', 'firstName', 'lastName', 'email', 'phone', 'languagesId', 'currencyId', 'billingFirstName', 'billingLastName', 'billingAddressLine1', 'billingAddressLine2', 'billingPostalCode', 'billingCity', 'billingCountry', 'billingCountriesId', 'billingStateProvince', 'billingCompanyName', 'billingMethod', 'deliveryFirstName', 'deliveryLastName', 'deliveryAddressLine1', 'deliveryAddressLine2', 'deliveryPostalCode', 'deliveryCity', 'deliveryCountry', 'deliveryCountriesId', 'deliveryStateProvince', 'deliveryCompanyName', 'deliveryMethod', 'finishedAt', 'createdAt', 'updatedAt', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::VERSION_ID, self::SESSION_ID, self::PAYMENT_GATEWAY_ID, self::STATE, self::IN_EDIT, self::CUSTOMERS_ID, self::FIRST_NAME, self::LAST_NAME, self::EMAIL, self::PHONE, self::LANGUAGES_ID, self::CURRENCY_ID, self::BILLING_FIRST_NAME, self::BILLING_LAST_NAME, self::BILLING_ADDRESS_LINE_1, self::BILLING_ADDRESS_LINE_2, self::BILLING_POSTAL_CODE, self::BILLING_CITY, self::BILLING_COUNTRY, self::BILLING_COUNTRIES_ID, self::BILLING_STATE_PROVINCE, self::BILLING_COMPANY_NAME, self::BILLING_METHOD, self::DELIVERY_FIRST_NAME, self::DELIVERY_LAST_NAME, self::DELIVERY_ADDRESS_LINE_1, self::DELIVERY_ADDRESS_LINE_2, self::DELIVERY_POSTAL_CODE, self::DELIVERY_CITY, self::DELIVERY_COUNTRY, self::DELIVERY_COUNTRIES_ID, self::DELIVERY_STATE_PROVINCE, self::DELIVERY_COMPANY_NAME, self::DELIVERY_METHOD, self::FINISHED_AT, self::CREATED_AT, self::UPDATED_AT, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'VERSION_ID', 'SESSION_ID', 'PAYMENT_GATEWAY_ID', 'STATE', 'IN_EDIT', 'CUSTOMERS_ID', 'FIRST_NAME', 'LAST_NAME', 'EMAIL', 'PHONE', 'LANGUAGES_ID', 'CURRENCY_ID', 'BILLING_FIRST_NAME', 'BILLING_LAST_NAME', 'BILLING_ADDRESS_LINE_1', 'BILLING_ADDRESS_LINE_2', 'BILLING_POSTAL_CODE', 'BILLING_CITY', 'BILLING_COUNTRY', 'BILLING_COUNTRIES_ID', 'BILLING_STATE_PROVINCE', 'BILLING_COMPANY_NAME', 'BILLING_METHOD', 'DELIVERY_FIRST_NAME', 'DELIVERY_LAST_NAME', 'DELIVERY_ADDRESS_LINE_1', 'DELIVERY_ADDRESS_LINE_2', 'DELIVERY_POSTAL_CODE', 'DELIVERY_CITY', 'DELIVERY_COUNTRY', 'DELIVERY_COUNTRIES_ID', 'DELIVERY_STATE_PROVINCE', 'DELIVERY_COMPANY_NAME', 'DELIVERY_METHOD', 'FINISHED_AT', 'CREATED_AT', 'UPDATED_AT', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'version_id', 'session_id', 'payment_gateway_id', 'state', 'in_edit', 'customers_id', 'first_name', 'last_name', 'email', 'phone', 'languages_id', 'currency_id', 'billing_first_name', 'billing_last_name', 'billing_address_line_1', 'billing_address_line_2', 'billing_postal_code', 'billing_city', 'billing_country', 'billing_countries_id', 'billing_state_province', 'billing_company_name', 'billing_method', 'delivery_first_name', 'delivery_last_name', 'delivery_address_line_1', 'delivery_address_line_2', 'delivery_postal_code', 'delivery_city', 'delivery_country', 'delivery_countries_id', 'delivery_state_province', 'delivery_company_name', 'delivery_method', 'finished_at', 'created_at', 'updated_at', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'VersionId' => 1, 'SessionId' => 2, 'PaymentGatewayId' => 3, 'State' => 4, 'InEdit' => 5, 'CustomersId' => 6, 'FirstName' => 7, 'LastName' => 8, 'Email' => 9, 'Phone' => 10, 'LanguagesId' => 11, 'CurrencyId' => 12, 'BillingFirstName' => 13, 'BillingLastName' => 14, 'BillingAddressLine1' => 15, 'BillingAddressLine2' => 16, 'BillingPostalCode' => 17, 'BillingCity' => 18, 'BillingCountry' => 19, 'BillingCountriesId' => 20, 'BillingStateProvince' => 21, 'BillingCompanyName' => 22, 'BillingMethod' => 23, 'DeliveryFirstName' => 24, 'DeliveryLastName' => 25, 'DeliveryAddressLine1' => 26, 'DeliveryAddressLine2' => 27, 'DeliveryPostalCode' => 28, 'DeliveryCity' => 29, 'DeliveryCountry' => 30, 'DeliveryCountriesId' => 31, 'DeliveryStateProvince' => 32, 'DeliveryCompanyName' => 33, 'DeliveryMethod' => 34, 'FinishedAt' => 35, 'CreatedAt' => 36, 'UpdatedAt' => 37, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'versionId' => 1, 'sessionId' => 2, 'paymentGatewayId' => 3, 'state' => 4, 'inEdit' => 5, 'customersId' => 6, 'firstName' => 7, 'lastName' => 8, 'email' => 9, 'phone' => 10, 'languagesId' => 11, 'currencyId' => 12, 'billingFirstName' => 13, 'billingLastName' => 14, 'billingAddressLine1' => 15, 'billingAddressLine2' => 16, 'billingPostalCode' => 17, 'billingCity' => 18, 'billingCountry' => 19, 'billingCountriesId' => 20, 'billingStateProvince' => 21, 'billingCompanyName' => 22, 'billingMethod' => 23, 'deliveryFirstName' => 24, 'deliveryLastName' => 25, 'deliveryAddressLine1' => 26, 'deliveryAddressLine2' => 27, 'deliveryPostalCode' => 28, 'deliveryCity' => 29, 'deliveryCountry' => 30, 'deliveryCountriesId' => 31, 'deliveryStateProvince' => 32, 'deliveryCompanyName' => 33, 'deliveryMethod' => 34, 'finishedAt' => 35, 'createdAt' => 36, 'updatedAt' => 37, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::VERSION_ID => 1, self::SESSION_ID => 2, self::PAYMENT_GATEWAY_ID => 3, self::STATE => 4, self::IN_EDIT => 5, self::CUSTOMERS_ID => 6, self::FIRST_NAME => 7, self::LAST_NAME => 8, self::EMAIL => 9, self::PHONE => 10, self::LANGUAGES_ID => 11, self::CURRENCY_ID => 12, self::BILLING_FIRST_NAME => 13, self::BILLING_LAST_NAME => 14, self::BILLING_ADDRESS_LINE_1 => 15, self::BILLING_ADDRESS_LINE_2 => 16, self::BILLING_POSTAL_CODE => 17, self::BILLING_CITY => 18, self::BILLING_COUNTRY => 19, self::BILLING_COUNTRIES_ID => 20, self::BILLING_STATE_PROVINCE => 21, self::BILLING_COMPANY_NAME => 22, self::BILLING_METHOD => 23, self::DELIVERY_FIRST_NAME => 24, self::DELIVERY_LAST_NAME => 25, self::DELIVERY_ADDRESS_LINE_1 => 26, self::DELIVERY_ADDRESS_LINE_2 => 27, self::DELIVERY_POSTAL_CODE => 28, self::DELIVERY_CITY => 29, self::DELIVERY_COUNTRY => 30, self::DELIVERY_COUNTRIES_ID => 31, self::DELIVERY_STATE_PROVINCE => 32, self::DELIVERY_COMPANY_NAME => 33, self::DELIVERY_METHOD => 34, self::FINISHED_AT => 35, self::CREATED_AT => 36, self::UPDATED_AT => 37, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'VERSION_ID' => 1, 'SESSION_ID' => 2, 'PAYMENT_GATEWAY_ID' => 3, 'STATE' => 4, 'IN_EDIT' => 5, 'CUSTOMERS_ID' => 6, 'FIRST_NAME' => 7, 'LAST_NAME' => 8, 'EMAIL' => 9, 'PHONE' => 10, 'LANGUAGES_ID' => 11, 'CURRENCY_ID' => 12, 'BILLING_FIRST_NAME' => 13, 'BILLING_LAST_NAME' => 14, 'BILLING_ADDRESS_LINE_1' => 15, 'BILLING_ADDRESS_LINE_2' => 16, 'BILLING_POSTAL_CODE' => 17, 'BILLING_CITY' => 18, 'BILLING_COUNTRY' => 19, 'BILLING_COUNTRIES_ID' => 20, 'BILLING_STATE_PROVINCE' => 21, 'BILLING_COMPANY_NAME' => 22, 'BILLING_METHOD' => 23, 'DELIVERY_FIRST_NAME' => 24, 'DELIVERY_LAST_NAME' => 25, 'DELIVERY_ADDRESS_LINE_1' => 26, 'DELIVERY_ADDRESS_LINE_2' => 27, 'DELIVERY_POSTAL_CODE' => 28, 'DELIVERY_CITY' => 29, 'DELIVERY_COUNTRY' => 30, 'DELIVERY_COUNTRIES_ID' => 31, 'DELIVERY_STATE_PROVINCE' => 32, 'DELIVERY_COMPANY_NAME' => 33, 'DELIVERY_METHOD' => 34, 'FINISHED_AT' => 35, 'CREATED_AT' => 36, 'UPDATED_AT' => 37, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'version_id' => 1, 'session_id' => 2, 'payment_gateway_id' => 3, 'state' => 4, 'in_edit' => 5, 'customers_id' => 6, 'first_name' => 7, 'last_name' => 8, 'email' => 9, 'phone' => 10, 'languages_id' => 11, 'currency_id' => 12, 'billing_first_name' => 13, 'billing_last_name' => 14, 'billing_address_line_1' => 15, 'billing_address_line_2' => 16, 'billing_postal_code' => 17, 'billing_city' => 18, 'billing_country' => 19, 'billing_countries_id' => 20, 'billing_state_province' => 21, 'billing_company_name' => 22, 'billing_method' => 23, 'delivery_first_name' => 24, 'delivery_last_name' => 25, 'delivery_address_line_1' => 26, 'delivery_address_line_2' => 27, 'delivery_postal_code' => 28, 'delivery_city' => 29, 'delivery_country' => 30, 'delivery_countries_id' => 31, 'delivery_state_province' => 32, 'delivery_company_name' => 33, 'delivery_method' => 34, 'finished_at' => 35, 'created_at' => 36, 'updated_at' => 37, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
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
	 * @return     string
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
	 * @throws     PropelException Any exceptions caught during processing will be
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
			$criteria->addSelectColumn(OrdersPeer::CURRENCY_ID);
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
			$criteria->addSelectColumn(OrdersPeer::FINISHED_AT);
			$criteria->addSelectColumn(OrdersPeer::CREATED_AT);
			$criteria->addSelectColumn(OrdersPeer::UPDATED_AT);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.VERSION_ID');
			$criteria->addSelectColumn($alias . '.SESSION_ID');
			$criteria->addSelectColumn($alias . '.PAYMENT_GATEWAY_ID');
			$criteria->addSelectColumn($alias . '.STATE');
			$criteria->addSelectColumn($alias . '.IN_EDIT');
			$criteria->addSelectColumn($alias . '.CUSTOMERS_ID');
			$criteria->addSelectColumn($alias . '.FIRST_NAME');
			$criteria->addSelectColumn($alias . '.LAST_NAME');
			$criteria->addSelectColumn($alias . '.EMAIL');
			$criteria->addSelectColumn($alias . '.PHONE');
			$criteria->addSelectColumn($alias . '.LANGUAGES_ID');
			$criteria->addSelectColumn($alias . '.CURRENCY_ID');
			$criteria->addSelectColumn($alias . '.BILLING_FIRST_NAME');
			$criteria->addSelectColumn($alias . '.BILLING_LAST_NAME');
			$criteria->addSelectColumn($alias . '.BILLING_ADDRESS_LINE_1');
			$criteria->addSelectColumn($alias . '.BILLING_ADDRESS_LINE_2');
			$criteria->addSelectColumn($alias . '.BILLING_POSTAL_CODE');
			$criteria->addSelectColumn($alias . '.BILLING_CITY');
			$criteria->addSelectColumn($alias . '.BILLING_COUNTRY');
			$criteria->addSelectColumn($alias . '.BILLING_COUNTRIES_ID');
			$criteria->addSelectColumn($alias . '.BILLING_STATE_PROVINCE');
			$criteria->addSelectColumn($alias . '.BILLING_COMPANY_NAME');
			$criteria->addSelectColumn($alias . '.BILLING_METHOD');
			$criteria->addSelectColumn($alias . '.DELIVERY_FIRST_NAME');
			$criteria->addSelectColumn($alias . '.DELIVERY_LAST_NAME');
			$criteria->addSelectColumn($alias . '.DELIVERY_ADDRESS_LINE_1');
			$criteria->addSelectColumn($alias . '.DELIVERY_ADDRESS_LINE_2');
			$criteria->addSelectColumn($alias . '.DELIVERY_POSTAL_CODE');
			$criteria->addSelectColumn($alias . '.DELIVERY_CITY');
			$criteria->addSelectColumn($alias . '.DELIVERY_COUNTRY');
			$criteria->addSelectColumn($alias . '.DELIVERY_COUNTRIES_ID');
			$criteria->addSelectColumn($alias . '.DELIVERY_STATE_PROVINCE');
			$criteria->addSelectColumn($alias . '.DELIVERY_COMPANY_NAME');
			$criteria->addSelectColumn($alias . '.DELIVERY_METHOD');
			$criteria->addSelectColumn($alias . '.FINISHED_AT');
			$criteria->addSelectColumn($alias . '.CREATED_AT');
			$criteria->addSelectColumn($alias . '.UPDATED_AT');
		}
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

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
	 * @return     Orders
	 * @throws     PropelException Any exceptions caught during processing will be
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
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return OrdersPeer::populateObjects(OrdersPeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * @param      Orders $value A Orders object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
			} // if key === null
			self::$instances[$key] = $obj;
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

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     Orders Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to orders
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
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
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
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
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return (int) $row[$startcol];
	}
	
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = OrdersPeer::getOMClass(false);
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
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (Orders object, last column rank)
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
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * Selects a collection of Orders objects pre-filled with their Customers objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCustomers(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
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

				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CustomersPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CustomersPeer::getOMClass(false);

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
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCountriesRelatedByBillingCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
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

				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CountriesPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CountriesPeer::getOMClass(false);

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
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCountriesRelatedByDeliveryCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
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

				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CountriesPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CountriesPeer::getOMClass(false);

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
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * Selects a collection of Orders objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
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
				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Customers rows

			$key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = CustomersPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CustomersPeer::getOMClass(false);

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

					$cls = CountriesPeer::getOMClass(false);

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

					$cls = CountriesPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CountriesPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (Orders) to the collection in $obj4 (Countries)
				$obj4->addOrdersRelatedByDeliveryCountriesId($obj1);
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
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
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
	 * Returns the number of rows matching criteria, joining the related CountriesRelatedByBillingCountriesId table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * Returns the number of rows matching criteria, joining the related CountriesRelatedByDeliveryCountriesId table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * Selects a collection of Orders objects pre-filled with all related objects except Customers.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCustomers(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		OrdersPeer::addSelectColumns($criteria);
		$startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

		CountriesPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + CountriesPeer::NUM_HYDRATE_COLUMNS;

		CountriesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + CountriesPeer::NUM_HYDRATE_COLUMNS;

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
				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Countries rows

				$key2 = CountriesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CountriesPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CountriesPeer::getOMClass(false);

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
	
						$cls = CountriesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CountriesPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (Orders) to the collection in $obj3 (Countries)
				$obj3->addOrdersRelatedByDeliveryCountriesId($obj1);

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
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCountriesRelatedByBillingCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		OrdersPeer::addSelectColumns($criteria);
		$startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

		CustomersPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

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
				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Customers rows

				$key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CustomersPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CustomersPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CustomersPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (Orders) to the collection in $obj2 (Customers)
				$obj2->addOrders($obj1);

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
	 * @return     array Array of Orders objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCountriesRelatedByDeliveryCountriesId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		OrdersPeer::addSelectColumns($criteria);
		$startcol2 = OrdersPeer::NUM_HYDRATE_COLUMNS;

		CustomersPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + CustomersPeer::NUM_HYDRATE_COLUMNS;

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
				$cls = OrdersPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				OrdersPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Customers rows

				$key2 = CustomersPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CustomersPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CustomersPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CustomersPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (Orders) to the collection in $obj2 (Customers)
				$obj2->addOrders($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseOrdersPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseOrdersPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new OrdersTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean $withPrefix Whether or not to return the path with the class name
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? OrdersPeer::CLASS_DEFAULT : OrdersPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a Orders or Criteria object.
	 *
	 * @param      mixed $values Criteria or Orders object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
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
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
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
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

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
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the orders table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
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
		} catch (PropelException $e) {
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
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
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
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(OrdersPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				OrdersPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			OrdersPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
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
	 * @param      Orders $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
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
				if ($tableMap->containsColumn($colName)) {
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
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     Orders
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
	 * @throws     PropelException Any exceptions caught during processing will be
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


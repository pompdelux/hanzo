<?php

namespace Hanzo\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \DateTimeZone;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\OrdersAttributes;
use Hanzo\Model\OrdersAttributesQuery;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersStateLog;
use Hanzo\Model\OrdersStateLogQuery;
use Hanzo\Model\OrdersSyncLog;
use Hanzo\Model\OrdersSyncLogQuery;

/**
 * Base class that represents a row from the 'orders' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseOrders extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\OrdersPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        OrdersPeer
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
	 * The value for the session_id field.
	 * @var        string
	 */
	protected $session_id;

	/**
	 * The value for the payment_gateway_id field.
	 * @var        int
	 */
	protected $payment_gateway_id;

	/**
	 * The value for the state field.
	 * Note: this column has a database default value of: -3
	 * @var        int
	 */
	protected $state;

	/**
	 * The value for the in_edit field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $in_edit;

	/**
	 * The value for the customers_id field.
	 * @var        int
	 */
	protected $customers_id;

	/**
	 * The value for the first_name field.
	 * @var        string
	 */
	protected $first_name;

	/**
	 * The value for the last_name field.
	 * @var        string
	 */
	protected $last_name;

	/**
	 * The value for the email field.
	 * @var        string
	 */
	protected $email;

	/**
	 * The value for the phone field.
	 * @var        string
	 */
	protected $phone;

	/**
	 * The value for the languages_id field.
	 * @var        int
	 */
	protected $languages_id;

	/**
	 * The value for the currency_id field.
	 * @var        int
	 */
	protected $currency_id;

	/**
	 * The value for the billing_first_name field.
	 * @var        string
	 */
	protected $billing_first_name;

	/**
	 * The value for the billing_last_name field.
	 * @var        string
	 */
	protected $billing_last_name;

	/**
	 * The value for the billing_address_line_1 field.
	 * @var        string
	 */
	protected $billing_address_line_1;

	/**
	 * The value for the billing_address_line_2 field.
	 * @var        string
	 */
	protected $billing_address_line_2;

	/**
	 * The value for the billing_postal_code field.
	 * @var        string
	 */
	protected $billing_postal_code;

	/**
	 * The value for the billing_city field.
	 * @var        string
	 */
	protected $billing_city;

	/**
	 * The value for the billing_country field.
	 * @var        string
	 */
	protected $billing_country;

	/**
	 * The value for the billing_countries_id field.
	 * @var        int
	 */
	protected $billing_countries_id;

	/**
	 * The value for the billing_state_province field.
	 * @var        string
	 */
	protected $billing_state_province;

	/**
	 * The value for the billing_company_name field.
	 * @var        string
	 */
	protected $billing_company_name;

	/**
	 * The value for the billing_method field.
	 * @var        string
	 */
	protected $billing_method;

	/**
	 * The value for the delivery_first_name field.
	 * @var        string
	 */
	protected $delivery_first_name;

	/**
	 * The value for the delivery_last_name field.
	 * @var        string
	 */
	protected $delivery_last_name;

	/**
	 * The value for the delivery_address_line_1 field.
	 * @var        string
	 */
	protected $delivery_address_line_1;

	/**
	 * The value for the delivery_address_line_2 field.
	 * @var        string
	 */
	protected $delivery_address_line_2;

	/**
	 * The value for the delivery_postal_code field.
	 * @var        string
	 */
	protected $delivery_postal_code;

	/**
	 * The value for the delivery_city field.
	 * @var        string
	 */
	protected $delivery_city;

	/**
	 * The value for the delivery_country field.
	 * @var        string
	 */
	protected $delivery_country;

	/**
	 * The value for the delivery_countries_id field.
	 * @var        int
	 */
	protected $delivery_countries_id;

	/**
	 * The value for the delivery_state_province field.
	 * @var        string
	 */
	protected $delivery_state_province;

	/**
	 * The value for the delivery_company_name field.
	 * @var        string
	 */
	protected $delivery_company_name;

	/**
	 * The value for the delivery_method field.
	 * @var        string
	 */
	protected $delivery_method;

	/**
	 * The value for the finished_at field.
	 * @var        string
	 */
	protected $finished_at;

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
	 * @var        Countries
	 */
	protected $aCountriesRelatedByBillingCountriesId;

	/**
	 * @var        Countries
	 */
	protected $aCountriesRelatedByDeliveryCountriesId;

	/**
	 * @var        array OrdersAttributes[] Collection to store aggregation of OrdersAttributes objects.
	 */
	protected $collOrdersAttributess;

	/**
	 * @var        array OrdersLines[] Collection to store aggregation of OrdersLines objects.
	 */
	protected $collOrdersLiness;

	/**
	 * @var        array OrdersStateLog[] Collection to store aggregation of OrdersStateLog objects.
	 */
	protected $collOrdersStateLogs;

	/**
	 * @var        array OrdersSyncLog[] Collection to store aggregation of OrdersSyncLog objects.
	 */
	protected $collOrdersSyncLogs;

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
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $ordersAttributessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $ordersLinessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $ordersStateLogsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $ordersSyncLogsScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->state = -3;
		$this->in_edit = false;
	}

	/**
	 * Initializes internal state of BaseOrders object.
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
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [session_id] column value.
	 * 
	 * @return     string
	 */
	public function getSessionId()
	{
		return $this->session_id;
	}

	/**
	 * Get the [payment_gateway_id] column value.
	 * 
	 * @return     int
	 */
	public function getPaymentGatewayId()
	{
		return $this->payment_gateway_id;
	}

	/**
	 * Get the [state] column value.
	 * 
	 * @return     int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get the [in_edit] column value.
	 * 
	 * @return     boolean
	 */
	public function getInEdit()
	{
		return $this->in_edit;
	}

	/**
	 * Get the [customers_id] column value.
	 * 
	 * @return     int
	 */
	public function getCustomersId()
	{
		return $this->customers_id;
	}

	/**
	 * Get the [first_name] column value.
	 * 
	 * @return     string
	 */
	public function getFirstName()
	{
		return $this->first_name;
	}

	/**
	 * Get the [last_name] column value.
	 * 
	 * @return     string
	 */
	public function getLastName()
	{
		return $this->last_name;
	}

	/**
	 * Get the [email] column value.
	 * 
	 * @return     string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the [phone] column value.
	 * 
	 * @return     string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * Get the [languages_id] column value.
	 * 
	 * @return     int
	 */
	public function getLanguagesId()
	{
		return $this->languages_id;
	}

	/**
	 * Get the [currency_id] column value.
	 * 
	 * @return     int
	 */
	public function getCurrencyId()
	{
		return $this->currency_id;
	}

	/**
	 * Get the [billing_first_name] column value.
	 * 
	 * @return     string
	 */
	public function getBillingFirstName()
	{
		return $this->billing_first_name;
	}

	/**
	 * Get the [billing_last_name] column value.
	 * 
	 * @return     string
	 */
	public function getBillingLastName()
	{
		return $this->billing_last_name;
	}

	/**
	 * Get the [billing_address_line_1] column value.
	 * 
	 * @return     string
	 */
	public function getBillingAddressLine1()
	{
		return $this->billing_address_line_1;
	}

	/**
	 * Get the [billing_address_line_2] column value.
	 * 
	 * @return     string
	 */
	public function getBillingAddressLine2()
	{
		return $this->billing_address_line_2;
	}

	/**
	 * Get the [billing_postal_code] column value.
	 * 
	 * @return     string
	 */
	public function getBillingPostalCode()
	{
		return $this->billing_postal_code;
	}

	/**
	 * Get the [billing_city] column value.
	 * 
	 * @return     string
	 */
	public function getBillingCity()
	{
		return $this->billing_city;
	}

	/**
	 * Get the [billing_country] column value.
	 * 
	 * @return     string
	 */
	public function getBillingCountry()
	{
		return $this->billing_country;
	}

	/**
	 * Get the [billing_countries_id] column value.
	 * 
	 * @return     int
	 */
	public function getBillingCountriesId()
	{
		return $this->billing_countries_id;
	}

	/**
	 * Get the [billing_state_province] column value.
	 * 
	 * @return     string
	 */
	public function getBillingStateProvince()
	{
		return $this->billing_state_province;
	}

	/**
	 * Get the [billing_company_name] column value.
	 * 
	 * @return     string
	 */
	public function getBillingCompanyName()
	{
		return $this->billing_company_name;
	}

	/**
	 * Get the [billing_method] column value.
	 * 
	 * @return     string
	 */
	public function getBillingMethod()
	{
		return $this->billing_method;
	}

	/**
	 * Get the [delivery_first_name] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryFirstName()
	{
		return $this->delivery_first_name;
	}

	/**
	 * Get the [delivery_last_name] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryLastName()
	{
		return $this->delivery_last_name;
	}

	/**
	 * Get the [delivery_address_line_1] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryAddressLine1()
	{
		return $this->delivery_address_line_1;
	}

	/**
	 * Get the [delivery_address_line_2] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryAddressLine2()
	{
		return $this->delivery_address_line_2;
	}

	/**
	 * Get the [delivery_postal_code] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryPostalCode()
	{
		return $this->delivery_postal_code;
	}

	/**
	 * Get the [delivery_city] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryCity()
	{
		return $this->delivery_city;
	}

	/**
	 * Get the [delivery_country] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryCountry()
	{
		return $this->delivery_country;
	}

	/**
	 * Get the [delivery_countries_id] column value.
	 * 
	 * @return     int
	 */
	public function getDeliveryCountriesId()
	{
		return $this->delivery_countries_id;
	}

	/**
	 * Get the [delivery_state_province] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryStateProvince()
	{
		return $this->delivery_state_province;
	}

	/**
	 * Get the [delivery_company_name] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryCompanyName()
	{
		return $this->delivery_company_name;
	}

	/**
	 * Get the [delivery_method] column value.
	 * 
	 * @return     string
	 */
	public function getDeliveryMethod()
	{
		return $this->delivery_method;
	}

	/**
	 * Get the [optionally formatted] temporal [finished_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getFinishedAt($format = NULL)
	{
		if ($this->finished_at === null) {
			return null;
		}


		if ($this->finished_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->finished_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->finished_at, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = NULL)
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = NULL)
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = OrdersPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [session_id] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setSessionId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->session_id !== $v) {
			$this->session_id = $v;
			$this->modifiedColumns[] = OrdersPeer::SESSION_ID;
		}

		return $this;
	} // setSessionId()

	/**
	 * Set the value of [payment_gateway_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setPaymentGatewayId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->payment_gateway_id !== $v) {
			$this->payment_gateway_id = $v;
			$this->modifiedColumns[] = OrdersPeer::PAYMENT_GATEWAY_ID;
		}

		return $this;
	} // setPaymentGatewayId()

	/**
	 * Set the value of [state] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setState($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->state !== $v) {
			$this->state = $v;
			$this->modifiedColumns[] = OrdersPeer::STATE;
		}

		return $this;
	} // setState()

	/**
	 * Sets the value of the [in_edit] column.
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * 
	 * @param      boolean|integer|string $v The new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setInEdit($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->in_edit !== $v) {
			$this->in_edit = $v;
			$this->modifiedColumns[] = OrdersPeer::IN_EDIT;
		}

		return $this;
	} // setInEdit()

	/**
	 * Set the value of [customers_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setCustomersId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->customers_id !== $v) {
			$this->customers_id = $v;
			$this->modifiedColumns[] = OrdersPeer::CUSTOMERS_ID;
		}

		return $this;
	} // setCustomersId()

	/**
	 * Set the value of [first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setFirstName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->first_name !== $v) {
			$this->first_name = $v;
			$this->modifiedColumns[] = OrdersPeer::FIRST_NAME;
		}

		return $this;
	} // setFirstName()

	/**
	 * Set the value of [last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setLastName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->last_name !== $v) {
			$this->last_name = $v;
			$this->modifiedColumns[] = OrdersPeer::LAST_NAME;
		}

		return $this;
	} // setLastName()

	/**
	 * Set the value of [email] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = OrdersPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [phone] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setPhone($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->phone !== $v) {
			$this->phone = $v;
			$this->modifiedColumns[] = OrdersPeer::PHONE;
		}

		return $this;
	} // setPhone()

	/**
	 * Set the value of [languages_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setLanguagesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->languages_id !== $v) {
			$this->languages_id = $v;
			$this->modifiedColumns[] = OrdersPeer::LANGUAGES_ID;
		}

		return $this;
	} // setLanguagesId()

	/**
	 * Set the value of [currency_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setCurrencyId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->currency_id !== $v) {
			$this->currency_id = $v;
			$this->modifiedColumns[] = OrdersPeer::CURRENCY_ID;
		}

		return $this;
	} // setCurrencyId()

	/**
	 * Set the value of [billing_first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingFirstName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_first_name !== $v) {
			$this->billing_first_name = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_FIRST_NAME;
		}

		return $this;
	} // setBillingFirstName()

	/**
	 * Set the value of [billing_last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingLastName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_last_name !== $v) {
			$this->billing_last_name = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_LAST_NAME;
		}

		return $this;
	} // setBillingLastName()

	/**
	 * Set the value of [billing_address_line_1] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingAddressLine1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_address_line_1 !== $v) {
			$this->billing_address_line_1 = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_ADDRESS_LINE_1;
		}

		return $this;
	} // setBillingAddressLine1()

	/**
	 * Set the value of [billing_address_line_2] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingAddressLine2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_address_line_2 !== $v) {
			$this->billing_address_line_2 = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_ADDRESS_LINE_2;
		}

		return $this;
	} // setBillingAddressLine2()

	/**
	 * Set the value of [billing_postal_code] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingPostalCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_postal_code !== $v) {
			$this->billing_postal_code = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_POSTAL_CODE;
		}

		return $this;
	} // setBillingPostalCode()

	/**
	 * Set the value of [billing_city] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingCity($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_city !== $v) {
			$this->billing_city = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_CITY;
		}

		return $this;
	} // setBillingCity()

	/**
	 * Set the value of [billing_country] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingCountry($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_country !== $v) {
			$this->billing_country = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_COUNTRY;
		}

		return $this;
	} // setBillingCountry()

	/**
	 * Set the value of [billing_countries_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingCountriesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->billing_countries_id !== $v) {
			$this->billing_countries_id = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_COUNTRIES_ID;
		}

		if ($this->aCountriesRelatedByBillingCountriesId !== null && $this->aCountriesRelatedByBillingCountriesId->getId() !== $v) {
			$this->aCountriesRelatedByBillingCountriesId = null;
		}

		return $this;
	} // setBillingCountriesId()

	/**
	 * Set the value of [billing_state_province] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingStateProvince($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_state_province !== $v) {
			$this->billing_state_province = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_STATE_PROVINCE;
		}

		return $this;
	} // setBillingStateProvince()

	/**
	 * Set the value of [billing_company_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingCompanyName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_company_name !== $v) {
			$this->billing_company_name = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_COMPANY_NAME;
		}

		return $this;
	} // setBillingCompanyName()

	/**
	 * Set the value of [billing_method] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setBillingMethod($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_method !== $v) {
			$this->billing_method = $v;
			$this->modifiedColumns[] = OrdersPeer::BILLING_METHOD;
		}

		return $this;
	} // setBillingMethod()

	/**
	 * Set the value of [delivery_first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryFirstName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_first_name !== $v) {
			$this->delivery_first_name = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_FIRST_NAME;
		}

		return $this;
	} // setDeliveryFirstName()

	/**
	 * Set the value of [delivery_last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryLastName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_last_name !== $v) {
			$this->delivery_last_name = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_LAST_NAME;
		}

		return $this;
	} // setDeliveryLastName()

	/**
	 * Set the value of [delivery_address_line_1] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryAddressLine1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_address_line_1 !== $v) {
			$this->delivery_address_line_1 = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_ADDRESS_LINE_1;
		}

		return $this;
	} // setDeliveryAddressLine1()

	/**
	 * Set the value of [delivery_address_line_2] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryAddressLine2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_address_line_2 !== $v) {
			$this->delivery_address_line_2 = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_ADDRESS_LINE_2;
		}

		return $this;
	} // setDeliveryAddressLine2()

	/**
	 * Set the value of [delivery_postal_code] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryPostalCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_postal_code !== $v) {
			$this->delivery_postal_code = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_POSTAL_CODE;
		}

		return $this;
	} // setDeliveryPostalCode()

	/**
	 * Set the value of [delivery_city] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryCity($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_city !== $v) {
			$this->delivery_city = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_CITY;
		}

		return $this;
	} // setDeliveryCity()

	/**
	 * Set the value of [delivery_country] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryCountry($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_country !== $v) {
			$this->delivery_country = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_COUNTRY;
		}

		return $this;
	} // setDeliveryCountry()

	/**
	 * Set the value of [delivery_countries_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryCountriesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->delivery_countries_id !== $v) {
			$this->delivery_countries_id = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_COUNTRIES_ID;
		}

		if ($this->aCountriesRelatedByDeliveryCountriesId !== null && $this->aCountriesRelatedByDeliveryCountriesId->getId() !== $v) {
			$this->aCountriesRelatedByDeliveryCountriesId = null;
		}

		return $this;
	} // setDeliveryCountriesId()

	/**
	 * Set the value of [delivery_state_province] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryStateProvince($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_state_province !== $v) {
			$this->delivery_state_province = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_STATE_PROVINCE;
		}

		return $this;
	} // setDeliveryStateProvince()

	/**
	 * Set the value of [delivery_company_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryCompanyName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_company_name !== $v) {
			$this->delivery_company_name = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_COMPANY_NAME;
		}

		return $this;
	} // setDeliveryCompanyName()

	/**
	 * Set the value of [delivery_method] column.
	 * 
	 * @param      string $v new value
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setDeliveryMethod($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_method !== $v) {
			$this->delivery_method = $v;
			$this->modifiedColumns[] = OrdersPeer::DELIVERY_METHOD;
		}

		return $this;
	} // setDeliveryMethod()

	/**
	 * Sets the value of [finished_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setFinishedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->finished_at !== null || $dt !== null) {
			$currentDateAsString = ($this->finished_at !== null && $tmpDt = new DateTime($this->finished_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->finished_at = $newDateAsString;
				$this->modifiedColumns[] = OrdersPeer::FINISHED_AT;
			}
		} // if either are not null

		return $this;
	} // setFinishedAt()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = OrdersPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Orders The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = OrdersPeer::UPDATED_AT;
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
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->state !== -3) {
				return false;
			}

			if ($this->in_edit !== false) {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
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
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->session_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->payment_gateway_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->state = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->in_edit = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
			$this->customers_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->first_name = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->last_name = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->email = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->phone = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->languages_id = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->currency_id = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->billing_first_name = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->billing_last_name = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->billing_address_line_1 = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->billing_address_line_2 = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->billing_postal_code = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->billing_city = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->billing_country = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->billing_countries_id = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->billing_state_province = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->billing_company_name = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->billing_method = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->delivery_first_name = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->delivery_last_name = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->delivery_address_line_1 = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->delivery_address_line_2 = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->delivery_postal_code = ($row[$startcol + 27] !== null) ? (string) $row[$startcol + 27] : null;
			$this->delivery_city = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->delivery_country = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->delivery_countries_id = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
			$this->delivery_state_province = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
			$this->delivery_company_name = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
			$this->delivery_method = ($row[$startcol + 33] !== null) ? (string) $row[$startcol + 33] : null;
			$this->finished_at = ($row[$startcol + 34] !== null) ? (string) $row[$startcol + 34] : null;
			$this->created_at = ($row[$startcol + 35] !== null) ? (string) $row[$startcol + 35] : null;
			$this->updated_at = ($row[$startcol + 36] !== null) ? (string) $row[$startcol + 36] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 37; // 37 = OrdersPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Orders object", $e);
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
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aCountriesRelatedByBillingCountriesId !== null && $this->billing_countries_id !== $this->aCountriesRelatedByBillingCountriesId->getId()) {
			$this->aCountriesRelatedByBillingCountriesId = null;
		}
		if ($this->aCountriesRelatedByDeliveryCountriesId !== null && $this->delivery_countries_id !== $this->aCountriesRelatedByDeliveryCountriesId->getId()) {
			$this->aCountriesRelatedByDeliveryCountriesId = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
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
			$con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = OrdersPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCountriesRelatedByBillingCountriesId = null;
			$this->aCountriesRelatedByDeliveryCountriesId = null;
			$this->collOrdersAttributess = null;

			$this->collOrdersLiness = null;

			$this->collOrdersStateLogs = null;

			$this->collOrdersSyncLogs = null;

		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = OrdersQuery::create()
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
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(OrdersPeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(OrdersPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(OrdersPeer::UPDATED_AT)) {
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
				OrdersPeer::addInstanceToPool($this);
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
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
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

			if ($this->aCountriesRelatedByBillingCountriesId !== null) {
				if ($this->aCountriesRelatedByBillingCountriesId->isModified() || $this->aCountriesRelatedByBillingCountriesId->isNew()) {
					$affectedRows += $this->aCountriesRelatedByBillingCountriesId->save($con);
				}
				$this->setCountriesRelatedByBillingCountriesId($this->aCountriesRelatedByBillingCountriesId);
			}

			if ($this->aCountriesRelatedByDeliveryCountriesId !== null) {
				if ($this->aCountriesRelatedByDeliveryCountriesId->isModified() || $this->aCountriesRelatedByDeliveryCountriesId->isNew()) {
					$affectedRows += $this->aCountriesRelatedByDeliveryCountriesId->save($con);
				}
				$this->setCountriesRelatedByDeliveryCountriesId($this->aCountriesRelatedByDeliveryCountriesId);
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

			if ($this->ordersAttributessScheduledForDeletion !== null) {
				if (!$this->ordersAttributessScheduledForDeletion->isEmpty()) {
					OrdersAttributesQuery::create()
						->filterByPrimaryKeys($this->ordersAttributessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ordersAttributessScheduledForDeletion = null;
				}
			}

			if ($this->collOrdersAttributess !== null) {
				foreach ($this->collOrdersAttributess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->ordersLinessScheduledForDeletion !== null) {
				if (!$this->ordersLinessScheduledForDeletion->isEmpty()) {
					OrdersLinesQuery::create()
						->filterByPrimaryKeys($this->ordersLinessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ordersLinessScheduledForDeletion = null;
				}
			}

			if ($this->collOrdersLiness !== null) {
				foreach ($this->collOrdersLiness as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->ordersStateLogsScheduledForDeletion !== null) {
				if (!$this->ordersStateLogsScheduledForDeletion->isEmpty()) {
					OrdersStateLogQuery::create()
						->filterByPrimaryKeys($this->ordersStateLogsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ordersStateLogsScheduledForDeletion = null;
				}
			}

			if ($this->collOrdersStateLogs !== null) {
				foreach ($this->collOrdersStateLogs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->ordersSyncLogsScheduledForDeletion !== null) {
				if (!$this->ordersSyncLogsScheduledForDeletion->isEmpty()) {
					OrdersSyncLogQuery::create()
						->filterByPrimaryKeys($this->ordersSyncLogsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ordersSyncLogsScheduledForDeletion = null;
				}
			}

			if ($this->collOrdersSyncLogs !== null) {
				foreach ($this->collOrdersSyncLogs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
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
	 * @param      PropelPDO $con
	 *
	 * @throws     PropelException
	 * @see        doSave()
	 */
	protected function doInsert(PropelPDO $con)
	{
		$modifiedColumns = array();
		$index = 0;

		$this->modifiedColumns[] = OrdersPeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . OrdersPeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(OrdersPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = '`ID`';
		}
		if ($this->isColumnModified(OrdersPeer::SESSION_ID)) {
			$modifiedColumns[':p' . $index++]  = '`SESSION_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::PAYMENT_GATEWAY_ID)) {
			$modifiedColumns[':p' . $index++]  = '`PAYMENT_GATEWAY_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::STATE)) {
			$modifiedColumns[':p' . $index++]  = '`STATE`';
		}
		if ($this->isColumnModified(OrdersPeer::IN_EDIT)) {
			$modifiedColumns[':p' . $index++]  = '`IN_EDIT`';
		}
		if ($this->isColumnModified(OrdersPeer::CUSTOMERS_ID)) {
			$modifiedColumns[':p' . $index++]  = '`CUSTOMERS_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::FIRST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`FIRST_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::LAST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`LAST_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::EMAIL)) {
			$modifiedColumns[':p' . $index++]  = '`EMAIL`';
		}
		if ($this->isColumnModified(OrdersPeer::PHONE)) {
			$modifiedColumns[':p' . $index++]  = '`PHONE`';
		}
		if ($this->isColumnModified(OrdersPeer::LANGUAGES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`LANGUAGES_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::CURRENCY_ID)) {
			$modifiedColumns[':p' . $index++]  = '`CURRENCY_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_FIRST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_FIRST_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_LAST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_LAST_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_ADDRESS_LINE_1)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_ADDRESS_LINE_1`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_ADDRESS_LINE_2)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_ADDRESS_LINE_2`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_POSTAL_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_POSTAL_CODE`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_CITY)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_CITY`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_COUNTRY)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_COUNTRY`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_COUNTRIES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_COUNTRIES_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_STATE_PROVINCE)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_STATE_PROVINCE`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_COMPANY_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_COMPANY_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::BILLING_METHOD)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_METHOD`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_FIRST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_FIRST_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_LAST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_LAST_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_ADDRESS_LINE_1)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_ADDRESS_LINE_1`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_ADDRESS_LINE_2)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_ADDRESS_LINE_2`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_POSTAL_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_POSTAL_CODE`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_CITY)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_CITY`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_COUNTRY)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_COUNTRY`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_COUNTRIES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_COUNTRIES_ID`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_STATE_PROVINCE)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_STATE_PROVINCE`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_COMPANY_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_COMPANY_NAME`';
		}
		if ($this->isColumnModified(OrdersPeer::DELIVERY_METHOD)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_METHOD`';
		}
		if ($this->isColumnModified(OrdersPeer::FINISHED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`FINISHED_AT`';
		}
		if ($this->isColumnModified(OrdersPeer::CREATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`CREATED_AT`';
		}
		if ($this->isColumnModified(OrdersPeer::UPDATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`UPDATED_AT`';
		}

		$sql = sprintf(
			'INSERT INTO `orders` (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case '`ID`':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
						break;
					case '`SESSION_ID`':
						$stmt->bindValue($identifier, $this->session_id, PDO::PARAM_STR);
						break;
					case '`PAYMENT_GATEWAY_ID`':
						$stmt->bindValue($identifier, $this->payment_gateway_id, PDO::PARAM_INT);
						break;
					case '`STATE`':
						$stmt->bindValue($identifier, $this->state, PDO::PARAM_INT);
						break;
					case '`IN_EDIT`':
						$stmt->bindValue($identifier, (int) $this->in_edit, PDO::PARAM_INT);
						break;
					case '`CUSTOMERS_ID`':
						$stmt->bindValue($identifier, $this->customers_id, PDO::PARAM_INT);
						break;
					case '`FIRST_NAME`':
						$stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
						break;
					case '`LAST_NAME`':
						$stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
						break;
					case '`EMAIL`':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
						break;
					case '`PHONE`':
						$stmt->bindValue($identifier, $this->phone, PDO::PARAM_STR);
						break;
					case '`LANGUAGES_ID`':
						$stmt->bindValue($identifier, $this->languages_id, PDO::PARAM_INT);
						break;
					case '`CURRENCY_ID`':
						$stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
						break;
					case '`BILLING_FIRST_NAME`':
						$stmt->bindValue($identifier, $this->billing_first_name, PDO::PARAM_STR);
						break;
					case '`BILLING_LAST_NAME`':
						$stmt->bindValue($identifier, $this->billing_last_name, PDO::PARAM_STR);
						break;
					case '`BILLING_ADDRESS_LINE_1`':
						$stmt->bindValue($identifier, $this->billing_address_line_1, PDO::PARAM_STR);
						break;
					case '`BILLING_ADDRESS_LINE_2`':
						$stmt->bindValue($identifier, $this->billing_address_line_2, PDO::PARAM_STR);
						break;
					case '`BILLING_POSTAL_CODE`':
						$stmt->bindValue($identifier, $this->billing_postal_code, PDO::PARAM_STR);
						break;
					case '`BILLING_CITY`':
						$stmt->bindValue($identifier, $this->billing_city, PDO::PARAM_STR);
						break;
					case '`BILLING_COUNTRY`':
						$stmt->bindValue($identifier, $this->billing_country, PDO::PARAM_STR);
						break;
					case '`BILLING_COUNTRIES_ID`':
						$stmt->bindValue($identifier, $this->billing_countries_id, PDO::PARAM_INT);
						break;
					case '`BILLING_STATE_PROVINCE`':
						$stmt->bindValue($identifier, $this->billing_state_province, PDO::PARAM_STR);
						break;
					case '`BILLING_COMPANY_NAME`':
						$stmt->bindValue($identifier, $this->billing_company_name, PDO::PARAM_STR);
						break;
					case '`BILLING_METHOD`':
						$stmt->bindValue($identifier, $this->billing_method, PDO::PARAM_STR);
						break;
					case '`DELIVERY_FIRST_NAME`':
						$stmt->bindValue($identifier, $this->delivery_first_name, PDO::PARAM_STR);
						break;
					case '`DELIVERY_LAST_NAME`':
						$stmt->bindValue($identifier, $this->delivery_last_name, PDO::PARAM_STR);
						break;
					case '`DELIVERY_ADDRESS_LINE_1`':
						$stmt->bindValue($identifier, $this->delivery_address_line_1, PDO::PARAM_STR);
						break;
					case '`DELIVERY_ADDRESS_LINE_2`':
						$stmt->bindValue($identifier, $this->delivery_address_line_2, PDO::PARAM_STR);
						break;
					case '`DELIVERY_POSTAL_CODE`':
						$stmt->bindValue($identifier, $this->delivery_postal_code, PDO::PARAM_STR);
						break;
					case '`DELIVERY_CITY`':
						$stmt->bindValue($identifier, $this->delivery_city, PDO::PARAM_STR);
						break;
					case '`DELIVERY_COUNTRY`':
						$stmt->bindValue($identifier, $this->delivery_country, PDO::PARAM_STR);
						break;
					case '`DELIVERY_COUNTRIES_ID`':
						$stmt->bindValue($identifier, $this->delivery_countries_id, PDO::PARAM_INT);
						break;
					case '`DELIVERY_STATE_PROVINCE`':
						$stmt->bindValue($identifier, $this->delivery_state_province, PDO::PARAM_STR);
						break;
					case '`DELIVERY_COMPANY_NAME`':
						$stmt->bindValue($identifier, $this->delivery_company_name, PDO::PARAM_STR);
						break;
					case '`DELIVERY_METHOD`':
						$stmt->bindValue($identifier, $this->delivery_method, PDO::PARAM_STR);
						break;
					case '`FINISHED_AT`':
						$stmt->bindValue($identifier, $this->finished_at, PDO::PARAM_STR);
						break;
					case '`CREATED_AT`':
						$stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
						break;
					case '`UPDATED_AT`':
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
	 * @param      PropelPDO $con
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
	 * @return     array ValidationFailed[]
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
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
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
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
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

			if ($this->aCountriesRelatedByBillingCountriesId !== null) {
				if (!$this->aCountriesRelatedByBillingCountriesId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCountriesRelatedByBillingCountriesId->getValidationFailures());
				}
			}

			if ($this->aCountriesRelatedByDeliveryCountriesId !== null) {
				if (!$this->aCountriesRelatedByDeliveryCountriesId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCountriesRelatedByDeliveryCountriesId->getValidationFailures());
				}
			}


			if (($retval = OrdersPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collOrdersAttributess !== null) {
					foreach ($this->collOrdersAttributess as $referrerFK) {
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

				if ($this->collOrdersStateLogs !== null) {
					foreach ($this->collOrdersStateLogs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collOrdersSyncLogs !== null) {
					foreach ($this->collOrdersSyncLogs as $referrerFK) {
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
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = OrdersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getSessionId();
				break;
			case 2:
				return $this->getPaymentGatewayId();
				break;
			case 3:
				return $this->getState();
				break;
			case 4:
				return $this->getInEdit();
				break;
			case 5:
				return $this->getCustomersId();
				break;
			case 6:
				return $this->getFirstName();
				break;
			case 7:
				return $this->getLastName();
				break;
			case 8:
				return $this->getEmail();
				break;
			case 9:
				return $this->getPhone();
				break;
			case 10:
				return $this->getLanguagesId();
				break;
			case 11:
				return $this->getCurrencyId();
				break;
			case 12:
				return $this->getBillingFirstName();
				break;
			case 13:
				return $this->getBillingLastName();
				break;
			case 14:
				return $this->getBillingAddressLine1();
				break;
			case 15:
				return $this->getBillingAddressLine2();
				break;
			case 16:
				return $this->getBillingPostalCode();
				break;
			case 17:
				return $this->getBillingCity();
				break;
			case 18:
				return $this->getBillingCountry();
				break;
			case 19:
				return $this->getBillingCountriesId();
				break;
			case 20:
				return $this->getBillingStateProvince();
				break;
			case 21:
				return $this->getBillingCompanyName();
				break;
			case 22:
				return $this->getBillingMethod();
				break;
			case 23:
				return $this->getDeliveryFirstName();
				break;
			case 24:
				return $this->getDeliveryLastName();
				break;
			case 25:
				return $this->getDeliveryAddressLine1();
				break;
			case 26:
				return $this->getDeliveryAddressLine2();
				break;
			case 27:
				return $this->getDeliveryPostalCode();
				break;
			case 28:
				return $this->getDeliveryCity();
				break;
			case 29:
				return $this->getDeliveryCountry();
				break;
			case 30:
				return $this->getDeliveryCountriesId();
				break;
			case 31:
				return $this->getDeliveryStateProvince();
				break;
			case 32:
				return $this->getDeliveryCompanyName();
				break;
			case 33:
				return $this->getDeliveryMethod();
				break;
			case 34:
				return $this->getFinishedAt();
				break;
			case 35:
				return $this->getCreatedAt();
				break;
			case 36:
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
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		if (isset($alreadyDumpedObjects['Orders'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Orders'][$this->getPrimaryKey()] = true;
		$keys = OrdersPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getSessionId(),
			$keys[2] => $this->getPaymentGatewayId(),
			$keys[3] => $this->getState(),
			$keys[4] => $this->getInEdit(),
			$keys[5] => $this->getCustomersId(),
			$keys[6] => $this->getFirstName(),
			$keys[7] => $this->getLastName(),
			$keys[8] => $this->getEmail(),
			$keys[9] => $this->getPhone(),
			$keys[10] => $this->getLanguagesId(),
			$keys[11] => $this->getCurrencyId(),
			$keys[12] => $this->getBillingFirstName(),
			$keys[13] => $this->getBillingLastName(),
			$keys[14] => $this->getBillingAddressLine1(),
			$keys[15] => $this->getBillingAddressLine2(),
			$keys[16] => $this->getBillingPostalCode(),
			$keys[17] => $this->getBillingCity(),
			$keys[18] => $this->getBillingCountry(),
			$keys[19] => $this->getBillingCountriesId(),
			$keys[20] => $this->getBillingStateProvince(),
			$keys[21] => $this->getBillingCompanyName(),
			$keys[22] => $this->getBillingMethod(),
			$keys[23] => $this->getDeliveryFirstName(),
			$keys[24] => $this->getDeliveryLastName(),
			$keys[25] => $this->getDeliveryAddressLine1(),
			$keys[26] => $this->getDeliveryAddressLine2(),
			$keys[27] => $this->getDeliveryPostalCode(),
			$keys[28] => $this->getDeliveryCity(),
			$keys[29] => $this->getDeliveryCountry(),
			$keys[30] => $this->getDeliveryCountriesId(),
			$keys[31] => $this->getDeliveryStateProvince(),
			$keys[32] => $this->getDeliveryCompanyName(),
			$keys[33] => $this->getDeliveryMethod(),
			$keys[34] => $this->getFinishedAt(),
			$keys[35] => $this->getCreatedAt(),
			$keys[36] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCountriesRelatedByBillingCountriesId) {
				$result['CountriesRelatedByBillingCountriesId'] = $this->aCountriesRelatedByBillingCountriesId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aCountriesRelatedByDeliveryCountriesId) {
				$result['CountriesRelatedByDeliveryCountriesId'] = $this->aCountriesRelatedByDeliveryCountriesId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collOrdersAttributess) {
				$result['OrdersAttributess'] = $this->collOrdersAttributess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collOrdersLiness) {
				$result['OrdersLiness'] = $this->collOrdersLiness->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collOrdersStateLogs) {
				$result['OrdersStateLogs'] = $this->collOrdersStateLogs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collOrdersSyncLogs) {
				$result['OrdersSyncLogs'] = $this->collOrdersSyncLogs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
		}
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = OrdersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setSessionId($value);
				break;
			case 2:
				$this->setPaymentGatewayId($value);
				break;
			case 3:
				$this->setState($value);
				break;
			case 4:
				$this->setInEdit($value);
				break;
			case 5:
				$this->setCustomersId($value);
				break;
			case 6:
				$this->setFirstName($value);
				break;
			case 7:
				$this->setLastName($value);
				break;
			case 8:
				$this->setEmail($value);
				break;
			case 9:
				$this->setPhone($value);
				break;
			case 10:
				$this->setLanguagesId($value);
				break;
			case 11:
				$this->setCurrencyId($value);
				break;
			case 12:
				$this->setBillingFirstName($value);
				break;
			case 13:
				$this->setBillingLastName($value);
				break;
			case 14:
				$this->setBillingAddressLine1($value);
				break;
			case 15:
				$this->setBillingAddressLine2($value);
				break;
			case 16:
				$this->setBillingPostalCode($value);
				break;
			case 17:
				$this->setBillingCity($value);
				break;
			case 18:
				$this->setBillingCountry($value);
				break;
			case 19:
				$this->setBillingCountriesId($value);
				break;
			case 20:
				$this->setBillingStateProvince($value);
				break;
			case 21:
				$this->setBillingCompanyName($value);
				break;
			case 22:
				$this->setBillingMethod($value);
				break;
			case 23:
				$this->setDeliveryFirstName($value);
				break;
			case 24:
				$this->setDeliveryLastName($value);
				break;
			case 25:
				$this->setDeliveryAddressLine1($value);
				break;
			case 26:
				$this->setDeliveryAddressLine2($value);
				break;
			case 27:
				$this->setDeliveryPostalCode($value);
				break;
			case 28:
				$this->setDeliveryCity($value);
				break;
			case 29:
				$this->setDeliveryCountry($value);
				break;
			case 30:
				$this->setDeliveryCountriesId($value);
				break;
			case 31:
				$this->setDeliveryStateProvince($value);
				break;
			case 32:
				$this->setDeliveryCompanyName($value);
				break;
			case 33:
				$this->setDeliveryMethod($value);
				break;
			case 34:
				$this->setFinishedAt($value);
				break;
			case 35:
				$this->setCreatedAt($value);
				break;
			case 36:
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
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = OrdersPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setSessionId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPaymentGatewayId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setState($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setInEdit($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCustomersId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFirstName($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setLastName($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setEmail($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setPhone($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setLanguagesId($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCurrencyId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setBillingFirstName($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setBillingLastName($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setBillingAddressLine1($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setBillingAddressLine2($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setBillingPostalCode($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setBillingCity($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setBillingCountry($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setBillingCountriesId($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setBillingStateProvince($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setBillingCompanyName($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setBillingMethod($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setDeliveryFirstName($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setDeliveryLastName($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setDeliveryAddressLine1($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setDeliveryAddressLine2($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setDeliveryPostalCode($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setDeliveryCity($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setDeliveryCountry($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setDeliveryCountriesId($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setDeliveryStateProvince($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setDeliveryCompanyName($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setDeliveryMethod($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setFinishedAt($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setCreatedAt($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setUpdatedAt($arr[$keys[36]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(OrdersPeer::DATABASE_NAME);

		if ($this->isColumnModified(OrdersPeer::ID)) $criteria->add(OrdersPeer::ID, $this->id);
		if ($this->isColumnModified(OrdersPeer::SESSION_ID)) $criteria->add(OrdersPeer::SESSION_ID, $this->session_id);
		if ($this->isColumnModified(OrdersPeer::PAYMENT_GATEWAY_ID)) $criteria->add(OrdersPeer::PAYMENT_GATEWAY_ID, $this->payment_gateway_id);
		if ($this->isColumnModified(OrdersPeer::STATE)) $criteria->add(OrdersPeer::STATE, $this->state);
		if ($this->isColumnModified(OrdersPeer::IN_EDIT)) $criteria->add(OrdersPeer::IN_EDIT, $this->in_edit);
		if ($this->isColumnModified(OrdersPeer::CUSTOMERS_ID)) $criteria->add(OrdersPeer::CUSTOMERS_ID, $this->customers_id);
		if ($this->isColumnModified(OrdersPeer::FIRST_NAME)) $criteria->add(OrdersPeer::FIRST_NAME, $this->first_name);
		if ($this->isColumnModified(OrdersPeer::LAST_NAME)) $criteria->add(OrdersPeer::LAST_NAME, $this->last_name);
		if ($this->isColumnModified(OrdersPeer::EMAIL)) $criteria->add(OrdersPeer::EMAIL, $this->email);
		if ($this->isColumnModified(OrdersPeer::PHONE)) $criteria->add(OrdersPeer::PHONE, $this->phone);
		if ($this->isColumnModified(OrdersPeer::LANGUAGES_ID)) $criteria->add(OrdersPeer::LANGUAGES_ID, $this->languages_id);
		if ($this->isColumnModified(OrdersPeer::CURRENCY_ID)) $criteria->add(OrdersPeer::CURRENCY_ID, $this->currency_id);
		if ($this->isColumnModified(OrdersPeer::BILLING_FIRST_NAME)) $criteria->add(OrdersPeer::BILLING_FIRST_NAME, $this->billing_first_name);
		if ($this->isColumnModified(OrdersPeer::BILLING_LAST_NAME)) $criteria->add(OrdersPeer::BILLING_LAST_NAME, $this->billing_last_name);
		if ($this->isColumnModified(OrdersPeer::BILLING_ADDRESS_LINE_1)) $criteria->add(OrdersPeer::BILLING_ADDRESS_LINE_1, $this->billing_address_line_1);
		if ($this->isColumnModified(OrdersPeer::BILLING_ADDRESS_LINE_2)) $criteria->add(OrdersPeer::BILLING_ADDRESS_LINE_2, $this->billing_address_line_2);
		if ($this->isColumnModified(OrdersPeer::BILLING_POSTAL_CODE)) $criteria->add(OrdersPeer::BILLING_POSTAL_CODE, $this->billing_postal_code);
		if ($this->isColumnModified(OrdersPeer::BILLING_CITY)) $criteria->add(OrdersPeer::BILLING_CITY, $this->billing_city);
		if ($this->isColumnModified(OrdersPeer::BILLING_COUNTRY)) $criteria->add(OrdersPeer::BILLING_COUNTRY, $this->billing_country);
		if ($this->isColumnModified(OrdersPeer::BILLING_COUNTRIES_ID)) $criteria->add(OrdersPeer::BILLING_COUNTRIES_ID, $this->billing_countries_id);
		if ($this->isColumnModified(OrdersPeer::BILLING_STATE_PROVINCE)) $criteria->add(OrdersPeer::BILLING_STATE_PROVINCE, $this->billing_state_province);
		if ($this->isColumnModified(OrdersPeer::BILLING_COMPANY_NAME)) $criteria->add(OrdersPeer::BILLING_COMPANY_NAME, $this->billing_company_name);
		if ($this->isColumnModified(OrdersPeer::BILLING_METHOD)) $criteria->add(OrdersPeer::BILLING_METHOD, $this->billing_method);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_FIRST_NAME)) $criteria->add(OrdersPeer::DELIVERY_FIRST_NAME, $this->delivery_first_name);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_LAST_NAME)) $criteria->add(OrdersPeer::DELIVERY_LAST_NAME, $this->delivery_last_name);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_ADDRESS_LINE_1)) $criteria->add(OrdersPeer::DELIVERY_ADDRESS_LINE_1, $this->delivery_address_line_1);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_ADDRESS_LINE_2)) $criteria->add(OrdersPeer::DELIVERY_ADDRESS_LINE_2, $this->delivery_address_line_2);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_POSTAL_CODE)) $criteria->add(OrdersPeer::DELIVERY_POSTAL_CODE, $this->delivery_postal_code);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_CITY)) $criteria->add(OrdersPeer::DELIVERY_CITY, $this->delivery_city);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_COUNTRY)) $criteria->add(OrdersPeer::DELIVERY_COUNTRY, $this->delivery_country);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_COUNTRIES_ID)) $criteria->add(OrdersPeer::DELIVERY_COUNTRIES_ID, $this->delivery_countries_id);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_STATE_PROVINCE)) $criteria->add(OrdersPeer::DELIVERY_STATE_PROVINCE, $this->delivery_state_province);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_COMPANY_NAME)) $criteria->add(OrdersPeer::DELIVERY_COMPANY_NAME, $this->delivery_company_name);
		if ($this->isColumnModified(OrdersPeer::DELIVERY_METHOD)) $criteria->add(OrdersPeer::DELIVERY_METHOD, $this->delivery_method);
		if ($this->isColumnModified(OrdersPeer::FINISHED_AT)) $criteria->add(OrdersPeer::FINISHED_AT, $this->finished_at);
		if ($this->isColumnModified(OrdersPeer::CREATED_AT)) $criteria->add(OrdersPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(OrdersPeer::UPDATED_AT)) $criteria->add(OrdersPeer::UPDATED_AT, $this->updated_at);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(OrdersPeer::DATABASE_NAME);
		$criteria->add(OrdersPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
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
	 * @param      object $copyObj An object of Orders (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setSessionId($this->getSessionId());
		$copyObj->setPaymentGatewayId($this->getPaymentGatewayId());
		$copyObj->setState($this->getState());
		$copyObj->setInEdit($this->getInEdit());
		$copyObj->setCustomersId($this->getCustomersId());
		$copyObj->setFirstName($this->getFirstName());
		$copyObj->setLastName($this->getLastName());
		$copyObj->setEmail($this->getEmail());
		$copyObj->setPhone($this->getPhone());
		$copyObj->setLanguagesId($this->getLanguagesId());
		$copyObj->setCurrencyId($this->getCurrencyId());
		$copyObj->setBillingFirstName($this->getBillingFirstName());
		$copyObj->setBillingLastName($this->getBillingLastName());
		$copyObj->setBillingAddressLine1($this->getBillingAddressLine1());
		$copyObj->setBillingAddressLine2($this->getBillingAddressLine2());
		$copyObj->setBillingPostalCode($this->getBillingPostalCode());
		$copyObj->setBillingCity($this->getBillingCity());
		$copyObj->setBillingCountry($this->getBillingCountry());
		$copyObj->setBillingCountriesId($this->getBillingCountriesId());
		$copyObj->setBillingStateProvince($this->getBillingStateProvince());
		$copyObj->setBillingCompanyName($this->getBillingCompanyName());
		$copyObj->setBillingMethod($this->getBillingMethod());
		$copyObj->setDeliveryFirstName($this->getDeliveryFirstName());
		$copyObj->setDeliveryLastName($this->getDeliveryLastName());
		$copyObj->setDeliveryAddressLine1($this->getDeliveryAddressLine1());
		$copyObj->setDeliveryAddressLine2($this->getDeliveryAddressLine2());
		$copyObj->setDeliveryPostalCode($this->getDeliveryPostalCode());
		$copyObj->setDeliveryCity($this->getDeliveryCity());
		$copyObj->setDeliveryCountry($this->getDeliveryCountry());
		$copyObj->setDeliveryCountriesId($this->getDeliveryCountriesId());
		$copyObj->setDeliveryStateProvince($this->getDeliveryStateProvince());
		$copyObj->setDeliveryCompanyName($this->getDeliveryCompanyName());
		$copyObj->setDeliveryMethod($this->getDeliveryMethod());
		$copyObj->setFinishedAt($this->getFinishedAt());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setUpdatedAt($this->getUpdatedAt());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getOrdersAttributess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersAttributes($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getOrdersLiness() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersLines($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getOrdersStateLogs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersStateLog($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getOrdersSyncLogs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addOrdersSyncLog($relObj->copy($deepCopy));
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
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     Orders Clone of current object.
	 * @throws     PropelException
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
	 * @return     OrdersPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new OrdersPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Countries object.
	 *
	 * @param      Countries $v
	 * @return     Orders The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCountriesRelatedByBillingCountriesId(Countries $v = null)
	{
		if ($v === null) {
			$this->setBillingCountriesId(NULL);
		} else {
			$this->setBillingCountriesId($v->getId());
		}

		$this->aCountriesRelatedByBillingCountriesId = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Countries object, it will not be re-added.
		if ($v !== null) {
			$v->addOrdersRelatedByBillingCountriesId($this);
		}

		return $this;
	}


	/**
	 * Get the associated Countries object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Countries The associated Countries object.
	 * @throws     PropelException
	 */
	public function getCountriesRelatedByBillingCountriesId(PropelPDO $con = null)
	{
		if ($this->aCountriesRelatedByBillingCountriesId === null && ($this->billing_countries_id !== null)) {
			$this->aCountriesRelatedByBillingCountriesId = CountriesQuery::create()->findPk($this->billing_countries_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCountriesRelatedByBillingCountriesId->addOrderssRelatedByBillingCountriesId($this);
			 */
		}
		return $this->aCountriesRelatedByBillingCountriesId;
	}

	/**
	 * Declares an association between this object and a Countries object.
	 *
	 * @param      Countries $v
	 * @return     Orders The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCountriesRelatedByDeliveryCountriesId(Countries $v = null)
	{
		if ($v === null) {
			$this->setDeliveryCountriesId(NULL);
		} else {
			$this->setDeliveryCountriesId($v->getId());
		}

		$this->aCountriesRelatedByDeliveryCountriesId = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Countries object, it will not be re-added.
		if ($v !== null) {
			$v->addOrdersRelatedByDeliveryCountriesId($this);
		}

		return $this;
	}


	/**
	 * Get the associated Countries object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Countries The associated Countries object.
	 * @throws     PropelException
	 */
	public function getCountriesRelatedByDeliveryCountriesId(PropelPDO $con = null)
	{
		if ($this->aCountriesRelatedByDeliveryCountriesId === null && ($this->delivery_countries_id !== null)) {
			$this->aCountriesRelatedByDeliveryCountriesId = CountriesQuery::create()->findPk($this->delivery_countries_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCountriesRelatedByDeliveryCountriesId->addOrderssRelatedByDeliveryCountriesId($this);
			 */
		}
		return $this->aCountriesRelatedByDeliveryCountriesId;
	}


	/**
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('OrdersAttributes' == $relationName) {
			return $this->initOrdersAttributess();
		}
		if ('OrdersLines' == $relationName) {
			return $this->initOrdersLiness();
		}
		if ('OrdersStateLog' == $relationName) {
			return $this->initOrdersStateLogs();
		}
		if ('OrdersSyncLog' == $relationName) {
			return $this->initOrdersSyncLogs();
		}
	}

	/**
	 * Clears out the collOrdersAttributess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrdersAttributess()
	 */
	public function clearOrdersAttributess()
	{
		$this->collOrdersAttributess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrdersAttributess collection.
	 *
	 * By default this just sets the collOrdersAttributess collection to an empty array (like clearcollOrdersAttributess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrdersAttributess($overrideExisting = true)
	{
		if (null !== $this->collOrdersAttributess && !$overrideExisting) {
			return;
		}
		$this->collOrdersAttributess = new PropelObjectCollection();
		$this->collOrdersAttributess->setModel('OrdersAttributes');
	}

	/**
	 * Gets an array of OrdersAttributes objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Orders is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array OrdersAttributes[] List of OrdersAttributes objects
	 * @throws     PropelException
	 */
	public function getOrdersAttributess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrdersAttributess || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersAttributess) {
				// return empty collection
				$this->initOrdersAttributess();
			} else {
				$collOrdersAttributess = OrdersAttributesQuery::create(null, $criteria)
					->filterByOrders($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrdersAttributess;
				}
				$this->collOrdersAttributess = $collOrdersAttributess;
			}
		}
		return $this->collOrdersAttributess;
	}

	/**
	 * Sets a collection of OrdersAttributes objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $ordersAttributess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrdersAttributess(PropelCollection $ordersAttributess, PropelPDO $con = null)
	{
		$this->ordersAttributessScheduledForDeletion = $this->getOrdersAttributess(new Criteria(), $con)->diff($ordersAttributess);

		foreach ($ordersAttributess as $ordersAttributes) {
			// Fix issue with collection modified by reference
			if ($ordersAttributes->isNew()) {
				$ordersAttributes->setOrders($this);
			}
			$this->addOrdersAttributes($ordersAttributes);
		}

		$this->collOrdersAttributess = $ordersAttributess;
	}

	/**
	 * Returns the number of related OrdersAttributes objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related OrdersAttributes objects.
	 * @throws     PropelException
	 */
	public function countOrdersAttributess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrdersAttributess || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersAttributess) {
				return 0;
			} else {
				$query = OrdersAttributesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByOrders($this)
					->count($con);
			}
		} else {
			return count($this->collOrdersAttributess);
		}
	}

	/**
	 * Method called to associate a OrdersAttributes object to this object
	 * through the OrdersAttributes foreign key attribute.
	 *
	 * @param      OrdersAttributes $l OrdersAttributes
	 * @return     Orders The current object (for fluent API support)
	 */
	public function addOrdersAttributes(OrdersAttributes $l)
	{
		if ($this->collOrdersAttributess === null) {
			$this->initOrdersAttributess();
		}
		if (!$this->collOrdersAttributess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersAttributes($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersAttributes $ordersAttributes The ordersAttributes object to add.
	 */
	protected function doAddOrdersAttributes($ordersAttributes)
	{
		$this->collOrdersAttributess[]= $ordersAttributes;
		$ordersAttributes->setOrders($this);
	}

	/**
	 * Clears out the collOrdersLiness collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrdersLiness()
	 */
	public function clearOrdersLiness()
	{
		$this->collOrdersLiness = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrdersLiness collection.
	 *
	 * By default this just sets the collOrdersLiness collection to an empty array (like clearcollOrdersLiness());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
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
	 * If this Orders is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array OrdersLines[] List of OrdersLines objects
	 * @throws     PropelException
	 */
	public function getOrdersLiness($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrdersLiness || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersLiness) {
				// return empty collection
				$this->initOrdersLiness();
			} else {
				$collOrdersLiness = OrdersLinesQuery::create(null, $criteria)
					->filterByOrders($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrdersLiness;
				}
				$this->collOrdersLiness = $collOrdersLiness;
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
	 * @param      PropelCollection $ordersLiness A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrdersLiness(PropelCollection $ordersLiness, PropelPDO $con = null)
	{
		$this->ordersLinessScheduledForDeletion = $this->getOrdersLiness(new Criteria(), $con)->diff($ordersLiness);

		foreach ($ordersLiness as $ordersLines) {
			// Fix issue with collection modified by reference
			if ($ordersLines->isNew()) {
				$ordersLines->setOrders($this);
			}
			$this->addOrdersLines($ordersLines);
		}

		$this->collOrdersLiness = $ordersLiness;
	}

	/**
	 * Returns the number of related OrdersLines objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related OrdersLines objects.
	 * @throws     PropelException
	 */
	public function countOrdersLiness(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrdersLiness || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersLiness) {
				return 0;
			} else {
				$query = OrdersLinesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByOrders($this)
					->count($con);
			}
		} else {
			return count($this->collOrdersLiness);
		}
	}

	/**
	 * Method called to associate a OrdersLines object to this object
	 * through the OrdersLines foreign key attribute.
	 *
	 * @param      OrdersLines $l OrdersLines
	 * @return     Orders The current object (for fluent API support)
	 */
	public function addOrdersLines(OrdersLines $l)
	{
		if ($this->collOrdersLiness === null) {
			$this->initOrdersLiness();
		}
		if (!$this->collOrdersLiness->contains($l)) { // only add it if the **same** object is not already associated
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
		$ordersLines->setOrders($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Orders is new, it will return
	 * an empty collection; or if this Orders has previously
	 * been saved, it will retrieve related OrdersLiness from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Orders.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array OrdersLines[] List of OrdersLines objects
	 */
	public function getOrdersLinessJoinProducts($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = OrdersLinesQuery::create(null, $criteria);
		$query->joinWith('Products', $join_behavior);

		return $this->getOrdersLiness($query, $con);
	}

	/**
	 * Clears out the collOrdersStateLogs collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrdersStateLogs()
	 */
	public function clearOrdersStateLogs()
	{
		$this->collOrdersStateLogs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrdersStateLogs collection.
	 *
	 * By default this just sets the collOrdersStateLogs collection to an empty array (like clearcollOrdersStateLogs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrdersStateLogs($overrideExisting = true)
	{
		if (null !== $this->collOrdersStateLogs && !$overrideExisting) {
			return;
		}
		$this->collOrdersStateLogs = new PropelObjectCollection();
		$this->collOrdersStateLogs->setModel('OrdersStateLog');
	}

	/**
	 * Gets an array of OrdersStateLog objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Orders is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array OrdersStateLog[] List of OrdersStateLog objects
	 * @throws     PropelException
	 */
	public function getOrdersStateLogs($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrdersStateLogs || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersStateLogs) {
				// return empty collection
				$this->initOrdersStateLogs();
			} else {
				$collOrdersStateLogs = OrdersStateLogQuery::create(null, $criteria)
					->filterByOrders($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrdersStateLogs;
				}
				$this->collOrdersStateLogs = $collOrdersStateLogs;
			}
		}
		return $this->collOrdersStateLogs;
	}

	/**
	 * Sets a collection of OrdersStateLog objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $ordersStateLogs A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrdersStateLogs(PropelCollection $ordersStateLogs, PropelPDO $con = null)
	{
		$this->ordersStateLogsScheduledForDeletion = $this->getOrdersStateLogs(new Criteria(), $con)->diff($ordersStateLogs);

		foreach ($ordersStateLogs as $ordersStateLog) {
			// Fix issue with collection modified by reference
			if ($ordersStateLog->isNew()) {
				$ordersStateLog->setOrders($this);
			}
			$this->addOrdersStateLog($ordersStateLog);
		}

		$this->collOrdersStateLogs = $ordersStateLogs;
	}

	/**
	 * Returns the number of related OrdersStateLog objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related OrdersStateLog objects.
	 * @throws     PropelException
	 */
	public function countOrdersStateLogs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrdersStateLogs || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersStateLogs) {
				return 0;
			} else {
				$query = OrdersStateLogQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByOrders($this)
					->count($con);
			}
		} else {
			return count($this->collOrdersStateLogs);
		}
	}

	/**
	 * Method called to associate a OrdersStateLog object to this object
	 * through the OrdersStateLog foreign key attribute.
	 *
	 * @param      OrdersStateLog $l OrdersStateLog
	 * @return     Orders The current object (for fluent API support)
	 */
	public function addOrdersStateLog(OrdersStateLog $l)
	{
		if ($this->collOrdersStateLogs === null) {
			$this->initOrdersStateLogs();
		}
		if (!$this->collOrdersStateLogs->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersStateLog($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersStateLog $ordersStateLog The ordersStateLog object to add.
	 */
	protected function doAddOrdersStateLog($ordersStateLog)
	{
		$this->collOrdersStateLogs[]= $ordersStateLog;
		$ordersStateLog->setOrders($this);
	}

	/**
	 * Clears out the collOrdersSyncLogs collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addOrdersSyncLogs()
	 */
	public function clearOrdersSyncLogs()
	{
		$this->collOrdersSyncLogs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collOrdersSyncLogs collection.
	 *
	 * By default this just sets the collOrdersSyncLogs collection to an empty array (like clearcollOrdersSyncLogs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initOrdersSyncLogs($overrideExisting = true)
	{
		if (null !== $this->collOrdersSyncLogs && !$overrideExisting) {
			return;
		}
		$this->collOrdersSyncLogs = new PropelObjectCollection();
		$this->collOrdersSyncLogs->setModel('OrdersSyncLog');
	}

	/**
	 * Gets an array of OrdersSyncLog objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Orders is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array OrdersSyncLog[] List of OrdersSyncLog objects
	 * @throws     PropelException
	 */
	public function getOrdersSyncLogs($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collOrdersSyncLogs || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersSyncLogs) {
				// return empty collection
				$this->initOrdersSyncLogs();
			} else {
				$collOrdersSyncLogs = OrdersSyncLogQuery::create(null, $criteria)
					->filterByOrders($this)
					->find($con);
				if (null !== $criteria) {
					return $collOrdersSyncLogs;
				}
				$this->collOrdersSyncLogs = $collOrdersSyncLogs;
			}
		}
		return $this->collOrdersSyncLogs;
	}

	/**
	 * Sets a collection of OrdersSyncLog objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $ordersSyncLogs A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setOrdersSyncLogs(PropelCollection $ordersSyncLogs, PropelPDO $con = null)
	{
		$this->ordersSyncLogsScheduledForDeletion = $this->getOrdersSyncLogs(new Criteria(), $con)->diff($ordersSyncLogs);

		foreach ($ordersSyncLogs as $ordersSyncLog) {
			// Fix issue with collection modified by reference
			if ($ordersSyncLog->isNew()) {
				$ordersSyncLog->setOrders($this);
			}
			$this->addOrdersSyncLog($ordersSyncLog);
		}

		$this->collOrdersSyncLogs = $ordersSyncLogs;
	}

	/**
	 * Returns the number of related OrdersSyncLog objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related OrdersSyncLog objects.
	 * @throws     PropelException
	 */
	public function countOrdersSyncLogs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collOrdersSyncLogs || null !== $criteria) {
			if ($this->isNew() && null === $this->collOrdersSyncLogs) {
				return 0;
			} else {
				$query = OrdersSyncLogQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByOrders($this)
					->count($con);
			}
		} else {
			return count($this->collOrdersSyncLogs);
		}
	}

	/**
	 * Method called to associate a OrdersSyncLog object to this object
	 * through the OrdersSyncLog foreign key attribute.
	 *
	 * @param      OrdersSyncLog $l OrdersSyncLog
	 * @return     Orders The current object (for fluent API support)
	 */
	public function addOrdersSyncLog(OrdersSyncLog $l)
	{
		if ($this->collOrdersSyncLogs === null) {
			$this->initOrdersSyncLogs();
		}
		if (!$this->collOrdersSyncLogs->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddOrdersSyncLog($l);
		}

		return $this;
	}

	/**
	 * @param	OrdersSyncLog $ordersSyncLog The ordersSyncLog object to add.
	 */
	protected function doAddOrdersSyncLog($ordersSyncLog)
	{
		$this->collOrdersSyncLogs[]= $ordersSyncLog;
		$ordersSyncLog->setOrders($this);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->session_id = null;
		$this->payment_gateway_id = null;
		$this->state = null;
		$this->in_edit = null;
		$this->customers_id = null;
		$this->first_name = null;
		$this->last_name = null;
		$this->email = null;
		$this->phone = null;
		$this->languages_id = null;
		$this->currency_id = null;
		$this->billing_first_name = null;
		$this->billing_last_name = null;
		$this->billing_address_line_1 = null;
		$this->billing_address_line_2 = null;
		$this->billing_postal_code = null;
		$this->billing_city = null;
		$this->billing_country = null;
		$this->billing_countries_id = null;
		$this->billing_state_province = null;
		$this->billing_company_name = null;
		$this->billing_method = null;
		$this->delivery_first_name = null;
		$this->delivery_last_name = null;
		$this->delivery_address_line_1 = null;
		$this->delivery_address_line_2 = null;
		$this->delivery_postal_code = null;
		$this->delivery_city = null;
		$this->delivery_country = null;
		$this->delivery_countries_id = null;
		$this->delivery_state_province = null;
		$this->delivery_company_name = null;
		$this->delivery_method = null;
		$this->finished_at = null;
		$this->created_at = null;
		$this->updated_at = null;
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
	 * @param      boolean $deep Whether to also clear the references on all referrer objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
			if ($this->collOrdersAttributess) {
				foreach ($this->collOrdersAttributess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collOrdersLiness) {
				foreach ($this->collOrdersLiness as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collOrdersStateLogs) {
				foreach ($this->collOrdersStateLogs as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collOrdersSyncLogs) {
				foreach ($this->collOrdersSyncLogs as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collOrdersAttributess instanceof PropelCollection) {
			$this->collOrdersAttributess->clearIterator();
		}
		$this->collOrdersAttributess = null;
		if ($this->collOrdersLiness instanceof PropelCollection) {
			$this->collOrdersLiness->clearIterator();
		}
		$this->collOrdersLiness = null;
		if ($this->collOrdersStateLogs instanceof PropelCollection) {
			$this->collOrdersStateLogs->clearIterator();
		}
		$this->collOrdersStateLogs = null;
		if ($this->collOrdersSyncLogs instanceof PropelCollection) {
			$this->collOrdersSyncLogs->clearIterator();
		}
		$this->collOrdersSyncLogs = null;
		$this->aCountriesRelatedByBillingCountriesId = null;
		$this->aCountriesRelatedByDeliveryCountriesId = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(OrdersPeer::DEFAULT_STRING_FORMAT);
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     Orders The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = OrdersPeer::UPDATED_AT;
		return $this;
	}

} // BaseOrders

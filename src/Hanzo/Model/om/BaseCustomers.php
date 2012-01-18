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
use Hanzo\Model\ConsultantsInfo;
use Hanzo\Model\ConsultantsInfoQuery;
use Hanzo\Model\Countries;
use Hanzo\Model\CountriesQuery;
use Hanzo\Model\CouponsToCustomers;
use Hanzo\Model\CouponsToCustomersQuery;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Events;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\GothiaAccounts;
use Hanzo\Model\GothiaAccountsQuery;
use Hanzo\Model\Groups;
use Hanzo\Model\GroupsQuery;
use Hanzo\Model\Languages;
use Hanzo\Model\LanguagesQuery;

/**
 * Base class that represents a row from the 'customers' table.
 *
 * 
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCustomers extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'Hanzo\\Model\\CustomersPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CustomersPeer
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
	 * The value for the initials field.
	 * @var        string
	 */
	protected $initials;

	/**
	 * The value for the password field.
	 * @var        string
	 */
	protected $password;

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
	 * The value for the password_clear field.
	 * @var        string
	 */
	protected $password_clear;

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
	 * The value for the discount field.
	 * Note: this column has a database default value of: '0.00'
	 * @var        string
	 */
	protected $discount;

	/**
	 * The value for the groups_id field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $groups_id;

	/**
	 * The value for the is_active field.
	 * Note: this column has a database default value of: true
	 * @var        boolean
	 */
	protected $is_active;

	/**
	 * The value for the languages_id field.
	 * @var        int
	 */
	protected $languages_id;

	/**
	 * The value for the countries_id field.
	 * @var        int
	 */
	protected $countries_id;

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
	 * @var        Groups
	 */
	protected $aGroups;

	/**
	 * @var        Languages
	 */
	protected $aLanguages;

	/**
	 * @var        Countries
	 */
	protected $aCountriesRelatedByCountriesId;

	/**
	 * @var        Countries
	 */
	protected $aCountriesRelatedByBillingCountriesId;

	/**
	 * @var        Countries
	 */
	protected $aCountriesRelatedByDeliveryCountriesId;

	/**
	 * @var        ConsultantsInfo one-to-one related ConsultantsInfo object
	 */
	protected $singleConsultantsInfo;

	/**
	 * @var        array CouponsToCustomers[] Collection to store aggregation of CouponsToCustomers objects.
	 */
	protected $collCouponsToCustomerss;

	/**
	 * @var        array Events[] Collection to store aggregation of Events objects.
	 */
	protected $collEventssRelatedByConsultantsId;

	/**
	 * @var        array Events[] Collection to store aggregation of Events objects.
	 */
	protected $collEventssRelatedByCustomersId;

	/**
	 * @var        GothiaAccounts one-to-one related GothiaAccounts object
	 */
	protected $singleGothiaAccounts;

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
	protected $consultantsInfosScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $couponsToCustomerssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $eventssRelatedByConsultantsIdScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $eventssRelatedByCustomersIdScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $gothiaAccountssScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->discount = '0.00';
		$this->groups_id = 1;
		$this->is_active = true;
	}

	/**
	 * Initializes internal state of BaseCustomers object.
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
	 * Get the [initials] column value.
	 * 
	 * @return     string
	 */
	public function getInitials()
	{
		return $this->initials;
	}

	/**
	 * Get the [password] column value.
	 * 
	 * @return     string
	 */
	public function getPassword()
	{
		return $this->password;
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
	 * Get the [password_clear] column value.
	 * 
	 * @return     string
	 */
	public function getPasswordClear()
	{
		return $this->password_clear;
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
	 * Get the [discount] column value.
	 * 
	 * @return     string
	 */
	public function getDiscount()
	{
		return $this->discount;
	}

	/**
	 * Get the [groups_id] column value.
	 * 
	 * @return     int
	 */
	public function getGroupsId()
	{
		return $this->groups_id;
	}

	/**
	 * Get the [is_active] column value.
	 * 
	 * @return     boolean
	 */
	public function getIsActive()
	{
		return $this->is_active;
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
	 * Get the [countries_id] column value.
	 * 
	 * @return     int
	 */
	public function getCountriesId()
	{
		return $this->countries_id;
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
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CustomersPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [first_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setFirstName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->first_name !== $v) {
			$this->first_name = $v;
			$this->modifiedColumns[] = CustomersPeer::FIRST_NAME;
		}

		return $this;
	} // setFirstName()

	/**
	 * Set the value of [last_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setLastName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->last_name !== $v) {
			$this->last_name = $v;
			$this->modifiedColumns[] = CustomersPeer::LAST_NAME;
		}

		return $this;
	} // setLastName()

	/**
	 * Set the value of [initials] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setInitials($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->initials !== $v) {
			$this->initials = $v;
			$this->modifiedColumns[] = CustomersPeer::INITIALS;
		}

		return $this;
	} // setInitials()

	/**
	 * Set the value of [password] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setPassword($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->password !== $v) {
			$this->password = $v;
			$this->modifiedColumns[] = CustomersPeer::PASSWORD;
		}

		return $this;
	} // setPassword()

	/**
	 * Set the value of [email] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = CustomersPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [phone] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setPhone($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->phone !== $v) {
			$this->phone = $v;
			$this->modifiedColumns[] = CustomersPeer::PHONE;
		}

		return $this;
	} // setPhone()

	/**
	 * Set the value of [password_clear] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setPasswordClear($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->password_clear !== $v) {
			$this->password_clear = $v;
			$this->modifiedColumns[] = CustomersPeer::PASSWORD_CLEAR;
		}

		return $this;
	} // setPasswordClear()

	/**
	 * Set the value of [billing_address_line_1] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingAddressLine1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_address_line_1 !== $v) {
			$this->billing_address_line_1 = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_ADDRESS_LINE_1;
		}

		return $this;
	} // setBillingAddressLine1()

	/**
	 * Set the value of [billing_address_line_2] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingAddressLine2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_address_line_2 !== $v) {
			$this->billing_address_line_2 = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_ADDRESS_LINE_2;
		}

		return $this;
	} // setBillingAddressLine2()

	/**
	 * Set the value of [billing_postal_code] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingPostalCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_postal_code !== $v) {
			$this->billing_postal_code = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_POSTAL_CODE;
		}

		return $this;
	} // setBillingPostalCode()

	/**
	 * Set the value of [billing_city] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingCity($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_city !== $v) {
			$this->billing_city = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_CITY;
		}

		return $this;
	} // setBillingCity()

	/**
	 * Set the value of [billing_country] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingCountry($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_country !== $v) {
			$this->billing_country = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_COUNTRY;
		}

		return $this;
	} // setBillingCountry()

	/**
	 * Set the value of [billing_countries_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingCountriesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->billing_countries_id !== $v) {
			$this->billing_countries_id = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_COUNTRIES_ID;
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
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setBillingStateProvince($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->billing_state_province !== $v) {
			$this->billing_state_province = $v;
			$this->modifiedColumns[] = CustomersPeer::BILLING_STATE_PROVINCE;
		}

		return $this;
	} // setBillingStateProvince()

	/**
	 * Set the value of [delivery_address_line_1] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryAddressLine1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_address_line_1 !== $v) {
			$this->delivery_address_line_1 = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_ADDRESS_LINE_1;
		}

		return $this;
	} // setDeliveryAddressLine1()

	/**
	 * Set the value of [delivery_address_line_2] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryAddressLine2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_address_line_2 !== $v) {
			$this->delivery_address_line_2 = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_ADDRESS_LINE_2;
		}

		return $this;
	} // setDeliveryAddressLine2()

	/**
	 * Set the value of [delivery_postal_code] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryPostalCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_postal_code !== $v) {
			$this->delivery_postal_code = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_POSTAL_CODE;
		}

		return $this;
	} // setDeliveryPostalCode()

	/**
	 * Set the value of [delivery_city] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryCity($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_city !== $v) {
			$this->delivery_city = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_CITY;
		}

		return $this;
	} // setDeliveryCity()

	/**
	 * Set the value of [delivery_country] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryCountry($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_country !== $v) {
			$this->delivery_country = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_COUNTRY;
		}

		return $this;
	} // setDeliveryCountry()

	/**
	 * Set the value of [delivery_countries_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryCountriesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->delivery_countries_id !== $v) {
			$this->delivery_countries_id = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_COUNTRIES_ID;
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
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryStateProvince($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_state_province !== $v) {
			$this->delivery_state_province = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_STATE_PROVINCE;
		}

		return $this;
	} // setDeliveryStateProvince()

	/**
	 * Set the value of [delivery_company_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDeliveryCompanyName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->delivery_company_name !== $v) {
			$this->delivery_company_name = $v;
			$this->modifiedColumns[] = CustomersPeer::DELIVERY_COMPANY_NAME;
		}

		return $this;
	} // setDeliveryCompanyName()

	/**
	 * Set the value of [discount] column.
	 * 
	 * @param      string $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setDiscount($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->discount !== $v) {
			$this->discount = $v;
			$this->modifiedColumns[] = CustomersPeer::DISCOUNT;
		}

		return $this;
	} // setDiscount()

	/**
	 * Set the value of [groups_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setGroupsId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->groups_id !== $v) {
			$this->groups_id = $v;
			$this->modifiedColumns[] = CustomersPeer::GROUPS_ID;
		}

		if ($this->aGroups !== null && $this->aGroups->getId() !== $v) {
			$this->aGroups = null;
		}

		return $this;
	} // setGroupsId()

	/**
	 * Sets the value of the [is_active] column.
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * 
	 * @param      boolean|integer|string $v The new value
	 * @return     Customers The current object (for fluent API support)
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
			$this->modifiedColumns[] = CustomersPeer::IS_ACTIVE;
		}

		return $this;
	} // setIsActive()

	/**
	 * Set the value of [languages_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setLanguagesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->languages_id !== $v) {
			$this->languages_id = $v;
			$this->modifiedColumns[] = CustomersPeer::LANGUAGES_ID;
		}

		if ($this->aLanguages !== null && $this->aLanguages->getId() !== $v) {
			$this->aLanguages = null;
		}

		return $this;
	} // setLanguagesId()

	/**
	 * Set the value of [countries_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setCountriesId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->countries_id !== $v) {
			$this->countries_id = $v;
			$this->modifiedColumns[] = CustomersPeer::COUNTRIES_ID;
		}

		if ($this->aCountriesRelatedByCountriesId !== null && $this->aCountriesRelatedByCountriesId->getId() !== $v) {
			$this->aCountriesRelatedByCountriesId = null;
		}

		return $this;
	} // setCountriesId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = CustomersPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Customers The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = CustomersPeer::UPDATED_AT;
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
			if ($this->discount !== '0.00') {
				return false;
			}

			if ($this->groups_id !== 1) {
				return false;
			}

			if ($this->is_active !== true) {
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
			$this->first_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->last_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->initials = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->password = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->email = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->phone = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->password_clear = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->billing_address_line_1 = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->billing_address_line_2 = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->billing_postal_code = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->billing_city = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->billing_country = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->billing_countries_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->billing_state_province = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->delivery_address_line_1 = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->delivery_address_line_2 = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->delivery_postal_code = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->delivery_city = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->delivery_country = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->delivery_countries_id = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->delivery_state_province = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->delivery_company_name = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->discount = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->groups_id = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->is_active = ($row[$startcol + 25] !== null) ? (boolean) $row[$startcol + 25] : null;
			$this->languages_id = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->countries_id = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->created_at = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->updated_at = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 30; // 30 = CustomersPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Customers object", $e);
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
		if ($this->aGroups !== null && $this->groups_id !== $this->aGroups->getId()) {
			$this->aGroups = null;
		}
		if ($this->aLanguages !== null && $this->languages_id !== $this->aLanguages->getId()) {
			$this->aLanguages = null;
		}
		if ($this->aCountriesRelatedByCountriesId !== null && $this->countries_id !== $this->aCountriesRelatedByCountriesId->getId()) {
			$this->aCountriesRelatedByCountriesId = null;
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
			$con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CustomersPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aGroups = null;
			$this->aLanguages = null;
			$this->aCountriesRelatedByCountriesId = null;
			$this->aCountriesRelatedByBillingCountriesId = null;
			$this->aCountriesRelatedByDeliveryCountriesId = null;
			$this->singleConsultantsInfo = null;

			$this->collCouponsToCustomerss = null;

			$this->collEventssRelatedByConsultantsId = null;

			$this->collEventssRelatedByCustomersId = null;

			$this->singleGothiaAccounts = null;

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
			$con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = CustomersQuery::create()
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
			$con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(CustomersPeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(CustomersPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(CustomersPeer::UPDATED_AT)) {
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
				CustomersPeer::addInstanceToPool($this);
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

			if ($this->aGroups !== null) {
				if ($this->aGroups->isModified() || $this->aGroups->isNew()) {
					$affectedRows += $this->aGroups->save($con);
				}
				$this->setGroups($this->aGroups);
			}

			if ($this->aLanguages !== null) {
				if ($this->aLanguages->isModified() || $this->aLanguages->isNew()) {
					$affectedRows += $this->aLanguages->save($con);
				}
				$this->setLanguages($this->aLanguages);
			}

			if ($this->aCountriesRelatedByCountriesId !== null) {
				if ($this->aCountriesRelatedByCountriesId->isModified() || $this->aCountriesRelatedByCountriesId->isNew()) {
					$affectedRows += $this->aCountriesRelatedByCountriesId->save($con);
				}
				$this->setCountriesRelatedByCountriesId($this->aCountriesRelatedByCountriesId);
			}

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

			if ($this->consultantsInfosScheduledForDeletion !== null) {
				if (!$this->consultantsInfosScheduledForDeletion->isEmpty()) {
					ConsultantsInfoQuery::create()
						->filterByPrimaryKeys($this->consultantsInfosScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->consultantsInfosScheduledForDeletion = null;
				}
			}

			if ($this->singleConsultantsInfo !== null) {
				if (!$this->singleConsultantsInfo->isDeleted()) {
						$affectedRows += $this->singleConsultantsInfo->save($con);
				}
			}

			if ($this->couponsToCustomerssScheduledForDeletion !== null) {
				if (!$this->couponsToCustomerssScheduledForDeletion->isEmpty()) {
					CouponsToCustomersQuery::create()
						->filterByPrimaryKeys($this->couponsToCustomerssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->couponsToCustomerssScheduledForDeletion = null;
				}
			}

			if ($this->collCouponsToCustomerss !== null) {
				foreach ($this->collCouponsToCustomerss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->eventssRelatedByConsultantsIdScheduledForDeletion !== null) {
				if (!$this->eventssRelatedByConsultantsIdScheduledForDeletion->isEmpty()) {
					EventsQuery::create()
						->filterByPrimaryKeys($this->eventssRelatedByConsultantsIdScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->eventssRelatedByConsultantsIdScheduledForDeletion = null;
				}
			}

			if ($this->collEventssRelatedByConsultantsId !== null) {
				foreach ($this->collEventssRelatedByConsultantsId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->eventssRelatedByCustomersIdScheduledForDeletion !== null) {
				if (!$this->eventssRelatedByCustomersIdScheduledForDeletion->isEmpty()) {
					EventsQuery::create()
						->filterByPrimaryKeys($this->eventssRelatedByCustomersIdScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->eventssRelatedByCustomersIdScheduledForDeletion = null;
				}
			}

			if ($this->collEventssRelatedByCustomersId !== null) {
				foreach ($this->collEventssRelatedByCustomersId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->gothiaAccountssScheduledForDeletion !== null) {
				if (!$this->gothiaAccountssScheduledForDeletion->isEmpty()) {
					GothiaAccountsQuery::create()
						->filterByPrimaryKeys($this->gothiaAccountssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->gothiaAccountssScheduledForDeletion = null;
				}
			}

			if ($this->singleGothiaAccounts !== null) {
				if (!$this->singleGothiaAccounts->isDeleted()) {
						$affectedRows += $this->singleGothiaAccounts->save($con);
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

		$this->modifiedColumns[] = CustomersPeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . CustomersPeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(CustomersPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = '`ID`';
		}
		if ($this->isColumnModified(CustomersPeer::FIRST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`FIRST_NAME`';
		}
		if ($this->isColumnModified(CustomersPeer::LAST_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`LAST_NAME`';
		}
		if ($this->isColumnModified(CustomersPeer::INITIALS)) {
			$modifiedColumns[':p' . $index++]  = '`INITIALS`';
		}
		if ($this->isColumnModified(CustomersPeer::PASSWORD)) {
			$modifiedColumns[':p' . $index++]  = '`PASSWORD`';
		}
		if ($this->isColumnModified(CustomersPeer::EMAIL)) {
			$modifiedColumns[':p' . $index++]  = '`EMAIL`';
		}
		if ($this->isColumnModified(CustomersPeer::PHONE)) {
			$modifiedColumns[':p' . $index++]  = '`PHONE`';
		}
		if ($this->isColumnModified(CustomersPeer::PASSWORD_CLEAR)) {
			$modifiedColumns[':p' . $index++]  = '`PASSWORD_CLEAR`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_ADDRESS_LINE_1)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_ADDRESS_LINE_1`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_ADDRESS_LINE_2)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_ADDRESS_LINE_2`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_POSTAL_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_POSTAL_CODE`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_CITY)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_CITY`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_COUNTRY)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_COUNTRY`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_COUNTRIES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_COUNTRIES_ID`';
		}
		if ($this->isColumnModified(CustomersPeer::BILLING_STATE_PROVINCE)) {
			$modifiedColumns[':p' . $index++]  = '`BILLING_STATE_PROVINCE`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_ADDRESS_LINE_1)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_ADDRESS_LINE_1`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_ADDRESS_LINE_2)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_ADDRESS_LINE_2`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_POSTAL_CODE)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_POSTAL_CODE`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_CITY)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_CITY`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_COUNTRY)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_COUNTRY`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_COUNTRIES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_COUNTRIES_ID`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_STATE_PROVINCE)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_STATE_PROVINCE`';
		}
		if ($this->isColumnModified(CustomersPeer::DELIVERY_COMPANY_NAME)) {
			$modifiedColumns[':p' . $index++]  = '`DELIVERY_COMPANY_NAME`';
		}
		if ($this->isColumnModified(CustomersPeer::DISCOUNT)) {
			$modifiedColumns[':p' . $index++]  = '`DISCOUNT`';
		}
		if ($this->isColumnModified(CustomersPeer::GROUPS_ID)) {
			$modifiedColumns[':p' . $index++]  = '`GROUPS_ID`';
		}
		if ($this->isColumnModified(CustomersPeer::IS_ACTIVE)) {
			$modifiedColumns[':p' . $index++]  = '`IS_ACTIVE`';
		}
		if ($this->isColumnModified(CustomersPeer::LANGUAGES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`LANGUAGES_ID`';
		}
		if ($this->isColumnModified(CustomersPeer::COUNTRIES_ID)) {
			$modifiedColumns[':p' . $index++]  = '`COUNTRIES_ID`';
		}
		if ($this->isColumnModified(CustomersPeer::CREATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`CREATED_AT`';
		}
		if ($this->isColumnModified(CustomersPeer::UPDATED_AT)) {
			$modifiedColumns[':p' . $index++]  = '`UPDATED_AT`';
		}

		$sql = sprintf(
			'INSERT INTO `customers` (%s) VALUES (%s)',
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
					case '`FIRST_NAME`':
						$stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
						break;
					case '`LAST_NAME`':
						$stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
						break;
					case '`INITIALS`':
						$stmt->bindValue($identifier, $this->initials, PDO::PARAM_STR);
						break;
					case '`PASSWORD`':
						$stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
						break;
					case '`EMAIL`':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
						break;
					case '`PHONE`':
						$stmt->bindValue($identifier, $this->phone, PDO::PARAM_STR);
						break;
					case '`PASSWORD_CLEAR`':
						$stmt->bindValue($identifier, $this->password_clear, PDO::PARAM_STR);
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
					case '`DISCOUNT`':
						$stmt->bindValue($identifier, $this->discount, PDO::PARAM_STR);
						break;
					case '`GROUPS_ID`':
						$stmt->bindValue($identifier, $this->groups_id, PDO::PARAM_INT);
						break;
					case '`IS_ACTIVE`':
						$stmt->bindValue($identifier, (int) $this->is_active, PDO::PARAM_INT);
						break;
					case '`LANGUAGES_ID`':
						$stmt->bindValue($identifier, $this->languages_id, PDO::PARAM_INT);
						break;
					case '`COUNTRIES_ID`':
						$stmt->bindValue($identifier, $this->countries_id, PDO::PARAM_INT);
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

			if ($this->aGroups !== null) {
				if (!$this->aGroups->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGroups->getValidationFailures());
				}
			}

			if ($this->aLanguages !== null) {
				if (!$this->aLanguages->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aLanguages->getValidationFailures());
				}
			}

			if ($this->aCountriesRelatedByCountriesId !== null) {
				if (!$this->aCountriesRelatedByCountriesId->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCountriesRelatedByCountriesId->getValidationFailures());
				}
			}

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


			if (($retval = CustomersPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->singleConsultantsInfo !== null) {
					if (!$this->singleConsultantsInfo->validate($columns)) {
						$failureMap = array_merge($failureMap, $this->singleConsultantsInfo->getValidationFailures());
					}
				}

				if ($this->collCouponsToCustomerss !== null) {
					foreach ($this->collCouponsToCustomerss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEventssRelatedByConsultantsId !== null) {
					foreach ($this->collEventssRelatedByConsultantsId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEventssRelatedByCustomersId !== null) {
					foreach ($this->collEventssRelatedByCustomersId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->singleGothiaAccounts !== null) {
					if (!$this->singleGothiaAccounts->validate($columns)) {
						$failureMap = array_merge($failureMap, $this->singleGothiaAccounts->getValidationFailures());
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
		$pos = CustomersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getFirstName();
				break;
			case 2:
				return $this->getLastName();
				break;
			case 3:
				return $this->getInitials();
				break;
			case 4:
				return $this->getPassword();
				break;
			case 5:
				return $this->getEmail();
				break;
			case 6:
				return $this->getPhone();
				break;
			case 7:
				return $this->getPasswordClear();
				break;
			case 8:
				return $this->getBillingAddressLine1();
				break;
			case 9:
				return $this->getBillingAddressLine2();
				break;
			case 10:
				return $this->getBillingPostalCode();
				break;
			case 11:
				return $this->getBillingCity();
				break;
			case 12:
				return $this->getBillingCountry();
				break;
			case 13:
				return $this->getBillingCountriesId();
				break;
			case 14:
				return $this->getBillingStateProvince();
				break;
			case 15:
				return $this->getDeliveryAddressLine1();
				break;
			case 16:
				return $this->getDeliveryAddressLine2();
				break;
			case 17:
				return $this->getDeliveryPostalCode();
				break;
			case 18:
				return $this->getDeliveryCity();
				break;
			case 19:
				return $this->getDeliveryCountry();
				break;
			case 20:
				return $this->getDeliveryCountriesId();
				break;
			case 21:
				return $this->getDeliveryStateProvince();
				break;
			case 22:
				return $this->getDeliveryCompanyName();
				break;
			case 23:
				return $this->getDiscount();
				break;
			case 24:
				return $this->getGroupsId();
				break;
			case 25:
				return $this->getIsActive();
				break;
			case 26:
				return $this->getLanguagesId();
				break;
			case 27:
				return $this->getCountriesId();
				break;
			case 28:
				return $this->getCreatedAt();
				break;
			case 29:
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
		if (isset($alreadyDumpedObjects['Customers'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Customers'][$this->getPrimaryKey()] = true;
		$keys = CustomersPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getFirstName(),
			$keys[2] => $this->getLastName(),
			$keys[3] => $this->getInitials(),
			$keys[4] => $this->getPassword(),
			$keys[5] => $this->getEmail(),
			$keys[6] => $this->getPhone(),
			$keys[7] => $this->getPasswordClear(),
			$keys[8] => $this->getBillingAddressLine1(),
			$keys[9] => $this->getBillingAddressLine2(),
			$keys[10] => $this->getBillingPostalCode(),
			$keys[11] => $this->getBillingCity(),
			$keys[12] => $this->getBillingCountry(),
			$keys[13] => $this->getBillingCountriesId(),
			$keys[14] => $this->getBillingStateProvince(),
			$keys[15] => $this->getDeliveryAddressLine1(),
			$keys[16] => $this->getDeliveryAddressLine2(),
			$keys[17] => $this->getDeliveryPostalCode(),
			$keys[18] => $this->getDeliveryCity(),
			$keys[19] => $this->getDeliveryCountry(),
			$keys[20] => $this->getDeliveryCountriesId(),
			$keys[21] => $this->getDeliveryStateProvince(),
			$keys[22] => $this->getDeliveryCompanyName(),
			$keys[23] => $this->getDiscount(),
			$keys[24] => $this->getGroupsId(),
			$keys[25] => $this->getIsActive(),
			$keys[26] => $this->getLanguagesId(),
			$keys[27] => $this->getCountriesId(),
			$keys[28] => $this->getCreatedAt(),
			$keys[29] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aGroups) {
				$result['Groups'] = $this->aGroups->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aLanguages) {
				$result['Languages'] = $this->aLanguages->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aCountriesRelatedByCountriesId) {
				$result['CountriesRelatedByCountriesId'] = $this->aCountriesRelatedByCountriesId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aCountriesRelatedByBillingCountriesId) {
				$result['CountriesRelatedByBillingCountriesId'] = $this->aCountriesRelatedByBillingCountriesId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aCountriesRelatedByDeliveryCountriesId) {
				$result['CountriesRelatedByDeliveryCountriesId'] = $this->aCountriesRelatedByDeliveryCountriesId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->singleConsultantsInfo) {
				$result['ConsultantsInfo'] = $this->singleConsultantsInfo->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
			}
			if (null !== $this->collCouponsToCustomerss) {
				$result['CouponsToCustomerss'] = $this->collCouponsToCustomerss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collEventssRelatedByConsultantsId) {
				$result['EventssRelatedByConsultantsId'] = $this->collEventssRelatedByConsultantsId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collEventssRelatedByCustomersId) {
				$result['EventssRelatedByCustomersId'] = $this->collEventssRelatedByCustomersId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->singleGothiaAccounts) {
				$result['GothiaAccounts'] = $this->singleGothiaAccounts->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
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
		$pos = CustomersPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setFirstName($value);
				break;
			case 2:
				$this->setLastName($value);
				break;
			case 3:
				$this->setInitials($value);
				break;
			case 4:
				$this->setPassword($value);
				break;
			case 5:
				$this->setEmail($value);
				break;
			case 6:
				$this->setPhone($value);
				break;
			case 7:
				$this->setPasswordClear($value);
				break;
			case 8:
				$this->setBillingAddressLine1($value);
				break;
			case 9:
				$this->setBillingAddressLine2($value);
				break;
			case 10:
				$this->setBillingPostalCode($value);
				break;
			case 11:
				$this->setBillingCity($value);
				break;
			case 12:
				$this->setBillingCountry($value);
				break;
			case 13:
				$this->setBillingCountriesId($value);
				break;
			case 14:
				$this->setBillingStateProvince($value);
				break;
			case 15:
				$this->setDeliveryAddressLine1($value);
				break;
			case 16:
				$this->setDeliveryAddressLine2($value);
				break;
			case 17:
				$this->setDeliveryPostalCode($value);
				break;
			case 18:
				$this->setDeliveryCity($value);
				break;
			case 19:
				$this->setDeliveryCountry($value);
				break;
			case 20:
				$this->setDeliveryCountriesId($value);
				break;
			case 21:
				$this->setDeliveryStateProvince($value);
				break;
			case 22:
				$this->setDeliveryCompanyName($value);
				break;
			case 23:
				$this->setDiscount($value);
				break;
			case 24:
				$this->setGroupsId($value);
				break;
			case 25:
				$this->setIsActive($value);
				break;
			case 26:
				$this->setLanguagesId($value);
				break;
			case 27:
				$this->setCountriesId($value);
				break;
			case 28:
				$this->setCreatedAt($value);
				break;
			case 29:
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
		$keys = CustomersPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setFirstName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setLastName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setInitials($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPassword($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEmail($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPhone($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setPasswordClear($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setBillingAddressLine1($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setBillingAddressLine2($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setBillingPostalCode($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setBillingCity($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setBillingCountry($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setBillingCountriesId($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setBillingStateProvince($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setDeliveryAddressLine1($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setDeliveryAddressLine2($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setDeliveryPostalCode($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setDeliveryCity($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setDeliveryCountry($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setDeliveryCountriesId($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setDeliveryStateProvince($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setDeliveryCompanyName($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setDiscount($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setGroupsId($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setIsActive($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setLanguagesId($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setCountriesId($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setCreatedAt($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setUpdatedAt($arr[$keys[29]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CustomersPeer::DATABASE_NAME);

		if ($this->isColumnModified(CustomersPeer::ID)) $criteria->add(CustomersPeer::ID, $this->id);
		if ($this->isColumnModified(CustomersPeer::FIRST_NAME)) $criteria->add(CustomersPeer::FIRST_NAME, $this->first_name);
		if ($this->isColumnModified(CustomersPeer::LAST_NAME)) $criteria->add(CustomersPeer::LAST_NAME, $this->last_name);
		if ($this->isColumnModified(CustomersPeer::INITIALS)) $criteria->add(CustomersPeer::INITIALS, $this->initials);
		if ($this->isColumnModified(CustomersPeer::PASSWORD)) $criteria->add(CustomersPeer::PASSWORD, $this->password);
		if ($this->isColumnModified(CustomersPeer::EMAIL)) $criteria->add(CustomersPeer::EMAIL, $this->email);
		if ($this->isColumnModified(CustomersPeer::PHONE)) $criteria->add(CustomersPeer::PHONE, $this->phone);
		if ($this->isColumnModified(CustomersPeer::PASSWORD_CLEAR)) $criteria->add(CustomersPeer::PASSWORD_CLEAR, $this->password_clear);
		if ($this->isColumnModified(CustomersPeer::BILLING_ADDRESS_LINE_1)) $criteria->add(CustomersPeer::BILLING_ADDRESS_LINE_1, $this->billing_address_line_1);
		if ($this->isColumnModified(CustomersPeer::BILLING_ADDRESS_LINE_2)) $criteria->add(CustomersPeer::BILLING_ADDRESS_LINE_2, $this->billing_address_line_2);
		if ($this->isColumnModified(CustomersPeer::BILLING_POSTAL_CODE)) $criteria->add(CustomersPeer::BILLING_POSTAL_CODE, $this->billing_postal_code);
		if ($this->isColumnModified(CustomersPeer::BILLING_CITY)) $criteria->add(CustomersPeer::BILLING_CITY, $this->billing_city);
		if ($this->isColumnModified(CustomersPeer::BILLING_COUNTRY)) $criteria->add(CustomersPeer::BILLING_COUNTRY, $this->billing_country);
		if ($this->isColumnModified(CustomersPeer::BILLING_COUNTRIES_ID)) $criteria->add(CustomersPeer::BILLING_COUNTRIES_ID, $this->billing_countries_id);
		if ($this->isColumnModified(CustomersPeer::BILLING_STATE_PROVINCE)) $criteria->add(CustomersPeer::BILLING_STATE_PROVINCE, $this->billing_state_province);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_ADDRESS_LINE_1)) $criteria->add(CustomersPeer::DELIVERY_ADDRESS_LINE_1, $this->delivery_address_line_1);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_ADDRESS_LINE_2)) $criteria->add(CustomersPeer::DELIVERY_ADDRESS_LINE_2, $this->delivery_address_line_2);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_POSTAL_CODE)) $criteria->add(CustomersPeer::DELIVERY_POSTAL_CODE, $this->delivery_postal_code);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_CITY)) $criteria->add(CustomersPeer::DELIVERY_CITY, $this->delivery_city);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_COUNTRY)) $criteria->add(CustomersPeer::DELIVERY_COUNTRY, $this->delivery_country);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_COUNTRIES_ID)) $criteria->add(CustomersPeer::DELIVERY_COUNTRIES_ID, $this->delivery_countries_id);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_STATE_PROVINCE)) $criteria->add(CustomersPeer::DELIVERY_STATE_PROVINCE, $this->delivery_state_province);
		if ($this->isColumnModified(CustomersPeer::DELIVERY_COMPANY_NAME)) $criteria->add(CustomersPeer::DELIVERY_COMPANY_NAME, $this->delivery_company_name);
		if ($this->isColumnModified(CustomersPeer::DISCOUNT)) $criteria->add(CustomersPeer::DISCOUNT, $this->discount);
		if ($this->isColumnModified(CustomersPeer::GROUPS_ID)) $criteria->add(CustomersPeer::GROUPS_ID, $this->groups_id);
		if ($this->isColumnModified(CustomersPeer::IS_ACTIVE)) $criteria->add(CustomersPeer::IS_ACTIVE, $this->is_active);
		if ($this->isColumnModified(CustomersPeer::LANGUAGES_ID)) $criteria->add(CustomersPeer::LANGUAGES_ID, $this->languages_id);
		if ($this->isColumnModified(CustomersPeer::COUNTRIES_ID)) $criteria->add(CustomersPeer::COUNTRIES_ID, $this->countries_id);
		if ($this->isColumnModified(CustomersPeer::CREATED_AT)) $criteria->add(CustomersPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(CustomersPeer::UPDATED_AT)) $criteria->add(CustomersPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(CustomersPeer::DATABASE_NAME);
		$criteria->add(CustomersPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Customers (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setFirstName($this->getFirstName());
		$copyObj->setLastName($this->getLastName());
		$copyObj->setInitials($this->getInitials());
		$copyObj->setPassword($this->getPassword());
		$copyObj->setEmail($this->getEmail());
		$copyObj->setPhone($this->getPhone());
		$copyObj->setPasswordClear($this->getPasswordClear());
		$copyObj->setBillingAddressLine1($this->getBillingAddressLine1());
		$copyObj->setBillingAddressLine2($this->getBillingAddressLine2());
		$copyObj->setBillingPostalCode($this->getBillingPostalCode());
		$copyObj->setBillingCity($this->getBillingCity());
		$copyObj->setBillingCountry($this->getBillingCountry());
		$copyObj->setBillingCountriesId($this->getBillingCountriesId());
		$copyObj->setBillingStateProvince($this->getBillingStateProvince());
		$copyObj->setDeliveryAddressLine1($this->getDeliveryAddressLine1());
		$copyObj->setDeliveryAddressLine2($this->getDeliveryAddressLine2());
		$copyObj->setDeliveryPostalCode($this->getDeliveryPostalCode());
		$copyObj->setDeliveryCity($this->getDeliveryCity());
		$copyObj->setDeliveryCountry($this->getDeliveryCountry());
		$copyObj->setDeliveryCountriesId($this->getDeliveryCountriesId());
		$copyObj->setDeliveryStateProvince($this->getDeliveryStateProvince());
		$copyObj->setDeliveryCompanyName($this->getDeliveryCompanyName());
		$copyObj->setDiscount($this->getDiscount());
		$copyObj->setGroupsId($this->getGroupsId());
		$copyObj->setIsActive($this->getIsActive());
		$copyObj->setLanguagesId($this->getLanguagesId());
		$copyObj->setCountriesId($this->getCountriesId());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setUpdatedAt($this->getUpdatedAt());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			$relObj = $this->getConsultantsInfo();
			if ($relObj) {
				$copyObj->setConsultantsInfo($relObj->copy($deepCopy));
			}

			foreach ($this->getCouponsToCustomerss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCouponsToCustomers($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getEventssRelatedByConsultantsId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addEventsRelatedByConsultantsId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getEventssRelatedByCustomersId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addEventsRelatedByCustomersId($relObj->copy($deepCopy));
				}
			}

			$relObj = $this->getGothiaAccounts();
			if ($relObj) {
				$copyObj->setGothiaAccounts($relObj->copy($deepCopy));
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
	 * @return     Customers Clone of current object.
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
	 * @return     CustomersPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CustomersPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Groups object.
	 *
	 * @param      Groups $v
	 * @return     Customers The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setGroups(Groups $v = null)
	{
		if ($v === null) {
			$this->setGroupsId(1);
		} else {
			$this->setGroupsId($v->getId());
		}

		$this->aGroups = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Groups object, it will not be re-added.
		if ($v !== null) {
			$v->addCustomers($this);
		}

		return $this;
	}


	/**
	 * Get the associated Groups object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Groups The associated Groups object.
	 * @throws     PropelException
	 */
	public function getGroups(PropelPDO $con = null)
	{
		if ($this->aGroups === null && ($this->groups_id !== null)) {
			$this->aGroups = GroupsQuery::create()->findPk($this->groups_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aGroups->addCustomerss($this);
			 */
		}
		return $this->aGroups;
	}

	/**
	 * Declares an association between this object and a Languages object.
	 *
	 * @param      Languages $v
	 * @return     Customers The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setLanguages(Languages $v = null)
	{
		if ($v === null) {
			$this->setLanguagesId(NULL);
		} else {
			$this->setLanguagesId($v->getId());
		}

		$this->aLanguages = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Languages object, it will not be re-added.
		if ($v !== null) {
			$v->addCustomers($this);
		}

		return $this;
	}


	/**
	 * Get the associated Languages object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Languages The associated Languages object.
	 * @throws     PropelException
	 */
	public function getLanguages(PropelPDO $con = null)
	{
		if ($this->aLanguages === null && ($this->languages_id !== null)) {
			$this->aLanguages = LanguagesQuery::create()->findPk($this->languages_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aLanguages->addCustomerss($this);
			 */
		}
		return $this->aLanguages;
	}

	/**
	 * Declares an association between this object and a Countries object.
	 *
	 * @param      Countries $v
	 * @return     Customers The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCountriesRelatedByCountriesId(Countries $v = null)
	{
		if ($v === null) {
			$this->setCountriesId(NULL);
		} else {
			$this->setCountriesId($v->getId());
		}

		$this->aCountriesRelatedByCountriesId = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Countries object, it will not be re-added.
		if ($v !== null) {
			$v->addCustomersRelatedByCountriesId($this);
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
	public function getCountriesRelatedByCountriesId(PropelPDO $con = null)
	{
		if ($this->aCountriesRelatedByCountriesId === null && ($this->countries_id !== null)) {
			$this->aCountriesRelatedByCountriesId = CountriesQuery::create()->findPk($this->countries_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCountriesRelatedByCountriesId->addCustomerssRelatedByCountriesId($this);
			 */
		}
		return $this->aCountriesRelatedByCountriesId;
	}

	/**
	 * Declares an association between this object and a Countries object.
	 *
	 * @param      Countries $v
	 * @return     Customers The current object (for fluent API support)
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
			$v->addCustomersRelatedByBillingCountriesId($this);
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
				$this->aCountriesRelatedByBillingCountriesId->addCustomerssRelatedByBillingCountriesId($this);
			 */
		}
		return $this->aCountriesRelatedByBillingCountriesId;
	}

	/**
	 * Declares an association between this object and a Countries object.
	 *
	 * @param      Countries $v
	 * @return     Customers The current object (for fluent API support)
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
			$v->addCustomersRelatedByDeliveryCountriesId($this);
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
				$this->aCountriesRelatedByDeliveryCountriesId->addCustomerssRelatedByDeliveryCountriesId($this);
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
		if ('CouponsToCustomers' == $relationName) {
			return $this->initCouponsToCustomerss();
		}
		if ('EventsRelatedByConsultantsId' == $relationName) {
			return $this->initEventssRelatedByConsultantsId();
		}
		if ('EventsRelatedByCustomersId' == $relationName) {
			return $this->initEventssRelatedByCustomersId();
		}
	}

	/**
	 * Gets a single ConsultantsInfo object, which is related to this object by a one-to-one relationship.
	 *
	 * @param      PropelPDO $con optional connection object
	 * @return     ConsultantsInfo
	 * @throws     PropelException
	 */
	public function getConsultantsInfo(PropelPDO $con = null)
	{

		if ($this->singleConsultantsInfo === null && !$this->isNew()) {
			$this->singleConsultantsInfo = ConsultantsInfoQuery::create()->findPk($this->getPrimaryKey(), $con);
		}

		return $this->singleConsultantsInfo;
	}

	/**
	 * Sets a single ConsultantsInfo object as related to this object by a one-to-one relationship.
	 *
	 * @param      ConsultantsInfo $v ConsultantsInfo
	 * @return     Customers The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setConsultantsInfo(ConsultantsInfo $v = null)
	{
		$this->singleConsultantsInfo = $v;

		// Make sure that that the passed-in ConsultantsInfo isn't already associated with this object
		if ($v !== null && $v->getCustomers() === null) {
			$v->setCustomers($this);
		}

		return $this;
	}

	/**
	 * Clears out the collCouponsToCustomerss collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCouponsToCustomerss()
	 */
	public function clearCouponsToCustomerss()
	{
		$this->collCouponsToCustomerss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCouponsToCustomerss collection.
	 *
	 * By default this just sets the collCouponsToCustomerss collection to an empty array (like clearcollCouponsToCustomerss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCouponsToCustomerss($overrideExisting = true)
	{
		if (null !== $this->collCouponsToCustomerss && !$overrideExisting) {
			return;
		}
		$this->collCouponsToCustomerss = new PropelObjectCollection();
		$this->collCouponsToCustomerss->setModel('CouponsToCustomers');
	}

	/**
	 * Gets an array of CouponsToCustomers objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Customers is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CouponsToCustomers[] List of CouponsToCustomers objects
	 * @throws     PropelException
	 */
	public function getCouponsToCustomerss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCouponsToCustomerss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCouponsToCustomerss) {
				// return empty collection
				$this->initCouponsToCustomerss();
			} else {
				$collCouponsToCustomerss = CouponsToCustomersQuery::create(null, $criteria)
					->filterByCustomers($this)
					->find($con);
				if (null !== $criteria) {
					return $collCouponsToCustomerss;
				}
				$this->collCouponsToCustomerss = $collCouponsToCustomerss;
			}
		}
		return $this->collCouponsToCustomerss;
	}

	/**
	 * Sets a collection of CouponsToCustomers objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $couponsToCustomerss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCouponsToCustomerss(PropelCollection $couponsToCustomerss, PropelPDO $con = null)
	{
		$this->couponsToCustomerssScheduledForDeletion = $this->getCouponsToCustomerss(new Criteria(), $con)->diff($couponsToCustomerss);

		foreach ($couponsToCustomerss as $couponsToCustomers) {
			// Fix issue with collection modified by reference
			if ($couponsToCustomers->isNew()) {
				$couponsToCustomers->setCustomers($this);
			}
			$this->addCouponsToCustomers($couponsToCustomers);
		}

		$this->collCouponsToCustomerss = $couponsToCustomerss;
	}

	/**
	 * Returns the number of related CouponsToCustomers objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CouponsToCustomers objects.
	 * @throws     PropelException
	 */
	public function countCouponsToCustomerss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCouponsToCustomerss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCouponsToCustomerss) {
				return 0;
			} else {
				$query = CouponsToCustomersQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCustomers($this)
					->count($con);
			}
		} else {
			return count($this->collCouponsToCustomerss);
		}
	}

	/**
	 * Method called to associate a CouponsToCustomers object to this object
	 * through the CouponsToCustomers foreign key attribute.
	 *
	 * @param      CouponsToCustomers $l CouponsToCustomers
	 * @return     Customers The current object (for fluent API support)
	 */
	public function addCouponsToCustomers(CouponsToCustomers $l)
	{
		if ($this->collCouponsToCustomerss === null) {
			$this->initCouponsToCustomerss();
		}
		if (!$this->collCouponsToCustomerss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCouponsToCustomers($l);
		}

		return $this;
	}

	/**
	 * @param	CouponsToCustomers $couponsToCustomers The couponsToCustomers object to add.
	 */
	protected function doAddCouponsToCustomers($couponsToCustomers)
	{
		$this->collCouponsToCustomerss[]= $couponsToCustomers;
		$couponsToCustomers->setCustomers($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Customers is new, it will return
	 * an empty collection; or if this Customers has previously
	 * been saved, it will retrieve related CouponsToCustomerss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Customers.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CouponsToCustomers[] List of CouponsToCustomers objects
	 */
	public function getCouponsToCustomerssJoinCoupons($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CouponsToCustomersQuery::create(null, $criteria);
		$query->joinWith('Coupons', $join_behavior);

		return $this->getCouponsToCustomerss($query, $con);
	}

	/**
	 * Clears out the collEventssRelatedByConsultantsId collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addEventssRelatedByConsultantsId()
	 */
	public function clearEventssRelatedByConsultantsId()
	{
		$this->collEventssRelatedByConsultantsId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collEventssRelatedByConsultantsId collection.
	 *
	 * By default this just sets the collEventssRelatedByConsultantsId collection to an empty array (like clearcollEventssRelatedByConsultantsId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initEventssRelatedByConsultantsId($overrideExisting = true)
	{
		if (null !== $this->collEventssRelatedByConsultantsId && !$overrideExisting) {
			return;
		}
		$this->collEventssRelatedByConsultantsId = new PropelObjectCollection();
		$this->collEventssRelatedByConsultantsId->setModel('Events');
	}

	/**
	 * Gets an array of Events objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Customers is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Events[] List of Events objects
	 * @throws     PropelException
	 */
	public function getEventssRelatedByConsultantsId($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collEventssRelatedByConsultantsId || null !== $criteria) {
			if ($this->isNew() && null === $this->collEventssRelatedByConsultantsId) {
				// return empty collection
				$this->initEventssRelatedByConsultantsId();
			} else {
				$collEventssRelatedByConsultantsId = EventsQuery::create(null, $criteria)
					->filterByCustomersRelatedByConsultantsId($this)
					->find($con);
				if (null !== $criteria) {
					return $collEventssRelatedByConsultantsId;
				}
				$this->collEventssRelatedByConsultantsId = $collEventssRelatedByConsultantsId;
			}
		}
		return $this->collEventssRelatedByConsultantsId;
	}

	/**
	 * Sets a collection of EventsRelatedByConsultantsId objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $eventssRelatedByConsultantsId A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setEventssRelatedByConsultantsId(PropelCollection $eventssRelatedByConsultantsId, PropelPDO $con = null)
	{
		$this->eventssRelatedByConsultantsIdScheduledForDeletion = $this->getEventssRelatedByConsultantsId(new Criteria(), $con)->diff($eventssRelatedByConsultantsId);

		foreach ($eventssRelatedByConsultantsId as $eventsRelatedByConsultantsId) {
			// Fix issue with collection modified by reference
			if ($eventsRelatedByConsultantsId->isNew()) {
				$eventsRelatedByConsultantsId->setCustomersRelatedByConsultantsId($this);
			}
			$this->addEventsRelatedByConsultantsId($eventsRelatedByConsultantsId);
		}

		$this->collEventssRelatedByConsultantsId = $eventssRelatedByConsultantsId;
	}

	/**
	 * Returns the number of related Events objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Events objects.
	 * @throws     PropelException
	 */
	public function countEventssRelatedByConsultantsId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collEventssRelatedByConsultantsId || null !== $criteria) {
			if ($this->isNew() && null === $this->collEventssRelatedByConsultantsId) {
				return 0;
			} else {
				$query = EventsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCustomersRelatedByConsultantsId($this)
					->count($con);
			}
		} else {
			return count($this->collEventssRelatedByConsultantsId);
		}
	}

	/**
	 * Method called to associate a Events object to this object
	 * through the Events foreign key attribute.
	 *
	 * @param      Events $l Events
	 * @return     Customers The current object (for fluent API support)
	 */
	public function addEventsRelatedByConsultantsId(Events $l)
	{
		if ($this->collEventssRelatedByConsultantsId === null) {
			$this->initEventssRelatedByConsultantsId();
		}
		if (!$this->collEventssRelatedByConsultantsId->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddEventsRelatedByConsultantsId($l);
		}

		return $this;
	}

	/**
	 * @param	EventsRelatedByConsultantsId $eventsRelatedByConsultantsId The eventsRelatedByConsultantsId object to add.
	 */
	protected function doAddEventsRelatedByConsultantsId($eventsRelatedByConsultantsId)
	{
		$this->collEventssRelatedByConsultantsId[]= $eventsRelatedByConsultantsId;
		$eventsRelatedByConsultantsId->setCustomersRelatedByConsultantsId($this);
	}

	/**
	 * Clears out the collEventssRelatedByCustomersId collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addEventssRelatedByCustomersId()
	 */
	public function clearEventssRelatedByCustomersId()
	{
		$this->collEventssRelatedByCustomersId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collEventssRelatedByCustomersId collection.
	 *
	 * By default this just sets the collEventssRelatedByCustomersId collection to an empty array (like clearcollEventssRelatedByCustomersId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initEventssRelatedByCustomersId($overrideExisting = true)
	{
		if (null !== $this->collEventssRelatedByCustomersId && !$overrideExisting) {
			return;
		}
		$this->collEventssRelatedByCustomersId = new PropelObjectCollection();
		$this->collEventssRelatedByCustomersId->setModel('Events');
	}

	/**
	 * Gets an array of Events objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Customers is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Events[] List of Events objects
	 * @throws     PropelException
	 */
	public function getEventssRelatedByCustomersId($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collEventssRelatedByCustomersId || null !== $criteria) {
			if ($this->isNew() && null === $this->collEventssRelatedByCustomersId) {
				// return empty collection
				$this->initEventssRelatedByCustomersId();
			} else {
				$collEventssRelatedByCustomersId = EventsQuery::create(null, $criteria)
					->filterByCustomersRelatedByCustomersId($this)
					->find($con);
				if (null !== $criteria) {
					return $collEventssRelatedByCustomersId;
				}
				$this->collEventssRelatedByCustomersId = $collEventssRelatedByCustomersId;
			}
		}
		return $this->collEventssRelatedByCustomersId;
	}

	/**
	 * Sets a collection of EventsRelatedByCustomersId objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $eventssRelatedByCustomersId A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setEventssRelatedByCustomersId(PropelCollection $eventssRelatedByCustomersId, PropelPDO $con = null)
	{
		$this->eventssRelatedByCustomersIdScheduledForDeletion = $this->getEventssRelatedByCustomersId(new Criteria(), $con)->diff($eventssRelatedByCustomersId);

		foreach ($eventssRelatedByCustomersId as $eventsRelatedByCustomersId) {
			// Fix issue with collection modified by reference
			if ($eventsRelatedByCustomersId->isNew()) {
				$eventsRelatedByCustomersId->setCustomersRelatedByCustomersId($this);
			}
			$this->addEventsRelatedByCustomersId($eventsRelatedByCustomersId);
		}

		$this->collEventssRelatedByCustomersId = $eventssRelatedByCustomersId;
	}

	/**
	 * Returns the number of related Events objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Events objects.
	 * @throws     PropelException
	 */
	public function countEventssRelatedByCustomersId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collEventssRelatedByCustomersId || null !== $criteria) {
			if ($this->isNew() && null === $this->collEventssRelatedByCustomersId) {
				return 0;
			} else {
				$query = EventsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCustomersRelatedByCustomersId($this)
					->count($con);
			}
		} else {
			return count($this->collEventssRelatedByCustomersId);
		}
	}

	/**
	 * Method called to associate a Events object to this object
	 * through the Events foreign key attribute.
	 *
	 * @param      Events $l Events
	 * @return     Customers The current object (for fluent API support)
	 */
	public function addEventsRelatedByCustomersId(Events $l)
	{
		if ($this->collEventssRelatedByCustomersId === null) {
			$this->initEventssRelatedByCustomersId();
		}
		if (!$this->collEventssRelatedByCustomersId->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddEventsRelatedByCustomersId($l);
		}

		return $this;
	}

	/**
	 * @param	EventsRelatedByCustomersId $eventsRelatedByCustomersId The eventsRelatedByCustomersId object to add.
	 */
	protected function doAddEventsRelatedByCustomersId($eventsRelatedByCustomersId)
	{
		$this->collEventssRelatedByCustomersId[]= $eventsRelatedByCustomersId;
		$eventsRelatedByCustomersId->setCustomersRelatedByCustomersId($this);
	}

	/**
	 * Gets a single GothiaAccounts object, which is related to this object by a one-to-one relationship.
	 *
	 * @param      PropelPDO $con optional connection object
	 * @return     GothiaAccounts
	 * @throws     PropelException
	 */
	public function getGothiaAccounts(PropelPDO $con = null)
	{

		if ($this->singleGothiaAccounts === null && !$this->isNew()) {
			$this->singleGothiaAccounts = GothiaAccountsQuery::create()->findPk($this->getPrimaryKey(), $con);
		}

		return $this->singleGothiaAccounts;
	}

	/**
	 * Sets a single GothiaAccounts object as related to this object by a one-to-one relationship.
	 *
	 * @param      GothiaAccounts $v GothiaAccounts
	 * @return     Customers The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setGothiaAccounts(GothiaAccounts $v = null)
	{
		$this->singleGothiaAccounts = $v;

		// Make sure that that the passed-in GothiaAccounts isn't already associated with this object
		if ($v !== null && $v->getCustomers() === null) {
			$v->setCustomers($this);
		}

		return $this;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->first_name = null;
		$this->last_name = null;
		$this->initials = null;
		$this->password = null;
		$this->email = null;
		$this->phone = null;
		$this->password_clear = null;
		$this->billing_address_line_1 = null;
		$this->billing_address_line_2 = null;
		$this->billing_postal_code = null;
		$this->billing_city = null;
		$this->billing_country = null;
		$this->billing_countries_id = null;
		$this->billing_state_province = null;
		$this->delivery_address_line_1 = null;
		$this->delivery_address_line_2 = null;
		$this->delivery_postal_code = null;
		$this->delivery_city = null;
		$this->delivery_country = null;
		$this->delivery_countries_id = null;
		$this->delivery_state_province = null;
		$this->delivery_company_name = null;
		$this->discount = null;
		$this->groups_id = null;
		$this->is_active = null;
		$this->languages_id = null;
		$this->countries_id = null;
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
			if ($this->singleConsultantsInfo) {
				$this->singleConsultantsInfo->clearAllReferences($deep);
			}
			if ($this->collCouponsToCustomerss) {
				foreach ($this->collCouponsToCustomerss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEventssRelatedByConsultantsId) {
				foreach ($this->collEventssRelatedByConsultantsId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEventssRelatedByCustomersId) {
				foreach ($this->collEventssRelatedByCustomersId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->singleGothiaAccounts) {
				$this->singleGothiaAccounts->clearAllReferences($deep);
			}
		} // if ($deep)

		if ($this->singleConsultantsInfo instanceof PropelCollection) {
			$this->singleConsultantsInfo->clearIterator();
		}
		$this->singleConsultantsInfo = null;
		if ($this->collCouponsToCustomerss instanceof PropelCollection) {
			$this->collCouponsToCustomerss->clearIterator();
		}
		$this->collCouponsToCustomerss = null;
		if ($this->collEventssRelatedByConsultantsId instanceof PropelCollection) {
			$this->collEventssRelatedByConsultantsId->clearIterator();
		}
		$this->collEventssRelatedByConsultantsId = null;
		if ($this->collEventssRelatedByCustomersId instanceof PropelCollection) {
			$this->collEventssRelatedByCustomersId->clearIterator();
		}
		$this->collEventssRelatedByCustomersId = null;
		if ($this->singleGothiaAccounts instanceof PropelCollection) {
			$this->singleGothiaAccounts->clearIterator();
		}
		$this->singleGothiaAccounts = null;
		$this->aGroups = null;
		$this->aLanguages = null;
		$this->aCountriesRelatedByCountriesId = null;
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
		return (string) $this->exportTo(CustomersPeer::DEFAULT_STRING_FORMAT);
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     Customers The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = CustomersPeer::UPDATED_AT;
		return $this;
	}

} // BaseCustomers

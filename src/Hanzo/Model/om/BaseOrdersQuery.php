<?php

namespace Hanzo\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Hanzo\Model\Countries;
use Hanzo\Model\Customers;
use Hanzo\Model\Events;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersAttributes;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersStateLog;
use Hanzo\Model\OrdersSyncLog;
use Hanzo\Model\OrdersToCoupons;
use Hanzo\Model\OrdersVersions;

/**
 * @method OrdersQuery orderById($order = Criteria::ASC) Order by the id column
 * @method OrdersQuery orderByVersionId($order = Criteria::ASC) Order by the version_id column
 * @method OrdersQuery orderBySessionId($order = Criteria::ASC) Order by the session_id column
 * @method OrdersQuery orderByPaymentGatewayId($order = Criteria::ASC) Order by the payment_gateway_id column
 * @method OrdersQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method OrdersQuery orderByInEdit($order = Criteria::ASC) Order by the in_edit column
 * @method OrdersQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method OrdersQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method OrdersQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 * @method OrdersQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method OrdersQuery orderByPhone($order = Criteria::ASC) Order by the phone column
 * @method OrdersQuery orderByLanguagesId($order = Criteria::ASC) Order by the languages_id column
 * @method OrdersQuery orderByCurrencyCode($order = Criteria::ASC) Order by the currency_code column
 * @method OrdersQuery orderByBillingTitle($order = Criteria::ASC) Order by the billing_title column
 * @method OrdersQuery orderByBillingFirstName($order = Criteria::ASC) Order by the billing_first_name column
 * @method OrdersQuery orderByBillingLastName($order = Criteria::ASC) Order by the billing_last_name column
 * @method OrdersQuery orderByBillingAddressLine1($order = Criteria::ASC) Order by the billing_address_line_1 column
 * @method OrdersQuery orderByBillingAddressLine2($order = Criteria::ASC) Order by the billing_address_line_2 column
 * @method OrdersQuery orderByBillingPostalCode($order = Criteria::ASC) Order by the billing_postal_code column
 * @method OrdersQuery orderByBillingCity($order = Criteria::ASC) Order by the billing_city column
 * @method OrdersQuery orderByBillingCountry($order = Criteria::ASC) Order by the billing_country column
 * @method OrdersQuery orderByBillingCountriesId($order = Criteria::ASC) Order by the billing_countries_id column
 * @method OrdersQuery orderByBillingStateProvince($order = Criteria::ASC) Order by the billing_state_province column
 * @method OrdersQuery orderByBillingCompanyName($order = Criteria::ASC) Order by the billing_company_name column
 * @method OrdersQuery orderByBillingMethod($order = Criteria::ASC) Order by the billing_method column
 * @method OrdersQuery orderByBillingExternalAddressId($order = Criteria::ASC) Order by the billing_external_address_id column
 * @method OrdersQuery orderByDeliveryTitle($order = Criteria::ASC) Order by the delivery_title column
 * @method OrdersQuery orderByDeliveryFirstName($order = Criteria::ASC) Order by the delivery_first_name column
 * @method OrdersQuery orderByDeliveryLastName($order = Criteria::ASC) Order by the delivery_last_name column
 * @method OrdersQuery orderByDeliveryAddressLine1($order = Criteria::ASC) Order by the delivery_address_line_1 column
 * @method OrdersQuery orderByDeliveryAddressLine2($order = Criteria::ASC) Order by the delivery_address_line_2 column
 * @method OrdersQuery orderByDeliveryPostalCode($order = Criteria::ASC) Order by the delivery_postal_code column
 * @method OrdersQuery orderByDeliveryCity($order = Criteria::ASC) Order by the delivery_city column
 * @method OrdersQuery orderByDeliveryCountry($order = Criteria::ASC) Order by the delivery_country column
 * @method OrdersQuery orderByDeliveryCountriesId($order = Criteria::ASC) Order by the delivery_countries_id column
 * @method OrdersQuery orderByDeliveryStateProvince($order = Criteria::ASC) Order by the delivery_state_province column
 * @method OrdersQuery orderByDeliveryCompanyName($order = Criteria::ASC) Order by the delivery_company_name column
 * @method OrdersQuery orderByDeliveryMethod($order = Criteria::ASC) Order by the delivery_method column
 * @method OrdersQuery orderByDeliveryExternalAddressId($order = Criteria::ASC) Order by the delivery_external_address_id column
 * @method OrdersQuery orderByEventsId($order = Criteria::ASC) Order by the events_id column
 * @method OrdersQuery orderByFinishedAt($order = Criteria::ASC) Order by the finished_at column
 * @method OrdersQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method OrdersQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method OrdersQuery groupById() Group by the id column
 * @method OrdersQuery groupByVersionId() Group by the version_id column
 * @method OrdersQuery groupBySessionId() Group by the session_id column
 * @method OrdersQuery groupByPaymentGatewayId() Group by the payment_gateway_id column
 * @method OrdersQuery groupByState() Group by the state column
 * @method OrdersQuery groupByInEdit() Group by the in_edit column
 * @method OrdersQuery groupByCustomersId() Group by the customers_id column
 * @method OrdersQuery groupByFirstName() Group by the first_name column
 * @method OrdersQuery groupByLastName() Group by the last_name column
 * @method OrdersQuery groupByEmail() Group by the email column
 * @method OrdersQuery groupByPhone() Group by the phone column
 * @method OrdersQuery groupByLanguagesId() Group by the languages_id column
 * @method OrdersQuery groupByCurrencyCode() Group by the currency_code column
 * @method OrdersQuery groupByBillingTitle() Group by the billing_title column
 * @method OrdersQuery groupByBillingFirstName() Group by the billing_first_name column
 * @method OrdersQuery groupByBillingLastName() Group by the billing_last_name column
 * @method OrdersQuery groupByBillingAddressLine1() Group by the billing_address_line_1 column
 * @method OrdersQuery groupByBillingAddressLine2() Group by the billing_address_line_2 column
 * @method OrdersQuery groupByBillingPostalCode() Group by the billing_postal_code column
 * @method OrdersQuery groupByBillingCity() Group by the billing_city column
 * @method OrdersQuery groupByBillingCountry() Group by the billing_country column
 * @method OrdersQuery groupByBillingCountriesId() Group by the billing_countries_id column
 * @method OrdersQuery groupByBillingStateProvince() Group by the billing_state_province column
 * @method OrdersQuery groupByBillingCompanyName() Group by the billing_company_name column
 * @method OrdersQuery groupByBillingMethod() Group by the billing_method column
 * @method OrdersQuery groupByBillingExternalAddressId() Group by the billing_external_address_id column
 * @method OrdersQuery groupByDeliveryTitle() Group by the delivery_title column
 * @method OrdersQuery groupByDeliveryFirstName() Group by the delivery_first_name column
 * @method OrdersQuery groupByDeliveryLastName() Group by the delivery_last_name column
 * @method OrdersQuery groupByDeliveryAddressLine1() Group by the delivery_address_line_1 column
 * @method OrdersQuery groupByDeliveryAddressLine2() Group by the delivery_address_line_2 column
 * @method OrdersQuery groupByDeliveryPostalCode() Group by the delivery_postal_code column
 * @method OrdersQuery groupByDeliveryCity() Group by the delivery_city column
 * @method OrdersQuery groupByDeliveryCountry() Group by the delivery_country column
 * @method OrdersQuery groupByDeliveryCountriesId() Group by the delivery_countries_id column
 * @method OrdersQuery groupByDeliveryStateProvince() Group by the delivery_state_province column
 * @method OrdersQuery groupByDeliveryCompanyName() Group by the delivery_company_name column
 * @method OrdersQuery groupByDeliveryMethod() Group by the delivery_method column
 * @method OrdersQuery groupByDeliveryExternalAddressId() Group by the delivery_external_address_id column
 * @method OrdersQuery groupByEventsId() Group by the events_id column
 * @method OrdersQuery groupByFinishedAt() Group by the finished_at column
 * @method OrdersQuery groupByCreatedAt() Group by the created_at column
 * @method OrdersQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method OrdersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method OrdersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method OrdersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method OrdersQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method OrdersQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method OrdersQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method OrdersQuery leftJoinCountriesRelatedByBillingCountriesId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CountriesRelatedByBillingCountriesId relation
 * @method OrdersQuery rightJoinCountriesRelatedByBillingCountriesId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CountriesRelatedByBillingCountriesId relation
 * @method OrdersQuery innerJoinCountriesRelatedByBillingCountriesId($relationAlias = null) Adds a INNER JOIN clause to the query using the CountriesRelatedByBillingCountriesId relation
 *
 * @method OrdersQuery leftJoinCountriesRelatedByDeliveryCountriesId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CountriesRelatedByDeliveryCountriesId relation
 * @method OrdersQuery rightJoinCountriesRelatedByDeliveryCountriesId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CountriesRelatedByDeliveryCountriesId relation
 * @method OrdersQuery innerJoinCountriesRelatedByDeliveryCountriesId($relationAlias = null) Adds a INNER JOIN clause to the query using the CountriesRelatedByDeliveryCountriesId relation
 *
 * @method OrdersQuery leftJoinEvents($relationAlias = null) Adds a LEFT JOIN clause to the query using the Events relation
 * @method OrdersQuery rightJoinEvents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Events relation
 * @method OrdersQuery innerJoinEvents($relationAlias = null) Adds a INNER JOIN clause to the query using the Events relation
 *
 * @method OrdersQuery leftJoinOrdersToCoupons($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersToCoupons relation
 * @method OrdersQuery rightJoinOrdersToCoupons($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersToCoupons relation
 * @method OrdersQuery innerJoinOrdersToCoupons($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersToCoupons relation
 *
 * @method OrdersQuery leftJoinOrdersAttributes($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersAttributes relation
 * @method OrdersQuery rightJoinOrdersAttributes($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersAttributes relation
 * @method OrdersQuery innerJoinOrdersAttributes($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersAttributes relation
 *
 * @method OrdersQuery leftJoinOrdersLines($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersLines relation
 * @method OrdersQuery rightJoinOrdersLines($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersLines relation
 * @method OrdersQuery innerJoinOrdersLines($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersLines relation
 *
 * @method OrdersQuery leftJoinOrdersStateLog($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersStateLog relation
 * @method OrdersQuery rightJoinOrdersStateLog($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersStateLog relation
 * @method OrdersQuery innerJoinOrdersStateLog($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersStateLog relation
 *
 * @method OrdersQuery leftJoinOrdersSyncLog($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersSyncLog relation
 * @method OrdersQuery rightJoinOrdersSyncLog($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersSyncLog relation
 * @method OrdersQuery innerJoinOrdersSyncLog($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersSyncLog relation
 *
 * @method OrdersQuery leftJoinOrdersVersions($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersVersions relation
 * @method OrdersQuery rightJoinOrdersVersions($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersVersions relation
 * @method OrdersQuery innerJoinOrdersVersions($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersVersions relation
 *
 * @method Orders findOne(PropelPDO $con = null) Return the first Orders matching the query
 * @method Orders findOneOrCreate(PropelPDO $con = null) Return the first Orders matching the query, or a new Orders object populated from the query conditions when no match is found
 *
 * @method Orders findOneByVersionId(int $version_id) Return the first Orders filtered by the version_id column
 * @method Orders findOneBySessionId(string $session_id) Return the first Orders filtered by the session_id column
 * @method Orders findOneByPaymentGatewayId(int $payment_gateway_id) Return the first Orders filtered by the payment_gateway_id column
 * @method Orders findOneByState(int $state) Return the first Orders filtered by the state column
 * @method Orders findOneByInEdit(boolean $in_edit) Return the first Orders filtered by the in_edit column
 * @method Orders findOneByCustomersId(int $customers_id) Return the first Orders filtered by the customers_id column
 * @method Orders findOneByFirstName(string $first_name) Return the first Orders filtered by the first_name column
 * @method Orders findOneByLastName(string $last_name) Return the first Orders filtered by the last_name column
 * @method Orders findOneByEmail(string $email) Return the first Orders filtered by the email column
 * @method Orders findOneByPhone(string $phone) Return the first Orders filtered by the phone column
 * @method Orders findOneByLanguagesId(int $languages_id) Return the first Orders filtered by the languages_id column
 * @method Orders findOneByCurrencyCode(string $currency_code) Return the first Orders filtered by the currency_code column
 * @method Orders findOneByBillingTitle(string $billing_title) Return the first Orders filtered by the billing_title column
 * @method Orders findOneByBillingFirstName(string $billing_first_name) Return the first Orders filtered by the billing_first_name column
 * @method Orders findOneByBillingLastName(string $billing_last_name) Return the first Orders filtered by the billing_last_name column
 * @method Orders findOneByBillingAddressLine1(string $billing_address_line_1) Return the first Orders filtered by the billing_address_line_1 column
 * @method Orders findOneByBillingAddressLine2(string $billing_address_line_2) Return the first Orders filtered by the billing_address_line_2 column
 * @method Orders findOneByBillingPostalCode(string $billing_postal_code) Return the first Orders filtered by the billing_postal_code column
 * @method Orders findOneByBillingCity(string $billing_city) Return the first Orders filtered by the billing_city column
 * @method Orders findOneByBillingCountry(string $billing_country) Return the first Orders filtered by the billing_country column
 * @method Orders findOneByBillingCountriesId(int $billing_countries_id) Return the first Orders filtered by the billing_countries_id column
 * @method Orders findOneByBillingStateProvince(string $billing_state_province) Return the first Orders filtered by the billing_state_province column
 * @method Orders findOneByBillingCompanyName(string $billing_company_name) Return the first Orders filtered by the billing_company_name column
 * @method Orders findOneByBillingMethod(string $billing_method) Return the first Orders filtered by the billing_method column
 * @method Orders findOneByBillingExternalAddressId(string $billing_external_address_id) Return the first Orders filtered by the billing_external_address_id column
 * @method Orders findOneByDeliveryTitle(string $delivery_title) Return the first Orders filtered by the delivery_title column
 * @method Orders findOneByDeliveryFirstName(string $delivery_first_name) Return the first Orders filtered by the delivery_first_name column
 * @method Orders findOneByDeliveryLastName(string $delivery_last_name) Return the first Orders filtered by the delivery_last_name column
 * @method Orders findOneByDeliveryAddressLine1(string $delivery_address_line_1) Return the first Orders filtered by the delivery_address_line_1 column
 * @method Orders findOneByDeliveryAddressLine2(string $delivery_address_line_2) Return the first Orders filtered by the delivery_address_line_2 column
 * @method Orders findOneByDeliveryPostalCode(string $delivery_postal_code) Return the first Orders filtered by the delivery_postal_code column
 * @method Orders findOneByDeliveryCity(string $delivery_city) Return the first Orders filtered by the delivery_city column
 * @method Orders findOneByDeliveryCountry(string $delivery_country) Return the first Orders filtered by the delivery_country column
 * @method Orders findOneByDeliveryCountriesId(int $delivery_countries_id) Return the first Orders filtered by the delivery_countries_id column
 * @method Orders findOneByDeliveryStateProvince(string $delivery_state_province) Return the first Orders filtered by the delivery_state_province column
 * @method Orders findOneByDeliveryCompanyName(string $delivery_company_name) Return the first Orders filtered by the delivery_company_name column
 * @method Orders findOneByDeliveryMethod(string $delivery_method) Return the first Orders filtered by the delivery_method column
 * @method Orders findOneByDeliveryExternalAddressId(string $delivery_external_address_id) Return the first Orders filtered by the delivery_external_address_id column
 * @method Orders findOneByEventsId(int $events_id) Return the first Orders filtered by the events_id column
 * @method Orders findOneByFinishedAt(string $finished_at) Return the first Orders filtered by the finished_at column
 * @method Orders findOneByCreatedAt(string $created_at) Return the first Orders filtered by the created_at column
 * @method Orders findOneByUpdatedAt(string $updated_at) Return the first Orders filtered by the updated_at column
 *
 * @method array findById(int $id) Return Orders objects filtered by the id column
 * @method array findByVersionId(int $version_id) Return Orders objects filtered by the version_id column
 * @method array findBySessionId(string $session_id) Return Orders objects filtered by the session_id column
 * @method array findByPaymentGatewayId(int $payment_gateway_id) Return Orders objects filtered by the payment_gateway_id column
 * @method array findByState(int $state) Return Orders objects filtered by the state column
 * @method array findByInEdit(boolean $in_edit) Return Orders objects filtered by the in_edit column
 * @method array findByCustomersId(int $customers_id) Return Orders objects filtered by the customers_id column
 * @method array findByFirstName(string $first_name) Return Orders objects filtered by the first_name column
 * @method array findByLastName(string $last_name) Return Orders objects filtered by the last_name column
 * @method array findByEmail(string $email) Return Orders objects filtered by the email column
 * @method array findByPhone(string $phone) Return Orders objects filtered by the phone column
 * @method array findByLanguagesId(int $languages_id) Return Orders objects filtered by the languages_id column
 * @method array findByCurrencyCode(string $currency_code) Return Orders objects filtered by the currency_code column
 * @method array findByBillingTitle(string $billing_title) Return Orders objects filtered by the billing_title column
 * @method array findByBillingFirstName(string $billing_first_name) Return Orders objects filtered by the billing_first_name column
 * @method array findByBillingLastName(string $billing_last_name) Return Orders objects filtered by the billing_last_name column
 * @method array findByBillingAddressLine1(string $billing_address_line_1) Return Orders objects filtered by the billing_address_line_1 column
 * @method array findByBillingAddressLine2(string $billing_address_line_2) Return Orders objects filtered by the billing_address_line_2 column
 * @method array findByBillingPostalCode(string $billing_postal_code) Return Orders objects filtered by the billing_postal_code column
 * @method array findByBillingCity(string $billing_city) Return Orders objects filtered by the billing_city column
 * @method array findByBillingCountry(string $billing_country) Return Orders objects filtered by the billing_country column
 * @method array findByBillingCountriesId(int $billing_countries_id) Return Orders objects filtered by the billing_countries_id column
 * @method array findByBillingStateProvince(string $billing_state_province) Return Orders objects filtered by the billing_state_province column
 * @method array findByBillingCompanyName(string $billing_company_name) Return Orders objects filtered by the billing_company_name column
 * @method array findByBillingMethod(string $billing_method) Return Orders objects filtered by the billing_method column
 * @method array findByBillingExternalAddressId(string $billing_external_address_id) Return Orders objects filtered by the billing_external_address_id column
 * @method array findByDeliveryTitle(string $delivery_title) Return Orders objects filtered by the delivery_title column
 * @method array findByDeliveryFirstName(string $delivery_first_name) Return Orders objects filtered by the delivery_first_name column
 * @method array findByDeliveryLastName(string $delivery_last_name) Return Orders objects filtered by the delivery_last_name column
 * @method array findByDeliveryAddressLine1(string $delivery_address_line_1) Return Orders objects filtered by the delivery_address_line_1 column
 * @method array findByDeliveryAddressLine2(string $delivery_address_line_2) Return Orders objects filtered by the delivery_address_line_2 column
 * @method array findByDeliveryPostalCode(string $delivery_postal_code) Return Orders objects filtered by the delivery_postal_code column
 * @method array findByDeliveryCity(string $delivery_city) Return Orders objects filtered by the delivery_city column
 * @method array findByDeliveryCountry(string $delivery_country) Return Orders objects filtered by the delivery_country column
 * @method array findByDeliveryCountriesId(int $delivery_countries_id) Return Orders objects filtered by the delivery_countries_id column
 * @method array findByDeliveryStateProvince(string $delivery_state_province) Return Orders objects filtered by the delivery_state_province column
 * @method array findByDeliveryCompanyName(string $delivery_company_name) Return Orders objects filtered by the delivery_company_name column
 * @method array findByDeliveryMethod(string $delivery_method) Return Orders objects filtered by the delivery_method column
 * @method array findByDeliveryExternalAddressId(string $delivery_external_address_id) Return Orders objects filtered by the delivery_external_address_id column
 * @method array findByEventsId(int $events_id) Return Orders objects filtered by the events_id column
 * @method array findByFinishedAt(string $finished_at) Return Orders objects filtered by the finished_at column
 * @method array findByCreatedAt(string $created_at) Return Orders objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Orders objects filtered by the updated_at column
 */
abstract class BaseOrdersQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseOrdersQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Hanzo\\Model\\Orders';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new OrdersQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   OrdersQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return OrdersQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof OrdersQuery) {
            return $criteria;
        }
        $query = new OrdersQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Orders|Orders[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrdersPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(OrdersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Orders A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Orders A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `version_id`, `session_id`, `payment_gateway_id`, `state`, `in_edit`, `customers_id`, `first_name`, `last_name`, `email`, `phone`, `languages_id`, `currency_code`, `billing_title`, `billing_first_name`, `billing_last_name`, `billing_address_line_1`, `billing_address_line_2`, `billing_postal_code`, `billing_city`, `billing_country`, `billing_countries_id`, `billing_state_province`, `billing_company_name`, `billing_method`, `billing_external_address_id`, `delivery_title`, `delivery_first_name`, `delivery_last_name`, `delivery_address_line_1`, `delivery_address_line_2`, `delivery_postal_code`, `delivery_city`, `delivery_country`, `delivery_countries_id`, `delivery_state_province`, `delivery_company_name`, `delivery_method`, `delivery_external_address_id`, `events_id`, `finished_at`, `created_at`, `updated_at` FROM `orders` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Orders();
            $obj->hydrate($row);
            OrdersPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Orders|Orders[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Orders[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrdersPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrdersPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrdersPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrdersPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the version_id column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionId(1234); // WHERE version_id = 1234
     * $query->filterByVersionId(array(12, 34)); // WHERE version_id IN (12, 34)
     * $query->filterByVersionId(array('min' => 12)); // WHERE version_id >= 12
     * $query->filterByVersionId(array('max' => 12)); // WHERE version_id <= 12
     * </code>
     *
     * @param     mixed $versionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByVersionId($versionId = null, $comparison = null)
    {
        if (is_array($versionId)) {
            $useMinMax = false;
            if (isset($versionId['min'])) {
                $this->addUsingAlias(OrdersPeer::VERSION_ID, $versionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionId['max'])) {
                $this->addUsingAlias(OrdersPeer::VERSION_ID, $versionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::VERSION_ID, $versionId, $comparison);
    }

    /**
     * Filter the query on the session_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySessionId('fooValue');   // WHERE session_id = 'fooValue'
     * $query->filterBySessionId('%fooValue%'); // WHERE session_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessionId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterBySessionId($sessionId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessionId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessionId)) {
                $sessionId = str_replace('*', '%', $sessionId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::SESSION_ID, $sessionId, $comparison);
    }

    /**
     * Filter the query on the payment_gateway_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPaymentGatewayId(1234); // WHERE payment_gateway_id = 1234
     * $query->filterByPaymentGatewayId(array(12, 34)); // WHERE payment_gateway_id IN (12, 34)
     * $query->filterByPaymentGatewayId(array('min' => 12)); // WHERE payment_gateway_id >= 12
     * $query->filterByPaymentGatewayId(array('max' => 12)); // WHERE payment_gateway_id <= 12
     * </code>
     *
     * @param     mixed $paymentGatewayId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByPaymentGatewayId($paymentGatewayId = null, $comparison = null)
    {
        if (is_array($paymentGatewayId)) {
            $useMinMax = false;
            if (isset($paymentGatewayId['min'])) {
                $this->addUsingAlias(OrdersPeer::PAYMENT_GATEWAY_ID, $paymentGatewayId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($paymentGatewayId['max'])) {
                $this->addUsingAlias(OrdersPeer::PAYMENT_GATEWAY_ID, $paymentGatewayId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::PAYMENT_GATEWAY_ID, $paymentGatewayId, $comparison);
    }

    /**
     * Filter the query on the state column
     *
     * Example usage:
     * <code>
     * $query->filterByState(1234); // WHERE state = 1234
     * $query->filterByState(array(12, 34)); // WHERE state IN (12, 34)
     * $query->filterByState(array('min' => 12)); // WHERE state >= 12
     * $query->filterByState(array('max' => 12)); // WHERE state <= 12
     * </code>
     *
     * @param     mixed $state The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByState($state = null, $comparison = null)
    {
        if (is_array($state)) {
            $useMinMax = false;
            if (isset($state['min'])) {
                $this->addUsingAlias(OrdersPeer::STATE, $state['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($state['max'])) {
                $this->addUsingAlias(OrdersPeer::STATE, $state['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::STATE, $state, $comparison);
    }

    /**
     * Filter the query on the in_edit column
     *
     * Example usage:
     * <code>
     * $query->filterByInEdit(true); // WHERE in_edit = true
     * $query->filterByInEdit('yes'); // WHERE in_edit = true
     * </code>
     *
     * @param     boolean|string $inEdit The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByInEdit($inEdit = null, $comparison = null)
    {
        if (is_string($inEdit)) {
            $inEdit = in_array(strtolower($inEdit), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(OrdersPeer::IN_EDIT, $inEdit, $comparison);
    }

    /**
     * Filter the query on the customers_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomersId(1234); // WHERE customers_id = 1234
     * $query->filterByCustomersId(array(12, 34)); // WHERE customers_id IN (12, 34)
     * $query->filterByCustomersId(array('min' => 12)); // WHERE customers_id >= 12
     * $query->filterByCustomersId(array('max' => 12)); // WHERE customers_id <= 12
     * </code>
     *
     * @see       filterByCustomers()
     *
     * @param     mixed $customersId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByCustomersId($customersId = null, $comparison = null)
    {
        if (is_array($customersId)) {
            $useMinMax = false;
            if (isset($customersId['min'])) {
                $this->addUsingAlias(OrdersPeer::CUSTOMERS_ID, $customersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customersId['max'])) {
                $this->addUsingAlias(OrdersPeer::CUSTOMERS_ID, $customersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::CUSTOMERS_ID, $customersId, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%'); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $firstName)) {
                $firstName = str_replace('*', '%', $firstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByLastName('%fooValue%'); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByLastName($lastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lastName)) {
                $lastName = str_replace('*', '%', $lastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::LAST_NAME, $lastName, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the phone column
     *
     * Example usage:
     * <code>
     * $query->filterByPhone('fooValue');   // WHERE phone = 'fooValue'
     * $query->filterByPhone('%fooValue%'); // WHERE phone LIKE '%fooValue%'
     * </code>
     *
     * @param     string $phone The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByPhone($phone = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($phone)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $phone)) {
                $phone = str_replace('*', '%', $phone);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::PHONE, $phone, $comparison);
    }

    /**
     * Filter the query on the languages_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLanguagesId(1234); // WHERE languages_id = 1234
     * $query->filterByLanguagesId(array(12, 34)); // WHERE languages_id IN (12, 34)
     * $query->filterByLanguagesId(array('min' => 12)); // WHERE languages_id >= 12
     * $query->filterByLanguagesId(array('max' => 12)); // WHERE languages_id <= 12
     * </code>
     *
     * @param     mixed $languagesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByLanguagesId($languagesId = null, $comparison = null)
    {
        if (is_array($languagesId)) {
            $useMinMax = false;
            if (isset($languagesId['min'])) {
                $this->addUsingAlias(OrdersPeer::LANGUAGES_ID, $languagesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($languagesId['max'])) {
                $this->addUsingAlias(OrdersPeer::LANGUAGES_ID, $languagesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::LANGUAGES_ID, $languagesId, $comparison);
    }

    /**
     * Filter the query on the currency_code column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyCode('fooValue');   // WHERE currency_code = 'fooValue'
     * $query->filterByCurrencyCode('%fooValue%'); // WHERE currency_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $currencyCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByCurrencyCode($currencyCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($currencyCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $currencyCode)) {
                $currencyCode = str_replace('*', '%', $currencyCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::CURRENCY_CODE, $currencyCode, $comparison);
    }

    /**
     * Filter the query on the billing_title column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingTitle('fooValue');   // WHERE billing_title = 'fooValue'
     * $query->filterByBillingTitle('%fooValue%'); // WHERE billing_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingTitle($billingTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingTitle)) {
                $billingTitle = str_replace('*', '%', $billingTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_TITLE, $billingTitle, $comparison);
    }

    /**
     * Filter the query on the billing_first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingFirstName('fooValue');   // WHERE billing_first_name = 'fooValue'
     * $query->filterByBillingFirstName('%fooValue%'); // WHERE billing_first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingFirstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingFirstName($billingFirstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingFirstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingFirstName)) {
                $billingFirstName = str_replace('*', '%', $billingFirstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_FIRST_NAME, $billingFirstName, $comparison);
    }

    /**
     * Filter the query on the billing_last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingLastName('fooValue');   // WHERE billing_last_name = 'fooValue'
     * $query->filterByBillingLastName('%fooValue%'); // WHERE billing_last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingLastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingLastName($billingLastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingLastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingLastName)) {
                $billingLastName = str_replace('*', '%', $billingLastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_LAST_NAME, $billingLastName, $comparison);
    }

    /**
     * Filter the query on the billing_address_line_1 column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingAddressLine1('fooValue');   // WHERE billing_address_line_1 = 'fooValue'
     * $query->filterByBillingAddressLine1('%fooValue%'); // WHERE billing_address_line_1 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingAddressLine1 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingAddressLine1($billingAddressLine1 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingAddressLine1)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingAddressLine1)) {
                $billingAddressLine1 = str_replace('*', '%', $billingAddressLine1);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_ADDRESS_LINE_1, $billingAddressLine1, $comparison);
    }

    /**
     * Filter the query on the billing_address_line_2 column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingAddressLine2('fooValue');   // WHERE billing_address_line_2 = 'fooValue'
     * $query->filterByBillingAddressLine2('%fooValue%'); // WHERE billing_address_line_2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingAddressLine2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingAddressLine2($billingAddressLine2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingAddressLine2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingAddressLine2)) {
                $billingAddressLine2 = str_replace('*', '%', $billingAddressLine2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_ADDRESS_LINE_2, $billingAddressLine2, $comparison);
    }

    /**
     * Filter the query on the billing_postal_code column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingPostalCode('fooValue');   // WHERE billing_postal_code = 'fooValue'
     * $query->filterByBillingPostalCode('%fooValue%'); // WHERE billing_postal_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingPostalCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingPostalCode($billingPostalCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingPostalCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingPostalCode)) {
                $billingPostalCode = str_replace('*', '%', $billingPostalCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_POSTAL_CODE, $billingPostalCode, $comparison);
    }

    /**
     * Filter the query on the billing_city column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingCity('fooValue');   // WHERE billing_city = 'fooValue'
     * $query->filterByBillingCity('%fooValue%'); // WHERE billing_city LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingCity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingCity($billingCity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingCity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingCity)) {
                $billingCity = str_replace('*', '%', $billingCity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_CITY, $billingCity, $comparison);
    }

    /**
     * Filter the query on the billing_country column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingCountry('fooValue');   // WHERE billing_country = 'fooValue'
     * $query->filterByBillingCountry('%fooValue%'); // WHERE billing_country LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingCountry The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingCountry($billingCountry = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingCountry)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingCountry)) {
                $billingCountry = str_replace('*', '%', $billingCountry);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_COUNTRY, $billingCountry, $comparison);
    }

    /**
     * Filter the query on the billing_countries_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingCountriesId(1234); // WHERE billing_countries_id = 1234
     * $query->filterByBillingCountriesId(array(12, 34)); // WHERE billing_countries_id IN (12, 34)
     * $query->filterByBillingCountriesId(array('min' => 12)); // WHERE billing_countries_id >= 12
     * $query->filterByBillingCountriesId(array('max' => 12)); // WHERE billing_countries_id <= 12
     * </code>
     *
     * @see       filterByCountriesRelatedByBillingCountriesId()
     *
     * @param     mixed $billingCountriesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingCountriesId($billingCountriesId = null, $comparison = null)
    {
        if (is_array($billingCountriesId)) {
            $useMinMax = false;
            if (isset($billingCountriesId['min'])) {
                $this->addUsingAlias(OrdersPeer::BILLING_COUNTRIES_ID, $billingCountriesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($billingCountriesId['max'])) {
                $this->addUsingAlias(OrdersPeer::BILLING_COUNTRIES_ID, $billingCountriesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_COUNTRIES_ID, $billingCountriesId, $comparison);
    }

    /**
     * Filter the query on the billing_state_province column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingStateProvince('fooValue');   // WHERE billing_state_province = 'fooValue'
     * $query->filterByBillingStateProvince('%fooValue%'); // WHERE billing_state_province LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingStateProvince The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingStateProvince($billingStateProvince = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingStateProvince)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingStateProvince)) {
                $billingStateProvince = str_replace('*', '%', $billingStateProvince);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_STATE_PROVINCE, $billingStateProvince, $comparison);
    }

    /**
     * Filter the query on the billing_company_name column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingCompanyName('fooValue');   // WHERE billing_company_name = 'fooValue'
     * $query->filterByBillingCompanyName('%fooValue%'); // WHERE billing_company_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingCompanyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingCompanyName($billingCompanyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingCompanyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingCompanyName)) {
                $billingCompanyName = str_replace('*', '%', $billingCompanyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_COMPANY_NAME, $billingCompanyName, $comparison);
    }

    /**
     * Filter the query on the billing_method column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingMethod('fooValue');   // WHERE billing_method = 'fooValue'
     * $query->filterByBillingMethod('%fooValue%'); // WHERE billing_method LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingMethod The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingMethod($billingMethod = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingMethod)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingMethod)) {
                $billingMethod = str_replace('*', '%', $billingMethod);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_METHOD, $billingMethod, $comparison);
    }

    /**
     * Filter the query on the billing_external_address_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBillingExternalAddressId('fooValue');   // WHERE billing_external_address_id = 'fooValue'
     * $query->filterByBillingExternalAddressId('%fooValue%'); // WHERE billing_external_address_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $billingExternalAddressId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByBillingExternalAddressId($billingExternalAddressId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($billingExternalAddressId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $billingExternalAddressId)) {
                $billingExternalAddressId = str_replace('*', '%', $billingExternalAddressId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::BILLING_EXTERNAL_ADDRESS_ID, $billingExternalAddressId, $comparison);
    }

    /**
     * Filter the query on the delivery_title column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryTitle('fooValue');   // WHERE delivery_title = 'fooValue'
     * $query->filterByDeliveryTitle('%fooValue%'); // WHERE delivery_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryTitle($deliveryTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryTitle)) {
                $deliveryTitle = str_replace('*', '%', $deliveryTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_TITLE, $deliveryTitle, $comparison);
    }

    /**
     * Filter the query on the delivery_first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryFirstName('fooValue');   // WHERE delivery_first_name = 'fooValue'
     * $query->filterByDeliveryFirstName('%fooValue%'); // WHERE delivery_first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryFirstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryFirstName($deliveryFirstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryFirstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryFirstName)) {
                $deliveryFirstName = str_replace('*', '%', $deliveryFirstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_FIRST_NAME, $deliveryFirstName, $comparison);
    }

    /**
     * Filter the query on the delivery_last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryLastName('fooValue');   // WHERE delivery_last_name = 'fooValue'
     * $query->filterByDeliveryLastName('%fooValue%'); // WHERE delivery_last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryLastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryLastName($deliveryLastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryLastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryLastName)) {
                $deliveryLastName = str_replace('*', '%', $deliveryLastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_LAST_NAME, $deliveryLastName, $comparison);
    }

    /**
     * Filter the query on the delivery_address_line_1 column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryAddressLine1('fooValue');   // WHERE delivery_address_line_1 = 'fooValue'
     * $query->filterByDeliveryAddressLine1('%fooValue%'); // WHERE delivery_address_line_1 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryAddressLine1 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryAddressLine1($deliveryAddressLine1 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryAddressLine1)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryAddressLine1)) {
                $deliveryAddressLine1 = str_replace('*', '%', $deliveryAddressLine1);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_ADDRESS_LINE_1, $deliveryAddressLine1, $comparison);
    }

    /**
     * Filter the query on the delivery_address_line_2 column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryAddressLine2('fooValue');   // WHERE delivery_address_line_2 = 'fooValue'
     * $query->filterByDeliveryAddressLine2('%fooValue%'); // WHERE delivery_address_line_2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryAddressLine2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryAddressLine2($deliveryAddressLine2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryAddressLine2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryAddressLine2)) {
                $deliveryAddressLine2 = str_replace('*', '%', $deliveryAddressLine2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_ADDRESS_LINE_2, $deliveryAddressLine2, $comparison);
    }

    /**
     * Filter the query on the delivery_postal_code column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryPostalCode('fooValue');   // WHERE delivery_postal_code = 'fooValue'
     * $query->filterByDeliveryPostalCode('%fooValue%'); // WHERE delivery_postal_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryPostalCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryPostalCode($deliveryPostalCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryPostalCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryPostalCode)) {
                $deliveryPostalCode = str_replace('*', '%', $deliveryPostalCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_POSTAL_CODE, $deliveryPostalCode, $comparison);
    }

    /**
     * Filter the query on the delivery_city column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryCity('fooValue');   // WHERE delivery_city = 'fooValue'
     * $query->filterByDeliveryCity('%fooValue%'); // WHERE delivery_city LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryCity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryCity($deliveryCity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryCity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryCity)) {
                $deliveryCity = str_replace('*', '%', $deliveryCity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_CITY, $deliveryCity, $comparison);
    }

    /**
     * Filter the query on the delivery_country column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryCountry('fooValue');   // WHERE delivery_country = 'fooValue'
     * $query->filterByDeliveryCountry('%fooValue%'); // WHERE delivery_country LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryCountry The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryCountry($deliveryCountry = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryCountry)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryCountry)) {
                $deliveryCountry = str_replace('*', '%', $deliveryCountry);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_COUNTRY, $deliveryCountry, $comparison);
    }

    /**
     * Filter the query on the delivery_countries_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryCountriesId(1234); // WHERE delivery_countries_id = 1234
     * $query->filterByDeliveryCountriesId(array(12, 34)); // WHERE delivery_countries_id IN (12, 34)
     * $query->filterByDeliveryCountriesId(array('min' => 12)); // WHERE delivery_countries_id >= 12
     * $query->filterByDeliveryCountriesId(array('max' => 12)); // WHERE delivery_countries_id <= 12
     * </code>
     *
     * @see       filterByCountriesRelatedByDeliveryCountriesId()
     *
     * @param     mixed $deliveryCountriesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryCountriesId($deliveryCountriesId = null, $comparison = null)
    {
        if (is_array($deliveryCountriesId)) {
            $useMinMax = false;
            if (isset($deliveryCountriesId['min'])) {
                $this->addUsingAlias(OrdersPeer::DELIVERY_COUNTRIES_ID, $deliveryCountriesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($deliveryCountriesId['max'])) {
                $this->addUsingAlias(OrdersPeer::DELIVERY_COUNTRIES_ID, $deliveryCountriesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_COUNTRIES_ID, $deliveryCountriesId, $comparison);
    }

    /**
     * Filter the query on the delivery_state_province column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryStateProvince('fooValue');   // WHERE delivery_state_province = 'fooValue'
     * $query->filterByDeliveryStateProvince('%fooValue%'); // WHERE delivery_state_province LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryStateProvince The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryStateProvince($deliveryStateProvince = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryStateProvince)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryStateProvince)) {
                $deliveryStateProvince = str_replace('*', '%', $deliveryStateProvince);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_STATE_PROVINCE, $deliveryStateProvince, $comparison);
    }

    /**
     * Filter the query on the delivery_company_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryCompanyName('fooValue');   // WHERE delivery_company_name = 'fooValue'
     * $query->filterByDeliveryCompanyName('%fooValue%'); // WHERE delivery_company_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryCompanyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryCompanyName($deliveryCompanyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryCompanyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryCompanyName)) {
                $deliveryCompanyName = str_replace('*', '%', $deliveryCompanyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_COMPANY_NAME, $deliveryCompanyName, $comparison);
    }

    /**
     * Filter the query on the delivery_method column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryMethod('fooValue');   // WHERE delivery_method = 'fooValue'
     * $query->filterByDeliveryMethod('%fooValue%'); // WHERE delivery_method LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryMethod The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryMethod($deliveryMethod = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryMethod)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryMethod)) {
                $deliveryMethod = str_replace('*', '%', $deliveryMethod);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_METHOD, $deliveryMethod, $comparison);
    }

    /**
     * Filter the query on the delivery_external_address_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDeliveryExternalAddressId('fooValue');   // WHERE delivery_external_address_id = 'fooValue'
     * $query->filterByDeliveryExternalAddressId('%fooValue%'); // WHERE delivery_external_address_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deliveryExternalAddressId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByDeliveryExternalAddressId($deliveryExternalAddressId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deliveryExternalAddressId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deliveryExternalAddressId)) {
                $deliveryExternalAddressId = str_replace('*', '%', $deliveryExternalAddressId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersPeer::DELIVERY_EXTERNAL_ADDRESS_ID, $deliveryExternalAddressId, $comparison);
    }

    /**
     * Filter the query on the events_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEventsId(1234); // WHERE events_id = 1234
     * $query->filterByEventsId(array(12, 34)); // WHERE events_id IN (12, 34)
     * $query->filterByEventsId(array('min' => 12)); // WHERE events_id >= 12
     * $query->filterByEventsId(array('max' => 12)); // WHERE events_id <= 12
     * </code>
     *
     * @see       filterByEvents()
     *
     * @param     mixed $eventsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByEventsId($eventsId = null, $comparison = null)
    {
        if (is_array($eventsId)) {
            $useMinMax = false;
            if (isset($eventsId['min'])) {
                $this->addUsingAlias(OrdersPeer::EVENTS_ID, $eventsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($eventsId['max'])) {
                $this->addUsingAlias(OrdersPeer::EVENTS_ID, $eventsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::EVENTS_ID, $eventsId, $comparison);
    }

    /**
     * Filter the query on the finished_at column
     *
     * Example usage:
     * <code>
     * $query->filterByFinishedAt('2011-03-14'); // WHERE finished_at = '2011-03-14'
     * $query->filterByFinishedAt('now'); // WHERE finished_at = '2011-03-14'
     * $query->filterByFinishedAt(array('max' => 'yesterday')); // WHERE finished_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $finishedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByFinishedAt($finishedAt = null, $comparison = null)
    {
        if (is_array($finishedAt)) {
            $useMinMax = false;
            if (isset($finishedAt['min'])) {
                $this->addUsingAlias(OrdersPeer::FINISHED_AT, $finishedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($finishedAt['max'])) {
                $this->addUsingAlias(OrdersPeer::FINISHED_AT, $finishedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::FINISHED_AT, $finishedAt, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(OrdersPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(OrdersPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(OrdersPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(OrdersPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related Customers object
     *
     * @param   Customers|PropelObjectCollection $customers The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCustomers($customers, $comparison = null)
    {
        if ($customers instanceof Customers) {
            return $this
                ->addUsingAlias(OrdersPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
        } elseif ($customers instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCustomers() only accepts arguments of type Customers or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Customers relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinCustomers($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Customers');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Customers');
        }

        return $this;
    }

    /**
     * Use the Customers relation Customers object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CustomersQuery A secondary query class using the current class as primary query
     */
    public function useCustomersQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCustomers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
    }

    /**
     * Filter the query by a related Countries object
     *
     * @param   Countries|PropelObjectCollection $countries The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCountriesRelatedByBillingCountriesId($countries, $comparison = null)
    {
        if ($countries instanceof Countries) {
            return $this
                ->addUsingAlias(OrdersPeer::BILLING_COUNTRIES_ID, $countries->getId(), $comparison);
        } elseif ($countries instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersPeer::BILLING_COUNTRIES_ID, $countries->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCountriesRelatedByBillingCountriesId() only accepts arguments of type Countries or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CountriesRelatedByBillingCountriesId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinCountriesRelatedByBillingCountriesId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CountriesRelatedByBillingCountriesId');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CountriesRelatedByBillingCountriesId');
        }

        return $this;
    }

    /**
     * Use the CountriesRelatedByBillingCountriesId relation Countries object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CountriesQuery A secondary query class using the current class as primary query
     */
    public function useCountriesRelatedByBillingCountriesIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCountriesRelatedByBillingCountriesId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CountriesRelatedByBillingCountriesId', '\Hanzo\Model\CountriesQuery');
    }

    /**
     * Filter the query by a related Countries object
     *
     * @param   Countries|PropelObjectCollection $countries The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCountriesRelatedByDeliveryCountriesId($countries, $comparison = null)
    {
        if ($countries instanceof Countries) {
            return $this
                ->addUsingAlias(OrdersPeer::DELIVERY_COUNTRIES_ID, $countries->getId(), $comparison);
        } elseif ($countries instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersPeer::DELIVERY_COUNTRIES_ID, $countries->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCountriesRelatedByDeliveryCountriesId() only accepts arguments of type Countries or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CountriesRelatedByDeliveryCountriesId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinCountriesRelatedByDeliveryCountriesId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CountriesRelatedByDeliveryCountriesId');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CountriesRelatedByDeliveryCountriesId');
        }

        return $this;
    }

    /**
     * Use the CountriesRelatedByDeliveryCountriesId relation Countries object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CountriesQuery A secondary query class using the current class as primary query
     */
    public function useCountriesRelatedByDeliveryCountriesIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCountriesRelatedByDeliveryCountriesId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CountriesRelatedByDeliveryCountriesId', '\Hanzo\Model\CountriesQuery');
    }

    /**
     * Filter the query by a related Events object
     *
     * @param   Events|PropelObjectCollection $events The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByEvents($events, $comparison = null)
    {
        if ($events instanceof Events) {
            return $this
                ->addUsingAlias(OrdersPeer::EVENTS_ID, $events->getId(), $comparison);
        } elseif ($events instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersPeer::EVENTS_ID, $events->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByEvents() only accepts arguments of type Events or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Events relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinEvents($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Events');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Events');
        }

        return $this;
    }

    /**
     * Use the Events relation Events object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\EventsQuery A secondary query class using the current class as primary query
     */
    public function useEventsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinEvents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Events', '\Hanzo\Model\EventsQuery');
    }

    /**
     * Filter the query by a related OrdersToCoupons object
     *
     * @param   OrdersToCoupons|PropelObjectCollection $ordersToCoupons  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersToCoupons($ordersToCoupons, $comparison = null)
    {
        if ($ordersToCoupons instanceof OrdersToCoupons) {
            return $this
                ->addUsingAlias(OrdersPeer::ID, $ordersToCoupons->getOrdersId(), $comparison);
        } elseif ($ordersToCoupons instanceof PropelObjectCollection) {
            return $this
                ->useOrdersToCouponsQuery()
                ->filterByPrimaryKeys($ordersToCoupons->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersToCoupons() only accepts arguments of type OrdersToCoupons or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersToCoupons relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinOrdersToCoupons($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersToCoupons');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrdersToCoupons');
        }

        return $this;
    }

    /**
     * Use the OrdersToCoupons relation OrdersToCoupons object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersToCouponsQuery A secondary query class using the current class as primary query
     */
    public function useOrdersToCouponsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrdersToCoupons($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersToCoupons', '\Hanzo\Model\OrdersToCouponsQuery');
    }

    /**
     * Filter the query by a related OrdersAttributes object
     *
     * @param   OrdersAttributes|PropelObjectCollection $ordersAttributes  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersAttributes($ordersAttributes, $comparison = null)
    {
        if ($ordersAttributes instanceof OrdersAttributes) {
            return $this
                ->addUsingAlias(OrdersPeer::ID, $ordersAttributes->getOrdersId(), $comparison);
        } elseif ($ordersAttributes instanceof PropelObjectCollection) {
            return $this
                ->useOrdersAttributesQuery()
                ->filterByPrimaryKeys($ordersAttributes->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersAttributes() only accepts arguments of type OrdersAttributes or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersAttributes relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinOrdersAttributes($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersAttributes');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrdersAttributes');
        }

        return $this;
    }

    /**
     * Use the OrdersAttributes relation OrdersAttributes object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersAttributesQuery A secondary query class using the current class as primary query
     */
    public function useOrdersAttributesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrdersAttributes($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersAttributes', '\Hanzo\Model\OrdersAttributesQuery');
    }

    /**
     * Filter the query by a related OrdersLines object
     *
     * @param   OrdersLines|PropelObjectCollection $ordersLines  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersLines($ordersLines, $comparison = null)
    {
        if ($ordersLines instanceof OrdersLines) {
            return $this
                ->addUsingAlias(OrdersPeer::ID, $ordersLines->getOrdersId(), $comparison);
        } elseif ($ordersLines instanceof PropelObjectCollection) {
            return $this
                ->useOrdersLinesQuery()
                ->filterByPrimaryKeys($ordersLines->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersLines() only accepts arguments of type OrdersLines or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersLines relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinOrdersLines($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersLines');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrdersLines');
        }

        return $this;
    }

    /**
     * Use the OrdersLines relation OrdersLines object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersLinesQuery A secondary query class using the current class as primary query
     */
    public function useOrdersLinesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrdersLines($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersLines', '\Hanzo\Model\OrdersLinesQuery');
    }

    /**
     * Filter the query by a related OrdersStateLog object
     *
     * @param   OrdersStateLog|PropelObjectCollection $ordersStateLog  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersStateLog($ordersStateLog, $comparison = null)
    {
        if ($ordersStateLog instanceof OrdersStateLog) {
            return $this
                ->addUsingAlias(OrdersPeer::ID, $ordersStateLog->getOrdersId(), $comparison);
        } elseif ($ordersStateLog instanceof PropelObjectCollection) {
            return $this
                ->useOrdersStateLogQuery()
                ->filterByPrimaryKeys($ordersStateLog->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersStateLog() only accepts arguments of type OrdersStateLog or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersStateLog relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinOrdersStateLog($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersStateLog');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrdersStateLog');
        }

        return $this;
    }

    /**
     * Use the OrdersStateLog relation OrdersStateLog object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersStateLogQuery A secondary query class using the current class as primary query
     */
    public function useOrdersStateLogQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrdersStateLog($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersStateLog', '\Hanzo\Model\OrdersStateLogQuery');
    }

    /**
     * Filter the query by a related OrdersSyncLog object
     *
     * @param   OrdersSyncLog|PropelObjectCollection $ordersSyncLog  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersSyncLog($ordersSyncLog, $comparison = null)
    {
        if ($ordersSyncLog instanceof OrdersSyncLog) {
            return $this
                ->addUsingAlias(OrdersPeer::ID, $ordersSyncLog->getOrdersId(), $comparison);
        } elseif ($ordersSyncLog instanceof PropelObjectCollection) {
            return $this
                ->useOrdersSyncLogQuery()
                ->filterByPrimaryKeys($ordersSyncLog->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersSyncLog() only accepts arguments of type OrdersSyncLog or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersSyncLog relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinOrdersSyncLog($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersSyncLog');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrdersSyncLog');
        }

        return $this;
    }

    /**
     * Use the OrdersSyncLog relation OrdersSyncLog object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersSyncLogQuery A secondary query class using the current class as primary query
     */
    public function useOrdersSyncLogQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrdersSyncLog($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersSyncLog', '\Hanzo\Model\OrdersSyncLogQuery');
    }

    /**
     * Filter the query by a related OrdersVersions object
     *
     * @param   OrdersVersions|PropelObjectCollection $ordersVersions  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersVersions($ordersVersions, $comparison = null)
    {
        if ($ordersVersions instanceof OrdersVersions) {
            return $this
                ->addUsingAlias(OrdersPeer::ID, $ordersVersions->getOrdersId(), $comparison);
        } elseif ($ordersVersions instanceof PropelObjectCollection) {
            return $this
                ->useOrdersVersionsQuery()
                ->filterByPrimaryKeys($ordersVersions->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersVersions() only accepts arguments of type OrdersVersions or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersVersions relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function joinOrdersVersions($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersVersions');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrdersVersions');
        }

        return $this;
    }

    /**
     * Use the OrdersVersions relation OrdersVersions object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersVersionsQuery A secondary query class using the current class as primary query
     */
    public function useOrdersVersionsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrdersVersions($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersVersions', '\Hanzo\Model\OrdersVersionsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Orders $orders Object to remove from the list of results
     *
     * @return OrdersQuery The current query, for fluid interface
     */
    public function prune($orders = null)
    {
        if ($orders) {
            $this->addUsingAlias(OrdersPeer::ID, $orders->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     OrdersQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(OrdersPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     OrdersQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(OrdersPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     OrdersQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(OrdersPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     OrdersQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(OrdersPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     OrdersQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(OrdersPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     OrdersQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(OrdersPeer::CREATED_AT);
    }
}

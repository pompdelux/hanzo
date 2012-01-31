<?php

namespace Hanzo\Model\om;

use \Criteria;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelPDO;
use Hanzo\Model\Addresses;
use Hanzo\Model\ConsultantsInfo;
use Hanzo\Model\CouponsToCustomers;
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Events;
use Hanzo\Model\GothiaAccounts;
use Hanzo\Model\Groups;
use Hanzo\Model\Languages;

/**
 * Base class that represents a query for the 'customers' table.
 *
 * 
 *
 * @method     CustomersQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CustomersQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     CustomersQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 * @method     CustomersQuery orderByInitials($order = Criteria::ASC) Order by the initials column
 * @method     CustomersQuery orderByPassword($order = Criteria::ASC) Order by the password column
 * @method     CustomersQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     CustomersQuery orderByPhone($order = Criteria::ASC) Order by the phone column
 * @method     CustomersQuery orderByPasswordClear($order = Criteria::ASC) Order by the password_clear column
 * @method     CustomersQuery orderByDiscount($order = Criteria::ASC) Order by the discount column
 * @method     CustomersQuery orderByGroupsId($order = Criteria::ASC) Order by the groups_id column
 * @method     CustomersQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 * @method     CustomersQuery orderByLanguagesId($order = Criteria::ASC) Order by the languages_id column
 * @method     CustomersQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     CustomersQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     CustomersQuery groupById() Group by the id column
 * @method     CustomersQuery groupByFirstName() Group by the first_name column
 * @method     CustomersQuery groupByLastName() Group by the last_name column
 * @method     CustomersQuery groupByInitials() Group by the initials column
 * @method     CustomersQuery groupByPassword() Group by the password column
 * @method     CustomersQuery groupByEmail() Group by the email column
 * @method     CustomersQuery groupByPhone() Group by the phone column
 * @method     CustomersQuery groupByPasswordClear() Group by the password_clear column
 * @method     CustomersQuery groupByDiscount() Group by the discount column
 * @method     CustomersQuery groupByGroupsId() Group by the groups_id column
 * @method     CustomersQuery groupByIsActive() Group by the is_active column
 * @method     CustomersQuery groupByLanguagesId() Group by the languages_id column
 * @method     CustomersQuery groupByCreatedAt() Group by the created_at column
 * @method     CustomersQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     CustomersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CustomersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CustomersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CustomersQuery leftJoinGroups($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groups relation
 * @method     CustomersQuery rightJoinGroups($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groups relation
 * @method     CustomersQuery innerJoinGroups($relationAlias = null) Adds a INNER JOIN clause to the query using the Groups relation
 *
 * @method     CustomersQuery leftJoinLanguages($relationAlias = null) Adds a LEFT JOIN clause to the query using the Languages relation
 * @method     CustomersQuery rightJoinLanguages($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Languages relation
 * @method     CustomersQuery innerJoinLanguages($relationAlias = null) Adds a INNER JOIN clause to the query using the Languages relation
 *
 * @method     CustomersQuery leftJoinConsultantsInfo($relationAlias = null) Adds a LEFT JOIN clause to the query using the ConsultantsInfo relation
 * @method     CustomersQuery rightJoinConsultantsInfo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ConsultantsInfo relation
 * @method     CustomersQuery innerJoinConsultantsInfo($relationAlias = null) Adds a INNER JOIN clause to the query using the ConsultantsInfo relation
 *
 * @method     CustomersQuery leftJoinCouponsToCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the CouponsToCustomers relation
 * @method     CustomersQuery rightJoinCouponsToCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CouponsToCustomers relation
 * @method     CustomersQuery innerJoinCouponsToCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the CouponsToCustomers relation
 *
 * @method     CustomersQuery leftJoinAddresses($relationAlias = null) Adds a LEFT JOIN clause to the query using the Addresses relation
 * @method     CustomersQuery rightJoinAddresses($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Addresses relation
 * @method     CustomersQuery innerJoinAddresses($relationAlias = null) Adds a INNER JOIN clause to the query using the Addresses relation
 *
 * @method     CustomersQuery leftJoinEventsRelatedByConsultantsId($relationAlias = null) Adds a LEFT JOIN clause to the query using the EventsRelatedByConsultantsId relation
 * @method     CustomersQuery rightJoinEventsRelatedByConsultantsId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EventsRelatedByConsultantsId relation
 * @method     CustomersQuery innerJoinEventsRelatedByConsultantsId($relationAlias = null) Adds a INNER JOIN clause to the query using the EventsRelatedByConsultantsId relation
 *
 * @method     CustomersQuery leftJoinEventsRelatedByCustomersId($relationAlias = null) Adds a LEFT JOIN clause to the query using the EventsRelatedByCustomersId relation
 * @method     CustomersQuery rightJoinEventsRelatedByCustomersId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EventsRelatedByCustomersId relation
 * @method     CustomersQuery innerJoinEventsRelatedByCustomersId($relationAlias = null) Adds a INNER JOIN clause to the query using the EventsRelatedByCustomersId relation
 *
 * @method     CustomersQuery leftJoinGothiaAccounts($relationAlias = null) Adds a LEFT JOIN clause to the query using the GothiaAccounts relation
 * @method     CustomersQuery rightJoinGothiaAccounts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GothiaAccounts relation
 * @method     CustomersQuery innerJoinGothiaAccounts($relationAlias = null) Adds a INNER JOIN clause to the query using the GothiaAccounts relation
 *
 * @method     Customers findOne(PropelPDO $con = null) Return the first Customers matching the query
 * @method     Customers findOneOrCreate(PropelPDO $con = null) Return the first Customers matching the query, or a new Customers object populated from the query conditions when no match is found
 *
 * @method     Customers findOneById(int $id) Return the first Customers filtered by the id column
 * @method     Customers findOneByFirstName(string $first_name) Return the first Customers filtered by the first_name column
 * @method     Customers findOneByLastName(string $last_name) Return the first Customers filtered by the last_name column
 * @method     Customers findOneByInitials(string $initials) Return the first Customers filtered by the initials column
 * @method     Customers findOneByPassword(string $password) Return the first Customers filtered by the password column
 * @method     Customers findOneByEmail(string $email) Return the first Customers filtered by the email column
 * @method     Customers findOneByPhone(string $phone) Return the first Customers filtered by the phone column
 * @method     Customers findOneByPasswordClear(string $password_clear) Return the first Customers filtered by the password_clear column
 * @method     Customers findOneByDiscount(string $discount) Return the first Customers filtered by the discount column
 * @method     Customers findOneByGroupsId(int $groups_id) Return the first Customers filtered by the groups_id column
 * @method     Customers findOneByIsActive(boolean $is_active) Return the first Customers filtered by the is_active column
 * @method     Customers findOneByLanguagesId(int $languages_id) Return the first Customers filtered by the languages_id column
 * @method     Customers findOneByCreatedAt(string $created_at) Return the first Customers filtered by the created_at column
 * @method     Customers findOneByUpdatedAt(string $updated_at) Return the first Customers filtered by the updated_at column
 *
 * @method     array findById(int $id) Return Customers objects filtered by the id column
 * @method     array findByFirstName(string $first_name) Return Customers objects filtered by the first_name column
 * @method     array findByLastName(string $last_name) Return Customers objects filtered by the last_name column
 * @method     array findByInitials(string $initials) Return Customers objects filtered by the initials column
 * @method     array findByPassword(string $password) Return Customers objects filtered by the password column
 * @method     array findByEmail(string $email) Return Customers objects filtered by the email column
 * @method     array findByPhone(string $phone) Return Customers objects filtered by the phone column
 * @method     array findByPasswordClear(string $password_clear) Return Customers objects filtered by the password_clear column
 * @method     array findByDiscount(string $discount) Return Customers objects filtered by the discount column
 * @method     array findByGroupsId(int $groups_id) Return Customers objects filtered by the groups_id column
 * @method     array findByIsActive(boolean $is_active) Return Customers objects filtered by the is_active column
 * @method     array findByLanguagesId(int $languages_id) Return Customers objects filtered by the languages_id column
 * @method     array findByCreatedAt(string $created_at) Return Customers objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return Customers objects filtered by the updated_at column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCustomersQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCustomersQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\Customers', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CustomersQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CustomersQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CustomersQuery) {
			return $criteria;
		}
		$query = new CustomersQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
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
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Customers|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CustomersPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CustomersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Customers A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ID`, `FIRST_NAME`, `LAST_NAME`, `INITIALS`, `PASSWORD`, `EMAIL`, `PHONE`, `PASSWORD_CLEAR`, `DISCOUNT`, `GROUPS_ID`, `IS_ACTIVE`, `LANGUAGES_ID`, `CREATED_AT`, `UPDATED_AT` FROM `customers` WHERE `ID` = :p0';
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
			$obj = new Customers();
			$obj->hydrate($row);
			CustomersPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    Customers|array|mixed the result, formatted by the current formatter
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
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
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
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CustomersPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CustomersPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterById(1234); // WHERE id = 1234
	 * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
	 * $query->filterById(array('min' => 12)); // WHERE id > 12
	 * </code>
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CustomersPeer::ID, $id, $comparison);
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
	 * @return    CustomersQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CustomersPeer::FIRST_NAME, $firstName, $comparison);
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
	 * @return    CustomersQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CustomersPeer::LAST_NAME, $lastName, $comparison);
	}

	/**
	 * Filter the query on the initials column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByInitials('fooValue');   // WHERE initials = 'fooValue'
	 * $query->filterByInitials('%fooValue%'); // WHERE initials LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $initials The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByInitials($initials = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($initials)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $initials)) {
				$initials = str_replace('*', '%', $initials);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CustomersPeer::INITIALS, $initials, $comparison);
	}

	/**
	 * Filter the query on the password column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPassword('fooValue');   // WHERE password = 'fooValue'
	 * $query->filterByPassword('%fooValue%'); // WHERE password LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $password The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByPassword($password = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($password)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $password)) {
				$password = str_replace('*', '%', $password);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CustomersPeer::PASSWORD, $password, $comparison);
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
	 * @return    CustomersQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CustomersPeer::EMAIL, $email, $comparison);
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
	 * @return    CustomersQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CustomersPeer::PHONE, $phone, $comparison);
	}

	/**
	 * Filter the query on the password_clear column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPasswordClear('fooValue');   // WHERE password_clear = 'fooValue'
	 * $query->filterByPasswordClear('%fooValue%'); // WHERE password_clear LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $passwordClear The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByPasswordClear($passwordClear = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($passwordClear)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $passwordClear)) {
				$passwordClear = str_replace('*', '%', $passwordClear);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CustomersPeer::PASSWORD_CLEAR, $passwordClear, $comparison);
	}

	/**
	 * Filter the query on the discount column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDiscount(1234); // WHERE discount = 1234
	 * $query->filterByDiscount(array(12, 34)); // WHERE discount IN (12, 34)
	 * $query->filterByDiscount(array('min' => 12)); // WHERE discount > 12
	 * </code>
	 *
	 * @param     mixed $discount The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByDiscount($discount = null, $comparison = null)
	{
		if (is_array($discount)) {
			$useMinMax = false;
			if (isset($discount['min'])) {
				$this->addUsingAlias(CustomersPeer::DISCOUNT, $discount['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($discount['max'])) {
				$this->addUsingAlias(CustomersPeer::DISCOUNT, $discount['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CustomersPeer::DISCOUNT, $discount, $comparison);
	}

	/**
	 * Filter the query on the groups_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByGroupsId(1234); // WHERE groups_id = 1234
	 * $query->filterByGroupsId(array(12, 34)); // WHERE groups_id IN (12, 34)
	 * $query->filterByGroupsId(array('min' => 12)); // WHERE groups_id > 12
	 * </code>
	 *
	 * @see       filterByGroups()
	 *
	 * @param     mixed $groupsId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByGroupsId($groupsId = null, $comparison = null)
	{
		if (is_array($groupsId)) {
			$useMinMax = false;
			if (isset($groupsId['min'])) {
				$this->addUsingAlias(CustomersPeer::GROUPS_ID, $groupsId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($groupsId['max'])) {
				$this->addUsingAlias(CustomersPeer::GROUPS_ID, $groupsId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CustomersPeer::GROUPS_ID, $groupsId, $comparison);
	}

	/**
	 * Filter the query on the is_active column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIsActive(true); // WHERE is_active = true
	 * $query->filterByIsActive('yes'); // WHERE is_active = true
	 * </code>
	 *
	 * @param     boolean|string $isActive The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByIsActive($isActive = null, $comparison = null)
	{
		if (is_string($isActive)) {
			$is_active = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(CustomersPeer::IS_ACTIVE, $isActive, $comparison);
	}

	/**
	 * Filter the query on the languages_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByLanguagesId(1234); // WHERE languages_id = 1234
	 * $query->filterByLanguagesId(array(12, 34)); // WHERE languages_id IN (12, 34)
	 * $query->filterByLanguagesId(array('min' => 12)); // WHERE languages_id > 12
	 * </code>
	 *
	 * @see       filterByLanguages()
	 *
	 * @param     mixed $languagesId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByLanguagesId($languagesId = null, $comparison = null)
	{
		if (is_array($languagesId)) {
			$useMinMax = false;
			if (isset($languagesId['min'])) {
				$this->addUsingAlias(CustomersPeer::LANGUAGES_ID, $languagesId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($languagesId['max'])) {
				$this->addUsingAlias(CustomersPeer::LANGUAGES_ID, $languagesId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CustomersPeer::LANGUAGES_ID, $languagesId, $comparison);
	}

	/**
	 * Filter the query on the created_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
	 * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
	 * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
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
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(CustomersPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(CustomersPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CustomersPeer::CREATED_AT, $createdAt, $comparison);
	}

	/**
	 * Filter the query on the updated_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
	 * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
	 * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
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
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(CustomersPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(CustomersPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CustomersPeer::UPDATED_AT, $updatedAt, $comparison);
	}

	/**
	 * Filter the query by a related Groups object
	 *
	 * @param     Groups|PropelCollection $groups The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByGroups($groups, $comparison = null)
	{
		if ($groups instanceof Groups) {
			return $this
				->addUsingAlias(CustomersPeer::GROUPS_ID, $groups->getId(), $comparison);
		} elseif ($groups instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CustomersPeer::GROUPS_ID, $groups->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByGroups() only accepts arguments of type Groups or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Groups relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinGroups($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Groups');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Groups');
		}

		return $this;
	}

	/**
	 * Use the Groups relation Groups object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\GroupsQuery A secondary query class using the current class as primary query
	 */
	public function useGroupsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGroups($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groups', '\Hanzo\Model\GroupsQuery');
	}

	/**
	 * Filter the query by a related Languages object
	 *
	 * @param     Languages|PropelCollection $languages The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByLanguages($languages, $comparison = null)
	{
		if ($languages instanceof Languages) {
			return $this
				->addUsingAlias(CustomersPeer::LANGUAGES_ID, $languages->getId(), $comparison);
		} elseif ($languages instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CustomersPeer::LANGUAGES_ID, $languages->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByLanguages() only accepts arguments of type Languages or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Languages relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinLanguages($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Languages');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Languages');
		}

		return $this;
	}

	/**
	 * Use the Languages relation Languages object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\LanguagesQuery A secondary query class using the current class as primary query
	 */
	public function useLanguagesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinLanguages($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Languages', '\Hanzo\Model\LanguagesQuery');
	}

	/**
	 * Filter the query by a related ConsultantsInfo object
	 *
	 * @param     ConsultantsInfo $consultantsInfo  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByConsultantsInfo($consultantsInfo, $comparison = null)
	{
		if ($consultantsInfo instanceof ConsultantsInfo) {
			return $this
				->addUsingAlias(CustomersPeer::ID, $consultantsInfo->getConsultantsId(), $comparison);
		} elseif ($consultantsInfo instanceof PropelCollection) {
			return $this
				->useConsultantsInfoQuery()
				->filterByPrimaryKeys($consultantsInfo->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByConsultantsInfo() only accepts arguments of type ConsultantsInfo or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the ConsultantsInfo relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinConsultantsInfo($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ConsultantsInfo');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'ConsultantsInfo');
		}

		return $this;
	}

	/**
	 * Use the ConsultantsInfo relation ConsultantsInfo object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\ConsultantsInfoQuery A secondary query class using the current class as primary query
	 */
	public function useConsultantsInfoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinConsultantsInfo($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ConsultantsInfo', '\Hanzo\Model\ConsultantsInfoQuery');
	}

	/**
	 * Filter the query by a related CouponsToCustomers object
	 *
	 * @param     CouponsToCustomers $couponsToCustomers  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByCouponsToCustomers($couponsToCustomers, $comparison = null)
	{
		if ($couponsToCustomers instanceof CouponsToCustomers) {
			return $this
				->addUsingAlias(CustomersPeer::ID, $couponsToCustomers->getCustomersId(), $comparison);
		} elseif ($couponsToCustomers instanceof PropelCollection) {
			return $this
				->useCouponsToCustomersQuery()
				->filterByPrimaryKeys($couponsToCustomers->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByCouponsToCustomers() only accepts arguments of type CouponsToCustomers or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CouponsToCustomers relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinCouponsToCustomers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CouponsToCustomers');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CouponsToCustomers');
		}

		return $this;
	}

	/**
	 * Use the CouponsToCustomers relation CouponsToCustomers object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\CouponsToCustomersQuery A secondary query class using the current class as primary query
	 */
	public function useCouponsToCustomersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCouponsToCustomers($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CouponsToCustomers', '\Hanzo\Model\CouponsToCustomersQuery');
	}

	/**
	 * Filter the query by a related Addresses object
	 *
	 * @param     Addresses $addresses  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByAddresses($addresses, $comparison = null)
	{
		if ($addresses instanceof Addresses) {
			return $this
				->addUsingAlias(CustomersPeer::ID, $addresses->getCustomersId(), $comparison);
		} elseif ($addresses instanceof PropelCollection) {
			return $this
				->useAddressesQuery()
				->filterByPrimaryKeys($addresses->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByAddresses() only accepts arguments of type Addresses or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Addresses relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinAddresses($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Addresses');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Addresses');
		}

		return $this;
	}

	/**
	 * Use the Addresses relation Addresses object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\AddressesQuery A secondary query class using the current class as primary query
	 */
	public function useAddressesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAddresses($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Addresses', '\Hanzo\Model\AddressesQuery');
	}

	/**
	 * Filter the query by a related Events object
	 *
	 * @param     Events $events  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByEventsRelatedByConsultantsId($events, $comparison = null)
	{
		if ($events instanceof Events) {
			return $this
				->addUsingAlias(CustomersPeer::ID, $events->getConsultantsId(), $comparison);
		} elseif ($events instanceof PropelCollection) {
			return $this
				->useEventsRelatedByConsultantsIdQuery()
				->filterByPrimaryKeys($events->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByEventsRelatedByConsultantsId() only accepts arguments of type Events or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the EventsRelatedByConsultantsId relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinEventsRelatedByConsultantsId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EventsRelatedByConsultantsId');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'EventsRelatedByConsultantsId');
		}

		return $this;
	}

	/**
	 * Use the EventsRelatedByConsultantsId relation Events object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\EventsQuery A secondary query class using the current class as primary query
	 */
	public function useEventsRelatedByConsultantsIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEventsRelatedByConsultantsId($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EventsRelatedByConsultantsId', '\Hanzo\Model\EventsQuery');
	}

	/**
	 * Filter the query by a related Events object
	 *
	 * @param     Events $events  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByEventsRelatedByCustomersId($events, $comparison = null)
	{
		if ($events instanceof Events) {
			return $this
				->addUsingAlias(CustomersPeer::ID, $events->getCustomersId(), $comparison);
		} elseif ($events instanceof PropelCollection) {
			return $this
				->useEventsRelatedByCustomersIdQuery()
				->filterByPrimaryKeys($events->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByEventsRelatedByCustomersId() only accepts arguments of type Events or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the EventsRelatedByCustomersId relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinEventsRelatedByCustomersId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EventsRelatedByCustomersId');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'EventsRelatedByCustomersId');
		}

		return $this;
	}

	/**
	 * Use the EventsRelatedByCustomersId relation Events object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\EventsQuery A secondary query class using the current class as primary query
	 */
	public function useEventsRelatedByCustomersIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEventsRelatedByCustomersId($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EventsRelatedByCustomersId', '\Hanzo\Model\EventsQuery');
	}

	/**
	 * Filter the query by a related GothiaAccounts object
	 *
	 * @param     GothiaAccounts $gothiaAccounts  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function filterByGothiaAccounts($gothiaAccounts, $comparison = null)
	{
		if ($gothiaAccounts instanceof GothiaAccounts) {
			return $this
				->addUsingAlias(CustomersPeer::ID, $gothiaAccounts->getCustomersId(), $comparison);
		} elseif ($gothiaAccounts instanceof PropelCollection) {
			return $this
				->useGothiaAccountsQuery()
				->filterByPrimaryKeys($gothiaAccounts->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByGothiaAccounts() only accepts arguments of type GothiaAccounts or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the GothiaAccounts relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function joinGothiaAccounts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('GothiaAccounts');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'GothiaAccounts');
		}

		return $this;
	}

	/**
	 * Use the GothiaAccounts relation GothiaAccounts object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\GothiaAccountsQuery A secondary query class using the current class as primary query
	 */
	public function useGothiaAccountsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGothiaAccounts($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'GothiaAccounts', '\Hanzo\Model\GothiaAccountsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Customers $customers Object to remove from the list of results
	 *
	 * @return    CustomersQuery The current query, for fluid interface
	 */
	public function prune($customers = null)
	{
		if ($customers) {
			$this->addUsingAlias(CustomersPeer::ID, $customers->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     CustomersQuery The current query, for fluid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(CustomersPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     CustomersQuery The current query, for fluid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(CustomersPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     CustomersQuery The current query, for fluid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(CustomersPeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     CustomersQuery The current query, for fluid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(CustomersPeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     CustomersQuery The current query, for fluid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(CustomersPeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     CustomersQuery The current query, for fluid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(CustomersPeer::CREATED_AT);
	}

} // BaseCustomersQuery
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
use Hanzo\Model\Coupons;
use Hanzo\Model\CouponsPeer;
use Hanzo\Model\CouponsQuery;
use Hanzo\Model\CouponsToCustomers;

/**
 * Base class that represents a query for the 'coupons' table.
 *
 * 
 *
 * @method     CouponsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CouponsQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     CouponsQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method     CouponsQuery orderByVat($order = Criteria::ASC) Order by the vat column
 * @method     CouponsQuery orderByCurrencyCode($order = Criteria::ASC) Order by the currency_code column
 * @method     CouponsQuery orderByUsesPrCoupon($order = Criteria::ASC) Order by the uses_pr_coupon column
 * @method     CouponsQuery orderByUsesPrCoustomer($order = Criteria::ASC) Order by the uses_pr_coustomer column
 * @method     CouponsQuery orderByActiveFrom($order = Criteria::ASC) Order by the active_from column
 * @method     CouponsQuery orderByActiveTo($order = Criteria::ASC) Order by the active_to column
 * @method     CouponsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     CouponsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     CouponsQuery groupById() Group by the id column
 * @method     CouponsQuery groupByCode() Group by the code column
 * @method     CouponsQuery groupByAmount() Group by the amount column
 * @method     CouponsQuery groupByVat() Group by the vat column
 * @method     CouponsQuery groupByCurrencyCode() Group by the currency_code column
 * @method     CouponsQuery groupByUsesPrCoupon() Group by the uses_pr_coupon column
 * @method     CouponsQuery groupByUsesPrCoustomer() Group by the uses_pr_coustomer column
 * @method     CouponsQuery groupByActiveFrom() Group by the active_from column
 * @method     CouponsQuery groupByActiveTo() Group by the active_to column
 * @method     CouponsQuery groupByCreatedAt() Group by the created_at column
 * @method     CouponsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     CouponsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CouponsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CouponsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CouponsQuery leftJoinCouponsToCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the CouponsToCustomers relation
 * @method     CouponsQuery rightJoinCouponsToCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CouponsToCustomers relation
 * @method     CouponsQuery innerJoinCouponsToCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the CouponsToCustomers relation
 *
 * @method     Coupons findOne(PropelPDO $con = null) Return the first Coupons matching the query
 * @method     Coupons findOneOrCreate(PropelPDO $con = null) Return the first Coupons matching the query, or a new Coupons object populated from the query conditions when no match is found
 *
 * @method     Coupons findOneById(int $id) Return the first Coupons filtered by the id column
 * @method     Coupons findOneByCode(string $code) Return the first Coupons filtered by the code column
 * @method     Coupons findOneByAmount(string $amount) Return the first Coupons filtered by the amount column
 * @method     Coupons findOneByVat(string $vat) Return the first Coupons filtered by the vat column
 * @method     Coupons findOneByCurrencyCode(string $currency_code) Return the first Coupons filtered by the currency_code column
 * @method     Coupons findOneByUsesPrCoupon(int $uses_pr_coupon) Return the first Coupons filtered by the uses_pr_coupon column
 * @method     Coupons findOneByUsesPrCoustomer(int $uses_pr_coustomer) Return the first Coupons filtered by the uses_pr_coustomer column
 * @method     Coupons findOneByActiveFrom(string $active_from) Return the first Coupons filtered by the active_from column
 * @method     Coupons findOneByActiveTo(string $active_to) Return the first Coupons filtered by the active_to column
 * @method     Coupons findOneByCreatedAt(string $created_at) Return the first Coupons filtered by the created_at column
 * @method     Coupons findOneByUpdatedAt(string $updated_at) Return the first Coupons filtered by the updated_at column
 *
 * @method     array findById(int $id) Return Coupons objects filtered by the id column
 * @method     array findByCode(string $code) Return Coupons objects filtered by the code column
 * @method     array findByAmount(string $amount) Return Coupons objects filtered by the amount column
 * @method     array findByVat(string $vat) Return Coupons objects filtered by the vat column
 * @method     array findByCurrencyCode(string $currency_code) Return Coupons objects filtered by the currency_code column
 * @method     array findByUsesPrCoupon(int $uses_pr_coupon) Return Coupons objects filtered by the uses_pr_coupon column
 * @method     array findByUsesPrCoustomer(int $uses_pr_coustomer) Return Coupons objects filtered by the uses_pr_coustomer column
 * @method     array findByActiveFrom(string $active_from) Return Coupons objects filtered by the active_from column
 * @method     array findByActiveTo(string $active_to) Return Coupons objects filtered by the active_to column
 * @method     array findByCreatedAt(string $created_at) Return Coupons objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return Coupons objects filtered by the updated_at column
 *
 * @package    propel.generator.home/un/Documents/Arbejde/Pompdelux/www/hanzo/Symfony/src/Hanzo/Model.om
 */
abstract class BaseCouponsQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCouponsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\Coupons', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CouponsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CouponsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CouponsQuery) {
			return $criteria;
		}
		$query = new CouponsQuery();
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
	 * @return    Coupons|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CouponsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CouponsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    Coupons A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ID`, `CODE`, `AMOUNT`, `VAT`, `CURRENCY_CODE`, `USES_PR_COUPON`, `USES_PR_COUSTOMER`, `ACTIVE_FROM`, `ACTIVE_TO`, `CREATED_AT`, `UPDATED_AT` FROM `coupons` WHERE `ID` = :p0';
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
			$obj = new Coupons();
			$obj->hydrate($row);
			CouponsPeer::addInstanceToPool($obj, (string) $row[0]);
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
	 * @return    Coupons|array|mixed the result, formatted by the current formatter
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
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CouponsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CouponsPeer::ID, $keys, Criteria::IN);
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
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CouponsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the code column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
	 * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $code The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByCode($code = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($code)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $code)) {
				$code = str_replace('*', '%', $code);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CouponsPeer::CODE, $code, $comparison);
	}

	/**
	 * Filter the query on the amount column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAmount(1234); // WHERE amount = 1234
	 * $query->filterByAmount(array(12, 34)); // WHERE amount IN (12, 34)
	 * $query->filterByAmount(array('min' => 12)); // WHERE amount > 12
	 * </code>
	 *
	 * @param     mixed $amount The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByAmount($amount = null, $comparison = null)
	{
		if (is_array($amount)) {
			$useMinMax = false;
			if (isset($amount['min'])) {
				$this->addUsingAlias(CouponsPeer::AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($amount['max'])) {
				$this->addUsingAlias(CouponsPeer::AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::AMOUNT, $amount, $comparison);
	}

	/**
	 * Filter the query on the vat column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByVat(1234); // WHERE vat = 1234
	 * $query->filterByVat(array(12, 34)); // WHERE vat IN (12, 34)
	 * $query->filterByVat(array('min' => 12)); // WHERE vat > 12
	 * </code>
	 *
	 * @param     mixed $vat The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByVat($vat = null, $comparison = null)
	{
		if (is_array($vat)) {
			$useMinMax = false;
			if (isset($vat['min'])) {
				$this->addUsingAlias(CouponsPeer::VAT, $vat['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($vat['max'])) {
				$this->addUsingAlias(CouponsPeer::VAT, $vat['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::VAT, $vat, $comparison);
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
	 * @return    CouponsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CouponsPeer::CURRENCY_CODE, $currencyCode, $comparison);
	}

	/**
	 * Filter the query on the uses_pr_coupon column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUsesPrCoupon(1234); // WHERE uses_pr_coupon = 1234
	 * $query->filterByUsesPrCoupon(array(12, 34)); // WHERE uses_pr_coupon IN (12, 34)
	 * $query->filterByUsesPrCoupon(array('min' => 12)); // WHERE uses_pr_coupon > 12
	 * </code>
	 *
	 * @param     mixed $usesPrCoupon The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByUsesPrCoupon($usesPrCoupon = null, $comparison = null)
	{
		if (is_array($usesPrCoupon)) {
			$useMinMax = false;
			if (isset($usesPrCoupon['min'])) {
				$this->addUsingAlias(CouponsPeer::USES_PR_COUPON, $usesPrCoupon['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($usesPrCoupon['max'])) {
				$this->addUsingAlias(CouponsPeer::USES_PR_COUPON, $usesPrCoupon['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::USES_PR_COUPON, $usesPrCoupon, $comparison);
	}

	/**
	 * Filter the query on the uses_pr_coustomer column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUsesPrCoustomer(1234); // WHERE uses_pr_coustomer = 1234
	 * $query->filterByUsesPrCoustomer(array(12, 34)); // WHERE uses_pr_coustomer IN (12, 34)
	 * $query->filterByUsesPrCoustomer(array('min' => 12)); // WHERE uses_pr_coustomer > 12
	 * </code>
	 *
	 * @param     mixed $usesPrCoustomer The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByUsesPrCoustomer($usesPrCoustomer = null, $comparison = null)
	{
		if (is_array($usesPrCoustomer)) {
			$useMinMax = false;
			if (isset($usesPrCoustomer['min'])) {
				$this->addUsingAlias(CouponsPeer::USES_PR_COUSTOMER, $usesPrCoustomer['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($usesPrCoustomer['max'])) {
				$this->addUsingAlias(CouponsPeer::USES_PR_COUSTOMER, $usesPrCoustomer['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::USES_PR_COUSTOMER, $usesPrCoustomer, $comparison);
	}

	/**
	 * Filter the query on the active_from column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByActiveFrom('2011-03-14'); // WHERE active_from = '2011-03-14'
	 * $query->filterByActiveFrom('now'); // WHERE active_from = '2011-03-14'
	 * $query->filterByActiveFrom(array('max' => 'yesterday')); // WHERE active_from > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $activeFrom The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByActiveFrom($activeFrom = null, $comparison = null)
	{
		if (is_array($activeFrom)) {
			$useMinMax = false;
			if (isset($activeFrom['min'])) {
				$this->addUsingAlias(CouponsPeer::ACTIVE_FROM, $activeFrom['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($activeFrom['max'])) {
				$this->addUsingAlias(CouponsPeer::ACTIVE_FROM, $activeFrom['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::ACTIVE_FROM, $activeFrom, $comparison);
	}

	/**
	 * Filter the query on the active_to column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByActiveTo('2011-03-14'); // WHERE active_to = '2011-03-14'
	 * $query->filterByActiveTo('now'); // WHERE active_to = '2011-03-14'
	 * $query->filterByActiveTo(array('max' => 'yesterday')); // WHERE active_to > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $activeTo The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByActiveTo($activeTo = null, $comparison = null)
	{
		if (is_array($activeTo)) {
			$useMinMax = false;
			if (isset($activeTo['min'])) {
				$this->addUsingAlias(CouponsPeer::ACTIVE_TO, $activeTo['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($activeTo['max'])) {
				$this->addUsingAlias(CouponsPeer::ACTIVE_TO, $activeTo['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::ACTIVE_TO, $activeTo, $comparison);
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
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(CouponsPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(CouponsPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::CREATED_AT, $createdAt, $comparison);
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
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(CouponsPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(CouponsPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsPeer::UPDATED_AT, $updatedAt, $comparison);
	}

	/**
	 * Filter the query by a related CouponsToCustomers object
	 *
	 * @param     CouponsToCustomers $couponsToCustomers  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByCouponsToCustomers($couponsToCustomers, $comparison = null)
	{
		if ($couponsToCustomers instanceof CouponsToCustomers) {
			return $this
				->addUsingAlias(CouponsPeer::ID, $couponsToCustomers->getCouponsId(), $comparison);
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
	 * @return    CouponsQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     Coupons $coupons Object to remove from the list of results
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function prune($coupons = null)
	{
		if ($coupons) {
			$this->addUsingAlias(CouponsPeer::ID, $coupons->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     CouponsQuery The current query, for fluid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(CouponsPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     CouponsQuery The current query, for fluid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(CouponsPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     CouponsQuery The current query, for fluid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(CouponsPeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     CouponsQuery The current query, for fluid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(CouponsPeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     CouponsQuery The current query, for fluid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(CouponsPeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     CouponsQuery The current query, for fluid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(CouponsPeer::CREATED_AT);
	}

} // BaseCouponsQuery
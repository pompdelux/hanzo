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
use Hanzo\Model\OrdersToCoupons;

/**
 * Base class that represents a query for the 'coupons' table.
 *
 * 
 *
 * @method     CouponsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CouponsQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     CouponsQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method     CouponsQuery orderByCurrencyCode($order = Criteria::ASC) Order by the currency_code column
 * @method     CouponsQuery orderByActiveFrom($order = Criteria::ASC) Order by the active_from column
 * @method     CouponsQuery orderByActiveTo($order = Criteria::ASC) Order by the active_to column
 * @method     CouponsQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 * @method     CouponsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     CouponsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     CouponsQuery groupById() Group by the id column
 * @method     CouponsQuery groupByCode() Group by the code column
 * @method     CouponsQuery groupByAmount() Group by the amount column
 * @method     CouponsQuery groupByCurrencyCode() Group by the currency_code column
 * @method     CouponsQuery groupByActiveFrom() Group by the active_from column
 * @method     CouponsQuery groupByActiveTo() Group by the active_to column
 * @method     CouponsQuery groupByIsActive() Group by the is_active column
 * @method     CouponsQuery groupByCreatedAt() Group by the created_at column
 * @method     CouponsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     CouponsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CouponsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CouponsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CouponsQuery leftJoinOrdersToCoupons($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersToCoupons relation
 * @method     CouponsQuery rightJoinOrdersToCoupons($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersToCoupons relation
 * @method     CouponsQuery innerJoinOrdersToCoupons($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersToCoupons relation
 *
 * @method     Coupons findOne(PropelPDO $con = null) Return the first Coupons matching the query
 * @method     Coupons findOneOrCreate(PropelPDO $con = null) Return the first Coupons matching the query, or a new Coupons object populated from the query conditions when no match is found
 *
 * @method     Coupons findOneById(int $id) Return the first Coupons filtered by the id column
 * @method     Coupons findOneByCode(string $code) Return the first Coupons filtered by the code column
 * @method     Coupons findOneByAmount(string $amount) Return the first Coupons filtered by the amount column
 * @method     Coupons findOneByCurrencyCode(string $currency_code) Return the first Coupons filtered by the currency_code column
 * @method     Coupons findOneByActiveFrom(string $active_from) Return the first Coupons filtered by the active_from column
 * @method     Coupons findOneByActiveTo(string $active_to) Return the first Coupons filtered by the active_to column
 * @method     Coupons findOneByIsActive(boolean $is_active) Return the first Coupons filtered by the is_active column
 * @method     Coupons findOneByCreatedAt(string $created_at) Return the first Coupons filtered by the created_at column
 * @method     Coupons findOneByUpdatedAt(string $updated_at) Return the first Coupons filtered by the updated_at column
 *
 * @method     array findById(int $id) Return Coupons objects filtered by the id column
 * @method     array findByCode(string $code) Return Coupons objects filtered by the code column
 * @method     array findByAmount(string $amount) Return Coupons objects filtered by the amount column
 * @method     array findByCurrencyCode(string $currency_code) Return Coupons objects filtered by the currency_code column
 * @method     array findByActiveFrom(string $active_from) Return Coupons objects filtered by the active_from column
 * @method     array findByActiveTo(string $active_to) Return Coupons objects filtered by the active_to column
 * @method     array findByIsActive(boolean $is_active) Return Coupons objects filtered by the is_active column
 * @method     array findByCreatedAt(string $created_at) Return Coupons objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return Coupons objects filtered by the updated_at column
 *
 * @package    propel.generator.src.Hanzo.Model.om
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
		$sql = 'SELECT `ID`, `CODE`, `AMOUNT`, `CURRENCY_CODE`, `ACTIVE_FROM`, `ACTIVE_TO`, `IS_ACTIVE`, `CREATED_AT`, `UPDATED_AT` FROM `coupons` WHERE `ID` = :p0';
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
			CouponsPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByIsActive($isActive = null, $comparison = null)
	{
		if (is_string($isActive)) {
			$is_active = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(CouponsPeer::IS_ACTIVE, $isActive, $comparison);
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
	 * Filter the query by a related OrdersToCoupons object
	 *
	 * @param     OrdersToCoupons $ordersToCoupons  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsQuery The current query, for fluid interface
	 */
	public function filterByOrdersToCoupons($ordersToCoupons, $comparison = null)
	{
		if ($ordersToCoupons instanceof OrdersToCoupons) {
			return $this
				->addUsingAlias(CouponsPeer::ID, $ordersToCoupons->getCouponsId(), $comparison);
		} elseif ($ordersToCoupons instanceof PropelCollection) {
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
	 * @return    CouponsQuery The current query, for fluid interface
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
		if($relationAlias) {
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
	 * @return    \Hanzo\Model\OrdersToCouponsQuery A secondary query class using the current class as primary query
	 */
	public function useOrdersToCouponsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinOrdersToCoupons($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'OrdersToCoupons', '\Hanzo\Model\OrdersToCouponsQuery');
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
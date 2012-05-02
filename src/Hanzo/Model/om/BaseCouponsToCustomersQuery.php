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
use Hanzo\Model\CouponsToCustomers;
use Hanzo\Model\CouponsToCustomersPeer;
use Hanzo\Model\CouponsToCustomersQuery;
use Hanzo\Model\Customers;

/**
 * Base class that represents a query for the 'coupons_to_customers' table.
 *
 * 
 *
 * @method     CouponsToCustomersQuery orderByCouponsId($order = Criteria::ASC) Order by the coupons_id column
 * @method     CouponsToCustomersQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method     CouponsToCustomersQuery orderByUseCount($order = Criteria::ASC) Order by the use_count column
 *
 * @method     CouponsToCustomersQuery groupByCouponsId() Group by the coupons_id column
 * @method     CouponsToCustomersQuery groupByCustomersId() Group by the customers_id column
 * @method     CouponsToCustomersQuery groupByUseCount() Group by the use_count column
 *
 * @method     CouponsToCustomersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CouponsToCustomersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CouponsToCustomersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CouponsToCustomersQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method     CouponsToCustomersQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method     CouponsToCustomersQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method     CouponsToCustomersQuery leftJoinCoupons($relationAlias = null) Adds a LEFT JOIN clause to the query using the Coupons relation
 * @method     CouponsToCustomersQuery rightJoinCoupons($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Coupons relation
 * @method     CouponsToCustomersQuery innerJoinCoupons($relationAlias = null) Adds a INNER JOIN clause to the query using the Coupons relation
 *
 * @method     CouponsToCustomers findOne(PropelPDO $con = null) Return the first CouponsToCustomers matching the query
 * @method     CouponsToCustomers findOneOrCreate(PropelPDO $con = null) Return the first CouponsToCustomers matching the query, or a new CouponsToCustomers object populated from the query conditions when no match is found
 *
 * @method     CouponsToCustomers findOneByCouponsId(int $coupons_id) Return the first CouponsToCustomers filtered by the coupons_id column
 * @method     CouponsToCustomers findOneByCustomersId(int $customers_id) Return the first CouponsToCustomers filtered by the customers_id column
 * @method     CouponsToCustomers findOneByUseCount(int $use_count) Return the first CouponsToCustomers filtered by the use_count column
 *
 * @method     array findByCouponsId(int $coupons_id) Return CouponsToCustomers objects filtered by the coupons_id column
 * @method     array findByCustomersId(int $customers_id) Return CouponsToCustomers objects filtered by the customers_id column
 * @method     array findByUseCount(int $use_count) Return CouponsToCustomers objects filtered by the use_count column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCouponsToCustomersQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCouponsToCustomersQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\CouponsToCustomers', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CouponsToCustomersQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CouponsToCustomersQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CouponsToCustomersQuery) {
			return $criteria;
		}
		$query = new CouponsToCustomersQuery();
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
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 *
	 * @param     array[$coupons_id, $customers_id] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CouponsToCustomers|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CouponsToCustomersPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CouponsToCustomersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    CouponsToCustomers A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `COUPONS_ID`, `CUSTOMERS_ID`, `USE_COUNT` FROM `coupons_to_customers` WHERE `COUPONS_ID` = :p0 AND `CUSTOMERS_ID` = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new CouponsToCustomers();
			$obj->hydrate($row);
			CouponsToCustomersPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    CouponsToCustomers|array|mixed the result, formatted by the current formatter
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(CouponsToCustomersPeer::COUPONS_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(CouponsToCustomersPeer::CUSTOMERS_ID, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(CouponsToCustomersPeer::COUPONS_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(CouponsToCustomersPeer::CUSTOMERS_ID, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the coupons_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCouponsId(1234); // WHERE coupons_id = 1234
	 * $query->filterByCouponsId(array(12, 34)); // WHERE coupons_id IN (12, 34)
	 * $query->filterByCouponsId(array('min' => 12)); // WHERE coupons_id > 12
	 * </code>
	 *
	 * @see       filterByCoupons()
	 *
	 * @param     mixed $couponsId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByCouponsId($couponsId = null, $comparison = null)
	{
		if (is_array($couponsId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CouponsToCustomersPeer::COUPONS_ID, $couponsId, $comparison);
	}

	/**
	 * Filter the query on the customers_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCustomersId(1234); // WHERE customers_id = 1234
	 * $query->filterByCustomersId(array(12, 34)); // WHERE customers_id IN (12, 34)
	 * $query->filterByCustomersId(array('min' => 12)); // WHERE customers_id > 12
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
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByCustomersId($customersId = null, $comparison = null)
	{
		if (is_array($customersId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CouponsToCustomersPeer::CUSTOMERS_ID, $customersId, $comparison);
	}

	/**
	 * Filter the query on the use_count column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUseCount(1234); // WHERE use_count = 1234
	 * $query->filterByUseCount(array(12, 34)); // WHERE use_count IN (12, 34)
	 * $query->filterByUseCount(array('min' => 12)); // WHERE use_count > 12
	 * </code>
	 *
	 * @param     mixed $useCount The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByUseCount($useCount = null, $comparison = null)
	{
		if (is_array($useCount)) {
			$useMinMax = false;
			if (isset($useCount['min'])) {
				$this->addUsingAlias(CouponsToCustomersPeer::USE_COUNT, $useCount['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($useCount['max'])) {
				$this->addUsingAlias(CouponsToCustomersPeer::USE_COUNT, $useCount['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CouponsToCustomersPeer::USE_COUNT, $useCount, $comparison);
	}

	/**
	 * Filter the query by a related Customers object
	 *
	 * @param     Customers|PropelCollection $customers The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByCustomers($customers, $comparison = null)
	{
		if ($customers instanceof Customers) {
			return $this
				->addUsingAlias(CouponsToCustomersPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
		} elseif ($customers instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CouponsToCustomersPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function joinCustomers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
		if($relationAlias) {
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
	 * @return    \Hanzo\Model\CustomersQuery A secondary query class using the current class as primary query
	 */
	public function useCustomersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCustomers($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
	}

	/**
	 * Filter the query by a related Coupons object
	 *
	 * @param     Coupons|PropelCollection $coupons The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function filterByCoupons($coupons, $comparison = null)
	{
		if ($coupons instanceof Coupons) {
			return $this
				->addUsingAlias(CouponsToCustomersPeer::COUPONS_ID, $coupons->getId(), $comparison);
		} elseif ($coupons instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CouponsToCustomersPeer::COUPONS_ID, $coupons->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByCoupons() only accepts arguments of type Coupons or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Coupons relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function joinCoupons($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Coupons');

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
			$this->addJoinObject($join, 'Coupons');
		}

		return $this;
	}

	/**
	 * Use the Coupons relation Coupons object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\CouponsQuery A secondary query class using the current class as primary query
	 */
	public function useCouponsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCoupons($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Coupons', '\Hanzo\Model\CouponsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CouponsToCustomers $couponsToCustomers Object to remove from the list of results
	 *
	 * @return    CouponsToCustomersQuery The current query, for fluid interface
	 */
	public function prune($couponsToCustomers = null)
	{
		if ($couponsToCustomers) {
			$this->addCond('pruneCond0', $this->getAliasedColName(CouponsToCustomersPeer::COUPONS_ID), $couponsToCustomers->getCouponsId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(CouponsToCustomersPeer::CUSTOMERS_ID), $couponsToCustomers->getCustomersId(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseCouponsToCustomersQuery
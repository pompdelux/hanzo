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
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersStateLog;
use Hanzo\Model\OrdersStateLogPeer;
use Hanzo\Model\OrdersStateLogQuery;

/**
 * Base class that represents a query for the 'orders_state_log' table.
 *
 * 
 *
 * @method     OrdersStateLogQuery orderByOrdersId($order = Criteria::ASC) Order by the orders_id column
 * @method     OrdersStateLogQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method     OrdersStateLogQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     OrdersStateLogQuery orderByMessage($order = Criteria::ASC) Order by the message column
 *
 * @method     OrdersStateLogQuery groupByOrdersId() Group by the orders_id column
 * @method     OrdersStateLogQuery groupByState() Group by the state column
 * @method     OrdersStateLogQuery groupByCreatedAt() Group by the created_at column
 * @method     OrdersStateLogQuery groupByMessage() Group by the message column
 *
 * @method     OrdersStateLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     OrdersStateLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     OrdersStateLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     OrdersStateLogQuery leftJoinOrders($relationAlias = null) Adds a LEFT JOIN clause to the query using the Orders relation
 * @method     OrdersStateLogQuery rightJoinOrders($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Orders relation
 * @method     OrdersStateLogQuery innerJoinOrders($relationAlias = null) Adds a INNER JOIN clause to the query using the Orders relation
 *
 * @method     OrdersStateLog findOne(PropelPDO $con = null) Return the first OrdersStateLog matching the query
 * @method     OrdersStateLog findOneOrCreate(PropelPDO $con = null) Return the first OrdersStateLog matching the query, or a new OrdersStateLog object populated from the query conditions when no match is found
 *
 * @method     OrdersStateLog findOneByOrdersId(int $orders_id) Return the first OrdersStateLog filtered by the orders_id column
 * @method     OrdersStateLog findOneByState(int $state) Return the first OrdersStateLog filtered by the state column
 * @method     OrdersStateLog findOneByCreatedAt(string $created_at) Return the first OrdersStateLog filtered by the created_at column
 * @method     OrdersStateLog findOneByMessage(string $message) Return the first OrdersStateLog filtered by the message column
 *
 * @method     array findByOrdersId(int $orders_id) Return OrdersStateLog objects filtered by the orders_id column
 * @method     array findByState(int $state) Return OrdersStateLog objects filtered by the state column
 * @method     array findByCreatedAt(string $created_at) Return OrdersStateLog objects filtered by the created_at column
 * @method     array findByMessage(string $message) Return OrdersStateLog objects filtered by the message column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseOrdersStateLogQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseOrdersStateLogQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\OrdersStateLog', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new OrdersStateLogQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    OrdersStateLogQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof OrdersStateLogQuery) {
			return $criteria;
		}
		$query = new OrdersStateLogQuery();
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
	 * $obj = $c->findPk(array(12, 34, 56), $con);
	 * </code>
	 *
	 * @param     array[$orders_id, $state, $created_at] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    OrdersStateLog|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = OrdersStateLogPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(OrdersStateLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    OrdersStateLog A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ORDERS_ID`, `STATE`, `CREATED_AT`, `MESSAGE` FROM `orders_state_log` WHERE `ORDERS_ID` = :p0 AND `STATE` = :p1 AND `CREATED_AT` = :p2';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->bindValue(':p2', $key[2], PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new OrdersStateLog();
			$obj->hydrate($row);
			OrdersStateLogPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
	 * @return    OrdersStateLog|array|mixed the result, formatted by the current formatter
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
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(OrdersStateLogPeer::ORDERS_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(OrdersStateLogPeer::STATE, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(OrdersStateLogPeer::CREATED_AT, $key[2], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(OrdersStateLogPeer::ORDERS_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(OrdersStateLogPeer::STATE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(OrdersStateLogPeer::CREATED_AT, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the orders_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByOrdersId(1234); // WHERE orders_id = 1234
	 * $query->filterByOrdersId(array(12, 34)); // WHERE orders_id IN (12, 34)
	 * $query->filterByOrdersId(array('min' => 12)); // WHERE orders_id > 12
	 * </code>
	 *
	 * @see       filterByOrders()
	 *
	 * @param     mixed $ordersId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByOrdersId($ordersId = null, $comparison = null)
	{
		if (is_array($ordersId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(OrdersStateLogPeer::ORDERS_ID, $ordersId, $comparison);
	}

	/**
	 * Filter the query on the state column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByState(1234); // WHERE state = 1234
	 * $query->filterByState(array(12, 34)); // WHERE state IN (12, 34)
	 * $query->filterByState(array('min' => 12)); // WHERE state > 12
	 * </code>
	 *
	 * @param     mixed $state The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByState($state = null, $comparison = null)
	{
		if (is_array($state) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(OrdersStateLogPeer::STATE, $state, $comparison);
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
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(OrdersStateLogPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(OrdersStateLogPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(OrdersStateLogPeer::CREATED_AT, $createdAt, $comparison);
	}

	/**
	 * Filter the query on the message column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
	 * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $message The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByMessage($message = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($message)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $message)) {
				$message = str_replace('*', '%', $message);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(OrdersStateLogPeer::MESSAGE, $message, $comparison);
	}

	/**
	 * Filter the query by a related Orders object
	 *
	 * @param     Orders|PropelCollection $orders The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function filterByOrders($orders, $comparison = null)
	{
		if ($orders instanceof Orders) {
			return $this
				->addUsingAlias(OrdersStateLogPeer::ORDERS_ID, $orders->getId(), $comparison);
		} elseif ($orders instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(OrdersStateLogPeer::ORDERS_ID, $orders->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByOrders() only accepts arguments of type Orders or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Orders relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function joinOrders($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Orders');

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
			$this->addJoinObject($join, 'Orders');
		}

		return $this;
	}

	/**
	 * Use the Orders relation Orders object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\OrdersQuery A secondary query class using the current class as primary query
	 */
	public function useOrdersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinOrders($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Orders', '\Hanzo\Model\OrdersQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     OrdersStateLog $ordersStateLog Object to remove from the list of results
	 *
	 * @return    OrdersStateLogQuery The current query, for fluid interface
	 */
	public function prune($ordersStateLog = null)
	{
		if ($ordersStateLog) {
			$this->addCond('pruneCond0', $this->getAliasedColName(OrdersStateLogPeer::ORDERS_ID), $ordersStateLog->getOrdersId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(OrdersStateLogPeer::STATE), $ordersStateLog->getState(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(OrdersStateLogPeer::CREATED_AT), $ordersStateLog->getCreatedAt(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseOrdersStateLogQuery
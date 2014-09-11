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
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\QueryEvent;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersToAxQueueLog;
use Hanzo\Model\OrdersToAxQueueLogPeer;
use Hanzo\Model\OrdersToAxQueueLogQuery;

/**
 * @method OrdersToAxQueueLogQuery orderByOrdersId($order = Criteria::ASC) Order by the orders_id column
 * @method OrdersToAxQueueLogQuery orderByQueueId($order = Criteria::ASC) Order by the queue_id column
 * @method OrdersToAxQueueLogQuery orderByIteration($order = Criteria::ASC) Order by the iteration column
 * @method OrdersToAxQueueLogQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method OrdersToAxQueueLogQuery groupByOrdersId() Group by the orders_id column
 * @method OrdersToAxQueueLogQuery groupByQueueId() Group by the queue_id column
 * @method OrdersToAxQueueLogQuery groupByIteration() Group by the iteration column
 * @method OrdersToAxQueueLogQuery groupByCreatedAt() Group by the created_at column
 *
 * @method OrdersToAxQueueLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method OrdersToAxQueueLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method OrdersToAxQueueLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method OrdersToAxQueueLogQuery leftJoinOrders($relationAlias = null) Adds a LEFT JOIN clause to the query using the Orders relation
 * @method OrdersToAxQueueLogQuery rightJoinOrders($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Orders relation
 * @method OrdersToAxQueueLogQuery innerJoinOrders($relationAlias = null) Adds a INNER JOIN clause to the query using the Orders relation
 *
 * @method OrdersToAxQueueLog findOne(PropelPDO $con = null) Return the first OrdersToAxQueueLog matching the query
 * @method OrdersToAxQueueLog findOneOrCreate(PropelPDO $con = null) Return the first OrdersToAxQueueLog matching the query, or a new OrdersToAxQueueLog object populated from the query conditions when no match is found
 *
 * @method OrdersToAxQueueLog findOneByOrdersId(int $orders_id) Return the first OrdersToAxQueueLog filtered by the orders_id column
 * @method OrdersToAxQueueLog findOneByQueueId(int $queue_id) Return the first OrdersToAxQueueLog filtered by the queue_id column
 * @method OrdersToAxQueueLog findOneByIteration(int $iteration) Return the first OrdersToAxQueueLog filtered by the iteration column
 * @method OrdersToAxQueueLog findOneByCreatedAt(string $created_at) Return the first OrdersToAxQueueLog filtered by the created_at column
 *
 * @method array findByOrdersId(int $orders_id) Return OrdersToAxQueueLog objects filtered by the orders_id column
 * @method array findByQueueId(int $queue_id) Return OrdersToAxQueueLog objects filtered by the queue_id column
 * @method array findByIteration(int $iteration) Return OrdersToAxQueueLog objects filtered by the iteration column
 * @method array findByCreatedAt(string $created_at) Return OrdersToAxQueueLog objects filtered by the created_at column
 */
abstract class BaseOrdersToAxQueueLogQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseOrdersToAxQueueLogQuery object.
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
            $modelName = 'Hanzo\\Model\\OrdersToAxQueueLog';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new OrdersToAxQueueLogQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   OrdersToAxQueueLogQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return OrdersToAxQueueLogQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof OrdersToAxQueueLogQuery) {
            return $criteria;
        }
        $query = new OrdersToAxQueueLogQuery(null, null, $modelAlias);

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
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$orders_id, $queue_id, $created_at]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   OrdersToAxQueueLog|OrdersToAxQueueLog[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrdersToAxQueueLogPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(OrdersToAxQueueLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 OrdersToAxQueueLog A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `orders_id`, `queue_id`, `iteration`, `created_at` FROM `orders_to_ax_queue_log` WHERE `orders_id` = :p0 AND `queue_id` = :p1 AND `created_at` = :p2';
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
            $obj = new OrdersToAxQueueLog();
            $obj->hydrate($row);
            OrdersToAxQueueLogPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
     * @return OrdersToAxQueueLog|OrdersToAxQueueLog[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|OrdersToAxQueueLog[]|mixed the list of results, formatted by the current formatter
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
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(OrdersToAxQueueLogPeer::ORDERS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(OrdersToAxQueueLogPeer::QUEUE_ID, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(OrdersToAxQueueLogPeer::CREATED_AT, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(OrdersToAxQueueLogPeer::ORDERS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(OrdersToAxQueueLogPeer::QUEUE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(OrdersToAxQueueLogPeer::CREATED_AT, $key[2], Criteria::EQUAL);
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
     * $query->filterByOrdersId(array('min' => 12)); // WHERE orders_id >= 12
     * $query->filterByOrdersId(array('max' => 12)); // WHERE orders_id <= 12
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
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function filterByOrdersId($ordersId = null, $comparison = null)
    {
        if (is_array($ordersId)) {
            $useMinMax = false;
            if (isset($ordersId['min'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::ORDERS_ID, $ordersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ordersId['max'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::ORDERS_ID, $ordersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersToAxQueueLogPeer::ORDERS_ID, $ordersId, $comparison);
    }

    /**
     * Filter the query on the queue_id column
     *
     * Example usage:
     * <code>
     * $query->filterByQueueId(1234); // WHERE queue_id = 1234
     * $query->filterByQueueId(array(12, 34)); // WHERE queue_id IN (12, 34)
     * $query->filterByQueueId(array('min' => 12)); // WHERE queue_id >= 12
     * $query->filterByQueueId(array('max' => 12)); // WHERE queue_id <= 12
     * </code>
     *
     * @param     mixed $queueId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function filterByQueueId($queueId = null, $comparison = null)
    {
        if (is_array($queueId)) {
            $useMinMax = false;
            if (isset($queueId['min'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::QUEUE_ID, $queueId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($queueId['max'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::QUEUE_ID, $queueId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersToAxQueueLogPeer::QUEUE_ID, $queueId, $comparison);
    }

    /**
     * Filter the query on the iteration column
     *
     * Example usage:
     * <code>
     * $query->filterByIteration(1234); // WHERE iteration = 1234
     * $query->filterByIteration(array(12, 34)); // WHERE iteration IN (12, 34)
     * $query->filterByIteration(array('min' => 12)); // WHERE iteration >= 12
     * $query->filterByIteration(array('max' => 12)); // WHERE iteration <= 12
     * </code>
     *
     * @param     mixed $iteration The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function filterByIteration($iteration = null, $comparison = null)
    {
        if (is_array($iteration)) {
            $useMinMax = false;
            if (isset($iteration['min'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::ITERATION, $iteration['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($iteration['max'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::ITERATION, $iteration['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersToAxQueueLogPeer::ITERATION, $iteration, $comparison);
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
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(OrdersToAxQueueLogPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersToAxQueueLogPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related Orders object
     *
     * @param   Orders|PropelObjectCollection $orders The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersToAxQueueLogQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrders($orders, $comparison = null)
    {
        if ($orders instanceof Orders) {
            return $this
                ->addUsingAlias(OrdersToAxQueueLogPeer::ORDERS_ID, $orders->getId(), $comparison);
        } elseif ($orders instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersToAxQueueLogPeer::ORDERS_ID, $orders->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
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
        if ($relationAlias) {
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
     * @return   \Hanzo\Model\OrdersQuery A secondary query class using the current class as primary query
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
     * @param   OrdersToAxQueueLog $ordersToAxQueueLog Object to remove from the list of results
     *
     * @return OrdersToAxQueueLogQuery The current query, for fluid interface
     */
    public function prune($ordersToAxQueueLog = null)
    {
        if ($ordersToAxQueueLog) {
            $this->addCond('pruneCond0', $this->getAliasedColName(OrdersToAxQueueLogPeer::ORDERS_ID), $ordersToAxQueueLog->getOrdersId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(OrdersToAxQueueLogPeer::QUEUE_ID), $ordersToAxQueueLog->getQueueId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(OrdersToAxQueueLogPeer::CREATED_AT), $ordersToAxQueueLog->getCreatedAt(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Code to execute before every SELECT statement
     *
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePreSelect(PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger('query.select.pre', new QueryEvent($this));

        return $this->preSelect($con);
    }

    /**
     * Code to execute before every DELETE statement
     *
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePreDelete(PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('delete.pre','query.delete.pre'), new QueryEvent($this));

        return $this->preDelete($con);
    }

    /**
     * Code to execute after every DELETE statement
     *
     * @param     int $affectedRows the number of deleted rows
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePostDelete($affectedRows, PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('delete.post','query.delete.post'), new QueryEvent($this));

        return $this->postDelete($affectedRows, $con);
    }

    /**
     * Code to execute before every UPDATE statement
     *
     * @param     array $values The associative array of columns and values for the update
     * @param     PropelPDO $con The connection object used by the query
     * @param     boolean $forceIndividualSaves If false (default), the resulting call is a BasePeer::doUpdate(), otherwise it is a series of save() calls on all the found objects
     */
    protected function basePreUpdate(&$values, PropelPDO $con, $forceIndividualSaves = false)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('update.pre', 'query.update.pre'), new QueryEvent($this));

        return $this->preUpdate($values, $con, $forceIndividualSaves);
    }

    /**
     * Code to execute after every UPDATE statement
     *
     * @param     int $affectedRows the number of updated rows
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePostUpdate($affectedRows, PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('update.post', 'query.update.post'), new QueryEvent($this));

        return $this->postUpdate($affectedRows, $con);
    }

}

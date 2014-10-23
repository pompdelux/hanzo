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
use Hanzo\Model\OrdersAttributes;
use Hanzo\Model\OrdersAttributesPeer;
use Hanzo\Model\OrdersAttributesQuery;

/**
 * @method OrdersAttributesQuery orderByOrdersId($order = Criteria::ASC) Order by the orders_id column
 * @method OrdersAttributesQuery orderByNs($order = Criteria::ASC) Order by the ns column
 * @method OrdersAttributesQuery orderByCKey($order = Criteria::ASC) Order by the c_key column
 * @method OrdersAttributesQuery orderByCValue($order = Criteria::ASC) Order by the c_value column
 *
 * @method OrdersAttributesQuery groupByOrdersId() Group by the orders_id column
 * @method OrdersAttributesQuery groupByNs() Group by the ns column
 * @method OrdersAttributesQuery groupByCKey() Group by the c_key column
 * @method OrdersAttributesQuery groupByCValue() Group by the c_value column
 *
 * @method OrdersAttributesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method OrdersAttributesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method OrdersAttributesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method OrdersAttributesQuery leftJoinOrders($relationAlias = null) Adds a LEFT JOIN clause to the query using the Orders relation
 * @method OrdersAttributesQuery rightJoinOrders($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Orders relation
 * @method OrdersAttributesQuery innerJoinOrders($relationAlias = null) Adds a INNER JOIN clause to the query using the Orders relation
 *
 * @method OrdersAttributes findOne(PropelPDO $con = null) Return the first OrdersAttributes matching the query
 * @method OrdersAttributes findOneOrCreate(PropelPDO $con = null) Return the first OrdersAttributes matching the query, or a new OrdersAttributes object populated from the query conditions when no match is found
 *
 * @method OrdersAttributes findOneByOrdersId(int $orders_id) Return the first OrdersAttributes filtered by the orders_id column
 * @method OrdersAttributes findOneByNs(string $ns) Return the first OrdersAttributes filtered by the ns column
 * @method OrdersAttributes findOneByCKey(string $c_key) Return the first OrdersAttributes filtered by the c_key column
 * @method OrdersAttributes findOneByCValue(string $c_value) Return the first OrdersAttributes filtered by the c_value column
 *
 * @method array findByOrdersId(int $orders_id) Return OrdersAttributes objects filtered by the orders_id column
 * @method array findByNs(string $ns) Return OrdersAttributes objects filtered by the ns column
 * @method array findByCKey(string $c_key) Return OrdersAttributes objects filtered by the c_key column
 * @method array findByCValue(string $c_value) Return OrdersAttributes objects filtered by the c_value column
 */
abstract class BaseOrdersAttributesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseOrdersAttributesQuery object.
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
            $modelName = 'Hanzo\\Model\\OrdersAttributes';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new OrdersAttributesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   OrdersAttributesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return OrdersAttributesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof OrdersAttributesQuery) {
            return $criteria;
        }
        $query = new OrdersAttributesQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$orders_id, $ns, $c_key]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   OrdersAttributes|OrdersAttributes[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrdersAttributesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(OrdersAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 OrdersAttributes A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `orders_id`, `ns`, `c_key`, `c_value` FROM `orders_attributes` WHERE `orders_id` = :p0 AND `ns` = :p1 AND `c_key` = :p2';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->bindValue(':p2', $key[2], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new OrdersAttributes();
            $obj->hydrate($row);
            OrdersAttributesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
     * @return OrdersAttributes|OrdersAttributes[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|OrdersAttributes[]|mixed the list of results, formatted by the current formatter
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
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(OrdersAttributesPeer::ORDERS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(OrdersAttributesPeer::NS, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(OrdersAttributesPeer::C_KEY, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(OrdersAttributesPeer::ORDERS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(OrdersAttributesPeer::NS, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(OrdersAttributesPeer::C_KEY, $key[2], Criteria::EQUAL);
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
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function filterByOrdersId($ordersId = null, $comparison = null)
    {
        if (is_array($ordersId)) {
            $useMinMax = false;
            if (isset($ordersId['min'])) {
                $this->addUsingAlias(OrdersAttributesPeer::ORDERS_ID, $ordersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ordersId['max'])) {
                $this->addUsingAlias(OrdersAttributesPeer::ORDERS_ID, $ordersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersAttributesPeer::ORDERS_ID, $ordersId, $comparison);
    }

    /**
     * Filter the query on the ns column
     *
     * Example usage:
     * <code>
     * $query->filterByNs('fooValue');   // WHERE ns = 'fooValue'
     * $query->filterByNs('%fooValue%'); // WHERE ns LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ns The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function filterByNs($ns = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ns)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ns)) {
                $ns = str_replace('*', '%', $ns);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersAttributesPeer::NS, $ns, $comparison);
    }

    /**
     * Filter the query on the c_key column
     *
     * Example usage:
     * <code>
     * $query->filterByCKey('fooValue');   // WHERE c_key = 'fooValue'
     * $query->filterByCKey('%fooValue%'); // WHERE c_key LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cKey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function filterByCKey($cKey = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cKey)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cKey)) {
                $cKey = str_replace('*', '%', $cKey);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersAttributesPeer::C_KEY, $cKey, $comparison);
    }

    /**
     * Filter the query on the c_value column
     *
     * Example usage:
     * <code>
     * $query->filterByCValue('fooValue');   // WHERE c_value = 'fooValue'
     * $query->filterByCValue('%fooValue%'); // WHERE c_value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cValue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function filterByCValue($cValue = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cValue)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cValue)) {
                $cValue = str_replace('*', '%', $cValue);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersAttributesPeer::C_VALUE, $cValue, $comparison);
    }

    /**
     * Filter the query by a related Orders object
     *
     * @param   Orders|PropelObjectCollection $orders The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersAttributesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrders($orders, $comparison = null)
    {
        if ($orders instanceof Orders) {
            return $this
                ->addUsingAlias(OrdersAttributesPeer::ORDERS_ID, $orders->getId(), $comparison);
        } elseif ($orders instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersAttributesPeer::ORDERS_ID, $orders->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return OrdersAttributesQuery The current query, for fluid interface
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
     * @param   OrdersAttributes $ordersAttributes Object to remove from the list of results
     *
     * @return OrdersAttributesQuery The current query, for fluid interface
     */
    public function prune($ordersAttributes = null)
    {
        if ($ordersAttributes) {
            $this->addCond('pruneCond0', $this->getAliasedColName(OrdersAttributesPeer::ORDERS_ID), $ordersAttributes->getOrdersId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(OrdersAttributesPeer::NS), $ordersAttributes->getNs(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(OrdersAttributesPeer::C_KEY), $ordersAttributes->getCKey(), Criteria::NOT_EQUAL);
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
        EventDispatcherProxy::trigger(array('delete.pre','query.delete.pre'), new QueryEvent($this));
        // event behavior
        // placeholder, issue #5

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

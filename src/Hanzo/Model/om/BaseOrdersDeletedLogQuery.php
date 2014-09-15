<?php

namespace Hanzo\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Glorpen\Propel\PropelBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\Propel\PropelBundle\Events\QueryEvent;
use Hanzo\Model\OrdersDeletedLog;
use Hanzo\Model\OrdersDeletedLogPeer;
use Hanzo\Model\OrdersDeletedLogQuery;

/**
 * @method OrdersDeletedLogQuery orderByOrdersId($order = Criteria::ASC) Order by the orders_id column
 * @method OrdersDeletedLogQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method OrdersDeletedLogQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method OrdersDeletedLogQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method OrdersDeletedLogQuery orderByTrigger($order = Criteria::ASC) Order by the trigger column
 * @method OrdersDeletedLogQuery orderByContent($order = Criteria::ASC) Order by the content column
 * @method OrdersDeletedLogQuery orderByDeletedBy($order = Criteria::ASC) Order by the deleted_by column
 * @method OrdersDeletedLogQuery orderByDeletedAt($order = Criteria::ASC) Order by the deleted_at column
 *
 * @method OrdersDeletedLogQuery groupByOrdersId() Group by the orders_id column
 * @method OrdersDeletedLogQuery groupByCustomersId() Group by the customers_id column
 * @method OrdersDeletedLogQuery groupByName() Group by the name column
 * @method OrdersDeletedLogQuery groupByEmail() Group by the email column
 * @method OrdersDeletedLogQuery groupByTrigger() Group by the trigger column
 * @method OrdersDeletedLogQuery groupByContent() Group by the content column
 * @method OrdersDeletedLogQuery groupByDeletedBy() Group by the deleted_by column
 * @method OrdersDeletedLogQuery groupByDeletedAt() Group by the deleted_at column
 *
 * @method OrdersDeletedLogQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method OrdersDeletedLogQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method OrdersDeletedLogQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method OrdersDeletedLog findOne(PropelPDO $con = null) Return the first OrdersDeletedLog matching the query
 * @method OrdersDeletedLog findOneOrCreate(PropelPDO $con = null) Return the first OrdersDeletedLog matching the query, or a new OrdersDeletedLog object populated from the query conditions when no match is found
 *
 * @method OrdersDeletedLog findOneByCustomersId(int $customers_id) Return the first OrdersDeletedLog filtered by the customers_id column
 * @method OrdersDeletedLog findOneByName(string $name) Return the first OrdersDeletedLog filtered by the name column
 * @method OrdersDeletedLog findOneByEmail(string $email) Return the first OrdersDeletedLog filtered by the email column
 * @method OrdersDeletedLog findOneByTrigger(string $trigger) Return the first OrdersDeletedLog filtered by the trigger column
 * @method OrdersDeletedLog findOneByContent(string $content) Return the first OrdersDeletedLog filtered by the content column
 * @method OrdersDeletedLog findOneByDeletedBy(string $deleted_by) Return the first OrdersDeletedLog filtered by the deleted_by column
 * @method OrdersDeletedLog findOneByDeletedAt(string $deleted_at) Return the first OrdersDeletedLog filtered by the deleted_at column
 *
 * @method array findByOrdersId(int $orders_id) Return OrdersDeletedLog objects filtered by the orders_id column
 * @method array findByCustomersId(int $customers_id) Return OrdersDeletedLog objects filtered by the customers_id column
 * @method array findByName(string $name) Return OrdersDeletedLog objects filtered by the name column
 * @method array findByEmail(string $email) Return OrdersDeletedLog objects filtered by the email column
 * @method array findByTrigger(string $trigger) Return OrdersDeletedLog objects filtered by the trigger column
 * @method array findByContent(string $content) Return OrdersDeletedLog objects filtered by the content column
 * @method array findByDeletedBy(string $deleted_by) Return OrdersDeletedLog objects filtered by the deleted_by column
 * @method array findByDeletedAt(string $deleted_at) Return OrdersDeletedLog objects filtered by the deleted_at column
 */
abstract class BaseOrdersDeletedLogQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseOrdersDeletedLogQuery object.
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
            $modelName = 'Hanzo\\Model\\OrdersDeletedLog';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new OrdersDeletedLogQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   OrdersDeletedLogQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return OrdersDeletedLogQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof OrdersDeletedLogQuery) {
            return $criteria;
        }
        $query = new OrdersDeletedLogQuery(null, null, $modelAlias);

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
     * @return   OrdersDeletedLog|OrdersDeletedLog[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrdersDeletedLogPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(OrdersDeletedLogPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 OrdersDeletedLog A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByOrdersId($key, $con = null)
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
     * @return                 OrdersDeletedLog A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `orders_id`, `customers_id`, `name`, `email`, `trigger`, `content`, `deleted_by`, `deleted_at` FROM `orders_deleted_log` WHERE `orders_id` = :p0';
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
            $obj = new OrdersDeletedLog();
            $obj->hydrate($row);
            OrdersDeletedLogPeer::addInstanceToPool($obj, (string) $key);
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
     * @return OrdersDeletedLog|OrdersDeletedLog[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|OrdersDeletedLog[]|mixed the list of results, formatted by the current formatter
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
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrdersDeletedLogPeer::ORDERS_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrdersDeletedLogPeer::ORDERS_ID, $keys, Criteria::IN);
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
     * @param     mixed $ordersId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByOrdersId($ordersId = null, $comparison = null)
    {
        if (is_array($ordersId)) {
            $useMinMax = false;
            if (isset($ordersId['min'])) {
                $this->addUsingAlias(OrdersDeletedLogPeer::ORDERS_ID, $ordersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ordersId['max'])) {
                $this->addUsingAlias(OrdersDeletedLogPeer::ORDERS_ID, $ordersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::ORDERS_ID, $ordersId, $comparison);
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
     * @param     mixed $customersId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByCustomersId($customersId = null, $comparison = null)
    {
        if (is_array($customersId)) {
            $useMinMax = false;
            if (isset($customersId['min'])) {
                $this->addUsingAlias(OrdersDeletedLogPeer::CUSTOMERS_ID, $customersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customersId['max'])) {
                $this->addUsingAlias(OrdersDeletedLogPeer::CUSTOMERS_ID, $customersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::CUSTOMERS_ID, $customersId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::NAME, $name, $comparison);
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
     * @return OrdersDeletedLogQuery The current query, for fluid interface
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

        return $this->addUsingAlias(OrdersDeletedLogPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the trigger column
     *
     * Example usage:
     * <code>
     * $query->filterByTrigger('fooValue');   // WHERE trigger = 'fooValue'
     * $query->filterByTrigger('%fooValue%'); // WHERE trigger LIKE '%fooValue%'
     * </code>
     *
     * @param     string $trigger The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByTrigger($trigger = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($trigger)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $trigger)) {
                $trigger = str_replace('*', '%', $trigger);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::TRIGGER, $trigger, $comparison);
    }

    /**
     * Filter the query on the content column
     *
     * Example usage:
     * <code>
     * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
     * $query->filterByContent('%fooValue%'); // WHERE content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $content The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $content)) {
                $content = str_replace('*', '%', $content);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::CONTENT, $content, $comparison);
    }

    /**
     * Filter the query on the deleted_by column
     *
     * Example usage:
     * <code>
     * $query->filterByDeletedBy('fooValue');   // WHERE deleted_by = 'fooValue'
     * $query->filterByDeletedBy('%fooValue%'); // WHERE deleted_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deletedBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByDeletedBy($deletedBy = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deletedBy)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deletedBy)) {
                $deletedBy = str_replace('*', '%', $deletedBy);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::DELETED_BY, $deletedBy, $comparison);
    }

    /**
     * Filter the query on the deleted_at column
     *
     * Example usage:
     * <code>
     * $query->filterByDeletedAt('2011-03-14'); // WHERE deleted_at = '2011-03-14'
     * $query->filterByDeletedAt('now'); // WHERE deleted_at = '2011-03-14'
     * $query->filterByDeletedAt(array('max' => 'yesterday')); // WHERE deleted_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $deletedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function filterByDeletedAt($deletedAt = null, $comparison = null)
    {
        if (is_array($deletedAt)) {
            $useMinMax = false;
            if (isset($deletedAt['min'])) {
                $this->addUsingAlias(OrdersDeletedLogPeer::DELETED_AT, $deletedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($deletedAt['max'])) {
                $this->addUsingAlias(OrdersDeletedLogPeer::DELETED_AT, $deletedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersDeletedLogPeer::DELETED_AT, $deletedAt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   OrdersDeletedLog $ordersDeletedLog Object to remove from the list of results
     *
     * @return OrdersDeletedLogQuery The current query, for fluid interface
     */
    public function prune($ordersDeletedLog = null)
    {
        if ($ordersDeletedLog) {
            $this->addUsingAlias(OrdersDeletedLogPeer::ORDERS_ID, $ordersDeletedLog->getOrdersId(), Criteria::NOT_EQUAL);
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

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
use Hanzo\Model\Customers;
use Hanzo\Model\Wall;
use Hanzo\Model\WallLikes;
use Hanzo\Model\WallLikesPeer;
use Hanzo\Model\WallLikesQuery;

/**
 * @method WallLikesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method WallLikesQuery orderByWallId($order = Criteria::ASC) Order by the wall_id column
 * @method WallLikesQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method WallLikesQuery orderByStatus($order = Criteria::ASC) Order by the status column
 *
 * @method WallLikesQuery groupById() Group by the id column
 * @method WallLikesQuery groupByWallId() Group by the wall_id column
 * @method WallLikesQuery groupByCustomersId() Group by the customers_id column
 * @method WallLikesQuery groupByStatus() Group by the status column
 *
 * @method WallLikesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method WallLikesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method WallLikesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method WallLikesQuery leftJoinWall($relationAlias = null) Adds a LEFT JOIN clause to the query using the Wall relation
 * @method WallLikesQuery rightJoinWall($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Wall relation
 * @method WallLikesQuery innerJoinWall($relationAlias = null) Adds a INNER JOIN clause to the query using the Wall relation
 *
 * @method WallLikesQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method WallLikesQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method WallLikesQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method WallLikes findOne(PropelPDO $con = null) Return the first WallLikes matching the query
 * @method WallLikes findOneOrCreate(PropelPDO $con = null) Return the first WallLikes matching the query, or a new WallLikes object populated from the query conditions when no match is found
 *
 * @method WallLikes findOneByWallId(int $wall_id) Return the first WallLikes filtered by the wall_id column
 * @method WallLikes findOneByCustomersId(int $customers_id) Return the first WallLikes filtered by the customers_id column
 * @method WallLikes findOneByStatus(boolean $status) Return the first WallLikes filtered by the status column
 *
 * @method array findById(int $id) Return WallLikes objects filtered by the id column
 * @method array findByWallId(int $wall_id) Return WallLikes objects filtered by the wall_id column
 * @method array findByCustomersId(int $customers_id) Return WallLikes objects filtered by the customers_id column
 * @method array findByStatus(boolean $status) Return WallLikes objects filtered by the status column
 */
abstract class BaseWallLikesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseWallLikesQuery object.
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
            $modelName = 'Hanzo\\Model\\WallLikes';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new WallLikesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   WallLikesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return WallLikesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof WallLikesQuery) {
            return $criteria;
        }
        $query = new WallLikesQuery(null, null, $modelAlias);

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
     * @return   WallLikes|WallLikes[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = WallLikesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(WallLikesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 WallLikes A model object, or null if the key is not found
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
     * @return                 WallLikes A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `wall_id`, `customers_id`, `status` FROM `wall_likes` WHERE `id` = :p0';
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
            $obj = new WallLikes();
            $obj->hydrate($row);
            WallLikesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return WallLikes|WallLikes[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|WallLikes[]|mixed the list of results, formatted by the current formatter
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
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(WallLikesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(WallLikesPeer::ID, $keys, Criteria::IN);
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
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(WallLikesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(WallLikesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WallLikesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the wall_id column
     *
     * Example usage:
     * <code>
     * $query->filterByWallId(1234); // WHERE wall_id = 1234
     * $query->filterByWallId(array(12, 34)); // WHERE wall_id IN (12, 34)
     * $query->filterByWallId(array('min' => 12)); // WHERE wall_id >= 12
     * $query->filterByWallId(array('max' => 12)); // WHERE wall_id <= 12
     * </code>
     *
     * @see       filterByWall()
     *
     * @param     mixed $wallId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function filterByWallId($wallId = null, $comparison = null)
    {
        if (is_array($wallId)) {
            $useMinMax = false;
            if (isset($wallId['min'])) {
                $this->addUsingAlias(WallLikesPeer::WALL_ID, $wallId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($wallId['max'])) {
                $this->addUsingAlias(WallLikesPeer::WALL_ID, $wallId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WallLikesPeer::WALL_ID, $wallId, $comparison);
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
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function filterByCustomersId($customersId = null, $comparison = null)
    {
        if (is_array($customersId)) {
            $useMinMax = false;
            if (isset($customersId['min'])) {
                $this->addUsingAlias(WallLikesPeer::CUSTOMERS_ID, $customersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customersId['max'])) {
                $this->addUsingAlias(WallLikesPeer::CUSTOMERS_ID, $customersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WallLikesPeer::CUSTOMERS_ID, $customersId, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus(true); // WHERE status = true
     * $query->filterByStatus('yes'); // WHERE status = true
     * </code>
     *
     * @param     boolean|string $status The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (is_string($status)) {
            $status = in_array(strtolower($status), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(WallLikesPeer::STATUS, $status, $comparison);
    }

    /**
     * Filter the query by a related Wall object
     *
     * @param   Wall|PropelObjectCollection $wall The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WallLikesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByWall($wall, $comparison = null)
    {
        if ($wall instanceof Wall) {
            return $this
                ->addUsingAlias(WallLikesPeer::WALL_ID, $wall->getId(), $comparison);
        } elseif ($wall instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WallLikesPeer::WALL_ID, $wall->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByWall() only accepts arguments of type Wall or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Wall relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function joinWall($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Wall');

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
            $this->addJoinObject($join, 'Wall');
        }

        return $this;
    }

    /**
     * Use the Wall relation Wall object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\WallQuery A secondary query class using the current class as primary query
     */
    public function useWallQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWall($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Wall', '\Hanzo\Model\WallQuery');
    }

    /**
     * Filter the query by a related Customers object
     *
     * @param   Customers|PropelObjectCollection $customers The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WallLikesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCustomers($customers, $comparison = null)
    {
        if ($customers instanceof Customers) {
            return $this
                ->addUsingAlias(WallLikesPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
        } elseif ($customers instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WallLikesPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return WallLikesQuery The current query, for fluid interface
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
    public function useCustomersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   WallLikes $wallLikes Object to remove from the list of results
     *
     * @return WallLikesQuery The current query, for fluid interface
     */
    public function prune($wallLikes = null)
    {
        if ($wallLikes) {
            $this->addUsingAlias(WallLikesPeer::ID, $wallLikes->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

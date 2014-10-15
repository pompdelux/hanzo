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
use Hanzo\Model\Customers;
use Hanzo\Model\GothiaAccounts;
use Hanzo\Model\GothiaAccountsPeer;
use Hanzo\Model\GothiaAccountsQuery;

/**
 * @method GothiaAccountsQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method GothiaAccountsQuery orderByDistributionBy($order = Criteria::ASC) Order by the distribution_by column
 * @method GothiaAccountsQuery orderByDistributionType($order = Criteria::ASC) Order by the distribution_type column
 * @method GothiaAccountsQuery orderBySocialSecurityNum($order = Criteria::ASC) Order by the social_security_num column
 *
 * @method GothiaAccountsQuery groupByCustomersId() Group by the customers_id column
 * @method GothiaAccountsQuery groupByDistributionBy() Group by the distribution_by column
 * @method GothiaAccountsQuery groupByDistributionType() Group by the distribution_type column
 * @method GothiaAccountsQuery groupBySocialSecurityNum() Group by the social_security_num column
 *
 * @method GothiaAccountsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method GothiaAccountsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method GothiaAccountsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method GothiaAccountsQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method GothiaAccountsQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method GothiaAccountsQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method GothiaAccounts findOne(PropelPDO $con = null) Return the first GothiaAccounts matching the query
 * @method GothiaAccounts findOneOrCreate(PropelPDO $con = null) Return the first GothiaAccounts matching the query, or a new GothiaAccounts object populated from the query conditions when no match is found
 *
 * @method GothiaAccounts findOneByDistributionBy(string $distribution_by) Return the first GothiaAccounts filtered by the distribution_by column
 * @method GothiaAccounts findOneByDistributionType(string $distribution_type) Return the first GothiaAccounts filtered by the distribution_type column
 * @method GothiaAccounts findOneBySocialSecurityNum(string $social_security_num) Return the first GothiaAccounts filtered by the social_security_num column
 *
 * @method array findByCustomersId(int $customers_id) Return GothiaAccounts objects filtered by the customers_id column
 * @method array findByDistributionBy(string $distribution_by) Return GothiaAccounts objects filtered by the distribution_by column
 * @method array findByDistributionType(string $distribution_type) Return GothiaAccounts objects filtered by the distribution_type column
 * @method array findBySocialSecurityNum(string $social_security_num) Return GothiaAccounts objects filtered by the social_security_num column
 */
abstract class BaseGothiaAccountsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseGothiaAccountsQuery object.
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
            $modelName = 'Hanzo\\Model\\GothiaAccounts';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new GothiaAccountsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   GothiaAccountsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GothiaAccountsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GothiaAccountsQuery) {
            return $criteria;
        }
        $query = new GothiaAccountsQuery(null, null, $modelAlias);

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
     * @return   GothiaAccounts|GothiaAccounts[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GothiaAccountsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GothiaAccountsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 GothiaAccounts A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByCustomersId($key, $con = null)
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
     * @return                 GothiaAccounts A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `customers_id`, `distribution_by`, `distribution_type`, `social_security_num` FROM `gothia_accounts` WHERE `customers_id` = :p0';
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
            $obj = new GothiaAccounts();
            $obj->hydrate($row);
            GothiaAccountsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return GothiaAccounts|GothiaAccounts[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|GothiaAccounts[]|mixed the list of results, formatted by the current formatter
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
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $keys, Criteria::IN);
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
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function filterByCustomersId($customersId = null, $comparison = null)
    {
        if (is_array($customersId)) {
            $useMinMax = false;
            if (isset($customersId['min'])) {
                $this->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $customersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customersId['max'])) {
                $this->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $customersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $customersId, $comparison);
    }

    /**
     * Filter the query on the distribution_by column
     *
     * Example usage:
     * <code>
     * $query->filterByDistributionBy('fooValue');   // WHERE distribution_by = 'fooValue'
     * $query->filterByDistributionBy('%fooValue%'); // WHERE distribution_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $distributionBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function filterByDistributionBy($distributionBy = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($distributionBy)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $distributionBy)) {
                $distributionBy = str_replace('*', '%', $distributionBy);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GothiaAccountsPeer::DISTRIBUTION_BY, $distributionBy, $comparison);
    }

    /**
     * Filter the query on the distribution_type column
     *
     * Example usage:
     * <code>
     * $query->filterByDistributionType('fooValue');   // WHERE distribution_type = 'fooValue'
     * $query->filterByDistributionType('%fooValue%'); // WHERE distribution_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $distributionType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function filterByDistributionType($distributionType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($distributionType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $distributionType)) {
                $distributionType = str_replace('*', '%', $distributionType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GothiaAccountsPeer::DISTRIBUTION_TYPE, $distributionType, $comparison);
    }

    /**
     * Filter the query on the social_security_num column
     *
     * Example usage:
     * <code>
     * $query->filterBySocialSecurityNum('fooValue');   // WHERE social_security_num = 'fooValue'
     * $query->filterBySocialSecurityNum('%fooValue%'); // WHERE social_security_num LIKE '%fooValue%'
     * </code>
     *
     * @param     string $socialSecurityNum The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function filterBySocialSecurityNum($socialSecurityNum = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($socialSecurityNum)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $socialSecurityNum)) {
                $socialSecurityNum = str_replace('*', '%', $socialSecurityNum);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GothiaAccountsPeer::SOCIAL_SECURITY_NUM, $socialSecurityNum, $comparison);
    }

    /**
     * Filter the query by a related Customers object
     *
     * @param   Customers|PropelObjectCollection $customers The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GothiaAccountsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCustomers($customers, $comparison = null)
    {
        if ($customers instanceof Customers) {
            return $this
                ->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
        } elseif ($customers instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return GothiaAccountsQuery The current query, for fluid interface
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
     * @param   GothiaAccounts $gothiaAccounts Object to remove from the list of results
     *
     * @return GothiaAccountsQuery The current query, for fluid interface
     */
    public function prune($gothiaAccounts = null)
    {
        if ($gothiaAccounts) {
            $this->addUsingAlias(GothiaAccountsPeer::CUSTOMERS_ID, $gothiaAccounts->getCustomersId(), Criteria::NOT_EQUAL);
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

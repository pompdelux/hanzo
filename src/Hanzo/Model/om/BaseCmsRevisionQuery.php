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
use Hanzo\Model\Cms;
use Hanzo\Model\CmsRevision;
use Hanzo\Model\CmsRevisionPeer;
use Hanzo\Model\CmsRevisionQuery;

/**
 * @method CmsRevisionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CmsRevisionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method CmsRevisionQuery orderByPublishOnDate($order = Criteria::ASC) Order by the publish_on_date column
 * @method CmsRevisionQuery orderByRevision($order = Criteria::ASC) Order by the revision column
 *
 * @method CmsRevisionQuery groupById() Group by the id column
 * @method CmsRevisionQuery groupByCreatedAt() Group by the created_at column
 * @method CmsRevisionQuery groupByPublishOnDate() Group by the publish_on_date column
 * @method CmsRevisionQuery groupByRevision() Group by the revision column
 *
 * @method CmsRevisionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CmsRevisionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CmsRevisionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CmsRevisionQuery leftJoinCms($relationAlias = null) Adds a LEFT JOIN clause to the query using the Cms relation
 * @method CmsRevisionQuery rightJoinCms($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Cms relation
 * @method CmsRevisionQuery innerJoinCms($relationAlias = null) Adds a INNER JOIN clause to the query using the Cms relation
 *
 * @method CmsRevision findOne(PropelPDO $con = null) Return the first CmsRevision matching the query
 * @method CmsRevision findOneOrCreate(PropelPDO $con = null) Return the first CmsRevision matching the query, or a new CmsRevision object populated from the query conditions when no match is found
 *
 * @method CmsRevision findOneById(int $id) Return the first CmsRevision filtered by the id column
 * @method CmsRevision findOneByCreatedAt(string $created_at) Return the first CmsRevision filtered by the created_at column
 * @method CmsRevision findOneByPublishOnDate(string $publish_on_date) Return the first CmsRevision filtered by the publish_on_date column
 * @method CmsRevision findOneByRevision( $revision) Return the first CmsRevision filtered by the revision column
 *
 * @method array findById(int $id) Return CmsRevision objects filtered by the id column
 * @method array findByCreatedAt(string $created_at) Return CmsRevision objects filtered by the created_at column
 * @method array findByPublishOnDate(string $publish_on_date) Return CmsRevision objects filtered by the publish_on_date column
 * @method array findByRevision( $revision) Return CmsRevision objects filtered by the revision column
 */
abstract class BaseCmsRevisionQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCmsRevisionQuery object.
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
            $modelName = 'Hanzo\\Model\\CmsRevision';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new CmsRevisionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CmsRevisionQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CmsRevisionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CmsRevisionQuery) {
            return $criteria;
        }
        $query = new CmsRevisionQuery(null, null, $modelAlias);

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
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$id, $created_at]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CmsRevision|CmsRevision[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CmsRevisionPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CmsRevisionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CmsRevision A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `created_at`, `publish_on_date`, `revision` FROM `cms_revision` WHERE `id` = :p0 AND `created_at` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CmsRevision();
            $obj->hydrate($row);
            CmsRevisionPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return CmsRevision|CmsRevision[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CmsRevision[]|mixed the list of results, formatted by the current formatter
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
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CmsRevisionPeer::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CmsRevisionPeer::CREATED_AT, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CmsRevisionPeer::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CmsRevisionPeer::CREATED_AT, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByCms()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CmsRevisionPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CmsRevisionPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsRevisionPeer::ID, $id, $comparison);
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
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CmsRevisionPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CmsRevisionPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsRevisionPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the publish_on_date column
     *
     * Example usage:
     * <code>
     * $query->filterByPublishOnDate('2011-03-14'); // WHERE publish_on_date = '2011-03-14'
     * $query->filterByPublishOnDate('now'); // WHERE publish_on_date = '2011-03-14'
     * $query->filterByPublishOnDate(array('max' => 'yesterday')); // WHERE publish_on_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $publishOnDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function filterByPublishOnDate($publishOnDate = null, $comparison = null)
    {
        if (is_array($publishOnDate)) {
            $useMinMax = false;
            if (isset($publishOnDate['min'])) {
                $this->addUsingAlias(CmsRevisionPeer::PUBLISH_ON_DATE, $publishOnDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publishOnDate['max'])) {
                $this->addUsingAlias(CmsRevisionPeer::PUBLISH_ON_DATE, $publishOnDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsRevisionPeer::PUBLISH_ON_DATE, $publishOnDate, $comparison);
    }

    /**
     * Filter the query on the revision column
     *
     * @param     mixed $revision The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function filterByRevision($revision = null, $comparison = null)
    {
        if (is_object($revision)) {
            $revision = serialize($revision);
        }

        return $this->addUsingAlias(CmsRevisionPeer::REVISION, $revision, $comparison);
    }

    /**
     * Filter the query by a related Cms object
     *
     * @param   Cms|PropelObjectCollection $cms The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsRevisionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCms($cms, $comparison = null)
    {
        if ($cms instanceof Cms) {
            return $this
                ->addUsingAlias(CmsRevisionPeer::ID, $cms->getId(), $comparison);
        } elseif ($cms instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CmsRevisionPeer::ID, $cms->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCms() only accepts arguments of type Cms or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Cms relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function joinCms($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Cms');

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
            $this->addJoinObject($join, 'Cms');
        }

        return $this;
    }

    /**
     * Use the Cms relation Cms object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsQuery A secondary query class using the current class as primary query
     */
    public function useCmsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCms($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Cms', '\Hanzo\Model\CmsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CmsRevision $cmsRevision Object to remove from the list of results
     *
     * @return CmsRevisionQuery The current query, for fluid interface
     */
    public function prune($cmsRevision = null)
    {
        if ($cmsRevision) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CmsRevisionPeer::ID), $cmsRevision->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CmsRevisionPeer::CREATED_AT), $cmsRevision->getCreatedAt(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
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

    // timestampable behavior

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     CmsRevisionQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CmsRevisionPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     CmsRevisionQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CmsRevisionPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     CmsRevisionQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CmsRevisionPeer::CREATED_AT);
    }
}

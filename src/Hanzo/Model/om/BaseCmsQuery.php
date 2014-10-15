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
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsRevision;
use Hanzo\Model\CmsThread;

/**
 * @method CmsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CmsQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method CmsQuery orderByCmsThreadId($order = Criteria::ASC) Order by the cms_thread_id column
 * @method CmsQuery orderBySort($order = Criteria::ASC) Order by the sort column
 * @method CmsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method CmsQuery orderByUpdatedBy($order = Criteria::ASC) Order by the updated_by column
 * @method CmsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method CmsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method CmsQuery groupById() Group by the id column
 * @method CmsQuery groupByParentId() Group by the parent_id column
 * @method CmsQuery groupByCmsThreadId() Group by the cms_thread_id column
 * @method CmsQuery groupBySort() Group by the sort column
 * @method CmsQuery groupByType() Group by the type column
 * @method CmsQuery groupByUpdatedBy() Group by the updated_by column
 * @method CmsQuery groupByCreatedAt() Group by the created_at column
 * @method CmsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method CmsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CmsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CmsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CmsQuery leftJoinCmsThread($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsThread relation
 * @method CmsQuery rightJoinCmsThread($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsThread relation
 * @method CmsQuery innerJoinCmsThread($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsThread relation
 *
 * @method CmsQuery leftJoinCmsRelatedByParentId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsRelatedByParentId relation
 * @method CmsQuery rightJoinCmsRelatedByParentId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsRelatedByParentId relation
 * @method CmsQuery innerJoinCmsRelatedByParentId($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsRelatedByParentId relation
 *
 * @method CmsQuery leftJoinCmsRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsRelatedById relation
 * @method CmsQuery rightJoinCmsRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsRelatedById relation
 * @method CmsQuery innerJoinCmsRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsRelatedById relation
 *
 * @method CmsQuery leftJoinCmsRevision($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsRevision relation
 * @method CmsQuery rightJoinCmsRevision($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsRevision relation
 * @method CmsQuery innerJoinCmsRevision($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsRevision relation
 *
 * @method CmsQuery leftJoinCmsI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsI18n relation
 * @method CmsQuery rightJoinCmsI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsI18n relation
 * @method CmsQuery innerJoinCmsI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsI18n relation
 *
 * @method Cms findOne(PropelPDO $con = null) Return the first Cms matching the query
 * @method Cms findOneOrCreate(PropelPDO $con = null) Return the first Cms matching the query, or a new Cms object populated from the query conditions when no match is found
 *
 * @method Cms findOneByParentId(int $parent_id) Return the first Cms filtered by the parent_id column
 * @method Cms findOneByCmsThreadId(int $cms_thread_id) Return the first Cms filtered by the cms_thread_id column
 * @method Cms findOneBySort(int $sort) Return the first Cms filtered by the sort column
 * @method Cms findOneByType(string $type) Return the first Cms filtered by the type column
 * @method Cms findOneByUpdatedBy(string $updated_by) Return the first Cms filtered by the updated_by column
 * @method Cms findOneByCreatedAt(string $created_at) Return the first Cms filtered by the created_at column
 * @method Cms findOneByUpdatedAt(string $updated_at) Return the first Cms filtered by the updated_at column
 *
 * @method array findById(int $id) Return Cms objects filtered by the id column
 * @method array findByParentId(int $parent_id) Return Cms objects filtered by the parent_id column
 * @method array findByCmsThreadId(int $cms_thread_id) Return Cms objects filtered by the cms_thread_id column
 * @method array findBySort(int $sort) Return Cms objects filtered by the sort column
 * @method array findByType(string $type) Return Cms objects filtered by the type column
 * @method array findByUpdatedBy(string $updated_by) Return Cms objects filtered by the updated_by column
 * @method array findByCreatedAt(string $created_at) Return Cms objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Cms objects filtered by the updated_at column
 */
abstract class BaseCmsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCmsQuery object.
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
            $modelName = 'Hanzo\\Model\\Cms';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new CmsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CmsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CmsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CmsQuery) {
            return $criteria;
        }
        $query = new CmsQuery(null, null, $modelAlias);

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
     * @return   Cms|Cms[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CmsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CmsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Cms A model object, or null if the key is not found
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
     * @return                 Cms A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `parent_id`, `cms_thread_id`, `sort`, `type`, `updated_by`, `created_at`, `updated_at` FROM `cms` WHERE `id` = :p0';
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
            $obj = new Cms();
            $obj->hydrate($row);
            CmsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Cms|Cms[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Cms[]|mixed the list of results, formatted by the current formatter
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
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CmsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CmsPeer::ID, $keys, Criteria::IN);
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
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CmsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CmsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentId(1234); // WHERE parent_id = 1234
     * $query->filterByParentId(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByParentId(array('min' => 12)); // WHERE parent_id >= 12
     * $query->filterByParentId(array('max' => 12)); // WHERE parent_id <= 12
     * </code>
     *
     * @see       filterByCmsRelatedByParentId()
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(CmsPeer::PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(CmsPeer::PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsPeer::PARENT_ID, $parentId, $comparison);
    }

    /**
     * Filter the query on the cms_thread_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCmsThreadId(1234); // WHERE cms_thread_id = 1234
     * $query->filterByCmsThreadId(array(12, 34)); // WHERE cms_thread_id IN (12, 34)
     * $query->filterByCmsThreadId(array('min' => 12)); // WHERE cms_thread_id >= 12
     * $query->filterByCmsThreadId(array('max' => 12)); // WHERE cms_thread_id <= 12
     * </code>
     *
     * @see       filterByCmsThread()
     *
     * @param     mixed $cmsThreadId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByCmsThreadId($cmsThreadId = null, $comparison = null)
    {
        if (is_array($cmsThreadId)) {
            $useMinMax = false;
            if (isset($cmsThreadId['min'])) {
                $this->addUsingAlias(CmsPeer::CMS_THREAD_ID, $cmsThreadId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cmsThreadId['max'])) {
                $this->addUsingAlias(CmsPeer::CMS_THREAD_ID, $cmsThreadId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsPeer::CMS_THREAD_ID, $cmsThreadId, $comparison);
    }

    /**
     * Filter the query on the sort column
     *
     * Example usage:
     * <code>
     * $query->filterBySort(1234); // WHERE sort = 1234
     * $query->filterBySort(array(12, 34)); // WHERE sort IN (12, 34)
     * $query->filterBySort(array('min' => 12)); // WHERE sort >= 12
     * $query->filterBySort(array('max' => 12)); // WHERE sort <= 12
     * </code>
     *
     * @param     mixed $sort The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(CmsPeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(CmsPeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsPeer::SORT, $sort, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CmsPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the updated_by column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedBy('fooValue');   // WHERE updated_by = 'fooValue'
     * $query->filterByUpdatedBy('%fooValue%'); // WHERE updated_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $updatedBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByUpdatedBy($updatedBy = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($updatedBy)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $updatedBy)) {
                $updatedBy = str_replace('*', '%', $updatedBy);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CmsPeer::UPDATED_BY, $updatedBy, $comparison);
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
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CmsPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CmsPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
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
     * @return CmsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CmsPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CmsPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related CmsThread object
     *
     * @param   CmsThread|PropelObjectCollection $cmsThread The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsThread($cmsThread, $comparison = null)
    {
        if ($cmsThread instanceof CmsThread) {
            return $this
                ->addUsingAlias(CmsPeer::CMS_THREAD_ID, $cmsThread->getId(), $comparison);
        } elseif ($cmsThread instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CmsPeer::CMS_THREAD_ID, $cmsThread->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCmsThread() only accepts arguments of type CmsThread or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsThread relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function joinCmsThread($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsThread');

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
            $this->addJoinObject($join, 'CmsThread');
        }

        return $this;
    }

    /**
     * Use the CmsThread relation CmsThread object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsThreadQuery A secondary query class using the current class as primary query
     */
    public function useCmsThreadQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCmsThread($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsThread', '\Hanzo\Model\CmsThreadQuery');
    }

    /**
     * Filter the query by a related Cms object
     *
     * @param   Cms|PropelObjectCollection $cms The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsRelatedByParentId($cms, $comparison = null)
    {
        if ($cms instanceof Cms) {
            return $this
                ->addUsingAlias(CmsPeer::PARENT_ID, $cms->getId(), $comparison);
        } elseif ($cms instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CmsPeer::PARENT_ID, $cms->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCmsRelatedByParentId() only accepts arguments of type Cms or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsRelatedByParentId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function joinCmsRelatedByParentId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsRelatedByParentId');

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
            $this->addJoinObject($join, 'CmsRelatedByParentId');
        }

        return $this;
    }

    /**
     * Use the CmsRelatedByParentId relation Cms object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsQuery A secondary query class using the current class as primary query
     */
    public function useCmsRelatedByParentIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCmsRelatedByParentId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsRelatedByParentId', '\Hanzo\Model\CmsQuery');
    }

    /**
     * Filter the query by a related Cms object
     *
     * @param   Cms|PropelObjectCollection $cms  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsRelatedById($cms, $comparison = null)
    {
        if ($cms instanceof Cms) {
            return $this
                ->addUsingAlias(CmsPeer::ID, $cms->getParentId(), $comparison);
        } elseif ($cms instanceof PropelObjectCollection) {
            return $this
                ->useCmsRelatedByIdQuery()
                ->filterByPrimaryKeys($cms->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCmsRelatedById() only accepts arguments of type Cms or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function joinCmsRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsRelatedById');

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
            $this->addJoinObject($join, 'CmsRelatedById');
        }

        return $this;
    }

    /**
     * Use the CmsRelatedById relation Cms object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsQuery A secondary query class using the current class as primary query
     */
    public function useCmsRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCmsRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsRelatedById', '\Hanzo\Model\CmsQuery');
    }

    /**
     * Filter the query by a related CmsRevision object
     *
     * @param   CmsRevision|PropelObjectCollection $cmsRevision  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsRevision($cmsRevision, $comparison = null)
    {
        if ($cmsRevision instanceof CmsRevision) {
            return $this
                ->addUsingAlias(CmsPeer::ID, $cmsRevision->getId(), $comparison);
        } elseif ($cmsRevision instanceof PropelObjectCollection) {
            return $this
                ->useCmsRevisionQuery()
                ->filterByPrimaryKeys($cmsRevision->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCmsRevision() only accepts arguments of type CmsRevision or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsRevision relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function joinCmsRevision($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsRevision');

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
            $this->addJoinObject($join, 'CmsRevision');
        }

        return $this;
    }

    /**
     * Use the CmsRevision relation CmsRevision object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsRevisionQuery A secondary query class using the current class as primary query
     */
    public function useCmsRevisionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCmsRevision($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsRevision', '\Hanzo\Model\CmsRevisionQuery');
    }

    /**
     * Filter the query by a related CmsI18n object
     *
     * @param   CmsI18n|PropelObjectCollection $cmsI18n  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsI18n($cmsI18n, $comparison = null)
    {
        if ($cmsI18n instanceof CmsI18n) {
            return $this
                ->addUsingAlias(CmsPeer::ID, $cmsI18n->getId(), $comparison);
        } elseif ($cmsI18n instanceof PropelObjectCollection) {
            return $this
                ->useCmsI18nQuery()
                ->filterByPrimaryKeys($cmsI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCmsI18n() only accepts arguments of type CmsI18n or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function joinCmsI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsI18n');

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
            $this->addJoinObject($join, 'CmsI18n');
        }

        return $this;
    }

    /**
     * Use the CmsI18n relation CmsI18n object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsI18nQuery A secondary query class using the current class as primary query
     */
    public function useCmsI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCmsI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsI18n', '\Hanzo\Model\CmsI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Cms $cms Object to remove from the list of results
     *
     * @return CmsQuery The current query, for fluid interface
     */
    public function prune($cms = null)
    {
        if ($cms) {
            $this->addUsingAlias(CmsPeer::ID, $cms->getId(), Criteria::NOT_EQUAL);
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

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    CmsQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'da_DK', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CmsI18n';

        return $this
            ->joinCmsI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    CmsQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'da_DK', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CmsI18n');
        $this->with['CmsI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    CmsI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'da_DK', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsI18n', 'Hanzo\Model\CmsI18nQuery');
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     CmsQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CmsPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     CmsQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CmsPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     CmsQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CmsPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     CmsQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CmsPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     CmsQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CmsPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     CmsQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CmsPeer::CREATED_AT);
    }
}

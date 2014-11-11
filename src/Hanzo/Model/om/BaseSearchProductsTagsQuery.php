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
use Hanzo\Model\Products;
use Hanzo\Model\SearchProductsTags;
use Hanzo\Model\SearchProductsTagsPeer;
use Hanzo\Model\SearchProductsTagsQuery;

/**
 * @method SearchProductsTagsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method SearchProductsTagsQuery orderByMasterProductsId($order = Criteria::ASC) Order by the master_products_id column
 * @method SearchProductsTagsQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method SearchProductsTagsQuery orderByToken($order = Criteria::ASC) Order by the token column
 * @method SearchProductsTagsQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 *
 * @method SearchProductsTagsQuery groupById() Group by the id column
 * @method SearchProductsTagsQuery groupByMasterProductsId() Group by the master_products_id column
 * @method SearchProductsTagsQuery groupByProductsId() Group by the products_id column
 * @method SearchProductsTagsQuery groupByToken() Group by the token column
 * @method SearchProductsTagsQuery groupByLocale() Group by the locale column
 *
 * @method SearchProductsTagsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SearchProductsTagsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SearchProductsTagsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method SearchProductsTagsQuery leftJoinProductsRelatedByMasterProductsId($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsRelatedByMasterProductsId relation
 * @method SearchProductsTagsQuery rightJoinProductsRelatedByMasterProductsId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsRelatedByMasterProductsId relation
 * @method SearchProductsTagsQuery innerJoinProductsRelatedByMasterProductsId($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsRelatedByMasterProductsId relation
 *
 * @method SearchProductsTagsQuery leftJoinProductsRelatedByProductsId($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsRelatedByProductsId relation
 * @method SearchProductsTagsQuery rightJoinProductsRelatedByProductsId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsRelatedByProductsId relation
 * @method SearchProductsTagsQuery innerJoinProductsRelatedByProductsId($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsRelatedByProductsId relation
 *
 * @method SearchProductsTags findOne(PropelPDO $con = null) Return the first SearchProductsTags matching the query
 * @method SearchProductsTags findOneOrCreate(PropelPDO $con = null) Return the first SearchProductsTags matching the query, or a new SearchProductsTags object populated from the query conditions when no match is found
 *
 * @method SearchProductsTags findOneByMasterProductsId(int $master_products_id) Return the first SearchProductsTags filtered by the master_products_id column
 * @method SearchProductsTags findOneByProductsId(int $products_id) Return the first SearchProductsTags filtered by the products_id column
 * @method SearchProductsTags findOneByToken(string $token) Return the first SearchProductsTags filtered by the token column
 * @method SearchProductsTags findOneByLocale(string $locale) Return the first SearchProductsTags filtered by the locale column
 *
 * @method array findById(int $id) Return SearchProductsTags objects filtered by the id column
 * @method array findByMasterProductsId(int $master_products_id) Return SearchProductsTags objects filtered by the master_products_id column
 * @method array findByProductsId(int $products_id) Return SearchProductsTags objects filtered by the products_id column
 * @method array findByToken(string $token) Return SearchProductsTags objects filtered by the token column
 * @method array findByLocale(string $locale) Return SearchProductsTags objects filtered by the locale column
 */
abstract class BaseSearchProductsTagsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSearchProductsTagsQuery object.
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
            $modelName = 'Hanzo\\Model\\SearchProductsTags';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new SearchProductsTagsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SearchProductsTagsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SearchProductsTagsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SearchProductsTagsQuery) {
            return $criteria;
        }
        $query = new SearchProductsTagsQuery(null, null, $modelAlias);

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
     * @return   SearchProductsTags|SearchProductsTags[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SearchProductsTagsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SearchProductsTagsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 SearchProductsTags A model object, or null if the key is not found
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
     * @return                 SearchProductsTags A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `master_products_id`, `products_id`, `token`, `locale` FROM `search_products_tags` WHERE `id` = :p0';
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
            $obj = new SearchProductsTags();
            $obj->hydrate($row);
            SearchProductsTagsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return SearchProductsTags|SearchProductsTags[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SearchProductsTags[]|mixed the list of results, formatted by the current formatter
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
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SearchProductsTagsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SearchProductsTagsPeer::ID, $keys, Criteria::IN);
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
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SearchProductsTagsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SearchProductsTagsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SearchProductsTagsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the master_products_id column
     *
     * Example usage:
     * <code>
     * $query->filterByMasterProductsId(1234); // WHERE master_products_id = 1234
     * $query->filterByMasterProductsId(array(12, 34)); // WHERE master_products_id IN (12, 34)
     * $query->filterByMasterProductsId(array('min' => 12)); // WHERE master_products_id >= 12
     * $query->filterByMasterProductsId(array('max' => 12)); // WHERE master_products_id <= 12
     * </code>
     *
     * @see       filterByProductsRelatedByMasterProductsId()
     *
     * @param     mixed $masterProductsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterByMasterProductsId($masterProductsId = null, $comparison = null)
    {
        if (is_array($masterProductsId)) {
            $useMinMax = false;
            if (isset($masterProductsId['min'])) {
                $this->addUsingAlias(SearchProductsTagsPeer::MASTER_PRODUCTS_ID, $masterProductsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($masterProductsId['max'])) {
                $this->addUsingAlias(SearchProductsTagsPeer::MASTER_PRODUCTS_ID, $masterProductsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SearchProductsTagsPeer::MASTER_PRODUCTS_ID, $masterProductsId, $comparison);
    }

    /**
     * Filter the query on the products_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsId(1234); // WHERE products_id = 1234
     * $query->filterByProductsId(array(12, 34)); // WHERE products_id IN (12, 34)
     * $query->filterByProductsId(array('min' => 12)); // WHERE products_id >= 12
     * $query->filterByProductsId(array('max' => 12)); // WHERE products_id <= 12
     * </code>
     *
     * @see       filterByProductsRelatedByProductsId()
     *
     * @param     mixed $productsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(SearchProductsTagsPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(SearchProductsTagsPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SearchProductsTagsPeer::PRODUCTS_ID, $productsId, $comparison);
    }

    /**
     * Filter the query on the token column
     *
     * Example usage:
     * <code>
     * $query->filterByToken('fooValue');   // WHERE token = 'fooValue'
     * $query->filterByToken('%fooValue%'); // WHERE token LIKE '%fooValue%'
     * </code>
     *
     * @param     string $token The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterByToken($token = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($token)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $token)) {
                $token = str_replace('*', '%', $token);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SearchProductsTagsPeer::TOKEN, $token, $comparison);
    }

    /**
     * Filter the query on the locale column
     *
     * Example usage:
     * <code>
     * $query->filterByLocale('fooValue');   // WHERE locale = 'fooValue'
     * $query->filterByLocale('%fooValue%'); // WHERE locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $locale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function filterByLocale($locale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($locale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $locale)) {
                $locale = str_replace('*', '%', $locale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SearchProductsTagsPeer::LOCALE, $locale, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 SearchProductsTagsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsRelatedByMasterProductsId($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(SearchProductsTagsPeer::MASTER_PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SearchProductsTagsPeer::MASTER_PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProductsRelatedByMasterProductsId() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsRelatedByMasterProductsId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function joinProductsRelatedByMasterProductsId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsRelatedByMasterProductsId');

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
            $this->addJoinObject($join, 'ProductsRelatedByMasterProductsId');
        }

        return $this;
    }

    /**
     * Use the ProductsRelatedByMasterProductsId relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsRelatedByMasterProductsIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsRelatedByMasterProductsId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsRelatedByMasterProductsId', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 SearchProductsTagsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsRelatedByProductsId($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(SearchProductsTagsPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SearchProductsTagsPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProductsRelatedByProductsId() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsRelatedByProductsId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function joinProductsRelatedByProductsId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsRelatedByProductsId');

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
            $this->addJoinObject($join, 'ProductsRelatedByProductsId');
        }

        return $this;
    }

    /**
     * Use the ProductsRelatedByProductsId relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsRelatedByProductsIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsRelatedByProductsId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsRelatedByProductsId', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   SearchProductsTags $searchProductsTags Object to remove from the list of results
     *
     * @return SearchProductsTagsQuery The current query, for fluid interface
     */
    public function prune($searchProductsTags = null)
    {
        if ($searchProductsTags) {
            $this->addUsingAlias(SearchProductsTagsPeer::ID, $searchProductsTags->getId(), Criteria::NOT_EQUAL);
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

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
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesI18n;
use Hanzo\Model\CategoriesPeer;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsToCategories;

/**
 * @method CategoriesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CategoriesQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method CategoriesQuery orderByContext($order = Criteria::ASC) Order by the context column
 * @method CategoriesQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 *
 * @method CategoriesQuery groupById() Group by the id column
 * @method CategoriesQuery groupByParentId() Group by the parent_id column
 * @method CategoriesQuery groupByContext() Group by the context column
 * @method CategoriesQuery groupByIsActive() Group by the is_active column
 *
 * @method CategoriesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CategoriesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CategoriesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CategoriesQuery leftJoinCategoriesRelatedByParentId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoriesRelatedByParentId relation
 * @method CategoriesQuery rightJoinCategoriesRelatedByParentId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoriesRelatedByParentId relation
 * @method CategoriesQuery innerJoinCategoriesRelatedByParentId($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoriesRelatedByParentId relation
 *
 * @method CategoriesQuery leftJoinCategoriesRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoriesRelatedById relation
 * @method CategoriesQuery rightJoinCategoriesRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoriesRelatedById relation
 * @method CategoriesQuery innerJoinCategoriesRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoriesRelatedById relation
 *
 * @method CategoriesQuery leftJoinProductsImagesCategoriesSort($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImagesCategoriesSort relation
 * @method CategoriesQuery rightJoinProductsImagesCategoriesSort($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImagesCategoriesSort relation
 * @method CategoriesQuery innerJoinProductsImagesCategoriesSort($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImagesCategoriesSort relation
 *
 * @method CategoriesQuery leftJoinProductsToCategories($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsToCategories relation
 * @method CategoriesQuery rightJoinProductsToCategories($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsToCategories relation
 * @method CategoriesQuery innerJoinProductsToCategories($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsToCategories relation
 *
 * @method CategoriesQuery leftJoinCategoriesI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoriesI18n relation
 * @method CategoriesQuery rightJoinCategoriesI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoriesI18n relation
 * @method CategoriesQuery innerJoinCategoriesI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoriesI18n relation
 *
 * @method Categories findOne(PropelPDO $con = null) Return the first Categories matching the query
 * @method Categories findOneOrCreate(PropelPDO $con = null) Return the first Categories matching the query, or a new Categories object populated from the query conditions when no match is found
 *
 * @method Categories findOneByParentId(int $parent_id) Return the first Categories filtered by the parent_id column
 * @method Categories findOneByContext(string $context) Return the first Categories filtered by the context column
 * @method Categories findOneByIsActive(boolean $is_active) Return the first Categories filtered by the is_active column
 *
 * @method array findById(int $id) Return Categories objects filtered by the id column
 * @method array findByParentId(int $parent_id) Return Categories objects filtered by the parent_id column
 * @method array findByContext(string $context) Return Categories objects filtered by the context column
 * @method array findByIsActive(boolean $is_active) Return Categories objects filtered by the is_active column
 */
abstract class BaseCategoriesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCategoriesQuery object.
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
            $modelName = 'Hanzo\\Model\\Categories';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CategoriesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CategoriesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CategoriesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CategoriesQuery) {
            return $criteria;
        }
        $query = new CategoriesQuery(null, null, $modelAlias);

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
     * @return   Categories|Categories[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CategoriesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CategoriesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Categories A model object, or null if the key is not found
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
     * @return                 Categories A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `parent_id`, `context`, `is_active` FROM `categories` WHERE `id` = :p0';
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
            $obj = new Categories();
            $obj->hydrate($row);
            CategoriesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Categories|Categories[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Categories[]|mixed the list of results, formatted by the current formatter
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
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CategoriesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CategoriesPeer::ID, $keys, Criteria::IN);
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
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CategoriesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CategoriesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoriesPeer::ID, $id, $comparison);
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
     * @see       filterByCategoriesRelatedByParentId()
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(CategoriesPeer::PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(CategoriesPeer::PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoriesPeer::PARENT_ID, $parentId, $comparison);
    }

    /**
     * Filter the query on the context column
     *
     * Example usage:
     * <code>
     * $query->filterByContext('fooValue');   // WHERE context = 'fooValue'
     * $query->filterByContext('%fooValue%'); // WHERE context LIKE '%fooValue%'
     * </code>
     *
     * @param     string $context The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function filterByContext($context = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($context)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $context)) {
                $context = str_replace('*', '%', $context);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CategoriesPeer::CONTEXT, $context, $comparison);
    }

    /**
     * Filter the query on the is_active column
     *
     * Example usage:
     * <code>
     * $query->filterByIsActive(true); // WHERE is_active = true
     * $query->filterByIsActive('yes'); // WHERE is_active = true
     * </code>
     *
     * @param     boolean|string $isActive The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function filterByIsActive($isActive = null, $comparison = null)
    {
        if (is_string($isActive)) {
            $isActive = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CategoriesPeer::IS_ACTIVE, $isActive, $comparison);
    }

    /**
     * Filter the query by a related Categories object
     *
     * @param   Categories|PropelObjectCollection $categories The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategoriesRelatedByParentId($categories, $comparison = null)
    {
        if ($categories instanceof Categories) {
            return $this
                ->addUsingAlias(CategoriesPeer::PARENT_ID, $categories->getId(), $comparison);
        } elseif ($categories instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CategoriesPeer::PARENT_ID, $categories->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategoriesRelatedByParentId() only accepts arguments of type Categories or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoriesRelatedByParentId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function joinCategoriesRelatedByParentId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoriesRelatedByParentId');

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
            $this->addJoinObject($join, 'CategoriesRelatedByParentId');
        }

        return $this;
    }

    /**
     * Use the CategoriesRelatedByParentId relation Categories object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CategoriesQuery A secondary query class using the current class as primary query
     */
    public function useCategoriesRelatedByParentIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCategoriesRelatedByParentId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoriesRelatedByParentId', '\Hanzo\Model\CategoriesQuery');
    }

    /**
     * Filter the query by a related Categories object
     *
     * @param   Categories|PropelObjectCollection $categories  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategoriesRelatedById($categories, $comparison = null)
    {
        if ($categories instanceof Categories) {
            return $this
                ->addUsingAlias(CategoriesPeer::ID, $categories->getParentId(), $comparison);
        } elseif ($categories instanceof PropelObjectCollection) {
            return $this
                ->useCategoriesRelatedByIdQuery()
                ->filterByPrimaryKeys($categories->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoriesRelatedById() only accepts arguments of type Categories or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoriesRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function joinCategoriesRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoriesRelatedById');

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
            $this->addJoinObject($join, 'CategoriesRelatedById');
        }

        return $this;
    }

    /**
     * Use the CategoriesRelatedById relation Categories object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CategoriesQuery A secondary query class using the current class as primary query
     */
    public function useCategoriesRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCategoriesRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoriesRelatedById', '\Hanzo\Model\CategoriesQuery');
    }

    /**
     * Filter the query by a related ProductsImagesCategoriesSort object
     *
     * @param   ProductsImagesCategoriesSort|PropelObjectCollection $productsImagesCategoriesSort  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImagesCategoriesSort($productsImagesCategoriesSort, $comparison = null)
    {
        if ($productsImagesCategoriesSort instanceof ProductsImagesCategoriesSort) {
            return $this
                ->addUsingAlias(CategoriesPeer::ID, $productsImagesCategoriesSort->getCategoriesId(), $comparison);
        } elseif ($productsImagesCategoriesSort instanceof PropelObjectCollection) {
            return $this
                ->useProductsImagesCategoriesSortQuery()
                ->filterByPrimaryKeys($productsImagesCategoriesSort->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsImagesCategoriesSort() only accepts arguments of type ProductsImagesCategoriesSort or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsImagesCategoriesSort relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function joinProductsImagesCategoriesSort($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsImagesCategoriesSort');

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
            $this->addJoinObject($join, 'ProductsImagesCategoriesSort');
        }

        return $this;
    }

    /**
     * Use the ProductsImagesCategoriesSort relation ProductsImagesCategoriesSort object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsImagesCategoriesSortQuery A secondary query class using the current class as primary query
     */
    public function useProductsImagesCategoriesSortQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsImagesCategoriesSort($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsImagesCategoriesSort', '\Hanzo\Model\ProductsImagesCategoriesSortQuery');
    }

    /**
     * Filter the query by a related ProductsToCategories object
     *
     * @param   ProductsToCategories|PropelObjectCollection $productsToCategories  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsToCategories($productsToCategories, $comparison = null)
    {
        if ($productsToCategories instanceof ProductsToCategories) {
            return $this
                ->addUsingAlias(CategoriesPeer::ID, $productsToCategories->getCategoriesId(), $comparison);
        } elseif ($productsToCategories instanceof PropelObjectCollection) {
            return $this
                ->useProductsToCategoriesQuery()
                ->filterByPrimaryKeys($productsToCategories->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsToCategories() only accepts arguments of type ProductsToCategories or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsToCategories relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function joinProductsToCategories($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsToCategories');

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
            $this->addJoinObject($join, 'ProductsToCategories');
        }

        return $this;
    }

    /**
     * Use the ProductsToCategories relation ProductsToCategories object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsToCategoriesQuery A secondary query class using the current class as primary query
     */
    public function useProductsToCategoriesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsToCategories($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsToCategories', '\Hanzo\Model\ProductsToCategoriesQuery');
    }

    /**
     * Filter the query by a related CategoriesI18n object
     *
     * @param   CategoriesI18n|PropelObjectCollection $categoriesI18n  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategoriesI18n($categoriesI18n, $comparison = null)
    {
        if ($categoriesI18n instanceof CategoriesI18n) {
            return $this
                ->addUsingAlias(CategoriesPeer::ID, $categoriesI18n->getId(), $comparison);
        } elseif ($categoriesI18n instanceof PropelObjectCollection) {
            return $this
                ->useCategoriesI18nQuery()
                ->filterByPrimaryKeys($categoriesI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoriesI18n() only accepts arguments of type CategoriesI18n or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoriesI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function joinCategoriesI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoriesI18n');

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
            $this->addJoinObject($join, 'CategoriesI18n');
        }

        return $this;
    }

    /**
     * Use the CategoriesI18n relation CategoriesI18n object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CategoriesI18nQuery A secondary query class using the current class as primary query
     */
    public function useCategoriesI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCategoriesI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoriesI18n', '\Hanzo\Model\CategoriesI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Categories $categories Object to remove from the list of results
     *
     * @return CategoriesQuery The current query, for fluid interface
     */
    public function prune($categories = null)
    {
        if ($categories) {
            $this->addUsingAlias(CategoriesPeer::ID, $categories->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    CategoriesQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'da_DK', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CategoriesI18n';

        return $this
            ->joinCategoriesI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    CategoriesQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'da_DK', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CategoriesI18n');
        $this->with['CategoriesI18n']->setIsWithOneToMany(false);

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
     * @return    CategoriesI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'da_DK', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoriesI18n', 'Hanzo\Model\CategoriesI18nQuery');
    }

}

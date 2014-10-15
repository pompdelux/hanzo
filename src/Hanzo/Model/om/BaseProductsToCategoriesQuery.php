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
use Hanzo\Model\Categories;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsToCategoriesPeer;
use Hanzo\Model\ProductsToCategoriesQuery;

/**
 * @method ProductsToCategoriesQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method ProductsToCategoriesQuery orderByCategoriesId($order = Criteria::ASC) Order by the categories_id column
 *
 * @method ProductsToCategoriesQuery groupByProductsId() Group by the products_id column
 * @method ProductsToCategoriesQuery groupByCategoriesId() Group by the categories_id column
 *
 * @method ProductsToCategoriesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsToCategoriesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsToCategoriesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsToCategoriesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method ProductsToCategoriesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method ProductsToCategoriesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method ProductsToCategoriesQuery leftJoinCategories($relationAlias = null) Adds a LEFT JOIN clause to the query using the Categories relation
 * @method ProductsToCategoriesQuery rightJoinCategories($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Categories relation
 * @method ProductsToCategoriesQuery innerJoinCategories($relationAlias = null) Adds a INNER JOIN clause to the query using the Categories relation
 *
 * @method ProductsToCategories findOne(PropelPDO $con = null) Return the first ProductsToCategories matching the query
 * @method ProductsToCategories findOneOrCreate(PropelPDO $con = null) Return the first ProductsToCategories matching the query, or a new ProductsToCategories object populated from the query conditions when no match is found
 *
 * @method ProductsToCategories findOneByProductsId(int $products_id) Return the first ProductsToCategories filtered by the products_id column
 * @method ProductsToCategories findOneByCategoriesId(int $categories_id) Return the first ProductsToCategories filtered by the categories_id column
 *
 * @method array findByProductsId(int $products_id) Return ProductsToCategories objects filtered by the products_id column
 * @method array findByCategoriesId(int $categories_id) Return ProductsToCategories objects filtered by the categories_id column
 */
abstract class BaseProductsToCategoriesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsToCategoriesQuery object.
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
            $modelName = 'Hanzo\\Model\\ProductsToCategories';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new ProductsToCategoriesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProductsToCategoriesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsToCategoriesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsToCategoriesQuery) {
            return $criteria;
        }
        $query = new ProductsToCategoriesQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$products_id, $categories_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ProductsToCategories|ProductsToCategories[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsToCategoriesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsToCategoriesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ProductsToCategories A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `products_id`, `categories_id` FROM `products_to_categories` WHERE `products_id` = :p0 AND `categories_id` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new ProductsToCategories();
            $obj->hydrate($row);
            ProductsToCategoriesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ProductsToCategories|ProductsToCategories[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ProductsToCategories[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductsToCategoriesPeer::PRODUCTS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductsToCategoriesPeer::CATEGORIES_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductsToCategoriesPeer::PRODUCTS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductsToCategoriesPeer::CATEGORIES_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByProducts()
     *
     * @param     mixed $productsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(ProductsToCategoriesPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(ProductsToCategoriesPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsToCategoriesPeer::PRODUCTS_ID, $productsId, $comparison);
    }

    /**
     * Filter the query on the categories_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoriesId(1234); // WHERE categories_id = 1234
     * $query->filterByCategoriesId(array(12, 34)); // WHERE categories_id IN (12, 34)
     * $query->filterByCategoriesId(array('min' => 12)); // WHERE categories_id >= 12
     * $query->filterByCategoriesId(array('max' => 12)); // WHERE categories_id <= 12
     * </code>
     *
     * @see       filterByCategories()
     *
     * @param     mixed $categoriesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function filterByCategoriesId($categoriesId = null, $comparison = null)
    {
        if (is_array($categoriesId)) {
            $useMinMax = false;
            if (isset($categoriesId['min'])) {
                $this->addUsingAlias(ProductsToCategoriesPeer::CATEGORIES_ID, $categoriesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoriesId['max'])) {
                $this->addUsingAlias(ProductsToCategoriesPeer::CATEGORIES_ID, $categoriesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsToCategoriesPeer::CATEGORIES_ID, $categoriesId, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsToCategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsToCategoriesPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsToCategoriesPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProducts() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Products relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function joinProducts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Products');

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
            $this->addJoinObject($join, 'Products');
        }

        return $this;
    }

    /**
     * Use the Products relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProducts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Products', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Filter the query by a related Categories object
     *
     * @param   Categories|PropelObjectCollection $categories The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsToCategoriesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategories($categories, $comparison = null)
    {
        if ($categories instanceof Categories) {
            return $this
                ->addUsingAlias(ProductsToCategoriesPeer::CATEGORIES_ID, $categories->getId(), $comparison);
        } elseif ($categories instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsToCategoriesPeer::CATEGORIES_ID, $categories->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategories() only accepts arguments of type Categories or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Categories relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function joinCategories($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Categories');

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
            $this->addJoinObject($join, 'Categories');
        }

        return $this;
    }

    /**
     * Use the Categories relation Categories object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CategoriesQuery A secondary query class using the current class as primary query
     */
    public function useCategoriesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategories($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Categories', '\Hanzo\Model\CategoriesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ProductsToCategories $productsToCategories Object to remove from the list of results
     *
     * @return ProductsToCategoriesQuery The current query, for fluid interface
     */
    public function prune($productsToCategories = null)
    {
        if ($productsToCategories) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductsToCategoriesPeer::PRODUCTS_ID), $productsToCategories->getProductsId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductsToCategoriesPeer::CATEGORIES_ID), $productsToCategories->getCategoriesId(), Criteria::NOT_EQUAL);
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

}

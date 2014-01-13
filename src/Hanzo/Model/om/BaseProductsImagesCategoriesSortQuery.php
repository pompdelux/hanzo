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
use Hanzo\Model\Products;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortPeer;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;

/**
 * @method ProductsImagesCategoriesSortQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method ProductsImagesCategoriesSortQuery orderByCategoriesId($order = Criteria::ASC) Order by the categories_id column
 * @method ProductsImagesCategoriesSortQuery orderByProductsImagesId($order = Criteria::ASC) Order by the products_images_id column
 * @method ProductsImagesCategoriesSortQuery orderBySort($order = Criteria::ASC) Order by the sort column
 *
 * @method ProductsImagesCategoriesSortQuery groupByProductsId() Group by the products_id column
 * @method ProductsImagesCategoriesSortQuery groupByCategoriesId() Group by the categories_id column
 * @method ProductsImagesCategoriesSortQuery groupByProductsImagesId() Group by the products_images_id column
 * @method ProductsImagesCategoriesSortQuery groupBySort() Group by the sort column
 *
 * @method ProductsImagesCategoriesSortQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsImagesCategoriesSortQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsImagesCategoriesSortQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsImagesCategoriesSortQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method ProductsImagesCategoriesSortQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method ProductsImagesCategoriesSortQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method ProductsImagesCategoriesSortQuery leftJoinProductsImages($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImages relation
 * @method ProductsImagesCategoriesSortQuery rightJoinProductsImages($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImages relation
 * @method ProductsImagesCategoriesSortQuery innerJoinProductsImages($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImages relation
 *
 * @method ProductsImagesCategoriesSortQuery leftJoinCategories($relationAlias = null) Adds a LEFT JOIN clause to the query using the Categories relation
 * @method ProductsImagesCategoriesSortQuery rightJoinCategories($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Categories relation
 * @method ProductsImagesCategoriesSortQuery innerJoinCategories($relationAlias = null) Adds a INNER JOIN clause to the query using the Categories relation
 *
 * @method ProductsImagesCategoriesSort findOne(PropelPDO $con = null) Return the first ProductsImagesCategoriesSort matching the query
 * @method ProductsImagesCategoriesSort findOneOrCreate(PropelPDO $con = null) Return the first ProductsImagesCategoriesSort matching the query, or a new ProductsImagesCategoriesSort object populated from the query conditions when no match is found
 *
 * @method ProductsImagesCategoriesSort findOneByProductsId(int $products_id) Return the first ProductsImagesCategoriesSort filtered by the products_id column
 * @method ProductsImagesCategoriesSort findOneByCategoriesId(int $categories_id) Return the first ProductsImagesCategoriesSort filtered by the categories_id column
 * @method ProductsImagesCategoriesSort findOneByProductsImagesId(int $products_images_id) Return the first ProductsImagesCategoriesSort filtered by the products_images_id column
 * @method ProductsImagesCategoriesSort findOneBySort(int $sort) Return the first ProductsImagesCategoriesSort filtered by the sort column
 *
 * @method array findByProductsId(int $products_id) Return ProductsImagesCategoriesSort objects filtered by the products_id column
 * @method array findByCategoriesId(int $categories_id) Return ProductsImagesCategoriesSort objects filtered by the categories_id column
 * @method array findByProductsImagesId(int $products_images_id) Return ProductsImagesCategoriesSort objects filtered by the products_images_id column
 * @method array findBySort(int $sort) Return ProductsImagesCategoriesSort objects filtered by the sort column
 */
abstract class BaseProductsImagesCategoriesSortQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsImagesCategoriesSortQuery object.
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
            $modelName = 'Hanzo\\Model\\ProductsImagesCategoriesSort';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ProductsImagesCategoriesSortQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProductsImagesCategoriesSortQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsImagesCategoriesSortQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsImagesCategoriesSortQuery) {
            return $criteria;
        }
        $query = new ProductsImagesCategoriesSortQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$products_id, $categories_id, $products_images_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ProductsImagesCategoriesSort|ProductsImagesCategoriesSort[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsImagesCategoriesSortPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsImagesCategoriesSortPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ProductsImagesCategoriesSort A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `products_id`, `categories_id`, `products_images_id`, `sort` FROM `products_images_categories_sort` WHERE `products_id` = :p0 AND `categories_id` = :p1 AND `products_images_id` = :p2';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->bindValue(':p2', $key[2], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new ProductsImagesCategoriesSort();
            $obj->hydrate($row);
            ProductsImagesCategoriesSortPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
     * @return ProductsImagesCategoriesSort|ProductsImagesCategoriesSort[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ProductsImagesCategoriesSort[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $key[2], Criteria::EQUAL);
            $cton0->addAnd($cton2);
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
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $productsId, $comparison);
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
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function filterByCategoriesId($categoriesId = null, $comparison = null)
    {
        if (is_array($categoriesId)) {
            $useMinMax = false;
            if (isset($categoriesId['min'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $categoriesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoriesId['max'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $categoriesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $categoriesId, $comparison);
    }

    /**
     * Filter the query on the products_images_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsImagesId(1234); // WHERE products_images_id = 1234
     * $query->filterByProductsImagesId(array(12, 34)); // WHERE products_images_id IN (12, 34)
     * $query->filterByProductsImagesId(array('min' => 12)); // WHERE products_images_id >= 12
     * $query->filterByProductsImagesId(array('max' => 12)); // WHERE products_images_id <= 12
     * </code>
     *
     * @see       filterByProductsImages()
     *
     * @param     mixed $productsImagesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function filterByProductsImagesId($productsImagesId = null, $comparison = null)
    {
        if (is_array($productsImagesId)) {
            $useMinMax = false;
            if (isset($productsImagesId['min'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $productsImagesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsImagesId['max'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $productsImagesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $productsImagesId, $comparison);
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
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(ProductsImagesCategoriesSortPeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesCategoriesSortPeer::SORT, $sort, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsImagesCategoriesSortQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
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
     * Filter the query by a related ProductsImages object
     *
     * @param   ProductsImages|PropelObjectCollection $productsImages The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsImagesCategoriesSortQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImages($productsImages, $comparison = null)
    {
        if ($productsImages instanceof ProductsImages) {
            return $this
                ->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $productsImages->getId(), $comparison);
        } elseif ($productsImages instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID, $productsImages->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProductsImages() only accepts arguments of type ProductsImages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsImages relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function joinProductsImages($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsImages');

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
            $this->addJoinObject($join, 'ProductsImages');
        }

        return $this;
    }

    /**
     * Use the ProductsImages relation ProductsImages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsImagesQuery A secondary query class using the current class as primary query
     */
    public function useProductsImagesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsImages($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsImages', '\Hanzo\Model\ProductsImagesQuery');
    }

    /**
     * Filter the query by a related Categories object
     *
     * @param   Categories|PropelObjectCollection $categories The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsImagesCategoriesSortQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategories($categories, $comparison = null)
    {
        if ($categories instanceof Categories) {
            return $this
                ->addUsingAlias(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $categories->getId(), $comparison);
        } elseif ($categories instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesCategoriesSortPeer::CATEGORIES_ID, $categories->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
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
     * @param   ProductsImagesCategoriesSort $productsImagesCategoriesSort Object to remove from the list of results
     *
     * @return ProductsImagesCategoriesSortQuery The current query, for fluid interface
     */
    public function prune($productsImagesCategoriesSort = null)
    {
        if ($productsImagesCategoriesSort) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductsImagesCategoriesSortPeer::PRODUCTS_ID), $productsImagesCategoriesSort->getProductsId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductsImagesCategoriesSortPeer::CATEGORIES_ID), $productsImagesCategoriesSort->getCategoriesId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(ProductsImagesCategoriesSortPeer::PRODUCTS_IMAGES_ID), $productsImagesCategoriesSort->getProductsImagesId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}

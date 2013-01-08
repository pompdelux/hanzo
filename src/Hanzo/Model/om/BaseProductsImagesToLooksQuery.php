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
use Hanzo\Model\Looks;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesToLooks;
use Hanzo\Model\ProductsImagesToLooksPeer;
use Hanzo\Model\ProductsImagesToLooksQuery;

/**
 * Base class that represents a query for the 'products_images_to_looks' table.
 *
 *
 *
 * @method ProductsImagesToLooksQuery orderByProductsImagesId($order = Criteria::ASC) Order by the products_images_id column
 * @method ProductsImagesToLooksQuery orderByLooksId($order = Criteria::ASC) Order by the looks_id column
 * @method ProductsImagesToLooksQuery orderBySort($order = Criteria::ASC) Order by the sort column
 *
 * @method ProductsImagesToLooksQuery groupByProductsImagesId() Group by the products_images_id column
 * @method ProductsImagesToLooksQuery groupByLooksId() Group by the looks_id column
 * @method ProductsImagesToLooksQuery groupBySort() Group by the sort column
 *
 * @method ProductsImagesToLooksQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsImagesToLooksQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsImagesToLooksQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsImagesToLooksQuery leftJoinProductsImages($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImages relation
 * @method ProductsImagesToLooksQuery rightJoinProductsImages($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImages relation
 * @method ProductsImagesToLooksQuery innerJoinProductsImages($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImages relation
 *
 * @method ProductsImagesToLooksQuery leftJoinLooks($relationAlias = null) Adds a LEFT JOIN clause to the query using the Looks relation
 * @method ProductsImagesToLooksQuery rightJoinLooks($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Looks relation
 * @method ProductsImagesToLooksQuery innerJoinLooks($relationAlias = null) Adds a INNER JOIN clause to the query using the Looks relation
 *
 * @method ProductsImagesToLooks findOne(PropelPDO $con = null) Return the first ProductsImagesToLooks matching the query
 * @method ProductsImagesToLooks findOneOrCreate(PropelPDO $con = null) Return the first ProductsImagesToLooks matching the query, or a new ProductsImagesToLooks object populated from the query conditions when no match is found
 *
 * @method ProductsImagesToLooks findOneByProductsImagesId(int $products_images_id) Return the first ProductsImagesToLooks filtered by the products_images_id column
 * @method ProductsImagesToLooks findOneByLooksId(int $looks_id) Return the first ProductsImagesToLooks filtered by the looks_id column
 * @method ProductsImagesToLooks findOneBySort(int $sort) Return the first ProductsImagesToLooks filtered by the sort column
 *
 * @method array findByProductsImagesId(int $products_images_id) Return ProductsImagesToLooks objects filtered by the products_images_id column
 * @method array findByLooksId(int $looks_id) Return ProductsImagesToLooks objects filtered by the looks_id column
 * @method array findBySort(int $sort) Return ProductsImagesToLooks objects filtered by the sort column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseProductsImagesToLooksQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsImagesToLooksQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\ProductsImagesToLooks', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ProductsImagesToLooksQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     ProductsImagesToLooksQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsImagesToLooksQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsImagesToLooksQuery) {
            return $criteria;
        }
        $query = new ProductsImagesToLooksQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
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
                         A Primary key composition: [$products_images_id, $looks_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ProductsImagesToLooks|ProductsImagesToLooks[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsImagesToLooksPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsImagesToLooksPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   ProductsImagesToLooks A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `products_images_id`, `looks_id`, `sort` FROM `products_images_to_looks` WHERE `products_images_id` = :p0 AND `looks_id` = :p1';
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
            $obj = new ProductsImagesToLooks();
            $obj->hydrate($row);
            ProductsImagesToLooksPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ProductsImagesToLooks|ProductsImagesToLooks[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ProductsImagesToLooks[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductsImagesToLooksPeer::PRODUCTS_IMAGES_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductsImagesToLooksPeer::LOOKS_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductsImagesToLooksPeer::PRODUCTS_IMAGES_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductsImagesToLooksPeer::LOOKS_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the products_images_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsImagesId(1234); // WHERE products_images_id = 1234
     * $query->filterByProductsImagesId(array(12, 34)); // WHERE products_images_id IN (12, 34)
     * $query->filterByProductsImagesId(array('min' => 12)); // WHERE products_images_id > 12
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
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function filterByProductsImagesId($productsImagesId = null, $comparison = null)
    {
        if (is_array($productsImagesId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(ProductsImagesToLooksPeer::PRODUCTS_IMAGES_ID, $productsImagesId, $comparison);
    }

    /**
     * Filter the query on the looks_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLooksId(1234); // WHERE looks_id = 1234
     * $query->filterByLooksId(array(12, 34)); // WHERE looks_id IN (12, 34)
     * $query->filterByLooksId(array('min' => 12)); // WHERE looks_id > 12
     * </code>
     *
     * @see       filterByLooks()
     *
     * @param     mixed $looksId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function filterByLooksId($looksId = null, $comparison = null)
    {
        if (is_array($looksId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(ProductsImagesToLooksPeer::LOOKS_ID, $looksId, $comparison);
    }

    /**
     * Filter the query on the sort column
     *
     * Example usage:
     * <code>
     * $query->filterBySort(1234); // WHERE sort = 1234
     * $query->filterBySort(array(12, 34)); // WHERE sort IN (12, 34)
     * $query->filterBySort(array('min' => 12)); // WHERE sort > 12
     * </code>
     *
     * @param     mixed $sort The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(ProductsImagesToLooksPeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(ProductsImagesToLooksPeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesToLooksPeer::SORT, $sort, $comparison);
    }

    /**
     * Filter the query by a related ProductsImages object
     *
     * @param   ProductsImages|PropelObjectCollection $productsImages The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ProductsImagesToLooksQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImages($productsImages, $comparison = null)
    {
        if ($productsImages instanceof ProductsImages) {
            return $this
                ->addUsingAlias(ProductsImagesToLooksPeer::PRODUCTS_IMAGES_ID, $productsImages->getId(), $comparison);
        } elseif ($productsImages instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesToLooksPeer::PRODUCTS_IMAGES_ID, $productsImages->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
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
     * Filter the query by a related Looks object
     *
     * @param   Looks|PropelObjectCollection $looks The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   ProductsImagesToLooksQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByLooks($looks, $comparison = null)
    {
        if ($looks instanceof Looks) {
            return $this
                ->addUsingAlias(ProductsImagesToLooksPeer::LOOKS_ID, $looks->getId(), $comparison);
        } elseif ($looks instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesToLooksPeer::LOOKS_ID, $looks->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByLooks() only accepts arguments of type Looks or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Looks relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function joinLooks($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Looks');

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
            $this->addJoinObject($join, 'Looks');
        }

        return $this;
    }

    /**
     * Use the Looks relation Looks object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\LooksQuery A secondary query class using the current class as primary query
     */
    public function useLooksQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLooks($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Looks', '\Hanzo\Model\LooksQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ProductsImagesToLooks $productsImagesToLooks Object to remove from the list of results
     *
     * @return ProductsImagesToLooksQuery The current query, for fluid interface
     */
    public function prune($productsImagesToLooks = null)
    {
        if ($productsImagesToLooks) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductsImagesToLooksPeer::PRODUCTS_IMAGES_ID), $productsImagesToLooks->getProductsImagesId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductsImagesToLooksPeer::LOOKS_ID), $productsImagesToLooks->getLooksId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}

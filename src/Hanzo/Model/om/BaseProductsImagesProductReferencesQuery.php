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
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesPeer;
use Hanzo\Model\ProductsImagesProductReferencesQuery;

/**
 * @method ProductsImagesProductReferencesQuery orderByProductsImagesId($order = Criteria::ASC) Order by the products_images_id column
 * @method ProductsImagesProductReferencesQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method ProductsImagesProductReferencesQuery orderByColor($order = Criteria::ASC) Order by the color column
 *
 * @method ProductsImagesProductReferencesQuery groupByProductsImagesId() Group by the products_images_id column
 * @method ProductsImagesProductReferencesQuery groupByProductsId() Group by the products_id column
 * @method ProductsImagesProductReferencesQuery groupByColor() Group by the color column
 *
 * @method ProductsImagesProductReferencesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsImagesProductReferencesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsImagesProductReferencesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsImagesProductReferencesQuery leftJoinProductsImages($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImages relation
 * @method ProductsImagesProductReferencesQuery rightJoinProductsImages($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImages relation
 * @method ProductsImagesProductReferencesQuery innerJoinProductsImages($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImages relation
 *
 * @method ProductsImagesProductReferencesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method ProductsImagesProductReferencesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method ProductsImagesProductReferencesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method ProductsImagesProductReferences findOne(PropelPDO $con = null) Return the first ProductsImagesProductReferences matching the query
 * @method ProductsImagesProductReferences findOneOrCreate(PropelPDO $con = null) Return the first ProductsImagesProductReferences matching the query, or a new ProductsImagesProductReferences object populated from the query conditions when no match is found
 *
 * @method ProductsImagesProductReferences findOneByProductsImagesId(int $products_images_id) Return the first ProductsImagesProductReferences filtered by the products_images_id column
 * @method ProductsImagesProductReferences findOneByProductsId(int $products_id) Return the first ProductsImagesProductReferences filtered by the products_id column
 * @method ProductsImagesProductReferences findOneByColor(string $color) Return the first ProductsImagesProductReferences filtered by the color column
 *
 * @method array findByProductsImagesId(int $products_images_id) Return ProductsImagesProductReferences objects filtered by the products_images_id column
 * @method array findByProductsId(int $products_id) Return ProductsImagesProductReferences objects filtered by the products_id column
 * @method array findByColor(string $color) Return ProductsImagesProductReferences objects filtered by the color column
 */
abstract class BaseProductsImagesProductReferencesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsImagesProductReferencesQuery object.
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
            $modelName = 'Hanzo\\Model\\ProductsImagesProductReferences';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new ProductsImagesProductReferencesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProductsImagesProductReferencesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsImagesProductReferencesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsImagesProductReferencesQuery) {
            return $criteria;
        }
        $query = new ProductsImagesProductReferencesQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$products_images_id, $products_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ProductsImagesProductReferences|ProductsImagesProductReferences[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsImagesProductReferencesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsImagesProductReferencesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ProductsImagesProductReferences A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `products_images_id`, `products_id`, `color` FROM `products_images_product_references` WHERE `products_images_id` = :p0 AND `products_id` = :p1';
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
            $obj = new ProductsImagesProductReferences();
            $obj->hydrate($row);
            ProductsImagesProductReferencesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ProductsImagesProductReferences|ProductsImagesProductReferences[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ProductsImagesProductReferences[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $key[1], Criteria::EQUAL);
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
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
     */
    public function filterByProductsImagesId($productsImagesId = null, $comparison = null)
    {
        if (is_array($productsImagesId)) {
            $useMinMax = false;
            if (isset($productsImagesId['min'])) {
                $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $productsImagesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsImagesId['max'])) {
                $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $productsImagesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $productsImagesId, $comparison);
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
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $productsId, $comparison);
    }

    /**
     * Filter the query on the color column
     *
     * Example usage:
     * <code>
     * $query->filterByColor('fooValue');   // WHERE color = 'fooValue'
     * $query->filterByColor('%fooValue%'); // WHERE color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $color The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
     */
    public function filterByColor($color = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($color)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $color)) {
                $color = str_replace('*', '%', $color);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductsImagesProductReferencesPeer::COLOR, $color, $comparison);
    }

    /**
     * Filter the query by a related ProductsImages object
     *
     * @param   ProductsImages|PropelObjectCollection $productsImages The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsImagesProductReferencesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImages($productsImages, $comparison = null)
    {
        if ($productsImages instanceof ProductsImages) {
            return $this
                ->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $productsImages->getId(), $comparison);
        } elseif ($productsImages instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID, $productsImages->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
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
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsImagesProductReferencesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsImagesProductReferencesPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ProductsImagesProductReferences $productsImagesProductReferences Object to remove from the list of results
     *
     * @return ProductsImagesProductReferencesQuery The current query, for fluid interface
     */
    public function prune($productsImagesProductReferences = null)
    {
        if ($productsImagesProductReferences) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductsImagesProductReferencesPeer::PRODUCTS_IMAGES_ID), $productsImagesProductReferences->getProductsImagesId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductsImagesProductReferencesPeer::PRODUCTS_ID), $productsImagesProductReferences->getProductsId(), Criteria::NOT_EQUAL);
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

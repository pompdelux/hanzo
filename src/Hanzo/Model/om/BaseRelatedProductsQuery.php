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
use Hanzo\Model\Products;
use Hanzo\Model\RelatedProducts;
use Hanzo\Model\RelatedProductsPeer;
use Hanzo\Model\RelatedProductsQuery;

/**
 * @method RelatedProductsQuery orderByMaster($order = Criteria::ASC) Order by the master column
 * @method RelatedProductsQuery orderBySku($order = Criteria::ASC) Order by the sku column
 *
 * @method RelatedProductsQuery groupByMaster() Group by the master column
 * @method RelatedProductsQuery groupBySku() Group by the sku column
 *
 * @method RelatedProductsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RelatedProductsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RelatedProductsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RelatedProductsQuery leftJoinProductsRelatedByMaster($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsRelatedByMaster relation
 * @method RelatedProductsQuery rightJoinProductsRelatedByMaster($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsRelatedByMaster relation
 * @method RelatedProductsQuery innerJoinProductsRelatedByMaster($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsRelatedByMaster relation
 *
 * @method RelatedProductsQuery leftJoinProductsRelatedBySku($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsRelatedBySku relation
 * @method RelatedProductsQuery rightJoinProductsRelatedBySku($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsRelatedBySku relation
 * @method RelatedProductsQuery innerJoinProductsRelatedBySku($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsRelatedBySku relation
 *
 * @method RelatedProducts findOne(PropelPDO $con = null) Return the first RelatedProducts matching the query
 * @method RelatedProducts findOneOrCreate(PropelPDO $con = null) Return the first RelatedProducts matching the query, or a new RelatedProducts object populated from the query conditions when no match is found
 *
 * @method RelatedProducts findOneByMaster(string $master) Return the first RelatedProducts filtered by the master column
 * @method RelatedProducts findOneBySku(string $sku) Return the first RelatedProducts filtered by the sku column
 *
 * @method array findByMaster(string $master) Return RelatedProducts objects filtered by the master column
 * @method array findBySku(string $sku) Return RelatedProducts objects filtered by the sku column
 */
abstract class BaseRelatedProductsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRelatedProductsQuery object.
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
            $modelName = 'Hanzo\\Model\\RelatedProducts';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RelatedProductsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RelatedProductsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RelatedProductsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RelatedProductsQuery) {
            return $criteria;
        }
        $query = new RelatedProductsQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$master, $sku]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   RelatedProducts|RelatedProducts[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RelatedProductsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RelatedProductsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 RelatedProducts A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `master`, `sku` FROM `related_products` WHERE `master` = :p0 AND `sku` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new RelatedProducts();
            $obj->hydrate($row);
            RelatedProductsPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return RelatedProducts|RelatedProducts[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|RelatedProducts[]|mixed the list of results, formatted by the current formatter
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
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(RelatedProductsPeer::MASTER, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(RelatedProductsPeer::SKU, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(RelatedProductsPeer::MASTER, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(RelatedProductsPeer::SKU, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the master column
     *
     * Example usage:
     * <code>
     * $query->filterByMaster('fooValue');   // WHERE master = 'fooValue'
     * $query->filterByMaster('%fooValue%'); // WHERE master LIKE '%fooValue%'
     * </code>
     *
     * @param     string $master The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function filterByMaster($master = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($master)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $master)) {
                $master = str_replace('*', '%', $master);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RelatedProductsPeer::MASTER, $master, $comparison);
    }

    /**
     * Filter the query on the sku column
     *
     * Example usage:
     * <code>
     * $query->filterBySku('fooValue');   // WHERE sku = 'fooValue'
     * $query->filterBySku('%fooValue%'); // WHERE sku LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sku The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function filterBySku($sku = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sku)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sku)) {
                $sku = str_replace('*', '%', $sku);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RelatedProductsPeer::SKU, $sku, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RelatedProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsRelatedByMaster($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(RelatedProductsPeer::MASTER, $products->getSku(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RelatedProductsPeer::MASTER, $products->toKeyValue('PrimaryKey', 'Sku'), $comparison);
        } else {
            throw new PropelException('filterByProductsRelatedByMaster() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsRelatedByMaster relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function joinProductsRelatedByMaster($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsRelatedByMaster');

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
            $this->addJoinObject($join, 'ProductsRelatedByMaster');
        }

        return $this;
    }

    /**
     * Use the ProductsRelatedByMaster relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsRelatedByMasterQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsRelatedByMaster($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsRelatedByMaster', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RelatedProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsRelatedBySku($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(RelatedProductsPeer::SKU, $products->getSku(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RelatedProductsPeer::SKU, $products->toKeyValue('PrimaryKey', 'Sku'), $comparison);
        } else {
            throw new PropelException('filterByProductsRelatedBySku() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsRelatedBySku relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function joinProductsRelatedBySku($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsRelatedBySku');

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
            $this->addJoinObject($join, 'ProductsRelatedBySku');
        }

        return $this;
    }

    /**
     * Use the ProductsRelatedBySku relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsRelatedBySkuQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsRelatedBySku($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsRelatedBySku', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   RelatedProducts $relatedProducts Object to remove from the list of results
     *
     * @return RelatedProductsQuery The current query, for fluid interface
     */
    public function prune($relatedProducts = null)
    {
        if ($relatedProducts) {
            $this->addCond('pruneCond0', $this->getAliasedColName(RelatedProductsPeer::MASTER), $relatedProducts->getMaster(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(RelatedProductsPeer::SKU), $relatedProducts->getSku(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}

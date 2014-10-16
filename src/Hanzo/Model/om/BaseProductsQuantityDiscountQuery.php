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
use Hanzo\Model\Domains;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsQuantityDiscount;
use Hanzo\Model\ProductsQuantityDiscountPeer;
use Hanzo\Model\ProductsQuantityDiscountQuery;

/**
 * @method ProductsQuantityDiscountQuery orderByProductsMaster($order = Criteria::ASC) Order by the products_master column
 * @method ProductsQuantityDiscountQuery orderByDomainsId($order = Criteria::ASC) Order by the domains_id column
 * @method ProductsQuantityDiscountQuery orderBySpan($order = Criteria::ASC) Order by the span column
 * @method ProductsQuantityDiscountQuery orderByDiscount($order = Criteria::ASC) Order by the discount column
 * @method ProductsQuantityDiscountQuery orderByValidFrom($order = Criteria::ASC) Order by the valid_from column
 * @method ProductsQuantityDiscountQuery orderByValidTo($order = Criteria::ASC) Order by the valid_to column
 *
 * @method ProductsQuantityDiscountQuery groupByProductsMaster() Group by the products_master column
 * @method ProductsQuantityDiscountQuery groupByDomainsId() Group by the domains_id column
 * @method ProductsQuantityDiscountQuery groupBySpan() Group by the span column
 * @method ProductsQuantityDiscountQuery groupByDiscount() Group by the discount column
 * @method ProductsQuantityDiscountQuery groupByValidFrom() Group by the valid_from column
 * @method ProductsQuantityDiscountQuery groupByValidTo() Group by the valid_to column
 *
 * @method ProductsQuantityDiscountQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsQuantityDiscountQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsQuantityDiscountQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsQuantityDiscountQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method ProductsQuantityDiscountQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method ProductsQuantityDiscountQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method ProductsQuantityDiscountQuery leftJoinDomains($relationAlias = null) Adds a LEFT JOIN clause to the query using the Domains relation
 * @method ProductsQuantityDiscountQuery rightJoinDomains($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Domains relation
 * @method ProductsQuantityDiscountQuery innerJoinDomains($relationAlias = null) Adds a INNER JOIN clause to the query using the Domains relation
 *
 * @method ProductsQuantityDiscount findOne(PropelPDO $con = null) Return the first ProductsQuantityDiscount matching the query
 * @method ProductsQuantityDiscount findOneOrCreate(PropelPDO $con = null) Return the first ProductsQuantityDiscount matching the query, or a new ProductsQuantityDiscount object populated from the query conditions when no match is found
 *
 * @method ProductsQuantityDiscount findOneByProductsMaster(string $products_master) Return the first ProductsQuantityDiscount filtered by the products_master column
 * @method ProductsQuantityDiscount findOneByDomainsId(int $domains_id) Return the first ProductsQuantityDiscount filtered by the domains_id column
 * @method ProductsQuantityDiscount findOneBySpan(int $span) Return the first ProductsQuantityDiscount filtered by the span column
 * @method ProductsQuantityDiscount findOneByDiscount(string $discount) Return the first ProductsQuantityDiscount filtered by the discount column
 * @method ProductsQuantityDiscount findOneByValidFrom(string $valid_from) Return the first ProductsQuantityDiscount filtered by the valid_from column
 * @method ProductsQuantityDiscount findOneByValidTo(string $valid_to) Return the first ProductsQuantityDiscount filtered by the valid_to column
 *
 * @method array findByProductsMaster(string $products_master) Return ProductsQuantityDiscount objects filtered by the products_master column
 * @method array findByDomainsId(int $domains_id) Return ProductsQuantityDiscount objects filtered by the domains_id column
 * @method array findBySpan(int $span) Return ProductsQuantityDiscount objects filtered by the span column
 * @method array findByDiscount(string $discount) Return ProductsQuantityDiscount objects filtered by the discount column
 * @method array findByValidFrom(string $valid_from) Return ProductsQuantityDiscount objects filtered by the valid_from column
 * @method array findByValidTo(string $valid_to) Return ProductsQuantityDiscount objects filtered by the valid_to column
 */
abstract class BaseProductsQuantityDiscountQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsQuantityDiscountQuery object.
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
            $modelName = 'Hanzo\\Model\\ProductsQuantityDiscount';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new ProductsQuantityDiscountQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProductsQuantityDiscountQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsQuantityDiscountQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsQuantityDiscountQuery) {
            return $criteria;
        }
        $query = new ProductsQuantityDiscountQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$products_master, $domains_id, $span]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ProductsQuantityDiscount|ProductsQuantityDiscount[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsQuantityDiscountPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsQuantityDiscountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ProductsQuantityDiscount A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `products_master`, `domains_id`, `span`, `discount`, `valid_from`, `valid_to` FROM `products_quantity_discount` WHERE `products_master` = :p0 AND `domains_id` = :p1 AND `span` = :p2';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->bindValue(':p2', $key[2], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new ProductsQuantityDiscount();
            $obj->hydrate($row);
            ProductsQuantityDiscountPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
     * @return ProductsQuantityDiscount|ProductsQuantityDiscount[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ProductsQuantityDiscount[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductsQuantityDiscountPeer::PRODUCTS_MASTER, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductsQuantityDiscountPeer::DOMAINS_ID, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(ProductsQuantityDiscountPeer::SPAN, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductsQuantityDiscountPeer::PRODUCTS_MASTER, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductsQuantityDiscountPeer::DOMAINS_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(ProductsQuantityDiscountPeer::SPAN, $key[2], Criteria::EQUAL);
            $cton0->addAnd($cton2);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the products_master column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsMaster('fooValue');   // WHERE products_master = 'fooValue'
     * $query->filterByProductsMaster('%fooValue%'); // WHERE products_master LIKE '%fooValue%'
     * </code>
     *
     * @param     string $productsMaster The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByProductsMaster($productsMaster = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($productsMaster)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $productsMaster)) {
                $productsMaster = str_replace('*', '%', $productsMaster);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductsQuantityDiscountPeer::PRODUCTS_MASTER, $productsMaster, $comparison);
    }

    /**
     * Filter the query on the domains_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDomainsId(1234); // WHERE domains_id = 1234
     * $query->filterByDomainsId(array(12, 34)); // WHERE domains_id IN (12, 34)
     * $query->filterByDomainsId(array('min' => 12)); // WHERE domains_id >= 12
     * $query->filterByDomainsId(array('max' => 12)); // WHERE domains_id <= 12
     * </code>
     *
     * @see       filterByDomains()
     *
     * @param     mixed $domainsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByDomainsId($domainsId = null, $comparison = null)
    {
        if (is_array($domainsId)) {
            $useMinMax = false;
            if (isset($domainsId['min'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::DOMAINS_ID, $domainsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($domainsId['max'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::DOMAINS_ID, $domainsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsQuantityDiscountPeer::DOMAINS_ID, $domainsId, $comparison);
    }

    /**
     * Filter the query on the span column
     *
     * Example usage:
     * <code>
     * $query->filterBySpan(1234); // WHERE span = 1234
     * $query->filterBySpan(array(12, 34)); // WHERE span IN (12, 34)
     * $query->filterBySpan(array('min' => 12)); // WHERE span >= 12
     * $query->filterBySpan(array('max' => 12)); // WHERE span <= 12
     * </code>
     *
     * @param     mixed $span The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterBySpan($span = null, $comparison = null)
    {
        if (is_array($span)) {
            $useMinMax = false;
            if (isset($span['min'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::SPAN, $span['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($span['max'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::SPAN, $span['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsQuantityDiscountPeer::SPAN, $span, $comparison);
    }

    /**
     * Filter the query on the discount column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscount(1234); // WHERE discount = 1234
     * $query->filterByDiscount(array(12, 34)); // WHERE discount IN (12, 34)
     * $query->filterByDiscount(array('min' => 12)); // WHERE discount >= 12
     * $query->filterByDiscount(array('max' => 12)); // WHERE discount <= 12
     * </code>
     *
     * @param     mixed $discount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByDiscount($discount = null, $comparison = null)
    {
        if (is_array($discount)) {
            $useMinMax = false;
            if (isset($discount['min'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::DISCOUNT, $discount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discount['max'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::DISCOUNT, $discount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsQuantityDiscountPeer::DISCOUNT, $discount, $comparison);
    }

    /**
     * Filter the query on the valid_from column
     *
     * Example usage:
     * <code>
     * $query->filterByValidFrom('2011-03-14'); // WHERE valid_from = '2011-03-14'
     * $query->filterByValidFrom('now'); // WHERE valid_from = '2011-03-14'
     * $query->filterByValidFrom(array('max' => 'yesterday')); // WHERE valid_from < '2011-03-13'
     * </code>
     *
     * @param     mixed $validFrom The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByValidFrom($validFrom = null, $comparison = null)
    {
        if (is_array($validFrom)) {
            $useMinMax = false;
            if (isset($validFrom['min'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::VALID_FROM, $validFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($validFrom['max'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::VALID_FROM, $validFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsQuantityDiscountPeer::VALID_FROM, $validFrom, $comparison);
    }

    /**
     * Filter the query on the valid_to column
     *
     * Example usage:
     * <code>
     * $query->filterByValidTo('2011-03-14'); // WHERE valid_to = '2011-03-14'
     * $query->filterByValidTo('now'); // WHERE valid_to = '2011-03-14'
     * $query->filterByValidTo(array('max' => 'yesterday')); // WHERE valid_to < '2011-03-13'
     * </code>
     *
     * @param     mixed $validTo The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function filterByValidTo($validTo = null, $comparison = null)
    {
        if (is_array($validTo)) {
            $useMinMax = false;
            if (isset($validTo['min'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::VALID_TO, $validTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($validTo['max'])) {
                $this->addUsingAlias(ProductsQuantityDiscountPeer::VALID_TO, $validTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsQuantityDiscountPeer::VALID_TO, $validTo, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuantityDiscountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsQuantityDiscountPeer::PRODUCTS_MASTER, $products->getSku(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsQuantityDiscountPeer::PRODUCTS_MASTER, $products->toKeyValue('PrimaryKey', 'Sku'), $comparison);
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
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
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
     * Filter the query by a related Domains object
     *
     * @param   Domains|PropelObjectCollection $domains The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuantityDiscountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDomains($domains, $comparison = null)
    {
        if ($domains instanceof Domains) {
            return $this
                ->addUsingAlias(ProductsQuantityDiscountPeer::DOMAINS_ID, $domains->getId(), $comparison);
        } elseif ($domains instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsQuantityDiscountPeer::DOMAINS_ID, $domains->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDomains() only accepts arguments of type Domains or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Domains relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function joinDomains($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Domains');

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
            $this->addJoinObject($join, 'Domains');
        }

        return $this;
    }

    /**
     * Use the Domains relation Domains object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\DomainsQuery A secondary query class using the current class as primary query
     */
    public function useDomainsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDomains($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Domains', '\Hanzo\Model\DomainsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ProductsQuantityDiscount $productsQuantityDiscount Object to remove from the list of results
     *
     * @return ProductsQuantityDiscountQuery The current query, for fluid interface
     */
    public function prune($productsQuantityDiscount = null)
    {
        if ($productsQuantityDiscount) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductsQuantityDiscountPeer::PRODUCTS_MASTER), $productsQuantityDiscount->getProductsMaster(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductsQuantityDiscountPeer::DOMAINS_ID), $productsQuantityDiscount->getDomainsId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(ProductsQuantityDiscountPeer::SPAN), $productsQuantityDiscount->getSpan(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
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

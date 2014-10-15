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
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsDomainsPricesPeer;
use Hanzo\Model\ProductsDomainsPricesQuery;

/**
 * @method ProductsDomainsPricesQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method ProductsDomainsPricesQuery orderByDomainsId($order = Criteria::ASC) Order by the domains_id column
 * @method ProductsDomainsPricesQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method ProductsDomainsPricesQuery orderByVat($order = Criteria::ASC) Order by the vat column
 * @method ProductsDomainsPricesQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method ProductsDomainsPricesQuery orderByFromDate($order = Criteria::ASC) Order by the from_date column
 * @method ProductsDomainsPricesQuery orderByToDate($order = Criteria::ASC) Order by the to_date column
 *
 * @method ProductsDomainsPricesQuery groupByProductsId() Group by the products_id column
 * @method ProductsDomainsPricesQuery groupByDomainsId() Group by the domains_id column
 * @method ProductsDomainsPricesQuery groupByPrice() Group by the price column
 * @method ProductsDomainsPricesQuery groupByVat() Group by the vat column
 * @method ProductsDomainsPricesQuery groupByCurrencyId() Group by the currency_id column
 * @method ProductsDomainsPricesQuery groupByFromDate() Group by the from_date column
 * @method ProductsDomainsPricesQuery groupByToDate() Group by the to_date column
 *
 * @method ProductsDomainsPricesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsDomainsPricesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsDomainsPricesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsDomainsPricesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method ProductsDomainsPricesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method ProductsDomainsPricesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method ProductsDomainsPricesQuery leftJoinDomains($relationAlias = null) Adds a LEFT JOIN clause to the query using the Domains relation
 * @method ProductsDomainsPricesQuery rightJoinDomains($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Domains relation
 * @method ProductsDomainsPricesQuery innerJoinDomains($relationAlias = null) Adds a INNER JOIN clause to the query using the Domains relation
 *
 * @method ProductsDomainsPrices findOne(PropelPDO $con = null) Return the first ProductsDomainsPrices matching the query
 * @method ProductsDomainsPrices findOneOrCreate(PropelPDO $con = null) Return the first ProductsDomainsPrices matching the query, or a new ProductsDomainsPrices object populated from the query conditions when no match is found
 *
 * @method ProductsDomainsPrices findOneByProductsId(int $products_id) Return the first ProductsDomainsPrices filtered by the products_id column
 * @method ProductsDomainsPrices findOneByDomainsId(int $domains_id) Return the first ProductsDomainsPrices filtered by the domains_id column
 * @method ProductsDomainsPrices findOneByPrice(string $price) Return the first ProductsDomainsPrices filtered by the price column
 * @method ProductsDomainsPrices findOneByVat(string $vat) Return the first ProductsDomainsPrices filtered by the vat column
 * @method ProductsDomainsPrices findOneByCurrencyId(int $currency_id) Return the first ProductsDomainsPrices filtered by the currency_id column
 * @method ProductsDomainsPrices findOneByFromDate(string $from_date) Return the first ProductsDomainsPrices filtered by the from_date column
 * @method ProductsDomainsPrices findOneByToDate(string $to_date) Return the first ProductsDomainsPrices filtered by the to_date column
 *
 * @method array findByProductsId(int $products_id) Return ProductsDomainsPrices objects filtered by the products_id column
 * @method array findByDomainsId(int $domains_id) Return ProductsDomainsPrices objects filtered by the domains_id column
 * @method array findByPrice(string $price) Return ProductsDomainsPrices objects filtered by the price column
 * @method array findByVat(string $vat) Return ProductsDomainsPrices objects filtered by the vat column
 * @method array findByCurrencyId(int $currency_id) Return ProductsDomainsPrices objects filtered by the currency_id column
 * @method array findByFromDate(string $from_date) Return ProductsDomainsPrices objects filtered by the from_date column
 * @method array findByToDate(string $to_date) Return ProductsDomainsPrices objects filtered by the to_date column
 */
abstract class BaseProductsDomainsPricesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsDomainsPricesQuery object.
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
            $modelName = 'Hanzo\\Model\\ProductsDomainsPrices';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new ProductsDomainsPricesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProductsDomainsPricesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsDomainsPricesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsDomainsPricesQuery) {
            return $criteria;
        }
        $query = new ProductsDomainsPricesQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$products_id, $domains_id, $from_date]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ProductsDomainsPrices|ProductsDomainsPrices[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsDomainsPricesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsDomainsPricesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ProductsDomainsPrices A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `products_id`, `domains_id`, `price`, `vat`, `currency_id`, `from_date`, `to_date` FROM `products_domains_prices` WHERE `products_id` = :p0 AND `domains_id` = :p1 AND `from_date` = :p2';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->bindValue(':p2', $key[2], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new ProductsDomainsPrices();
            $obj->hydrate($row);
            ProductsDomainsPricesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
     * @return ProductsDomainsPrices|ProductsDomainsPrices[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ProductsDomainsPrices[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductsDomainsPricesPeer::PRODUCTS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductsDomainsPricesPeer::DOMAINS_ID, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(ProductsDomainsPricesPeer::FROM_DATE, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductsDomainsPricesPeer::PRODUCTS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductsDomainsPricesPeer::DOMAINS_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(ProductsDomainsPricesPeer::FROM_DATE, $key[2], Criteria::EQUAL);
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
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::PRODUCTS_ID, $productsId, $comparison);
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
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByDomainsId($domainsId = null, $comparison = null)
    {
        if (is_array($domainsId)) {
            $useMinMax = false;
            if (isset($domainsId['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::DOMAINS_ID, $domainsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($domainsId['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::DOMAINS_ID, $domainsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::DOMAINS_ID, $domainsId, $comparison);
    }

    /**
     * Filter the query on the price column
     *
     * Example usage:
     * <code>
     * $query->filterByPrice(1234); // WHERE price = 1234
     * $query->filterByPrice(array(12, 34)); // WHERE price IN (12, 34)
     * $query->filterByPrice(array('min' => 12)); // WHERE price >= 12
     * $query->filterByPrice(array('max' => 12)); // WHERE price <= 12
     * </code>
     *
     * @param     mixed $price The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByPrice($price = null, $comparison = null)
    {
        if (is_array($price)) {
            $useMinMax = false;
            if (isset($price['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::PRICE, $price['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($price['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::PRICE, $price['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::PRICE, $price, $comparison);
    }

    /**
     * Filter the query on the vat column
     *
     * Example usage:
     * <code>
     * $query->filterByVat(1234); // WHERE vat = 1234
     * $query->filterByVat(array(12, 34)); // WHERE vat IN (12, 34)
     * $query->filterByVat(array('min' => 12)); // WHERE vat >= 12
     * $query->filterByVat(array('max' => 12)); // WHERE vat <= 12
     * </code>
     *
     * @param     mixed $vat The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByVat($vat = null, $comparison = null)
    {
        if (is_array($vat)) {
            $useMinMax = false;
            if (isset($vat['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::VAT, $vat['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vat['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::VAT, $vat['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::VAT, $vat, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id >= 12
     * $query->filterByCurrencyId(array('max' => 12)); // WHERE currency_id <= 12
     * </code>
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the from_date column
     *
     * Example usage:
     * <code>
     * $query->filterByFromDate('2011-03-14'); // WHERE from_date = '2011-03-14'
     * $query->filterByFromDate('now'); // WHERE from_date = '2011-03-14'
     * $query->filterByFromDate(array('max' => 'yesterday')); // WHERE from_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $fromDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByFromDate($fromDate = null, $comparison = null)
    {
        if (is_array($fromDate)) {
            $useMinMax = false;
            if (isset($fromDate['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::FROM_DATE, $fromDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fromDate['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::FROM_DATE, $fromDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::FROM_DATE, $fromDate, $comparison);
    }

    /**
     * Filter the query on the to_date column
     *
     * Example usage:
     * <code>
     * $query->filterByToDate('2011-03-14'); // WHERE to_date = '2011-03-14'
     * $query->filterByToDate('now'); // WHERE to_date = '2011-03-14'
     * $query->filterByToDate(array('max' => 'yesterday')); // WHERE to_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $toDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function filterByToDate($toDate = null, $comparison = null)
    {
        if (is_array($toDate)) {
            $useMinMax = false;
            if (isset($toDate['min'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::TO_DATE, $toDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($toDate['max'])) {
                $this->addUsingAlias(ProductsDomainsPricesPeer::TO_DATE, $toDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsDomainsPricesPeer::TO_DATE, $toDate, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsDomainsPricesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsDomainsPricesPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsDomainsPricesPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
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
     * @return                 ProductsDomainsPricesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDomains($domains, $comparison = null)
    {
        if ($domains instanceof Domains) {
            return $this
                ->addUsingAlias(ProductsDomainsPricesPeer::DOMAINS_ID, $domains->getId(), $comparison);
        } elseif ($domains instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsDomainsPricesPeer::DOMAINS_ID, $domains->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
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
     * @param   ProductsDomainsPrices $productsDomainsPrices Object to remove from the list of results
     *
     * @return ProductsDomainsPricesQuery The current query, for fluid interface
     */
    public function prune($productsDomainsPrices = null)
    {
        if ($productsDomainsPrices) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductsDomainsPricesPeer::PRODUCTS_ID), $productsDomainsPrices->getProductsId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductsDomainsPricesPeer::DOMAINS_ID), $productsDomainsPrices->getDomainsId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(ProductsDomainsPricesPeer::FROM_DATE), $productsDomainsPrices->getFromDate(), Criteria::NOT_EQUAL);
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

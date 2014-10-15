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
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\Products;

/**
 * @method OrdersLinesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method OrdersLinesQuery orderByOrdersId($order = Criteria::ASC) Order by the orders_id column
 * @method OrdersLinesQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method OrdersLinesQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method OrdersLinesQuery orderByProductsSku($order = Criteria::ASC) Order by the products_sku column
 * @method OrdersLinesQuery orderByProductsName($order = Criteria::ASC) Order by the products_name column
 * @method OrdersLinesQuery orderByProductsColor($order = Criteria::ASC) Order by the products_color column
 * @method OrdersLinesQuery orderByProductsSize($order = Criteria::ASC) Order by the products_size column
 * @method OrdersLinesQuery orderByExpectedAt($order = Criteria::ASC) Order by the expected_at column
 * @method OrdersLinesQuery orderByOriginalPrice($order = Criteria::ASC) Order by the original_price column
 * @method OrdersLinesQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method OrdersLinesQuery orderByVat($order = Criteria::ASC) Order by the vat column
 * @method OrdersLinesQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 * @method OrdersLinesQuery orderByUnit($order = Criteria::ASC) Order by the unit column
 * @method OrdersLinesQuery orderByIsVoucher($order = Criteria::ASC) Order by the is_voucher column
 * @method OrdersLinesQuery orderByNote($order = Criteria::ASC) Order by the note column
 *
 * @method OrdersLinesQuery groupById() Group by the id column
 * @method OrdersLinesQuery groupByOrdersId() Group by the orders_id column
 * @method OrdersLinesQuery groupByType() Group by the type column
 * @method OrdersLinesQuery groupByProductsId() Group by the products_id column
 * @method OrdersLinesQuery groupByProductsSku() Group by the products_sku column
 * @method OrdersLinesQuery groupByProductsName() Group by the products_name column
 * @method OrdersLinesQuery groupByProductsColor() Group by the products_color column
 * @method OrdersLinesQuery groupByProductsSize() Group by the products_size column
 * @method OrdersLinesQuery groupByExpectedAt() Group by the expected_at column
 * @method OrdersLinesQuery groupByOriginalPrice() Group by the original_price column
 * @method OrdersLinesQuery groupByPrice() Group by the price column
 * @method OrdersLinesQuery groupByVat() Group by the vat column
 * @method OrdersLinesQuery groupByQuantity() Group by the quantity column
 * @method OrdersLinesQuery groupByUnit() Group by the unit column
 * @method OrdersLinesQuery groupByIsVoucher() Group by the is_voucher column
 * @method OrdersLinesQuery groupByNote() Group by the note column
 *
 * @method OrdersLinesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method OrdersLinesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method OrdersLinesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method OrdersLinesQuery leftJoinOrders($relationAlias = null) Adds a LEFT JOIN clause to the query using the Orders relation
 * @method OrdersLinesQuery rightJoinOrders($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Orders relation
 * @method OrdersLinesQuery innerJoinOrders($relationAlias = null) Adds a INNER JOIN clause to the query using the Orders relation
 *
 * @method OrdersLinesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method OrdersLinesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method OrdersLinesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method OrdersLines findOne(PropelPDO $con = null) Return the first OrdersLines matching the query
 * @method OrdersLines findOneOrCreate(PropelPDO $con = null) Return the first OrdersLines matching the query, or a new OrdersLines object populated from the query conditions when no match is found
 *
 * @method OrdersLines findOneByOrdersId(int $orders_id) Return the first OrdersLines filtered by the orders_id column
 * @method OrdersLines findOneByType(string $type) Return the first OrdersLines filtered by the type column
 * @method OrdersLines findOneByProductsId(int $products_id) Return the first OrdersLines filtered by the products_id column
 * @method OrdersLines findOneByProductsSku(string $products_sku) Return the first OrdersLines filtered by the products_sku column
 * @method OrdersLines findOneByProductsName(string $products_name) Return the first OrdersLines filtered by the products_name column
 * @method OrdersLines findOneByProductsColor(string $products_color) Return the first OrdersLines filtered by the products_color column
 * @method OrdersLines findOneByProductsSize(string $products_size) Return the first OrdersLines filtered by the products_size column
 * @method OrdersLines findOneByExpectedAt(string $expected_at) Return the first OrdersLines filtered by the expected_at column
 * @method OrdersLines findOneByOriginalPrice(string $original_price) Return the first OrdersLines filtered by the original_price column
 * @method OrdersLines findOneByPrice(string $price) Return the first OrdersLines filtered by the price column
 * @method OrdersLines findOneByVat(string $vat) Return the first OrdersLines filtered by the vat column
 * @method OrdersLines findOneByQuantity(int $quantity) Return the first OrdersLines filtered by the quantity column
 * @method OrdersLines findOneByUnit(string $unit) Return the first OrdersLines filtered by the unit column
 * @method OrdersLines findOneByIsVoucher(boolean $is_voucher) Return the first OrdersLines filtered by the is_voucher column
 * @method OrdersLines findOneByNote(string $note) Return the first OrdersLines filtered by the note column
 *
 * @method array findById(int $id) Return OrdersLines objects filtered by the id column
 * @method array findByOrdersId(int $orders_id) Return OrdersLines objects filtered by the orders_id column
 * @method array findByType(string $type) Return OrdersLines objects filtered by the type column
 * @method array findByProductsId(int $products_id) Return OrdersLines objects filtered by the products_id column
 * @method array findByProductsSku(string $products_sku) Return OrdersLines objects filtered by the products_sku column
 * @method array findByProductsName(string $products_name) Return OrdersLines objects filtered by the products_name column
 * @method array findByProductsColor(string $products_color) Return OrdersLines objects filtered by the products_color column
 * @method array findByProductsSize(string $products_size) Return OrdersLines objects filtered by the products_size column
 * @method array findByExpectedAt(string $expected_at) Return OrdersLines objects filtered by the expected_at column
 * @method array findByOriginalPrice(string $original_price) Return OrdersLines objects filtered by the original_price column
 * @method array findByPrice(string $price) Return OrdersLines objects filtered by the price column
 * @method array findByVat(string $vat) Return OrdersLines objects filtered by the vat column
 * @method array findByQuantity(int $quantity) Return OrdersLines objects filtered by the quantity column
 * @method array findByUnit(string $unit) Return OrdersLines objects filtered by the unit column
 * @method array findByIsVoucher(boolean $is_voucher) Return OrdersLines objects filtered by the is_voucher column
 * @method array findByNote(string $note) Return OrdersLines objects filtered by the note column
 */
abstract class BaseOrdersLinesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseOrdersLinesQuery object.
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
            $modelName = 'Hanzo\\Model\\OrdersLines';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new OrdersLinesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   OrdersLinesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return OrdersLinesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof OrdersLinesQuery) {
            return $criteria;
        }
        $query = new OrdersLinesQuery(null, null, $modelAlias);

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
     * @return   OrdersLines|OrdersLines[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrdersLinesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(OrdersLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 OrdersLines A model object, or null if the key is not found
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
     * @return                 OrdersLines A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `orders_id`, `type`, `products_id`, `products_sku`, `products_name`, `products_color`, `products_size`, `expected_at`, `original_price`, `price`, `vat`, `quantity`, `unit`, `is_voucher`, `note` FROM `orders_lines` WHERE `id` = :p0';
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
            $obj = new OrdersLines();
            $obj->hydrate($row);
            OrdersLinesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return OrdersLines|OrdersLines[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|OrdersLines[]|mixed the list of results, formatted by the current formatter
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
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrdersLinesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrdersLinesPeer::ID, $keys, Criteria::IN);
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
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the orders_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrdersId(1234); // WHERE orders_id = 1234
     * $query->filterByOrdersId(array(12, 34)); // WHERE orders_id IN (12, 34)
     * $query->filterByOrdersId(array('min' => 12)); // WHERE orders_id >= 12
     * $query->filterByOrdersId(array('max' => 12)); // WHERE orders_id <= 12
     * </code>
     *
     * @see       filterByOrders()
     *
     * @param     mixed $ordersId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByOrdersId($ordersId = null, $comparison = null)
    {
        if (is_array($ordersId)) {
            $useMinMax = false;
            if (isset($ordersId['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::ORDERS_ID, $ordersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ordersId['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::ORDERS_ID, $ordersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::ORDERS_ID, $ordersId, $comparison);
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
     * @return OrdersLinesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(OrdersLinesPeer::TYPE, $type, $comparison);
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
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_ID, $productsId, $comparison);
    }

    /**
     * Filter the query on the products_sku column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsSku('fooValue');   // WHERE products_sku = 'fooValue'
     * $query->filterByProductsSku('%fooValue%'); // WHERE products_sku LIKE '%fooValue%'
     * </code>
     *
     * @param     string $productsSku The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByProductsSku($productsSku = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($productsSku)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $productsSku)) {
                $productsSku = str_replace('*', '%', $productsSku);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_SKU, $productsSku, $comparison);
    }

    /**
     * Filter the query on the products_name column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsName('fooValue');   // WHERE products_name = 'fooValue'
     * $query->filterByProductsName('%fooValue%'); // WHERE products_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $productsName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByProductsName($productsName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($productsName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $productsName)) {
                $productsName = str_replace('*', '%', $productsName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_NAME, $productsName, $comparison);
    }

    /**
     * Filter the query on the products_color column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsColor('fooValue');   // WHERE products_color = 'fooValue'
     * $query->filterByProductsColor('%fooValue%'); // WHERE products_color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $productsColor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByProductsColor($productsColor = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($productsColor)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $productsColor)) {
                $productsColor = str_replace('*', '%', $productsColor);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_COLOR, $productsColor, $comparison);
    }

    /**
     * Filter the query on the products_size column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsSize('fooValue');   // WHERE products_size = 'fooValue'
     * $query->filterByProductsSize('%fooValue%'); // WHERE products_size LIKE '%fooValue%'
     * </code>
     *
     * @param     string $productsSize The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByProductsSize($productsSize = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($productsSize)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $productsSize)) {
                $productsSize = str_replace('*', '%', $productsSize);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::PRODUCTS_SIZE, $productsSize, $comparison);
    }

    /**
     * Filter the query on the expected_at column
     *
     * Example usage:
     * <code>
     * $query->filterByExpectedAt('2011-03-14'); // WHERE expected_at = '2011-03-14'
     * $query->filterByExpectedAt('now'); // WHERE expected_at = '2011-03-14'
     * $query->filterByExpectedAt(array('max' => 'yesterday')); // WHERE expected_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $expectedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByExpectedAt($expectedAt = null, $comparison = null)
    {
        if (is_array($expectedAt)) {
            $useMinMax = false;
            if (isset($expectedAt['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::EXPECTED_AT, $expectedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($expectedAt['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::EXPECTED_AT, $expectedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::EXPECTED_AT, $expectedAt, $comparison);
    }

    /**
     * Filter the query on the original_price column
     *
     * Example usage:
     * <code>
     * $query->filterByOriginalPrice(1234); // WHERE original_price = 1234
     * $query->filterByOriginalPrice(array(12, 34)); // WHERE original_price IN (12, 34)
     * $query->filterByOriginalPrice(array('min' => 12)); // WHERE original_price >= 12
     * $query->filterByOriginalPrice(array('max' => 12)); // WHERE original_price <= 12
     * </code>
     *
     * @param     mixed $originalPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByOriginalPrice($originalPrice = null, $comparison = null)
    {
        if (is_array($originalPrice)) {
            $useMinMax = false;
            if (isset($originalPrice['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::ORIGINAL_PRICE, $originalPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($originalPrice['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::ORIGINAL_PRICE, $originalPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::ORIGINAL_PRICE, $originalPrice, $comparison);
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
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByPrice($price = null, $comparison = null)
    {
        if (is_array($price)) {
            $useMinMax = false;
            if (isset($price['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::PRICE, $price['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($price['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::PRICE, $price['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::PRICE, $price, $comparison);
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
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByVat($vat = null, $comparison = null)
    {
        if (is_array($vat)) {
            $useMinMax = false;
            if (isset($vat['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::VAT, $vat['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vat['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::VAT, $vat['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::VAT, $vat, $comparison);
    }

    /**
     * Filter the query on the quantity column
     *
     * Example usage:
     * <code>
     * $query->filterByQuantity(1234); // WHERE quantity = 1234
     * $query->filterByQuantity(array(12, 34)); // WHERE quantity IN (12, 34)
     * $query->filterByQuantity(array('min' => 12)); // WHERE quantity >= 12
     * $query->filterByQuantity(array('max' => 12)); // WHERE quantity <= 12
     * </code>
     *
     * @param     mixed $quantity The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByQuantity($quantity = null, $comparison = null)
    {
        if (is_array($quantity)) {
            $useMinMax = false;
            if (isset($quantity['min'])) {
                $this->addUsingAlias(OrdersLinesPeer::QUANTITY, $quantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantity['max'])) {
                $this->addUsingAlias(OrdersLinesPeer::QUANTITY, $quantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::QUANTITY, $quantity, $comparison);
    }

    /**
     * Filter the query on the unit column
     *
     * Example usage:
     * <code>
     * $query->filterByUnit('fooValue');   // WHERE unit = 'fooValue'
     * $query->filterByUnit('%fooValue%'); // WHERE unit LIKE '%fooValue%'
     * </code>
     *
     * @param     string $unit The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByUnit($unit = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($unit)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $unit)) {
                $unit = str_replace('*', '%', $unit);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::UNIT, $unit, $comparison);
    }

    /**
     * Filter the query on the is_voucher column
     *
     * Example usage:
     * <code>
     * $query->filterByIsVoucher(true); // WHERE is_voucher = true
     * $query->filterByIsVoucher('yes'); // WHERE is_voucher = true
     * </code>
     *
     * @param     boolean|string $isVoucher The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByIsVoucher($isVoucher = null, $comparison = null)
    {
        if (is_string($isVoucher)) {
            $isVoucher = in_array(strtolower($isVoucher), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(OrdersLinesPeer::IS_VOUCHER, $isVoucher, $comparison);
    }

    /**
     * Filter the query on the note column
     *
     * Example usage:
     * <code>
     * $query->filterByNote('fooValue');   // WHERE note = 'fooValue'
     * $query->filterByNote('%fooValue%'); // WHERE note LIKE '%fooValue%'
     * </code>
     *
     * @param     string $note The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function filterByNote($note = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($note)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $note)) {
                $note = str_replace('*', '%', $note);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrdersLinesPeer::NOTE, $note, $comparison);
    }

    /**
     * Filter the query by a related Orders object
     *
     * @param   Orders|PropelObjectCollection $orders The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersLinesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrders($orders, $comparison = null)
    {
        if ($orders instanceof Orders) {
            return $this
                ->addUsingAlias(OrdersLinesPeer::ORDERS_ID, $orders->getId(), $comparison);
        } elseif ($orders instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersLinesPeer::ORDERS_ID, $orders->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrders() only accepts arguments of type Orders or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Orders relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function joinOrders($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Orders');

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
            $this->addJoinObject($join, 'Orders');
        }

        return $this;
    }

    /**
     * Use the Orders relation Orders object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersQuery A secondary query class using the current class as primary query
     */
    public function useOrdersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrders($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Orders', '\Hanzo\Model\OrdersQuery');
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 OrdersLinesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(OrdersLinesPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrdersLinesPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function joinProducts($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useProductsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProducts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Products', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   OrdersLines $ordersLines Object to remove from the list of results
     *
     * @return OrdersLinesQuery The current query, for fluid interface
     */
    public function prune($ordersLines = null)
    {
        if ($ordersLines) {
            $this->addUsingAlias(OrdersLinesPeer::ID, $ordersLines->getId(), Criteria::NOT_EQUAL);
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

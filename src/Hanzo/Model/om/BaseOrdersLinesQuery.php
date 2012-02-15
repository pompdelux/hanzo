<?php

namespace Hanzo\Model\om;

use \Criteria;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelPDO;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\OrdersLinesPeer;
use Hanzo\Model\OrdersLinesQuery;
use Hanzo\Model\Products;

/**
 * Base class that represents a query for the 'orders_lines' table.
 *
 * 
 *
 * @method     OrdersLinesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     OrdersLinesQuery orderByOrdersId($order = Criteria::ASC) Order by the orders_id column
 * @method     OrdersLinesQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     OrdersLinesQuery orderByTax($order = Criteria::ASC) Order by the tax column
 * @method     OrdersLinesQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method     OrdersLinesQuery orderByProductsSku($order = Criteria::ASC) Order by the products_sku column
 * @method     OrdersLinesQuery orderByProductsName($order = Criteria::ASC) Order by the products_name column
 * @method     OrdersLinesQuery orderByProductsColor($order = Criteria::ASC) Order by the products_color column
 * @method     OrdersLinesQuery orderByProductsSize($order = Criteria::ASC) Order by the products_size column
 * @method     OrdersLinesQuery orderByExpectedAt($order = Criteria::ASC) Order by the expected_at column
 * @method     OrdersLinesQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method     OrdersLinesQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 *
 * @method     OrdersLinesQuery groupById() Group by the id column
 * @method     OrdersLinesQuery groupByOrdersId() Group by the orders_id column
 * @method     OrdersLinesQuery groupByType() Group by the type column
 * @method     OrdersLinesQuery groupByTax() Group by the tax column
 * @method     OrdersLinesQuery groupByProductsId() Group by the products_id column
 * @method     OrdersLinesQuery groupByProductsSku() Group by the products_sku column
 * @method     OrdersLinesQuery groupByProductsName() Group by the products_name column
 * @method     OrdersLinesQuery groupByProductsColor() Group by the products_color column
 * @method     OrdersLinesQuery groupByProductsSize() Group by the products_size column
 * @method     OrdersLinesQuery groupByExpectedAt() Group by the expected_at column
 * @method     OrdersLinesQuery groupByPrice() Group by the price column
 * @method     OrdersLinesQuery groupByQuantity() Group by the quantity column
 *
 * @method     OrdersLinesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     OrdersLinesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     OrdersLinesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     OrdersLinesQuery leftJoinOrders($relationAlias = null) Adds a LEFT JOIN clause to the query using the Orders relation
 * @method     OrdersLinesQuery rightJoinOrders($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Orders relation
 * @method     OrdersLinesQuery innerJoinOrders($relationAlias = null) Adds a INNER JOIN clause to the query using the Orders relation
 *
 * @method     OrdersLinesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method     OrdersLinesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method     OrdersLinesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method     OrdersLines findOne(PropelPDO $con = null) Return the first OrdersLines matching the query
 * @method     OrdersLines findOneOrCreate(PropelPDO $con = null) Return the first OrdersLines matching the query, or a new OrdersLines object populated from the query conditions when no match is found
 *
 * @method     OrdersLines findOneById(int $id) Return the first OrdersLines filtered by the id column
 * @method     OrdersLines findOneByOrdersId(int $orders_id) Return the first OrdersLines filtered by the orders_id column
 * @method     OrdersLines findOneByType(string $type) Return the first OrdersLines filtered by the type column
 * @method     OrdersLines findOneByTax(string $tax) Return the first OrdersLines filtered by the tax column
 * @method     OrdersLines findOneByProductsId(int $products_id) Return the first OrdersLines filtered by the products_id column
 * @method     OrdersLines findOneByProductsSku(string $products_sku) Return the first OrdersLines filtered by the products_sku column
 * @method     OrdersLines findOneByProductsName(string $products_name) Return the first OrdersLines filtered by the products_name column
 * @method     OrdersLines findOneByProductsColor(string $products_color) Return the first OrdersLines filtered by the products_color column
 * @method     OrdersLines findOneByProductsSize(string $products_size) Return the first OrdersLines filtered by the products_size column
 * @method     OrdersLines findOneByExpectedAt(string $expected_at) Return the first OrdersLines filtered by the expected_at column
 * @method     OrdersLines findOneByPrice(string $price) Return the first OrdersLines filtered by the price column
 * @method     OrdersLines findOneByQuantity(int $quantity) Return the first OrdersLines filtered by the quantity column
 *
 * @method     array findById(int $id) Return OrdersLines objects filtered by the id column
 * @method     array findByOrdersId(int $orders_id) Return OrdersLines objects filtered by the orders_id column
 * @method     array findByType(string $type) Return OrdersLines objects filtered by the type column
 * @method     array findByTax(string $tax) Return OrdersLines objects filtered by the tax column
 * @method     array findByProductsId(int $products_id) Return OrdersLines objects filtered by the products_id column
 * @method     array findByProductsSku(string $products_sku) Return OrdersLines objects filtered by the products_sku column
 * @method     array findByProductsName(string $products_name) Return OrdersLines objects filtered by the products_name column
 * @method     array findByProductsColor(string $products_color) Return OrdersLines objects filtered by the products_color column
 * @method     array findByProductsSize(string $products_size) Return OrdersLines objects filtered by the products_size column
 * @method     array findByExpectedAt(string $expected_at) Return OrdersLines objects filtered by the expected_at column
 * @method     array findByPrice(string $price) Return OrdersLines objects filtered by the price column
 * @method     array findByQuantity(int $quantity) Return OrdersLines objects filtered by the quantity column
 *
 * @package    propel.generator.src.Hanzo.Model.om
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
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\OrdersLines', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new OrdersLinesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    OrdersLinesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof OrdersLinesQuery) {
			return $criteria;
		}
		$query = new OrdersLinesQuery();
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
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    OrdersLines|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = OrdersLinesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
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
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    OrdersLines A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ID`, `ORDERS_ID`, `TYPE`, `TAX`, `PRODUCTS_ID`, `PRODUCTS_SKU`, `PRODUCTS_NAME`, `PRODUCTS_COLOR`, `PRODUCTS_SIZE`, `EXPECTED_AT`, `PRICE`, `QUANTITY` FROM `orders_lines` WHERE `ID` = :p0';
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
			OrdersLinesPeer::addInstanceToPool($obj, (string) $row[0]);
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
	 * @return    OrdersLines|array|mixed the result, formatted by the current formatter
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
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * $query->filterById(array('min' => 12)); // WHERE id > 12
	 * </code>
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
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
	 * $query->filterByOrdersId(array('min' => 12)); // WHERE orders_id > 12
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * Filter the query on the tax column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTax(1234); // WHERE tax = 1234
	 * $query->filterByTax(array(12, 34)); // WHERE tax IN (12, 34)
	 * $query->filterByTax(array('min' => 12)); // WHERE tax > 12
	 * </code>
	 *
	 * @param     mixed $tax The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
	 */
	public function filterByTax($tax = null, $comparison = null)
	{
		if (is_array($tax)) {
			$useMinMax = false;
			if (isset($tax['min'])) {
				$this->addUsingAlias(OrdersLinesPeer::TAX, $tax['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($tax['max'])) {
				$this->addUsingAlias(OrdersLinesPeer::TAX, $tax['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(OrdersLinesPeer::TAX, $tax, $comparison);
	}

	/**
	 * Filter the query on the products_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByProductsId(1234); // WHERE products_id = 1234
	 * $query->filterByProductsId(array(12, 34)); // WHERE products_id IN (12, 34)
	 * $query->filterByProductsId(array('min' => 12)); // WHERE products_id > 12
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * $query->filterByExpectedAt(array('max' => 'yesterday')); // WHERE expected_at > '2011-03-13'
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * Filter the query on the price column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPrice(1234); // WHERE price = 1234
	 * $query->filterByPrice(array(12, 34)); // WHERE price IN (12, 34)
	 * $query->filterByPrice(array('min' => 12)); // WHERE price > 12
	 * </code>
	 *
	 * @param     mixed $price The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * Filter the query on the quantity column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByQuantity(1234); // WHERE quantity = 1234
	 * $query->filterByQuantity(array(12, 34)); // WHERE quantity IN (12, 34)
	 * $query->filterByQuantity(array('min' => 12)); // WHERE quantity > 12
	 * </code>
	 *
	 * @param     mixed $quantity The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
	 * Filter the query by a related Orders object
	 *
	 * @param     Orders|PropelCollection $orders The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
	 */
	public function filterByOrders($orders, $comparison = null)
	{
		if ($orders instanceof Orders) {
			return $this
				->addUsingAlias(OrdersLinesPeer::ORDERS_ID, $orders->getId(), $comparison);
		} elseif ($orders instanceof PropelCollection) {
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
		if($relationAlias) {
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
	 * @return    \Hanzo\Model\OrdersQuery A secondary query class using the current class as primary query
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
	 * @param     Products|PropelCollection $products The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
	 */
	public function filterByProducts($products, $comparison = null)
	{
		if ($products instanceof Products) {
			return $this
				->addUsingAlias(OrdersLinesPeer::PRODUCTS_ID, $products->getId(), $comparison);
		} elseif ($products instanceof PropelCollection) {
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
	 * @return    OrdersLinesQuery The current query, for fluid interface
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
		if($relationAlias) {
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
	 * @return    \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
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
	 * @param     OrdersLines $ordersLines Object to remove from the list of results
	 *
	 * @return    OrdersLinesQuery The current query, for fluid interface
	 */
	public function prune($ordersLines = null)
	{
		if ($ordersLines) {
			$this->addUsingAlias(OrdersLinesPeer::ID, $ordersLines->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseOrdersLinesQuery
<?php

namespace Hanzo\Model\om;

use \Criteria;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelPDO;
use Hanzo\Model\ShippingMethods;
use Hanzo\Model\ShippingMethodsPeer;
use Hanzo\Model\ShippingMethodsQuery;

/**
 * Base class that represents a query for the 'shipping_methods' table.
 *
 * 
 *
 * @method     ShippingMethodsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ShippingMethodsQuery orderByCarrier($order = Criteria::ASC) Order by the carrier column
 * @method     ShippingMethodsQuery orderByExternalId($order = Criteria::ASC) Order by the external_id column
 * @method     ShippingMethodsQuery orderByCalcEngine($order = Criteria::ASC) Order by the calc_engine column
 * @method     ShippingMethodsQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method     ShippingMethodsQuery orderByFee($order = Criteria::ASC) Order by the fee column
 * @method     ShippingMethodsQuery orderByFeeExternalId($order = Criteria::ASC) Order by the fee_external_id column
 * @method     ShippingMethodsQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 *
 * @method     ShippingMethodsQuery groupById() Group by the id column
 * @method     ShippingMethodsQuery groupByCarrier() Group by the carrier column
 * @method     ShippingMethodsQuery groupByExternalId() Group by the external_id column
 * @method     ShippingMethodsQuery groupByCalcEngine() Group by the calc_engine column
 * @method     ShippingMethodsQuery groupByPrice() Group by the price column
 * @method     ShippingMethodsQuery groupByFee() Group by the fee column
 * @method     ShippingMethodsQuery groupByFeeExternalId() Group by the fee_external_id column
 * @method     ShippingMethodsQuery groupByIsActive() Group by the is_active column
 *
 * @method     ShippingMethodsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ShippingMethodsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ShippingMethodsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ShippingMethods findOne(PropelPDO $con = null) Return the first ShippingMethods matching the query
 * @method     ShippingMethods findOneOrCreate(PropelPDO $con = null) Return the first ShippingMethods matching the query, or a new ShippingMethods object populated from the query conditions when no match is found
 *
 * @method     ShippingMethods findOneById(int $id) Return the first ShippingMethods filtered by the id column
 * @method     ShippingMethods findOneByCarrier(string $carrier) Return the first ShippingMethods filtered by the carrier column
 * @method     ShippingMethods findOneByExternalId(string $external_id) Return the first ShippingMethods filtered by the external_id column
 * @method     ShippingMethods findOneByCalcEngine(string $calc_engine) Return the first ShippingMethods filtered by the calc_engine column
 * @method     ShippingMethods findOneByPrice(string $price) Return the first ShippingMethods filtered by the price column
 * @method     ShippingMethods findOneByFee(string $fee) Return the first ShippingMethods filtered by the fee column
 * @method     ShippingMethods findOneByFeeExternalId(string $fee_external_id) Return the first ShippingMethods filtered by the fee_external_id column
 * @method     ShippingMethods findOneByIsActive(boolean $is_active) Return the first ShippingMethods filtered by the is_active column
 *
 * @method     array findById(int $id) Return ShippingMethods objects filtered by the id column
 * @method     array findByCarrier(string $carrier) Return ShippingMethods objects filtered by the carrier column
 * @method     array findByExternalId(string $external_id) Return ShippingMethods objects filtered by the external_id column
 * @method     array findByCalcEngine(string $calc_engine) Return ShippingMethods objects filtered by the calc_engine column
 * @method     array findByPrice(string $price) Return ShippingMethods objects filtered by the price column
 * @method     array findByFee(string $fee) Return ShippingMethods objects filtered by the fee column
 * @method     array findByFeeExternalId(string $fee_external_id) Return ShippingMethods objects filtered by the fee_external_id column
 * @method     array findByIsActive(boolean $is_active) Return ShippingMethods objects filtered by the is_active column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseShippingMethodsQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseShippingMethodsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\ShippingMethods', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ShippingMethodsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ShippingMethodsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ShippingMethodsQuery) {
			return $criteria;
		}
		$query = new ShippingMethodsQuery();
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
	 * @return    ShippingMethods|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = ShippingMethodsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(ShippingMethodsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    ShippingMethods A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ID`, `CARRIER`, `EXTERNAL_ID`, `CALC_ENGINE`, `PRICE`, `FEE`, `FEE_EXTERNAL_ID`, `IS_ACTIVE` FROM `shipping_methods` WHERE `ID` = :p0';
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
			$obj = new ShippingMethods();
			$obj->hydrate($row);
			ShippingMethodsPeer::addInstanceToPool($obj, (string) $row[0]);
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
	 * @return    ShippingMethods|array|mixed the result, formatted by the current formatter
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
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(ShippingMethodsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(ShippingMethodsPeer::ID, $keys, Criteria::IN);
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
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(ShippingMethodsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the carrier column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCarrier('fooValue');   // WHERE carrier = 'fooValue'
	 * $query->filterByCarrier('%fooValue%'); // WHERE carrier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $carrier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByCarrier($carrier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($carrier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $carrier)) {
				$carrier = str_replace('*', '%', $carrier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ShippingMethodsPeer::CARRIER, $carrier, $comparison);
	}

	/**
	 * Filter the query on the external_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByExternalId('fooValue');   // WHERE external_id = 'fooValue'
	 * $query->filterByExternalId('%fooValue%'); // WHERE external_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $externalId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByExternalId($externalId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($externalId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $externalId)) {
				$externalId = str_replace('*', '%', $externalId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ShippingMethodsPeer::EXTERNAL_ID, $externalId, $comparison);
	}

	/**
	 * Filter the query on the calc_engine column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCalcEngine('fooValue');   // WHERE calc_engine = 'fooValue'
	 * $query->filterByCalcEngine('%fooValue%'); // WHERE calc_engine LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $calcEngine The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByCalcEngine($calcEngine = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($calcEngine)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $calcEngine)) {
				$calcEngine = str_replace('*', '%', $calcEngine);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ShippingMethodsPeer::CALC_ENGINE, $calcEngine, $comparison);
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
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByPrice($price = null, $comparison = null)
	{
		if (is_array($price)) {
			$useMinMax = false;
			if (isset($price['min'])) {
				$this->addUsingAlias(ShippingMethodsPeer::PRICE, $price['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($price['max'])) {
				$this->addUsingAlias(ShippingMethodsPeer::PRICE, $price['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ShippingMethodsPeer::PRICE, $price, $comparison);
	}

	/**
	 * Filter the query on the fee column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFee(1234); // WHERE fee = 1234
	 * $query->filterByFee(array(12, 34)); // WHERE fee IN (12, 34)
	 * $query->filterByFee(array('min' => 12)); // WHERE fee > 12
	 * </code>
	 *
	 * @param     mixed $fee The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByFee($fee = null, $comparison = null)
	{
		if (is_array($fee)) {
			$useMinMax = false;
			if (isset($fee['min'])) {
				$this->addUsingAlias(ShippingMethodsPeer::FEE, $fee['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fee['max'])) {
				$this->addUsingAlias(ShippingMethodsPeer::FEE, $fee['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ShippingMethodsPeer::FEE, $fee, $comparison);
	}

	/**
	 * Filter the query on the fee_external_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFeeExternalId('fooValue');   // WHERE fee_external_id = 'fooValue'
	 * $query->filterByFeeExternalId('%fooValue%'); // WHERE fee_external_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $feeExternalId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByFeeExternalId($feeExternalId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($feeExternalId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $feeExternalId)) {
				$feeExternalId = str_replace('*', '%', $feeExternalId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ShippingMethodsPeer::FEE_EXTERNAL_ID, $feeExternalId, $comparison);
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
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function filterByIsActive($isActive = null, $comparison = null)
	{
		if (is_string($isActive)) {
			$is_active = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(ShippingMethodsPeer::IS_ACTIVE, $isActive, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     ShippingMethods $shippingMethods Object to remove from the list of results
	 *
	 * @return    ShippingMethodsQuery The current query, for fluid interface
	 */
	public function prune($shippingMethods = null)
	{
		if ($shippingMethods) {
			$this->addUsingAlias(ShippingMethodsPeer::ID, $shippingMethods->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseShippingMethodsQuery
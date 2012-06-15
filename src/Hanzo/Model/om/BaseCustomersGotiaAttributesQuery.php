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
use Hanzo\Model\Customers;
use Hanzo\Model\CustomersGotiaAttributes;
use Hanzo\Model\CustomersGotiaAttributesPeer;
use Hanzo\Model\CustomersGotiaAttributesQuery;

/**
 * Base class that represents a query for the 'customers_gotia_attributes' table.
 *
 * 
 *
 * @method     CustomersGotiaAttributesQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method     CustomersGotiaAttributesQuery orderByCKey($order = Criteria::ASC) Order by the c_key column
 * @method     CustomersGotiaAttributesQuery orderByCValue($order = Criteria::ASC) Order by the c_value column
 *
 * @method     CustomersGotiaAttributesQuery groupByCustomersId() Group by the customers_id column
 * @method     CustomersGotiaAttributesQuery groupByCKey() Group by the c_key column
 * @method     CustomersGotiaAttributesQuery groupByCValue() Group by the c_value column
 *
 * @method     CustomersGotiaAttributesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CustomersGotiaAttributesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CustomersGotiaAttributesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CustomersGotiaAttributesQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method     CustomersGotiaAttributesQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method     CustomersGotiaAttributesQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method     CustomersGotiaAttributes findOne(PropelPDO $con = null) Return the first CustomersGotiaAttributes matching the query
 * @method     CustomersGotiaAttributes findOneOrCreate(PropelPDO $con = null) Return the first CustomersGotiaAttributes matching the query, or a new CustomersGotiaAttributes object populated from the query conditions when no match is found
 *
 * @method     CustomersGotiaAttributes findOneByCustomersId(int $customers_id) Return the first CustomersGotiaAttributes filtered by the customers_id column
 * @method     CustomersGotiaAttributes findOneByCKey(string $c_key) Return the first CustomersGotiaAttributes filtered by the c_key column
 * @method     CustomersGotiaAttributes findOneByCValue(string $c_value) Return the first CustomersGotiaAttributes filtered by the c_value column
 *
 * @method     array findByCustomersId(int $customers_id) Return CustomersGotiaAttributes objects filtered by the customers_id column
 * @method     array findByCKey(string $c_key) Return CustomersGotiaAttributes objects filtered by the c_key column
 * @method     array findByCValue(string $c_value) Return CustomersGotiaAttributes objects filtered by the c_value column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseCustomersGotiaAttributesQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCustomersGotiaAttributesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\CustomersGotiaAttributes', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CustomersGotiaAttributesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CustomersGotiaAttributesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CustomersGotiaAttributesQuery) {
			return $criteria;
		}
		$query = new CustomersGotiaAttributesQuery();
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
	 * @param     array[$customers_id, $c_key] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CustomersGotiaAttributes|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CustomersGotiaAttributesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CustomersGotiaAttributesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    CustomersGotiaAttributes A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `CUSTOMERS_ID`, `C_KEY`, `C_VALUE` FROM `customers_gotia_attributes` WHERE `CUSTOMERS_ID` = :p0 AND `C_KEY` = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new CustomersGotiaAttributes();
			$obj->hydrate($row);
			CustomersGotiaAttributesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    CustomersGotiaAttributes|array|mixed the result, formatted by the current formatter
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
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(CustomersGotiaAttributesPeer::C_KEY, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(CustomersGotiaAttributesPeer::C_KEY, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the customers_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCustomersId(1234); // WHERE customers_id = 1234
	 * $query->filterByCustomersId(array(12, 34)); // WHERE customers_id IN (12, 34)
	 * $query->filterByCustomersId(array('min' => 12)); // WHERE customers_id > 12
	 * </code>
	 *
	 * @see       filterByCustomers()
	 *
	 * @param     mixed $customersId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function filterByCustomersId($customersId = null, $comparison = null)
	{
		if (is_array($customersId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $customersId, $comparison);
	}

	/**
	 * Filter the query on the c_key column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCKey('fooValue');   // WHERE c_key = 'fooValue'
	 * $query->filterByCKey('%fooValue%'); // WHERE c_key LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $cKey The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function filterByCKey($cKey = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($cKey)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $cKey)) {
				$cKey = str_replace('*', '%', $cKey);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CustomersGotiaAttributesPeer::C_KEY, $cKey, $comparison);
	}

	/**
	 * Filter the query on the c_value column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCValue('fooValue');   // WHERE c_value = 'fooValue'
	 * $query->filterByCValue('%fooValue%'); // WHERE c_value LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $cValue The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function filterByCValue($cValue = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($cValue)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $cValue)) {
				$cValue = str_replace('*', '%', $cValue);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CustomersGotiaAttributesPeer::C_VALUE, $cValue, $comparison);
	}

	/**
	 * Filter the query by a related Customers object
	 *
	 * @param     Customers|PropelCollection $customers The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function filterByCustomers($customers, $comparison = null)
	{
		if ($customers instanceof Customers) {
			return $this
				->addUsingAlias(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
		} elseif ($customers instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CustomersGotiaAttributesPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByCustomers() only accepts arguments of type Customers or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Customers relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function joinCustomers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Customers');

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
			$this->addJoinObject($join, 'Customers');
		}

		return $this;
	}

	/**
	 * Use the Customers relation Customers object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    \Hanzo\Model\CustomersQuery A secondary query class using the current class as primary query
	 */
	public function useCustomersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCustomers($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CustomersGotiaAttributes $customersGotiaAttributes Object to remove from the list of results
	 *
	 * @return    CustomersGotiaAttributesQuery The current query, for fluid interface
	 */
	public function prune($customersGotiaAttributes = null)
	{
		if ($customersGotiaAttributes) {
			$this->addCond('pruneCond0', $this->getAliasedColName(CustomersGotiaAttributesPeer::CUSTOMERS_ID), $customersGotiaAttributes->getCustomersId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(CustomersGotiaAttributesPeer::C_KEY), $customersGotiaAttributes->getCKey(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseCustomersGotiaAttributesQuery
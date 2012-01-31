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
use Hanzo\Model\Groups;
use Hanzo\Model\GroupsPeer;
use Hanzo\Model\GroupsQuery;

/**
 * Base class that represents a query for the 'groups' table.
 *
 * 
 *
 * @method     GroupsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     GroupsQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     GroupsQuery orderByDiscount($order = Criteria::ASC) Order by the discount column
 *
 * @method     GroupsQuery groupById() Group by the id column
 * @method     GroupsQuery groupByName() Group by the name column
 * @method     GroupsQuery groupByDiscount() Group by the discount column
 *
 * @method     GroupsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     GroupsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     GroupsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     GroupsQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method     GroupsQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method     GroupsQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method     Groups findOne(PropelPDO $con = null) Return the first Groups matching the query
 * @method     Groups findOneOrCreate(PropelPDO $con = null) Return the first Groups matching the query, or a new Groups object populated from the query conditions when no match is found
 *
 * @method     Groups findOneById(int $id) Return the first Groups filtered by the id column
 * @method     Groups findOneByName(string $name) Return the first Groups filtered by the name column
 * @method     Groups findOneByDiscount(string $discount) Return the first Groups filtered by the discount column
 *
 * @method     array findById(int $id) Return Groups objects filtered by the id column
 * @method     array findByName(string $name) Return Groups objects filtered by the name column
 * @method     array findByDiscount(string $discount) Return Groups objects filtered by the discount column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseGroupsQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseGroupsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\Groups', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new GroupsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    GroupsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof GroupsQuery) {
			return $criteria;
		}
		$query = new GroupsQuery();
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
	 * @return    Groups|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = GroupsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(GroupsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    Groups A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `ID`, `NAME`, `DISCOUNT` FROM `groups` WHERE `ID` = :p0';
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
			$obj = new Groups();
			$obj->hydrate($row);
			GroupsPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    Groups|array|mixed the result, formatted by the current formatter
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
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(GroupsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(GroupsPeer::ID, $keys, Criteria::IN);
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
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(GroupsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the name column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
	 * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $name The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function filterByName($name = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($name)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $name)) {
				$name = str_replace('*', '%', $name);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(GroupsPeer::NAME, $name, $comparison);
	}

	/**
	 * Filter the query on the discount column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDiscount(1234); // WHERE discount = 1234
	 * $query->filterByDiscount(array(12, 34)); // WHERE discount IN (12, 34)
	 * $query->filterByDiscount(array('min' => 12)); // WHERE discount > 12
	 * </code>
	 *
	 * @param     mixed $discount The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function filterByDiscount($discount = null, $comparison = null)
	{
		if (is_array($discount)) {
			$useMinMax = false;
			if (isset($discount['min'])) {
				$this->addUsingAlias(GroupsPeer::DISCOUNT, $discount['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($discount['max'])) {
				$this->addUsingAlias(GroupsPeer::DISCOUNT, $discount['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(GroupsPeer::DISCOUNT, $discount, $comparison);
	}

	/**
	 * Filter the query by a related Customers object
	 *
	 * @param     Customers $customers  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function filterByCustomers($customers, $comparison = null)
	{
		if ($customers instanceof Customers) {
			return $this
				->addUsingAlias(GroupsPeer::ID, $customers->getGroupsId(), $comparison);
		} elseif ($customers instanceof PropelCollection) {
			return $this
				->useCustomersQuery()
				->filterByPrimaryKeys($customers->getPrimaryKeys())
				->endUse();
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
	 * @return    GroupsQuery The current query, for fluid interface
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
	 * @param     Groups $groups Object to remove from the list of results
	 *
	 * @return    GroupsQuery The current query, for fluid interface
	 */
	public function prune($groups = null)
	{
		if ($groups) {
			$this->addUsingAlias(GroupsPeer::ID, $groups->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseGroupsQuery
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
use Hanzo\Model\ConsultantsInfo;
use Hanzo\Model\ConsultantsInfoPeer;
use Hanzo\Model\ConsultantsInfoQuery;
use Hanzo\Model\Customers;

/**
 * Base class that represents a query for the 'consultants_info' table.
 *
 * 
 *
 * @method     ConsultantsInfoQuery orderByConsultantsId($order = Criteria::ASC) Order by the consultants_id column
 * @method     ConsultantsInfoQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     ConsultantsInfoQuery orderByMaxNotified($order = Criteria::ASC) Order by the max_notified column
 *
 * @method     ConsultantsInfoQuery groupByConsultantsId() Group by the consultants_id column
 * @method     ConsultantsInfoQuery groupByDescription() Group by the description column
 * @method     ConsultantsInfoQuery groupByMaxNotified() Group by the max_notified column
 *
 * @method     ConsultantsInfoQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ConsultantsInfoQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ConsultantsInfoQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ConsultantsInfoQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method     ConsultantsInfoQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method     ConsultantsInfoQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method     ConsultantsInfo findOne(PropelPDO $con = null) Return the first ConsultantsInfo matching the query
 * @method     ConsultantsInfo findOneOrCreate(PropelPDO $con = null) Return the first ConsultantsInfo matching the query, or a new ConsultantsInfo object populated from the query conditions when no match is found
 *
 * @method     ConsultantsInfo findOneByConsultantsId(int $consultants_id) Return the first ConsultantsInfo filtered by the consultants_id column
 * @method     ConsultantsInfo findOneByDescription(string $description) Return the first ConsultantsInfo filtered by the description column
 * @method     ConsultantsInfo findOneByMaxNotified(boolean $max_notified) Return the first ConsultantsInfo filtered by the max_notified column
 *
 * @method     array findByConsultantsId(int $consultants_id) Return ConsultantsInfo objects filtered by the consultants_id column
 * @method     array findByDescription(string $description) Return ConsultantsInfo objects filtered by the description column
 * @method     array findByMaxNotified(boolean $max_notified) Return ConsultantsInfo objects filtered by the max_notified column
 *
 * @package    propel.generator.src.Hanzo.Model.om
 */
abstract class BaseConsultantsInfoQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseConsultantsInfoQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\ConsultantsInfo', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ConsultantsInfoQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ConsultantsInfoQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ConsultantsInfoQuery) {
			return $criteria;
		}
		$query = new ConsultantsInfoQuery();
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
	 * @return    ConsultantsInfo|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = ConsultantsInfoPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(ConsultantsInfoPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    ConsultantsInfo A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `CONSULTANTS_ID`, `DESCRIPTION`, `MAX_NOTIFIED` FROM `consultants_info` WHERE `CONSULTANTS_ID` = :p0';
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
			$obj = new ConsultantsInfo();
			$obj->hydrate($row);
			ConsultantsInfoPeer::addInstanceToPool($obj, (string) $row[0]);
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
	 * @return    ConsultantsInfo|array|mixed the result, formatted by the current formatter
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
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(ConsultantsInfoPeer::CONSULTANTS_ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(ConsultantsInfoPeer::CONSULTANTS_ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the consultants_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByConsultantsId(1234); // WHERE consultants_id = 1234
	 * $query->filterByConsultantsId(array(12, 34)); // WHERE consultants_id IN (12, 34)
	 * $query->filterByConsultantsId(array('min' => 12)); // WHERE consultants_id > 12
	 * </code>
	 *
	 * @see       filterByCustomers()
	 *
	 * @param     mixed $consultantsId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function filterByConsultantsId($consultantsId = null, $comparison = null)
	{
		if (is_array($consultantsId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(ConsultantsInfoPeer::CONSULTANTS_ID, $consultantsId, $comparison);
	}

	/**
	 * Filter the query on the description column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
	 * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $description The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function filterByDescription($description = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($description)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $description)) {
				$description = str_replace('*', '%', $description);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ConsultantsInfoPeer::DESCRIPTION, $description, $comparison);
	}

	/**
	 * Filter the query on the max_notified column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMaxNotified(true); // WHERE max_notified = true
	 * $query->filterByMaxNotified('yes'); // WHERE max_notified = true
	 * </code>
	 *
	 * @param     boolean|string $maxNotified The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function filterByMaxNotified($maxNotified = null, $comparison = null)
	{
		if (is_string($maxNotified)) {
			$max_notified = in_array(strtolower($maxNotified), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(ConsultantsInfoPeer::MAX_NOTIFIED, $maxNotified, $comparison);
	}

	/**
	 * Filter the query by a related Customers object
	 *
	 * @param     Customers|PropelCollection $customers The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function filterByCustomers($customers, $comparison = null)
	{
		if ($customers instanceof Customers) {
			return $this
				->addUsingAlias(ConsultantsInfoPeer::CONSULTANTS_ID, $customers->getId(), $comparison);
		} elseif ($customers instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(ConsultantsInfoPeer::CONSULTANTS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
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
	 * @param     ConsultantsInfo $consultantsInfo Object to remove from the list of results
	 *
	 * @return    ConsultantsInfoQuery The current query, for fluid interface
	 */
	public function prune($consultantsInfo = null)
	{
		if ($consultantsInfo) {
			$this->addUsingAlias(ConsultantsInfoPeer::CONSULTANTS_ID, $consultantsInfo->getConsultantsId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseConsultantsInfoQuery
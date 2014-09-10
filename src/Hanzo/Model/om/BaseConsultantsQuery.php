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
use Hanzo\Model\Consultants;
use Hanzo\Model\ConsultantsPeer;
use Hanzo\Model\ConsultantsQuery;
use Hanzo\Model\Customers;

/**
 * @method ConsultantsQuery orderByInitials($order = Criteria::ASC) Order by the initials column
 * @method ConsultantsQuery orderByInfo($order = Criteria::ASC) Order by the info column
 * @method ConsultantsQuery orderByEventNotes($order = Criteria::ASC) Order by the event_notes column
 * @method ConsultantsQuery orderByHideInfo($order = Criteria::ASC) Order by the hide_info column
 * @method ConsultantsQuery orderByMaxNotified($order = Criteria::ASC) Order by the max_notified column
 * @method ConsultantsQuery orderById($order = Criteria::ASC) Order by the id column
 *
 * @method ConsultantsQuery groupByInitials() Group by the initials column
 * @method ConsultantsQuery groupByInfo() Group by the info column
 * @method ConsultantsQuery groupByEventNotes() Group by the event_notes column
 * @method ConsultantsQuery groupByHideInfo() Group by the hide_info column
 * @method ConsultantsQuery groupByMaxNotified() Group by the max_notified column
 * @method ConsultantsQuery groupById() Group by the id column
 *
 * @method ConsultantsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ConsultantsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ConsultantsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ConsultantsQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method ConsultantsQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method ConsultantsQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method Consultants findOne(PropelPDO $con = null) Return the first Consultants matching the query
 * @method Consultants findOneOrCreate(PropelPDO $con = null) Return the first Consultants matching the query, or a new Consultants object populated from the query conditions when no match is found
 *
 * @method Consultants findOneByInitials(string $initials) Return the first Consultants filtered by the initials column
 * @method Consultants findOneByInfo(string $info) Return the first Consultants filtered by the info column
 * @method Consultants findOneByEventNotes(string $event_notes) Return the first Consultants filtered by the event_notes column
 * @method Consultants findOneByHideInfo(boolean $hide_info) Return the first Consultants filtered by the hide_info column
 * @method Consultants findOneByMaxNotified(boolean $max_notified) Return the first Consultants filtered by the max_notified column
 *
 * @method array findByInitials(string $initials) Return Consultants objects filtered by the initials column
 * @method array findByInfo(string $info) Return Consultants objects filtered by the info column
 * @method array findByEventNotes(string $event_notes) Return Consultants objects filtered by the event_notes column
 * @method array findByHideInfo(boolean $hide_info) Return Consultants objects filtered by the hide_info column
 * @method array findByMaxNotified(boolean $max_notified) Return Consultants objects filtered by the max_notified column
 * @method array findById(int $id) Return Consultants objects filtered by the id column
 */
abstract class BaseConsultantsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseConsultantsQuery object.
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
            $modelName = 'Hanzo\\Model\\Consultants';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new ConsultantsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ConsultantsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ConsultantsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ConsultantsQuery) {
            return $criteria;
        }
        $query = new ConsultantsQuery(null, null, $modelAlias);

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
     * @return   Consultants|Consultants[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ConsultantsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ConsultantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Consultants A model object, or null if the key is not found
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
     * @return                 Consultants A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `initials`, `info`, `event_notes`, `hide_info`, `max_notified`, `id` FROM `consultants` WHERE `id` = :p0';
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
            $obj = new Consultants();
            $obj->hydrate($row);
            ConsultantsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Consultants|Consultants[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Consultants[]|mixed the list of results, formatted by the current formatter
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
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ConsultantsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ConsultantsPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the initials column
     *
     * Example usage:
     * <code>
     * $query->filterByInitials('fooValue');   // WHERE initials = 'fooValue'
     * $query->filterByInitials('%fooValue%'); // WHERE initials LIKE '%fooValue%'
     * </code>
     *
     * @param     string $initials The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByInitials($initials = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($initials)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $initials)) {
                $initials = str_replace('*', '%', $initials);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ConsultantsPeer::INITIALS, $initials, $comparison);
    }

    /**
     * Filter the query on the info column
     *
     * Example usage:
     * <code>
     * $query->filterByInfo('fooValue');   // WHERE info = 'fooValue'
     * $query->filterByInfo('%fooValue%'); // WHERE info LIKE '%fooValue%'
     * </code>
     *
     * @param     string $info The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByInfo($info = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($info)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $info)) {
                $info = str_replace('*', '%', $info);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ConsultantsPeer::INFO, $info, $comparison);
    }

    /**
     * Filter the query on the event_notes column
     *
     * Example usage:
     * <code>
     * $query->filterByEventNotes('fooValue');   // WHERE event_notes = 'fooValue'
     * $query->filterByEventNotes('%fooValue%'); // WHERE event_notes LIKE '%fooValue%'
     * </code>
     *
     * @param     string $eventNotes The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByEventNotes($eventNotes = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($eventNotes)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $eventNotes)) {
                $eventNotes = str_replace('*', '%', $eventNotes);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ConsultantsPeer::EVENT_NOTES, $eventNotes, $comparison);
    }

    /**
     * Filter the query on the hide_info column
     *
     * Example usage:
     * <code>
     * $query->filterByHideInfo(true); // WHERE hide_info = true
     * $query->filterByHideInfo('yes'); // WHERE hide_info = true
     * </code>
     *
     * @param     boolean|string $hideInfo The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByHideInfo($hideInfo = null, $comparison = null)
    {
        if (is_string($hideInfo)) {
            $hideInfo = in_array(strtolower($hideInfo), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ConsultantsPeer::HIDE_INFO, $hideInfo, $comparison);
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
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterByMaxNotified($maxNotified = null, $comparison = null)
    {
        if (is_string($maxNotified)) {
            $maxNotified = in_array(strtolower($maxNotified), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ConsultantsPeer::MAX_NOTIFIED, $maxNotified, $comparison);
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
     * @see       filterByCustomers()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ConsultantsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ConsultantsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ConsultantsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query by a related Customers object
     *
     * @param   Customers|PropelObjectCollection $customers The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ConsultantsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCustomers($customers, $comparison = null)
    {
        if ($customers instanceof Customers) {
            return $this
                ->addUsingAlias(ConsultantsPeer::ID, $customers->getId(), $comparison);
        } elseif ($customers instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ConsultantsPeer::ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function joinCustomers($relationAlias = null, $joinType = 'LEFT JOIN')
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
        if ($relationAlias) {
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
     * @return   \Hanzo\Model\CustomersQuery A secondary query class using the current class as primary query
     */
    public function useCustomersQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCustomers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Consultants $consultants Object to remove from the list of results
     *
     * @return ConsultantsQuery The current query, for fluid interface
     */
    public function prune($consultants = null)
    {
        if ($consultants) {
            $this->addUsingAlias(ConsultantsPeer::ID, $consultants->getId(), Criteria::NOT_EQUAL);
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

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
use Hanzo\Model\Consultants;
use Hanzo\Model\Customers;
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\EventsPeer;
use Hanzo\Model\EventsQuery;
use Hanzo\Model\Orders;

/**
 * @method EventsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method EventsQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method EventsQuery orderByKey($order = Criteria::ASC) Order by the key column
 * @method EventsQuery orderByConsultantsId($order = Criteria::ASC) Order by the consultants_id column
 * @method EventsQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method EventsQuery orderByEventDate($order = Criteria::ASC) Order by the event_date column
 * @method EventsQuery orderByHost($order = Criteria::ASC) Order by the host column
 * @method EventsQuery orderByAddressLine1($order = Criteria::ASC) Order by the address_line_1 column
 * @method EventsQuery orderByAddressLine2($order = Criteria::ASC) Order by the address_line_2 column
 * @method EventsQuery orderByPostalCode($order = Criteria::ASC) Order by the postal_code column
 * @method EventsQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method EventsQuery orderByPhone($order = Criteria::ASC) Order by the phone column
 * @method EventsQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method EventsQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method EventsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method EventsQuery orderByIsOpen($order = Criteria::ASC) Order by the is_open column
 * @method EventsQuery orderByNotifyHostess($order = Criteria::ASC) Order by the notify_hostess column
 * @method EventsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method EventsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method EventsQuery groupById() Group by the id column
 * @method EventsQuery groupByCode() Group by the code column
 * @method EventsQuery groupByKey() Group by the key column
 * @method EventsQuery groupByConsultantsId() Group by the consultants_id column
 * @method EventsQuery groupByCustomersId() Group by the customers_id column
 * @method EventsQuery groupByEventDate() Group by the event_date column
 * @method EventsQuery groupByHost() Group by the host column
 * @method EventsQuery groupByAddressLine1() Group by the address_line_1 column
 * @method EventsQuery groupByAddressLine2() Group by the address_line_2 column
 * @method EventsQuery groupByPostalCode() Group by the postal_code column
 * @method EventsQuery groupByCity() Group by the city column
 * @method EventsQuery groupByPhone() Group by the phone column
 * @method EventsQuery groupByEmail() Group by the email column
 * @method EventsQuery groupByDescription() Group by the description column
 * @method EventsQuery groupByType() Group by the type column
 * @method EventsQuery groupByIsOpen() Group by the is_open column
 * @method EventsQuery groupByNotifyHostess() Group by the notify_hostess column
 * @method EventsQuery groupByCreatedAt() Group by the created_at column
 * @method EventsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method EventsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method EventsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method EventsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method EventsQuery leftJoinConsultants($relationAlias = null) Adds a LEFT JOIN clause to the query using the Consultants relation
 * @method EventsQuery rightJoinConsultants($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Consultants relation
 * @method EventsQuery innerJoinConsultants($relationAlias = null) Adds a INNER JOIN clause to the query using the Consultants relation
 *
 * @method EventsQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method EventsQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method EventsQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method EventsQuery leftJoinEventsParticipants($relationAlias = null) Adds a LEFT JOIN clause to the query using the EventsParticipants relation
 * @method EventsQuery rightJoinEventsParticipants($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EventsParticipants relation
 * @method EventsQuery innerJoinEventsParticipants($relationAlias = null) Adds a INNER JOIN clause to the query using the EventsParticipants relation
 *
 * @method EventsQuery leftJoinOrders($relationAlias = null) Adds a LEFT JOIN clause to the query using the Orders relation
 * @method EventsQuery rightJoinOrders($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Orders relation
 * @method EventsQuery innerJoinOrders($relationAlias = null) Adds a INNER JOIN clause to the query using the Orders relation
 *
 * @method Events findOne(PropelPDO $con = null) Return the first Events matching the query
 * @method Events findOneOrCreate(PropelPDO $con = null) Return the first Events matching the query, or a new Events object populated from the query conditions when no match is found
 *
 * @method Events findOneById(int $id) Return the first Events filtered by the id column
 * @method Events findOneByCode(string $code) Return the first Events filtered by the code column
 * @method Events findOneByKey(string $key) Return the first Events filtered by the key column
 * @method Events findOneByConsultantsId(int $consultants_id) Return the first Events filtered by the consultants_id column
 * @method Events findOneByCustomersId(int $customers_id) Return the first Events filtered by the customers_id column
 * @method Events findOneByEventDate(string $event_date) Return the first Events filtered by the event_date column
 * @method Events findOneByHost(string $host) Return the first Events filtered by the host column
 * @method Events findOneByAddressLine1(string $address_line_1) Return the first Events filtered by the address_line_1 column
 * @method Events findOneByAddressLine2(string $address_line_2) Return the first Events filtered by the address_line_2 column
 * @method Events findOneByPostalCode(string $postal_code) Return the first Events filtered by the postal_code column
 * @method Events findOneByCity(string $city) Return the first Events filtered by the city column
 * @method Events findOneByPhone(string $phone) Return the first Events filtered by the phone column
 * @method Events findOneByEmail(string $email) Return the first Events filtered by the email column
 * @method Events findOneByDescription(string $description) Return the first Events filtered by the description column
 * @method Events findOneByType(string $type) Return the first Events filtered by the type column
 * @method Events findOneByIsOpen(boolean $is_open) Return the first Events filtered by the is_open column
 * @method Events findOneByNotifyHostess(boolean $notify_hostess) Return the first Events filtered by the notify_hostess column
 * @method Events findOneByCreatedAt(string $created_at) Return the first Events filtered by the created_at column
 * @method Events findOneByUpdatedAt(string $updated_at) Return the first Events filtered by the updated_at column
 *
 * @method array findById(int $id) Return Events objects filtered by the id column
 * @method array findByCode(string $code) Return Events objects filtered by the code column
 * @method array findByKey(string $key) Return Events objects filtered by the key column
 * @method array findByConsultantsId(int $consultants_id) Return Events objects filtered by the consultants_id column
 * @method array findByCustomersId(int $customers_id) Return Events objects filtered by the customers_id column
 * @method array findByEventDate(string $event_date) Return Events objects filtered by the event_date column
 * @method array findByHost(string $host) Return Events objects filtered by the host column
 * @method array findByAddressLine1(string $address_line_1) Return Events objects filtered by the address_line_1 column
 * @method array findByAddressLine2(string $address_line_2) Return Events objects filtered by the address_line_2 column
 * @method array findByPostalCode(string $postal_code) Return Events objects filtered by the postal_code column
 * @method array findByCity(string $city) Return Events objects filtered by the city column
 * @method array findByPhone(string $phone) Return Events objects filtered by the phone column
 * @method array findByEmail(string $email) Return Events objects filtered by the email column
 * @method array findByDescription(string $description) Return Events objects filtered by the description column
 * @method array findByType(string $type) Return Events objects filtered by the type column
 * @method array findByIsOpen(boolean $is_open) Return Events objects filtered by the is_open column
 * @method array findByNotifyHostess(boolean $notify_hostess) Return Events objects filtered by the notify_hostess column
 * @method array findByCreatedAt(string $created_at) Return Events objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Events objects filtered by the updated_at column
 */
abstract class BaseEventsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseEventsQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\Events', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new EventsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     EventsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return EventsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof EventsQuery) {
            return $criteria;
        }
        $query = new EventsQuery();
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
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Events|Events[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = EventsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(EventsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Events A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `ID`, `CODE`, `KEY`, `CONSULTANTS_ID`, `CUSTOMERS_ID`, `EVENT_DATE`, `HOST`, `ADDRESS_LINE_1`, `ADDRESS_LINE_2`, `POSTAL_CODE`, `CITY`, `PHONE`, `EMAIL`, `DESCRIPTION`, `TYPE`, `IS_OPEN`, `NOTIFY_HOSTESS`, `CREATED_AT`, `UPDATED_AT` FROM `events` WHERE `ID` = :p0';
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
            $obj = new Events();
            $obj->hydrate($row);
            EventsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Events|Events[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Events[]|mixed the list of results, formatted by the current formatter
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(EventsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(EventsPeer::ID, $keys, Criteria::IN);
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(EventsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the key column
     *
     * Example usage:
     * <code>
     * $query->filterByKey('fooValue');   // WHERE key = 'fooValue'
     * $query->filterByKey('%fooValue%'); // WHERE key LIKE '%fooValue%'
     * </code>
     *
     * @param     string $key The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByKey($key = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($key)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $key)) {
                $key = str_replace('*', '%', $key);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::KEY, $key, $comparison);
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
     * @see       filterByConsultants()
     *
     * @param     mixed $consultantsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByConsultantsId($consultantsId = null, $comparison = null)
    {
        if (is_array($consultantsId)) {
            $useMinMax = false;
            if (isset($consultantsId['min'])) {
                $this->addUsingAlias(EventsPeer::CONSULTANTS_ID, $consultantsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($consultantsId['max'])) {
                $this->addUsingAlias(EventsPeer::CONSULTANTS_ID, $consultantsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::CONSULTANTS_ID, $consultantsId, $comparison);
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByCustomersId($customersId = null, $comparison = null)
    {
        if (is_array($customersId)) {
            $useMinMax = false;
            if (isset($customersId['min'])) {
                $this->addUsingAlias(EventsPeer::CUSTOMERS_ID, $customersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customersId['max'])) {
                $this->addUsingAlias(EventsPeer::CUSTOMERS_ID, $customersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::CUSTOMERS_ID, $customersId, $comparison);
    }

    /**
     * Filter the query on the event_date column
     *
     * Example usage:
     * <code>
     * $query->filterByEventDate('2011-03-14'); // WHERE event_date = '2011-03-14'
     * $query->filterByEventDate('now'); // WHERE event_date = '2011-03-14'
     * $query->filterByEventDate(array('max' => 'yesterday')); // WHERE event_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $eventDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByEventDate($eventDate = null, $comparison = null)
    {
        if (is_array($eventDate)) {
            $useMinMax = false;
            if (isset($eventDate['min'])) {
                $this->addUsingAlias(EventsPeer::EVENT_DATE, $eventDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($eventDate['max'])) {
                $this->addUsingAlias(EventsPeer::EVENT_DATE, $eventDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::EVENT_DATE, $eventDate, $comparison);
    }

    /**
     * Filter the query on the host column
     *
     * Example usage:
     * <code>
     * $query->filterByHost('fooValue');   // WHERE host = 'fooValue'
     * $query->filterByHost('%fooValue%'); // WHERE host LIKE '%fooValue%'
     * </code>
     *
     * @param     string $host The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByHost($host = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($host)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $host)) {
                $host = str_replace('*', '%', $host);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::HOST, $host, $comparison);
    }

    /**
     * Filter the query on the address_line_1 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddressLine1('fooValue');   // WHERE address_line_1 = 'fooValue'
     * $query->filterByAddressLine1('%fooValue%'); // WHERE address_line_1 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $addressLine1 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByAddressLine1($addressLine1 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($addressLine1)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $addressLine1)) {
                $addressLine1 = str_replace('*', '%', $addressLine1);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::ADDRESS_LINE_1, $addressLine1, $comparison);
    }

    /**
     * Filter the query on the address_line_2 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddressLine2('fooValue');   // WHERE address_line_2 = 'fooValue'
     * $query->filterByAddressLine2('%fooValue%'); // WHERE address_line_2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $addressLine2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByAddressLine2($addressLine2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($addressLine2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $addressLine2)) {
                $addressLine2 = str_replace('*', '%', $addressLine2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::ADDRESS_LINE_2, $addressLine2, $comparison);
    }

    /**
     * Filter the query on the postal_code column
     *
     * Example usage:
     * <code>
     * $query->filterByPostalCode('fooValue');   // WHERE postal_code = 'fooValue'
     * $query->filterByPostalCode('%fooValue%'); // WHERE postal_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $postalCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByPostalCode($postalCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($postalCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $postalCode)) {
                $postalCode = str_replace('*', '%', $postalCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::POSTAL_CODE, $postalCode, $comparison);
    }

    /**
     * Filter the query on the city column
     *
     * Example usage:
     * <code>
     * $query->filterByCity('fooValue');   // WHERE city = 'fooValue'
     * $query->filterByCity('%fooValue%'); // WHERE city LIKE '%fooValue%'
     * </code>
     *
     * @param     string $city The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByCity($city = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($city)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $city)) {
                $city = str_replace('*', '%', $city);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::CITY, $city, $comparison);
    }

    /**
     * Filter the query on the phone column
     *
     * Example usage:
     * <code>
     * $query->filterByPhone('fooValue');   // WHERE phone = 'fooValue'
     * $query->filterByPhone('%fooValue%'); // WHERE phone LIKE '%fooValue%'
     * </code>
     *
     * @param     string $phone The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByPhone($phone = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($phone)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $phone)) {
                $phone = str_replace('*', '%', $phone);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::PHONE, $phone, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsPeer::EMAIL, $email, $comparison);
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
     * @return EventsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EventsPeer::DESCRIPTION, $description, $comparison);
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
     * @return EventsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EventsPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the is_open column
     *
     * Example usage:
     * <code>
     * $query->filterByIsOpen(true); // WHERE is_open = true
     * $query->filterByIsOpen('yes'); // WHERE is_open = true
     * </code>
     *
     * @param     boolean|string $isOpen The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByIsOpen($isOpen = null, $comparison = null)
    {
        if (is_string($isOpen)) {
            $is_open = in_array(strtolower($isOpen), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(EventsPeer::IS_OPEN, $isOpen, $comparison);
    }

    /**
     * Filter the query on the notify_hostess column
     *
     * Example usage:
     * <code>
     * $query->filterByNotifyHostess(true); // WHERE notify_hostess = true
     * $query->filterByNotifyHostess('yes'); // WHERE notify_hostess = true
     * </code>
     *
     * @param     boolean|string $notifyHostess The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByNotifyHostess($notifyHostess = null, $comparison = null)
    {
        if (is_string($notifyHostess)) {
            $notify_hostess = in_array(strtolower($notifyHostess), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(EventsPeer::NOTIFY_HOSTESS, $notifyHostess, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(EventsPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(EventsPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(EventsPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(EventsPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related Consultants object
     *
     * @param   Consultants|PropelObjectCollection $consultants The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   EventsQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByConsultants($consultants, $comparison = null)
    {
        if ($consultants instanceof Consultants) {
            return $this
                ->addUsingAlias(EventsPeer::CONSULTANTS_ID, $consultants->getId(), $comparison);
        } elseif ($consultants instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EventsPeer::CONSULTANTS_ID, $consultants->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByConsultants() only accepts arguments of type Consultants or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Consultants relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function joinConsultants($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Consultants');

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
            $this->addJoinObject($join, 'Consultants');
        }

        return $this;
    }

    /**
     * Use the Consultants relation Consultants object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ConsultantsQuery A secondary query class using the current class as primary query
     */
    public function useConsultantsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinConsultants($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Consultants', '\Hanzo\Model\ConsultantsQuery');
    }

    /**
     * Filter the query by a related Customers object
     *
     * @param   Customers|PropelObjectCollection $customers The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   EventsQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCustomers($customers, $comparison = null)
    {
        if ($customers instanceof Customers) {
            return $this
                ->addUsingAlias(EventsPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
        } elseif ($customers instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EventsPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return EventsQuery The current query, for fluid interface
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
    public function useCustomersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
    }

    /**
     * Filter the query by a related EventsParticipants object
     *
     * @param   EventsParticipants|PropelObjectCollection $eventsParticipants  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   EventsQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByEventsParticipants($eventsParticipants, $comparison = null)
    {
        if ($eventsParticipants instanceof EventsParticipants) {
            return $this
                ->addUsingAlias(EventsPeer::ID, $eventsParticipants->getEventsId(), $comparison);
        } elseif ($eventsParticipants instanceof PropelObjectCollection) {
            return $this
                ->useEventsParticipantsQuery()
                ->filterByPrimaryKeys($eventsParticipants->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByEventsParticipants() only accepts arguments of type EventsParticipants or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the EventsParticipants relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function joinEventsParticipants($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('EventsParticipants');

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
            $this->addJoinObject($join, 'EventsParticipants');
        }

        return $this;
    }

    /**
     * Use the EventsParticipants relation EventsParticipants object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\EventsParticipantsQuery A secondary query class using the current class as primary query
     */
    public function useEventsParticipantsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinEventsParticipants($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'EventsParticipants', '\Hanzo\Model\EventsParticipantsQuery');
    }

    /**
     * Filter the query by a related Orders object
     *
     * @param   Orders|PropelObjectCollection $orders  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   EventsQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByOrders($orders, $comparison = null)
    {
        if ($orders instanceof Orders) {
            return $this
                ->addUsingAlias(EventsPeer::ID, $orders->getEventsId(), $comparison);
        } elseif ($orders instanceof PropelObjectCollection) {
            return $this
                ->useOrdersQuery()
                ->filterByPrimaryKeys($orders->getPrimaryKeys())
                ->endUse();
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
     * @return EventsQuery The current query, for fluid interface
     */
    public function joinOrders($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useOrdersQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinOrders($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Orders', '\Hanzo\Model\OrdersQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Events $events Object to remove from the list of results
     *
     * @return EventsQuery The current query, for fluid interface
     */
    public function prune($events = null)
    {
        if ($events) {
            $this->addUsingAlias(EventsPeer::ID, $events->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     EventsQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(EventsPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     EventsQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(EventsPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     EventsQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(EventsPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     EventsQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(EventsPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     EventsQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(EventsPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     EventsQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(EventsPeer::CREATED_AT);
    }
}

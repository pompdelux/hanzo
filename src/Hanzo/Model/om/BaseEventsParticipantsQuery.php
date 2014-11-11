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
use Hanzo\Model\Events;
use Hanzo\Model\EventsParticipants;
use Hanzo\Model\EventsParticipantsPeer;
use Hanzo\Model\EventsParticipantsQuery;

/**
 * @method EventsParticipantsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method EventsParticipantsQuery orderByEventsId($order = Criteria::ASC) Order by the events_id column
 * @method EventsParticipantsQuery orderByKey($order = Criteria::ASC) Order by the key column
 * @method EventsParticipantsQuery orderByInvitedBy($order = Criteria::ASC) Order by the invited_by column
 * @method EventsParticipantsQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method EventsParticipantsQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 * @method EventsParticipantsQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method EventsParticipantsQuery orderByPhone($order = Criteria::ASC) Order by the phone column
 * @method EventsParticipantsQuery orderByTellAFriend($order = Criteria::ASC) Order by the tell_a_friend column
 * @method EventsParticipantsQuery orderByNotifyBySms($order = Criteria::ASC) Order by the notify_by_sms column
 * @method EventsParticipantsQuery orderBySmsSendAt($order = Criteria::ASC) Order by the sms_send_at column
 * @method EventsParticipantsQuery orderByHasAccepted($order = Criteria::ASC) Order by the has_accepted column
 * @method EventsParticipantsQuery orderByExpiresAt($order = Criteria::ASC) Order by the expires_at column
 * @method EventsParticipantsQuery orderByRespondedAt($order = Criteria::ASC) Order by the responded_at column
 * @method EventsParticipantsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method EventsParticipantsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method EventsParticipantsQuery groupById() Group by the id column
 * @method EventsParticipantsQuery groupByEventsId() Group by the events_id column
 * @method EventsParticipantsQuery groupByKey() Group by the key column
 * @method EventsParticipantsQuery groupByInvitedBy() Group by the invited_by column
 * @method EventsParticipantsQuery groupByFirstName() Group by the first_name column
 * @method EventsParticipantsQuery groupByLastName() Group by the last_name column
 * @method EventsParticipantsQuery groupByEmail() Group by the email column
 * @method EventsParticipantsQuery groupByPhone() Group by the phone column
 * @method EventsParticipantsQuery groupByTellAFriend() Group by the tell_a_friend column
 * @method EventsParticipantsQuery groupByNotifyBySms() Group by the notify_by_sms column
 * @method EventsParticipantsQuery groupBySmsSendAt() Group by the sms_send_at column
 * @method EventsParticipantsQuery groupByHasAccepted() Group by the has_accepted column
 * @method EventsParticipantsQuery groupByExpiresAt() Group by the expires_at column
 * @method EventsParticipantsQuery groupByRespondedAt() Group by the responded_at column
 * @method EventsParticipantsQuery groupByCreatedAt() Group by the created_at column
 * @method EventsParticipantsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method EventsParticipantsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method EventsParticipantsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method EventsParticipantsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method EventsParticipantsQuery leftJoinEvents($relationAlias = null) Adds a LEFT JOIN clause to the query using the Events relation
 * @method EventsParticipantsQuery rightJoinEvents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Events relation
 * @method EventsParticipantsQuery innerJoinEvents($relationAlias = null) Adds a INNER JOIN clause to the query using the Events relation
 *
 * @method EventsParticipants findOne(PropelPDO $con = null) Return the first EventsParticipants matching the query
 * @method EventsParticipants findOneOrCreate(PropelPDO $con = null) Return the first EventsParticipants matching the query, or a new EventsParticipants object populated from the query conditions when no match is found
 *
 * @method EventsParticipants findOneByEventsId(int $events_id) Return the first EventsParticipants filtered by the events_id column
 * @method EventsParticipants findOneByKey(string $key) Return the first EventsParticipants filtered by the key column
 * @method EventsParticipants findOneByInvitedBy(int $invited_by) Return the first EventsParticipants filtered by the invited_by column
 * @method EventsParticipants findOneByFirstName(string $first_name) Return the first EventsParticipants filtered by the first_name column
 * @method EventsParticipants findOneByLastName(string $last_name) Return the first EventsParticipants filtered by the last_name column
 * @method EventsParticipants findOneByEmail(string $email) Return the first EventsParticipants filtered by the email column
 * @method EventsParticipants findOneByPhone(string $phone) Return the first EventsParticipants filtered by the phone column
 * @method EventsParticipants findOneByTellAFriend(boolean $tell_a_friend) Return the first EventsParticipants filtered by the tell_a_friend column
 * @method EventsParticipants findOneByNotifyBySms(boolean $notify_by_sms) Return the first EventsParticipants filtered by the notify_by_sms column
 * @method EventsParticipants findOneBySmsSendAt(string $sms_send_at) Return the first EventsParticipants filtered by the sms_send_at column
 * @method EventsParticipants findOneByHasAccepted(boolean $has_accepted) Return the first EventsParticipants filtered by the has_accepted column
 * @method EventsParticipants findOneByExpiresAt(string $expires_at) Return the first EventsParticipants filtered by the expires_at column
 * @method EventsParticipants findOneByRespondedAt(string $responded_at) Return the first EventsParticipants filtered by the responded_at column
 * @method EventsParticipants findOneByCreatedAt(string $created_at) Return the first EventsParticipants filtered by the created_at column
 * @method EventsParticipants findOneByUpdatedAt(string $updated_at) Return the first EventsParticipants filtered by the updated_at column
 *
 * @method array findById(int $id) Return EventsParticipants objects filtered by the id column
 * @method array findByEventsId(int $events_id) Return EventsParticipants objects filtered by the events_id column
 * @method array findByKey(string $key) Return EventsParticipants objects filtered by the key column
 * @method array findByInvitedBy(int $invited_by) Return EventsParticipants objects filtered by the invited_by column
 * @method array findByFirstName(string $first_name) Return EventsParticipants objects filtered by the first_name column
 * @method array findByLastName(string $last_name) Return EventsParticipants objects filtered by the last_name column
 * @method array findByEmail(string $email) Return EventsParticipants objects filtered by the email column
 * @method array findByPhone(string $phone) Return EventsParticipants objects filtered by the phone column
 * @method array findByTellAFriend(boolean $tell_a_friend) Return EventsParticipants objects filtered by the tell_a_friend column
 * @method array findByNotifyBySms(boolean $notify_by_sms) Return EventsParticipants objects filtered by the notify_by_sms column
 * @method array findBySmsSendAt(string $sms_send_at) Return EventsParticipants objects filtered by the sms_send_at column
 * @method array findByHasAccepted(boolean $has_accepted) Return EventsParticipants objects filtered by the has_accepted column
 * @method array findByExpiresAt(string $expires_at) Return EventsParticipants objects filtered by the expires_at column
 * @method array findByRespondedAt(string $responded_at) Return EventsParticipants objects filtered by the responded_at column
 * @method array findByCreatedAt(string $created_at) Return EventsParticipants objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return EventsParticipants objects filtered by the updated_at column
 */
abstract class BaseEventsParticipantsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseEventsParticipantsQuery object.
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
            $modelName = 'Hanzo\\Model\\EventsParticipants';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new EventsParticipantsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   EventsParticipantsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return EventsParticipantsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof EventsParticipantsQuery) {
            return $criteria;
        }
        $query = new EventsParticipantsQuery(null, null, $modelAlias);

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
     * @return   EventsParticipants|EventsParticipants[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = EventsParticipantsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(EventsParticipantsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 EventsParticipants A model object, or null if the key is not found
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
     * @return                 EventsParticipants A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `events_id`, `key`, `invited_by`, `first_name`, `last_name`, `email`, `phone`, `tell_a_friend`, `notify_by_sms`, `sms_send_at`, `has_accepted`, `expires_at`, `responded_at`, `created_at`, `updated_at` FROM `events_participants` WHERE `id` = :p0';
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
            $obj = new EventsParticipants();
            $obj->hydrate($row);
            EventsParticipantsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return EventsParticipants|EventsParticipants[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|EventsParticipants[]|mixed the list of results, formatted by the current formatter
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
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(EventsParticipantsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(EventsParticipantsPeer::ID, $keys, Criteria::IN);
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
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the events_id column
     *
     * Example usage:
     * <code>
     * $query->filterByEventsId(1234); // WHERE events_id = 1234
     * $query->filterByEventsId(array(12, 34)); // WHERE events_id IN (12, 34)
     * $query->filterByEventsId(array('min' => 12)); // WHERE events_id >= 12
     * $query->filterByEventsId(array('max' => 12)); // WHERE events_id <= 12
     * </code>
     *
     * @see       filterByEvents()
     *
     * @param     mixed $eventsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByEventsId($eventsId = null, $comparison = null)
    {
        if (is_array($eventsId)) {
            $useMinMax = false;
            if (isset($eventsId['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::EVENTS_ID, $eventsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($eventsId['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::EVENTS_ID, $eventsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::EVENTS_ID, $eventsId, $comparison);
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
     * @return EventsParticipantsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EventsParticipantsPeer::KEY, $key, $comparison);
    }

    /**
     * Filter the query on the invited_by column
     *
     * Example usage:
     * <code>
     * $query->filterByInvitedBy(1234); // WHERE invited_by = 1234
     * $query->filterByInvitedBy(array(12, 34)); // WHERE invited_by IN (12, 34)
     * $query->filterByInvitedBy(array('min' => 12)); // WHERE invited_by >= 12
     * $query->filterByInvitedBy(array('max' => 12)); // WHERE invited_by <= 12
     * </code>
     *
     * @param     mixed $invitedBy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByInvitedBy($invitedBy = null, $comparison = null)
    {
        if (is_array($invitedBy)) {
            $useMinMax = false;
            if (isset($invitedBy['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::INVITED_BY, $invitedBy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($invitedBy['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::INVITED_BY, $invitedBy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::INVITED_BY, $invitedBy, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%'); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $firstName)) {
                $firstName = str_replace('*', '%', $firstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByLastName('%fooValue%'); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByLastName($lastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lastName)) {
                $lastName = str_replace('*', '%', $lastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::LAST_NAME, $lastName, $comparison);
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
     * @return EventsParticipantsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EventsParticipantsPeer::EMAIL, $email, $comparison);
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
     * @return EventsParticipantsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(EventsParticipantsPeer::PHONE, $phone, $comparison);
    }

    /**
     * Filter the query on the tell_a_friend column
     *
     * Example usage:
     * <code>
     * $query->filterByTellAFriend(true); // WHERE tell_a_friend = true
     * $query->filterByTellAFriend('yes'); // WHERE tell_a_friend = true
     * </code>
     *
     * @param     boolean|string $tellAFriend The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByTellAFriend($tellAFriend = null, $comparison = null)
    {
        if (is_string($tellAFriend)) {
            $tellAFriend = in_array(strtolower($tellAFriend), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(EventsParticipantsPeer::TELL_A_FRIEND, $tellAFriend, $comparison);
    }

    /**
     * Filter the query on the notify_by_sms column
     *
     * Example usage:
     * <code>
     * $query->filterByNotifyBySms(true); // WHERE notify_by_sms = true
     * $query->filterByNotifyBySms('yes'); // WHERE notify_by_sms = true
     * </code>
     *
     * @param     boolean|string $notifyBySms The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByNotifyBySms($notifyBySms = null, $comparison = null)
    {
        if (is_string($notifyBySms)) {
            $notifyBySms = in_array(strtolower($notifyBySms), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(EventsParticipantsPeer::NOTIFY_BY_SMS, $notifyBySms, $comparison);
    }

    /**
     * Filter the query on the sms_send_at column
     *
     * Example usage:
     * <code>
     * $query->filterBySmsSendAt('2011-03-14'); // WHERE sms_send_at = '2011-03-14'
     * $query->filterBySmsSendAt('now'); // WHERE sms_send_at = '2011-03-14'
     * $query->filterBySmsSendAt(array('max' => 'yesterday')); // WHERE sms_send_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $smsSendAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterBySmsSendAt($smsSendAt = null, $comparison = null)
    {
        if (is_array($smsSendAt)) {
            $useMinMax = false;
            if (isset($smsSendAt['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::SMS_SEND_AT, $smsSendAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($smsSendAt['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::SMS_SEND_AT, $smsSendAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::SMS_SEND_AT, $smsSendAt, $comparison);
    }

    /**
     * Filter the query on the has_accepted column
     *
     * Example usage:
     * <code>
     * $query->filterByHasAccepted(true); // WHERE has_accepted = true
     * $query->filterByHasAccepted('yes'); // WHERE has_accepted = true
     * </code>
     *
     * @param     boolean|string $hasAccepted The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByHasAccepted($hasAccepted = null, $comparison = null)
    {
        if (is_string($hasAccepted)) {
            $hasAccepted = in_array(strtolower($hasAccepted), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(EventsParticipantsPeer::HAS_ACCEPTED, $hasAccepted, $comparison);
    }

    /**
     * Filter the query on the expires_at column
     *
     * Example usage:
     * <code>
     * $query->filterByExpiresAt('2011-03-14'); // WHERE expires_at = '2011-03-14'
     * $query->filterByExpiresAt('now'); // WHERE expires_at = '2011-03-14'
     * $query->filterByExpiresAt(array('max' => 'yesterday')); // WHERE expires_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $expiresAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByExpiresAt($expiresAt = null, $comparison = null)
    {
        if (is_array($expiresAt)) {
            $useMinMax = false;
            if (isset($expiresAt['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::EXPIRES_AT, $expiresAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($expiresAt['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::EXPIRES_AT, $expiresAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::EXPIRES_AT, $expiresAt, $comparison);
    }

    /**
     * Filter the query on the responded_at column
     *
     * Example usage:
     * <code>
     * $query->filterByRespondedAt('2011-03-14'); // WHERE responded_at = '2011-03-14'
     * $query->filterByRespondedAt('now'); // WHERE responded_at = '2011-03-14'
     * $query->filterByRespondedAt(array('max' => 'yesterday')); // WHERE responded_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $respondedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByRespondedAt($respondedAt = null, $comparison = null)
    {
        if (is_array($respondedAt)) {
            $useMinMax = false;
            if (isset($respondedAt['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::RESPONDED_AT, $respondedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($respondedAt['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::RESPONDED_AT, $respondedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::RESPONDED_AT, $respondedAt, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
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
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
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
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(EventsParticipantsPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(EventsParticipantsPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(EventsParticipantsPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related Events object
     *
     * @param   Events|PropelObjectCollection $events The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 EventsParticipantsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByEvents($events, $comparison = null)
    {
        if ($events instanceof Events) {
            return $this
                ->addUsingAlias(EventsParticipantsPeer::EVENTS_ID, $events->getId(), $comparison);
        } elseif ($events instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(EventsParticipantsPeer::EVENTS_ID, $events->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByEvents() only accepts arguments of type Events or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Events relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function joinEvents($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Events');

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
            $this->addJoinObject($join, 'Events');
        }

        return $this;
    }

    /**
     * Use the Events relation Events object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\EventsQuery A secondary query class using the current class as primary query
     */
    public function useEventsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinEvents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Events', '\Hanzo\Model\EventsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   EventsParticipants $eventsParticipants Object to remove from the list of results
     *
     * @return EventsParticipantsQuery The current query, for fluid interface
     */
    public function prune($eventsParticipants = null)
    {
        if ($eventsParticipants) {
            $this->addUsingAlias(EventsParticipantsPeer::ID, $eventsParticipants->getId(), Criteria::NOT_EQUAL);
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

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     EventsParticipantsQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(EventsParticipantsPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     EventsParticipantsQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(EventsParticipantsPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     EventsParticipantsQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(EventsParticipantsPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     EventsParticipantsQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(EventsParticipantsPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     EventsParticipantsQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(EventsParticipantsPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     EventsParticipantsQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(EventsParticipantsPeer::CREATED_AT);
    }
}

<?php

namespace Hanzo\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Hanzo\Model\FreeShipping;
use Hanzo\Model\FreeShippingPeer;
use Hanzo\Model\FreeShippingQuery;

/**
 * @method FreeShippingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method FreeShippingQuery orderByDomainKey($order = Criteria::ASC) Order by the domain_key column
 * @method FreeShippingQuery orderByBreakAt($order = Criteria::ASC) Order by the break_at column
 * @method FreeShippingQuery orderByValidFrom($order = Criteria::ASC) Order by the valid_from column
 * @method FreeShippingQuery orderByValidTo($order = Criteria::ASC) Order by the valid_to column
 * @method FreeShippingQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method FreeShippingQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method FreeShippingQuery groupById() Group by the id column
 * @method FreeShippingQuery groupByDomainKey() Group by the domain_key column
 * @method FreeShippingQuery groupByBreakAt() Group by the break_at column
 * @method FreeShippingQuery groupByValidFrom() Group by the valid_from column
 * @method FreeShippingQuery groupByValidTo() Group by the valid_to column
 * @method FreeShippingQuery groupByCreatedAt() Group by the created_at column
 * @method FreeShippingQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method FreeShippingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FreeShippingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FreeShippingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FreeShipping findOne(PropelPDO $con = null) Return the first FreeShipping matching the query
 * @method FreeShipping findOneOrCreate(PropelPDO $con = null) Return the first FreeShipping matching the query, or a new FreeShipping object populated from the query conditions when no match is found
 *
 * @method FreeShipping findOneByDomainKey(string $domain_key) Return the first FreeShipping filtered by the domain_key column
 * @method FreeShipping findOneByBreakAt(string $break_at) Return the first FreeShipping filtered by the break_at column
 * @method FreeShipping findOneByValidFrom(string $valid_from) Return the first FreeShipping filtered by the valid_from column
 * @method FreeShipping findOneByValidTo(string $valid_to) Return the first FreeShipping filtered by the valid_to column
 * @method FreeShipping findOneByCreatedAt(string $created_at) Return the first FreeShipping filtered by the created_at column
 * @method FreeShipping findOneByUpdatedAt(string $updated_at) Return the first FreeShipping filtered by the updated_at column
 *
 * @method array findById(int $id) Return FreeShipping objects filtered by the id column
 * @method array findByDomainKey(string $domain_key) Return FreeShipping objects filtered by the domain_key column
 * @method array findByBreakAt(string $break_at) Return FreeShipping objects filtered by the break_at column
 * @method array findByValidFrom(string $valid_from) Return FreeShipping objects filtered by the valid_from column
 * @method array findByValidTo(string $valid_to) Return FreeShipping objects filtered by the valid_to column
 * @method array findByCreatedAt(string $created_at) Return FreeShipping objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return FreeShipping objects filtered by the updated_at column
 */
abstract class BaseFreeShippingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFreeShippingQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Hanzo\\Model\\FreeShipping', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FreeShippingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   FreeShippingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FreeShippingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FreeShippingQuery) {
            return $criteria;
        }
        $query = new FreeShippingQuery();
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
     * @return   FreeShipping|FreeShipping[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FreeShippingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FreeShippingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 FreeShipping A model object, or null if the key is not found
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
     * @return                 FreeShipping A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `domain_key`, `break_at`, `valid_from`, `valid_to`, `created_at`, `updated_at` FROM `free_shipping` WHERE `id` = :p0';
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
            $obj = new FreeShipping();
            $obj->hydrate($row);
            FreeShippingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FreeShipping|FreeShipping[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FreeShipping[]|mixed the list of results, formatted by the current formatter
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
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FreeShippingPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FreeShippingPeer::ID, $keys, Criteria::IN);
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
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(FreeShippingPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(FreeShippingPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the domain_key column
     *
     * Example usage:
     * <code>
     * $query->filterByDomainKey('fooValue');   // WHERE domain_key = 'fooValue'
     * $query->filterByDomainKey('%fooValue%'); // WHERE domain_key LIKE '%fooValue%'
     * </code>
     *
     * @param     string $domainKey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByDomainKey($domainKey = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($domainKey)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $domainKey)) {
                $domainKey = str_replace('*', '%', $domainKey);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::DOMAIN_KEY, $domainKey, $comparison);
    }

    /**
     * Filter the query on the break_at column
     *
     * Example usage:
     * <code>
     * $query->filterByBreakAt(1234); // WHERE break_at = 1234
     * $query->filterByBreakAt(array(12, 34)); // WHERE break_at IN (12, 34)
     * $query->filterByBreakAt(array('min' => 12)); // WHERE break_at >= 12
     * $query->filterByBreakAt(array('max' => 12)); // WHERE break_at <= 12
     * </code>
     *
     * @param     mixed $breakAt The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByBreakAt($breakAt = null, $comparison = null)
    {
        if (is_array($breakAt)) {
            $useMinMax = false;
            if (isset($breakAt['min'])) {
                $this->addUsingAlias(FreeShippingPeer::BREAK_AT, $breakAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($breakAt['max'])) {
                $this->addUsingAlias(FreeShippingPeer::BREAK_AT, $breakAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::BREAK_AT, $breakAt, $comparison);
    }

    /**
     * Filter the query on the valid_from column
     *
     * Example usage:
     * <code>
     * $query->filterByValidFrom('2011-03-14'); // WHERE valid_from = '2011-03-14'
     * $query->filterByValidFrom('now'); // WHERE valid_from = '2011-03-14'
     * $query->filterByValidFrom(array('max' => 'yesterday')); // WHERE valid_from > '2011-03-13'
     * </code>
     *
     * @param     mixed $validFrom The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByValidFrom($validFrom = null, $comparison = null)
    {
        if (is_array($validFrom)) {
            $useMinMax = false;
            if (isset($validFrom['min'])) {
                $this->addUsingAlias(FreeShippingPeer::VALID_FROM, $validFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($validFrom['max'])) {
                $this->addUsingAlias(FreeShippingPeer::VALID_FROM, $validFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::VALID_FROM, $validFrom, $comparison);
    }

    /**
     * Filter the query on the valid_to column
     *
     * Example usage:
     * <code>
     * $query->filterByValidTo('2011-03-14'); // WHERE valid_to = '2011-03-14'
     * $query->filterByValidTo('now'); // WHERE valid_to = '2011-03-14'
     * $query->filterByValidTo(array('max' => 'yesterday')); // WHERE valid_to > '2011-03-13'
     * </code>
     *
     * @param     mixed $validTo The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByValidTo($validTo = null, $comparison = null)
    {
        if (is_array($validTo)) {
            $useMinMax = false;
            if (isset($validTo['min'])) {
                $this->addUsingAlias(FreeShippingPeer::VALID_TO, $validTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($validTo['max'])) {
                $this->addUsingAlias(FreeShippingPeer::VALID_TO, $validTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::VALID_TO, $validTo, $comparison);
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
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(FreeShippingPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(FreeShippingPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::CREATED_AT, $createdAt, $comparison);
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
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(FreeShippingPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(FreeShippingPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeShippingPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   FreeShipping $freeShipping Object to remove from the list of results
     *
     * @return FreeShippingQuery The current query, for fluid interface
     */
    public function prune($freeShipping = null)
    {
        if ($freeShipping) {
            $this->addUsingAlias(FreeShippingPeer::ID, $freeShipping->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     FreeShippingQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(FreeShippingPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     FreeShippingQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(FreeShippingPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     FreeShippingQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(FreeShippingPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     FreeShippingQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(FreeShippingPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     FreeShippingQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(FreeShippingPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     FreeShippingQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(FreeShippingPeer::CREATED_AT);
    }
}

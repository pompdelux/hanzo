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
use Hanzo\Model\Settings;
use Hanzo\Model\SettingsPeer;
use Hanzo\Model\SettingsQuery;

/**
 * @method SettingsQuery orderByCKey($order = Criteria::ASC) Order by the c_key column
 * @method SettingsQuery orderByNs($order = Criteria::ASC) Order by the ns column
 * @method SettingsQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method SettingsQuery orderByCValue($order = Criteria::ASC) Order by the c_value column
 * @method SettingsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method SettingsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method SettingsQuery groupByCKey() Group by the c_key column
 * @method SettingsQuery groupByNs() Group by the ns column
 * @method SettingsQuery groupByTitle() Group by the title column
 * @method SettingsQuery groupByCValue() Group by the c_value column
 * @method SettingsQuery groupByCreatedAt() Group by the created_at column
 * @method SettingsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method SettingsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SettingsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SettingsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Settings findOne(PropelPDO $con = null) Return the first Settings matching the query
 * @method Settings findOneOrCreate(PropelPDO $con = null) Return the first Settings matching the query, or a new Settings object populated from the query conditions when no match is found
 *
 * @method Settings findOneByCKey(string $c_key) Return the first Settings filtered by the c_key column
 * @method Settings findOneByNs(string $ns) Return the first Settings filtered by the ns column
 * @method Settings findOneByTitle(string $title) Return the first Settings filtered by the title column
 * @method Settings findOneByCValue(string $c_value) Return the first Settings filtered by the c_value column
 * @method Settings findOneByCreatedAt(string $created_at) Return the first Settings filtered by the created_at column
 * @method Settings findOneByUpdatedAt(string $updated_at) Return the first Settings filtered by the updated_at column
 *
 * @method array findByCKey(string $c_key) Return Settings objects filtered by the c_key column
 * @method array findByNs(string $ns) Return Settings objects filtered by the ns column
 * @method array findByTitle(string $title) Return Settings objects filtered by the title column
 * @method array findByCValue(string $c_value) Return Settings objects filtered by the c_value column
 * @method array findByCreatedAt(string $created_at) Return Settings objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Settings objects filtered by the updated_at column
 */
abstract class BaseSettingsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSettingsQuery object.
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
            $modelName = 'Hanzo\\Model\\Settings';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SettingsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SettingsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SettingsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SettingsQuery) {
            return $criteria;
        }
        $query = new SettingsQuery(null, null, $modelAlias);

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
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$c_key, $ns]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Settings|Settings[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SettingsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SettingsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Settings A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `c_key`, `ns`, `title`, `c_value`, `created_at`, `updated_at` FROM `settings` WHERE `c_key` = :p0 AND `ns` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Settings();
            $obj->hydrate($row);
            SettingsPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return Settings|Settings[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Settings[]|mixed the list of results, formatted by the current formatter
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
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SettingsPeer::C_KEY, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SettingsPeer::NS, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SettingsPeer::C_KEY, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SettingsPeer::NS, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @return SettingsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SettingsPeer::C_KEY, $cKey, $comparison);
    }

    /**
     * Filter the query on the ns column
     *
     * Example usage:
     * <code>
     * $query->filterByNs('fooValue');   // WHERE ns = 'fooValue'
     * $query->filterByNs('%fooValue%'); // WHERE ns LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ns The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByNs($ns = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ns)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ns)) {
                $ns = str_replace('*', '%', $ns);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsPeer::NS, $ns, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsPeer::TITLE, $title, $comparison);
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
     * @return SettingsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SettingsPeer::C_VALUE, $cValue, $comparison);
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
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(SettingsPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(SettingsPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsPeer::CREATED_AT, $createdAt, $comparison);
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
     * @return SettingsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(SettingsPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(SettingsPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Settings $settings Object to remove from the list of results
     *
     * @return SettingsQuery The current query, for fluid interface
     */
    public function prune($settings = null)
    {
        if ($settings) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SettingsPeer::C_KEY), $settings->getCKey(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SettingsPeer::NS), $settings->getNs(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     SettingsQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(SettingsPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     SettingsQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(SettingsPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     SettingsQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(SettingsPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     SettingsQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(SettingsPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     SettingsQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(SettingsPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     SettingsQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(SettingsPeer::CREATED_AT);
    }
}

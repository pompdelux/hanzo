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
use Hanzo\Model\Redirects;
use Hanzo\Model\RedirectsPeer;
use Hanzo\Model\RedirectsQuery;

/**
 * @method RedirectsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method RedirectsQuery orderBySource($order = Criteria::ASC) Order by the source column
 * @method RedirectsQuery orderByTarget($order = Criteria::ASC) Order by the target column
 * @method RedirectsQuery orderByDomainKey($order = Criteria::ASC) Order by the domain_key column
 *
 * @method RedirectsQuery groupById() Group by the id column
 * @method RedirectsQuery groupBySource() Group by the source column
 * @method RedirectsQuery groupByTarget() Group by the target column
 * @method RedirectsQuery groupByDomainKey() Group by the domain_key column
 *
 * @method RedirectsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RedirectsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RedirectsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Redirects findOne(PropelPDO $con = null) Return the first Redirects matching the query
 * @method Redirects findOneOrCreate(PropelPDO $con = null) Return the first Redirects matching the query, or a new Redirects object populated from the query conditions when no match is found
 *
 * @method Redirects findOneBySource(string $source) Return the first Redirects filtered by the source column
 * @method Redirects findOneByTarget(string $target) Return the first Redirects filtered by the target column
 * @method Redirects findOneByDomainKey(string $domain_key) Return the first Redirects filtered by the domain_key column
 *
 * @method array findById(int $id) Return Redirects objects filtered by the id column
 * @method array findBySource(string $source) Return Redirects objects filtered by the source column
 * @method array findByTarget(string $target) Return Redirects objects filtered by the target column
 * @method array findByDomainKey(string $domain_key) Return Redirects objects filtered by the domain_key column
 */
abstract class BaseRedirectsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRedirectsQuery object.
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
            $modelName = 'Hanzo\\Model\\Redirects';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RedirectsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RedirectsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RedirectsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RedirectsQuery) {
            return $criteria;
        }
        $query = new RedirectsQuery(null, null, $modelAlias);

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
     * @return   Redirects|Redirects[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RedirectsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RedirectsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Redirects A model object, or null if the key is not found
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
     * @return                 Redirects A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `source`, `target`, `domain_key` FROM `redirects` WHERE `id` = :p0';
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
            $obj = new Redirects();
            $obj->hydrate($row);
            RedirectsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Redirects|Redirects[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Redirects[]|mixed the list of results, formatted by the current formatter
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
     * @return RedirectsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RedirectsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RedirectsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RedirectsPeer::ID, $keys, Criteria::IN);
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
     * @return RedirectsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(RedirectsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(RedirectsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RedirectsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the source column
     *
     * Example usage:
     * <code>
     * $query->filterBySource('fooValue');   // WHERE source = 'fooValue'
     * $query->filterBySource('%fooValue%'); // WHERE source LIKE '%fooValue%'
     * </code>
     *
     * @param     string $source The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RedirectsQuery The current query, for fluid interface
     */
    public function filterBySource($source = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($source)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $source)) {
                $source = str_replace('*', '%', $source);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RedirectsPeer::SOURCE, $source, $comparison);
    }

    /**
     * Filter the query on the target column
     *
     * Example usage:
     * <code>
     * $query->filterByTarget('fooValue');   // WHERE target = 'fooValue'
     * $query->filterByTarget('%fooValue%'); // WHERE target LIKE '%fooValue%'
     * </code>
     *
     * @param     string $target The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RedirectsQuery The current query, for fluid interface
     */
    public function filterByTarget($target = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($target)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $target)) {
                $target = str_replace('*', '%', $target);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RedirectsPeer::TARGET, $target, $comparison);
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
     * @return RedirectsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RedirectsPeer::DOMAIN_KEY, $domainKey, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Redirects $redirects Object to remove from the list of results
     *
     * @return RedirectsQuery The current query, for fluid interface
     */
    public function prune($redirects = null)
    {
        if ($redirects) {
            $this->addUsingAlias(RedirectsPeer::ID, $redirects->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

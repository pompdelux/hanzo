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
use Hanzo\Model\Domains;
use Hanzo\Model\DomainsSettings;
use Hanzo\Model\DomainsSettingsPeer;
use Hanzo\Model\DomainsSettingsQuery;

/**
 * @method DomainsSettingsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method DomainsSettingsQuery orderByDomainKey($order = Criteria::ASC) Order by the domain_key column
 * @method DomainsSettingsQuery orderByCKey($order = Criteria::ASC) Order by the c_key column
 * @method DomainsSettingsQuery orderByNs($order = Criteria::ASC) Order by the ns column
 * @method DomainsSettingsQuery orderByCValue($order = Criteria::ASC) Order by the c_value column
 *
 * @method DomainsSettingsQuery groupById() Group by the id column
 * @method DomainsSettingsQuery groupByDomainKey() Group by the domain_key column
 * @method DomainsSettingsQuery groupByCKey() Group by the c_key column
 * @method DomainsSettingsQuery groupByNs() Group by the ns column
 * @method DomainsSettingsQuery groupByCValue() Group by the c_value column
 *
 * @method DomainsSettingsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method DomainsSettingsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method DomainsSettingsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method DomainsSettingsQuery leftJoinDomains($relationAlias = null) Adds a LEFT JOIN clause to the query using the Domains relation
 * @method DomainsSettingsQuery rightJoinDomains($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Domains relation
 * @method DomainsSettingsQuery innerJoinDomains($relationAlias = null) Adds a INNER JOIN clause to the query using the Domains relation
 *
 * @method DomainsSettings findOne(PropelPDO $con = null) Return the first DomainsSettings matching the query
 * @method DomainsSettings findOneOrCreate(PropelPDO $con = null) Return the first DomainsSettings matching the query, or a new DomainsSettings object populated from the query conditions when no match is found
 *
 * @method DomainsSettings findOneByDomainKey(string $domain_key) Return the first DomainsSettings filtered by the domain_key column
 * @method DomainsSettings findOneByCKey(string $c_key) Return the first DomainsSettings filtered by the c_key column
 * @method DomainsSettings findOneByNs(string $ns) Return the first DomainsSettings filtered by the ns column
 * @method DomainsSettings findOneByCValue(string $c_value) Return the first DomainsSettings filtered by the c_value column
 *
 * @method array findById(int $id) Return DomainsSettings objects filtered by the id column
 * @method array findByDomainKey(string $domain_key) Return DomainsSettings objects filtered by the domain_key column
 * @method array findByCKey(string $c_key) Return DomainsSettings objects filtered by the c_key column
 * @method array findByNs(string $ns) Return DomainsSettings objects filtered by the ns column
 * @method array findByCValue(string $c_value) Return DomainsSettings objects filtered by the c_value column
 */
abstract class BaseDomainsSettingsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseDomainsSettingsQuery object.
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
            $modelName = 'Hanzo\\Model\\DomainsSettings';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DomainsSettingsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   DomainsSettingsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DomainsSettingsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DomainsSettingsQuery) {
            return $criteria;
        }
        $query = new DomainsSettingsQuery(null, null, $modelAlias);

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
     * @return   DomainsSettings|DomainsSettings[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DomainsSettingsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DomainsSettingsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 DomainsSettings A model object, or null if the key is not found
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
     * @return                 DomainsSettings A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `domain_key`, `c_key`, `ns`, `c_value` FROM `domains_settings` WHERE `id` = :p0';
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
            $obj = new DomainsSettings();
            $obj->hydrate($row);
            DomainsSettingsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return DomainsSettings|DomainsSettings[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|DomainsSettings[]|mixed the list of results, formatted by the current formatter
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
     * @return DomainsSettingsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DomainsSettingsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DomainsSettingsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DomainsSettingsPeer::ID, $keys, Criteria::IN);
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
     * @return DomainsSettingsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DomainsSettingsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DomainsSettingsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DomainsSettingsPeer::ID, $id, $comparison);
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
     * @return DomainsSettingsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainsSettingsPeer::DOMAIN_KEY, $domainKey, $comparison);
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
     * @return DomainsSettingsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainsSettingsPeer::C_KEY, $cKey, $comparison);
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
     * @return DomainsSettingsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainsSettingsPeer::NS, $ns, $comparison);
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
     * @return DomainsSettingsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainsSettingsPeer::C_VALUE, $cValue, $comparison);
    }

    /**
     * Filter the query by a related Domains object
     *
     * @param   Domains|PropelObjectCollection $domains The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 DomainsSettingsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDomains($domains, $comparison = null)
    {
        if ($domains instanceof Domains) {
            return $this
                ->addUsingAlias(DomainsSettingsPeer::DOMAIN_KEY, $domains->getDomainKey(), $comparison);
        } elseif ($domains instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DomainsSettingsPeer::DOMAIN_KEY, $domains->toKeyValue('PrimaryKey', 'DomainKey'), $comparison);
        } else {
            throw new PropelException('filterByDomains() only accepts arguments of type Domains or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Domains relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return DomainsSettingsQuery The current query, for fluid interface
     */
    public function joinDomains($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Domains');

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
            $this->addJoinObject($join, 'Domains');
        }

        return $this;
    }

    /**
     * Use the Domains relation Domains object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\DomainsQuery A secondary query class using the current class as primary query
     */
    public function useDomainsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDomains($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Domains', '\Hanzo\Model\DomainsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   DomainsSettings $domainsSettings Object to remove from the list of results
     *
     * @return DomainsSettingsQuery The current query, for fluid interface
     */
    public function prune($domainsSettings = null)
    {
        if ($domainsSettings) {
            $this->addUsingAlias(DomainsSettingsPeer::ID, $domainsSettings->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

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
use Hanzo\Model\DomainsPeer;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\DomainsSettings;
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsQuantityDiscount;

/**
 * @method DomainsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method DomainsQuery orderByDomainName($order = Criteria::ASC) Order by the domain_name column
 * @method DomainsQuery orderByDomainKey($order = Criteria::ASC) Order by the domain_key column
 *
 * @method DomainsQuery groupById() Group by the id column
 * @method DomainsQuery groupByDomainName() Group by the domain_name column
 * @method DomainsQuery groupByDomainKey() Group by the domain_key column
 *
 * @method DomainsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method DomainsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method DomainsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method DomainsQuery leftJoinDomainsSettings($relationAlias = null) Adds a LEFT JOIN clause to the query using the DomainsSettings relation
 * @method DomainsQuery rightJoinDomainsSettings($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DomainsSettings relation
 * @method DomainsQuery innerJoinDomainsSettings($relationAlias = null) Adds a INNER JOIN clause to the query using the DomainsSettings relation
 *
 * @method DomainsQuery leftJoinProductsDomainsPrices($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsDomainsPrices relation
 * @method DomainsQuery rightJoinProductsDomainsPrices($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsDomainsPrices relation
 * @method DomainsQuery innerJoinProductsDomainsPrices($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsDomainsPrices relation
 *
 * @method DomainsQuery leftJoinProductsQuantityDiscount($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsQuantityDiscount relation
 * @method DomainsQuery rightJoinProductsQuantityDiscount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsQuantityDiscount relation
 * @method DomainsQuery innerJoinProductsQuantityDiscount($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsQuantityDiscount relation
 *
 * @method Domains findOne(PropelPDO $con = null) Return the first Domains matching the query
 * @method Domains findOneOrCreate(PropelPDO $con = null) Return the first Domains matching the query, or a new Domains object populated from the query conditions when no match is found
 *
 * @method Domains findOneByDomainName(string $domain_name) Return the first Domains filtered by the domain_name column
 * @method Domains findOneByDomainKey(string $domain_key) Return the first Domains filtered by the domain_key column
 *
 * @method array findById(int $id) Return Domains objects filtered by the id column
 * @method array findByDomainName(string $domain_name) Return Domains objects filtered by the domain_name column
 * @method array findByDomainKey(string $domain_key) Return Domains objects filtered by the domain_key column
 */
abstract class BaseDomainsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseDomainsQuery object.
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
            $modelName = 'Hanzo\\Model\\Domains';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DomainsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   DomainsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DomainsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DomainsQuery) {
            return $criteria;
        }
        $query = new DomainsQuery(null, null, $modelAlias);

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
     * @return   Domains|Domains[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DomainsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DomainsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Domains A model object, or null if the key is not found
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
     * @return                 Domains A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `domain_name`, `domain_key` FROM `domains` WHERE `id` = :p0';
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
            $obj = new Domains();
            $obj->hydrate($row);
            DomainsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Domains|Domains[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Domains[]|mixed the list of results, formatted by the current formatter
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
     * @return DomainsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DomainsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DomainsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DomainsPeer::ID, $keys, Criteria::IN);
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
     * @return DomainsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DomainsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DomainsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DomainsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the domain_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDomainName('fooValue');   // WHERE domain_name = 'fooValue'
     * $query->filterByDomainName('%fooValue%'); // WHERE domain_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $domainName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainsQuery The current query, for fluid interface
     */
    public function filterByDomainName($domainName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($domainName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $domainName)) {
                $domainName = str_replace('*', '%', $domainName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainsPeer::DOMAIN_NAME, $domainName, $comparison);
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
     * @return DomainsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainsPeer::DOMAIN_KEY, $domainKey, $comparison);
    }

    /**
     * Filter the query by a related DomainsSettings object
     *
     * @param   DomainsSettings|PropelObjectCollection $domainsSettings  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 DomainsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDomainsSettings($domainsSettings, $comparison = null)
    {
        if ($domainsSettings instanceof DomainsSettings) {
            return $this
                ->addUsingAlias(DomainsPeer::DOMAIN_KEY, $domainsSettings->getDomainKey(), $comparison);
        } elseif ($domainsSettings instanceof PropelObjectCollection) {
            return $this
                ->useDomainsSettingsQuery()
                ->filterByPrimaryKeys($domainsSettings->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDomainsSettings() only accepts arguments of type DomainsSettings or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DomainsSettings relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return DomainsQuery The current query, for fluid interface
     */
    public function joinDomainsSettings($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DomainsSettings');

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
            $this->addJoinObject($join, 'DomainsSettings');
        }

        return $this;
    }

    /**
     * Use the DomainsSettings relation DomainsSettings object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\DomainsSettingsQuery A secondary query class using the current class as primary query
     */
    public function useDomainsSettingsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDomainsSettings($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DomainsSettings', '\Hanzo\Model\DomainsSettingsQuery');
    }

    /**
     * Filter the query by a related ProductsDomainsPrices object
     *
     * @param   ProductsDomainsPrices|PropelObjectCollection $productsDomainsPrices  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 DomainsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsDomainsPrices($productsDomainsPrices, $comparison = null)
    {
        if ($productsDomainsPrices instanceof ProductsDomainsPrices) {
            return $this
                ->addUsingAlias(DomainsPeer::ID, $productsDomainsPrices->getDomainsId(), $comparison);
        } elseif ($productsDomainsPrices instanceof PropelObjectCollection) {
            return $this
                ->useProductsDomainsPricesQuery()
                ->filterByPrimaryKeys($productsDomainsPrices->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsDomainsPrices() only accepts arguments of type ProductsDomainsPrices or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsDomainsPrices relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return DomainsQuery The current query, for fluid interface
     */
    public function joinProductsDomainsPrices($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsDomainsPrices');

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
            $this->addJoinObject($join, 'ProductsDomainsPrices');
        }

        return $this;
    }

    /**
     * Use the ProductsDomainsPrices relation ProductsDomainsPrices object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsDomainsPricesQuery A secondary query class using the current class as primary query
     */
    public function useProductsDomainsPricesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsDomainsPrices($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsDomainsPrices', '\Hanzo\Model\ProductsDomainsPricesQuery');
    }

    /**
     * Filter the query by a related ProductsQuantityDiscount object
     *
     * @param   ProductsQuantityDiscount|PropelObjectCollection $productsQuantityDiscount  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 DomainsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsQuantityDiscount($productsQuantityDiscount, $comparison = null)
    {
        if ($productsQuantityDiscount instanceof ProductsQuantityDiscount) {
            return $this
                ->addUsingAlias(DomainsPeer::ID, $productsQuantityDiscount->getDomainsId(), $comparison);
        } elseif ($productsQuantityDiscount instanceof PropelObjectCollection) {
            return $this
                ->useProductsQuantityDiscountQuery()
                ->filterByPrimaryKeys($productsQuantityDiscount->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsQuantityDiscount() only accepts arguments of type ProductsQuantityDiscount or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsQuantityDiscount relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return DomainsQuery The current query, for fluid interface
     */
    public function joinProductsQuantityDiscount($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsQuantityDiscount');

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
            $this->addJoinObject($join, 'ProductsQuantityDiscount');
        }

        return $this;
    }

    /**
     * Use the ProductsQuantityDiscount relation ProductsQuantityDiscount object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuantityDiscountQuery A secondary query class using the current class as primary query
     */
    public function useProductsQuantityDiscountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsQuantityDiscount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsQuantityDiscount', '\Hanzo\Model\ProductsQuantityDiscountQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Domains $domains Object to remove from the list of results
     *
     * @return DomainsQuery The current query, for fluid interface
     */
    public function prune($domains = null)
    {
        if ($domains) {
            $this->addUsingAlias(DomainsPeer::ID, $domains->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

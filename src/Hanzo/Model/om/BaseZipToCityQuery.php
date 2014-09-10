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
use Hanzo\Model\Countries;
use Hanzo\Model\ZipToCity;
use Hanzo\Model\ZipToCityPeer;
use Hanzo\Model\ZipToCityQuery;

/**
 * @method ZipToCityQuery orderById($order = Criteria::ASC) Order by the id column
 * @method ZipToCityQuery orderByZip($order = Criteria::ASC) Order by the zip column
 * @method ZipToCityQuery orderByCountriesIso2($order = Criteria::ASC) Order by the countries_iso2 column
 * @method ZipToCityQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method ZipToCityQuery orderByCountyId($order = Criteria::ASC) Order by the county_id column
 * @method ZipToCityQuery orderByCountyName($order = Criteria::ASC) Order by the county_name column
 * @method ZipToCityQuery orderByComment($order = Criteria::ASC) Order by the comment column
 *
 * @method ZipToCityQuery groupById() Group by the id column
 * @method ZipToCityQuery groupByZip() Group by the zip column
 * @method ZipToCityQuery groupByCountriesIso2() Group by the countries_iso2 column
 * @method ZipToCityQuery groupByCity() Group by the city column
 * @method ZipToCityQuery groupByCountyId() Group by the county_id column
 * @method ZipToCityQuery groupByCountyName() Group by the county_name column
 * @method ZipToCityQuery groupByComment() Group by the comment column
 *
 * @method ZipToCityQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ZipToCityQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ZipToCityQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ZipToCityQuery leftJoinCountries($relationAlias = null) Adds a LEFT JOIN clause to the query using the Countries relation
 * @method ZipToCityQuery rightJoinCountries($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Countries relation
 * @method ZipToCityQuery innerJoinCountries($relationAlias = null) Adds a INNER JOIN clause to the query using the Countries relation
 *
 * @method ZipToCity findOne(PropelPDO $con = null) Return the first ZipToCity matching the query
 * @method ZipToCity findOneOrCreate(PropelPDO $con = null) Return the first ZipToCity matching the query, or a new ZipToCity object populated from the query conditions when no match is found
 *
 * @method ZipToCity findOneByZip(string $zip) Return the first ZipToCity filtered by the zip column
 * @method ZipToCity findOneByCountriesIso2(string $countries_iso2) Return the first ZipToCity filtered by the countries_iso2 column
 * @method ZipToCity findOneByCity(string $city) Return the first ZipToCity filtered by the city column
 * @method ZipToCity findOneByCountyId(string $county_id) Return the first ZipToCity filtered by the county_id column
 * @method ZipToCity findOneByCountyName(string $county_name) Return the first ZipToCity filtered by the county_name column
 * @method ZipToCity findOneByComment(string $comment) Return the first ZipToCity filtered by the comment column
 *
 * @method array findById(int $id) Return ZipToCity objects filtered by the id column
 * @method array findByZip(string $zip) Return ZipToCity objects filtered by the zip column
 * @method array findByCountriesIso2(string $countries_iso2) Return ZipToCity objects filtered by the countries_iso2 column
 * @method array findByCity(string $city) Return ZipToCity objects filtered by the city column
 * @method array findByCountyId(string $county_id) Return ZipToCity objects filtered by the county_id column
 * @method array findByCountyName(string $county_name) Return ZipToCity objects filtered by the county_name column
 * @method array findByComment(string $comment) Return ZipToCity objects filtered by the comment column
 */
abstract class BaseZipToCityQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseZipToCityQuery object.
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
            $modelName = 'Hanzo\\Model\\ZipToCity';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new ZipToCityQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ZipToCityQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ZipToCityQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ZipToCityQuery) {
            return $criteria;
        }
        $query = new ZipToCityQuery(null, null, $modelAlias);

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
     * @return   ZipToCity|ZipToCity[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ZipToCityPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ZipToCityPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ZipToCity A model object, or null if the key is not found
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
     * @return                 ZipToCity A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `zip`, `countries_iso2`, `city`, `county_id`, `county_name`, `comment` FROM `zip_to_city` WHERE `id` = :p0';
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
            $obj = new ZipToCity();
            $obj->hydrate($row);
            ZipToCityPeer::addInstanceToPool($obj, (string) $key);
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
     * @return ZipToCity|ZipToCity[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ZipToCity[]|mixed the list of results, formatted by the current formatter
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
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ZipToCityPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ZipToCityPeer::ID, $keys, Criteria::IN);
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
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ZipToCityPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ZipToCityPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ZipToCityPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the zip column
     *
     * Example usage:
     * <code>
     * $query->filterByZip('fooValue');   // WHERE zip = 'fooValue'
     * $query->filterByZip('%fooValue%'); // WHERE zip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $zip The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByZip($zip = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($zip)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $zip)) {
                $zip = str_replace('*', '%', $zip);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ZipToCityPeer::ZIP, $zip, $comparison);
    }

    /**
     * Filter the query on the countries_iso2 column
     *
     * Example usage:
     * <code>
     * $query->filterByCountriesIso2('fooValue');   // WHERE countries_iso2 = 'fooValue'
     * $query->filterByCountriesIso2('%fooValue%'); // WHERE countries_iso2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $countriesIso2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByCountriesIso2($countriesIso2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($countriesIso2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $countriesIso2)) {
                $countriesIso2 = str_replace('*', '%', $countriesIso2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ZipToCityPeer::COUNTRIES_ISO2, $countriesIso2, $comparison);
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
     * @return ZipToCityQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ZipToCityPeer::CITY, $city, $comparison);
    }

    /**
     * Filter the query on the county_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCountyId('fooValue');   // WHERE county_id = 'fooValue'
     * $query->filterByCountyId('%fooValue%'); // WHERE county_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $countyId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByCountyId($countyId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($countyId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $countyId)) {
                $countyId = str_replace('*', '%', $countyId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ZipToCityPeer::COUNTY_ID, $countyId, $comparison);
    }

    /**
     * Filter the query on the county_name column
     *
     * Example usage:
     * <code>
     * $query->filterByCountyName('fooValue');   // WHERE county_name = 'fooValue'
     * $query->filterByCountyName('%fooValue%'); // WHERE county_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $countyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByCountyName($countyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($countyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $countyName)) {
                $countyName = str_replace('*', '%', $countyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ZipToCityPeer::COUNTY_NAME, $countyName, $comparison);
    }

    /**
     * Filter the query on the comment column
     *
     * Example usage:
     * <code>
     * $query->filterByComment('fooValue');   // WHERE comment = 'fooValue'
     * $query->filterByComment('%fooValue%'); // WHERE comment LIKE '%fooValue%'
     * </code>
     *
     * @param     string $comment The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function filterByComment($comment = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($comment)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $comment)) {
                $comment = str_replace('*', '%', $comment);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ZipToCityPeer::COMMENT, $comment, $comparison);
    }

    /**
     * Filter the query by a related Countries object
     *
     * @param   Countries|PropelObjectCollection $countries The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ZipToCityQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCountries($countries, $comparison = null)
    {
        if ($countries instanceof Countries) {
            return $this
                ->addUsingAlias(ZipToCityPeer::COUNTRIES_ISO2, $countries->getIso2(), $comparison);
        } elseif ($countries instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ZipToCityPeer::COUNTRIES_ISO2, $countries->toKeyValue('PrimaryKey', 'Iso2'), $comparison);
        } else {
            throw new PropelException('filterByCountries() only accepts arguments of type Countries or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Countries relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function joinCountries($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Countries');

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
            $this->addJoinObject($join, 'Countries');
        }

        return $this;
    }

    /**
     * Use the Countries relation Countries object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CountriesQuery A secondary query class using the current class as primary query
     */
    public function useCountriesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCountries($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Countries', '\Hanzo\Model\CountriesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ZipToCity $zipToCity Object to remove from the list of results
     *
     * @return ZipToCityQuery The current query, for fluid interface
     */
    public function prune($zipToCity = null)
    {
        if ($zipToCity) {
            $this->addUsingAlias(ZipToCityPeer::ID, $zipToCity->getId(), Criteria::NOT_EQUAL);
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

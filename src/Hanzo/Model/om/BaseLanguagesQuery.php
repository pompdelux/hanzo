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
use Hanzo\Model\Languages;
use Hanzo\Model\LanguagesPeer;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\ProductsSeoI18n;
use Hanzo\Model\ProductsWashingInstructions;

/**
 * @method LanguagesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method LanguagesQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method LanguagesQuery orderByLocalName($order = Criteria::ASC) Order by the local_name column
 * @method LanguagesQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 * @method LanguagesQuery orderByIso2($order = Criteria::ASC) Order by the iso2 column
 * @method LanguagesQuery orderByDirection($order = Criteria::ASC) Order by the direction column
 *
 * @method LanguagesQuery groupById() Group by the id column
 * @method LanguagesQuery groupByName() Group by the name column
 * @method LanguagesQuery groupByLocalName() Group by the local_name column
 * @method LanguagesQuery groupByLocale() Group by the locale column
 * @method LanguagesQuery groupByIso2() Group by the iso2 column
 * @method LanguagesQuery groupByDirection() Group by the direction column
 *
 * @method LanguagesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method LanguagesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method LanguagesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method LanguagesQuery leftJoinProductsWashingInstructions($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsWashingInstructions relation
 * @method LanguagesQuery rightJoinProductsWashingInstructions($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsWashingInstructions relation
 * @method LanguagesQuery innerJoinProductsWashingInstructions($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsWashingInstructions relation
 *
 * @method LanguagesQuery leftJoinProductsSeoI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsSeoI18n relation
 * @method LanguagesQuery rightJoinProductsSeoI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsSeoI18n relation
 * @method LanguagesQuery innerJoinProductsSeoI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsSeoI18n relation
 *
 * @method Languages findOne(PropelPDO $con = null) Return the first Languages matching the query
 * @method Languages findOneOrCreate(PropelPDO $con = null) Return the first Languages matching the query, or a new Languages object populated from the query conditions when no match is found
 *
 * @method Languages findOneByName(string $name) Return the first Languages filtered by the name column
 * @method Languages findOneByLocalName(string $local_name) Return the first Languages filtered by the local_name column
 * @method Languages findOneByLocale(string $locale) Return the first Languages filtered by the locale column
 * @method Languages findOneByIso2(string $iso2) Return the first Languages filtered by the iso2 column
 * @method Languages findOneByDirection(string $direction) Return the first Languages filtered by the direction column
 *
 * @method array findById(int $id) Return Languages objects filtered by the id column
 * @method array findByName(string $name) Return Languages objects filtered by the name column
 * @method array findByLocalName(string $local_name) Return Languages objects filtered by the local_name column
 * @method array findByLocale(string $locale) Return Languages objects filtered by the locale column
 * @method array findByIso2(string $iso2) Return Languages objects filtered by the iso2 column
 * @method array findByDirection(string $direction) Return Languages objects filtered by the direction column
 */
abstract class BaseLanguagesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseLanguagesQuery object.
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
            $modelName = 'Hanzo\\Model\\Languages';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new LanguagesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   LanguagesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LanguagesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LanguagesQuery) {
            return $criteria;
        }
        $query = new LanguagesQuery(null, null, $modelAlias);

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
     * @return   Languages|Languages[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LanguagesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LanguagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Languages A model object, or null if the key is not found
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
     * @return                 Languages A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `name`, `local_name`, `locale`, `iso2`, `direction` FROM `languages` WHERE `id` = :p0';
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
            $obj = new Languages();
            $obj->hydrate($row);
            LanguagesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Languages|Languages[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Languages[]|mixed the list of results, formatted by the current formatter
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
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LanguagesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LanguagesPeer::ID, $keys, Criteria::IN);
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
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LanguagesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LanguagesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LanguagesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LanguagesPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the local_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLocalName('fooValue');   // WHERE local_name = 'fooValue'
     * $query->filterByLocalName('%fooValue%'); // WHERE local_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $localName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByLocalName($localName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($localName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $localName)) {
                $localName = str_replace('*', '%', $localName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LanguagesPeer::LOCAL_NAME, $localName, $comparison);
    }

    /**
     * Filter the query on the locale column
     *
     * Example usage:
     * <code>
     * $query->filterByLocale('fooValue');   // WHERE locale = 'fooValue'
     * $query->filterByLocale('%fooValue%'); // WHERE locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $locale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByLocale($locale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($locale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $locale)) {
                $locale = str_replace('*', '%', $locale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LanguagesPeer::LOCALE, $locale, $comparison);
    }

    /**
     * Filter the query on the iso2 column
     *
     * Example usage:
     * <code>
     * $query->filterByIso2('fooValue');   // WHERE iso2 = 'fooValue'
     * $query->filterByIso2('%fooValue%'); // WHERE iso2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $iso2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByIso2($iso2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($iso2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $iso2)) {
                $iso2 = str_replace('*', '%', $iso2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LanguagesPeer::ISO2, $iso2, $comparison);
    }

    /**
     * Filter the query on the direction column
     *
     * Example usage:
     * <code>
     * $query->filterByDirection('fooValue');   // WHERE direction = 'fooValue'
     * $query->filterByDirection('%fooValue%'); // WHERE direction LIKE '%fooValue%'
     * </code>
     *
     * @param     string $direction The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function filterByDirection($direction = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($direction)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $direction)) {
                $direction = str_replace('*', '%', $direction);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LanguagesPeer::DIRECTION, $direction, $comparison);
    }

    /**
     * Filter the query by a related ProductsWashingInstructions object
     *
     * @param   ProductsWashingInstructions|PropelObjectCollection $productsWashingInstructions  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 LanguagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsWashingInstructions($productsWashingInstructions, $comparison = null)
    {
        if ($productsWashingInstructions instanceof ProductsWashingInstructions) {
            return $this
                ->addUsingAlias(LanguagesPeer::LOCALE, $productsWashingInstructions->getLocale(), $comparison);
        } elseif ($productsWashingInstructions instanceof PropelObjectCollection) {
            return $this
                ->useProductsWashingInstructionsQuery()
                ->filterByPrimaryKeys($productsWashingInstructions->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsWashingInstructions() only accepts arguments of type ProductsWashingInstructions or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsWashingInstructions relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function joinProductsWashingInstructions($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsWashingInstructions');

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
            $this->addJoinObject($join, 'ProductsWashingInstructions');
        }

        return $this;
    }

    /**
     * Use the ProductsWashingInstructions relation ProductsWashingInstructions object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsWashingInstructionsQuery A secondary query class using the current class as primary query
     */
    public function useProductsWashingInstructionsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsWashingInstructions($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsWashingInstructions', '\Hanzo\Model\ProductsWashingInstructionsQuery');
    }

    /**
     * Filter the query by a related ProductsSeoI18n object
     *
     * @param   ProductsSeoI18n|PropelObjectCollection $productsSeoI18n  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 LanguagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsSeoI18n($productsSeoI18n, $comparison = null)
    {
        if ($productsSeoI18n instanceof ProductsSeoI18n) {
            return $this
                ->addUsingAlias(LanguagesPeer::LOCALE, $productsSeoI18n->getLocale(), $comparison);
        } elseif ($productsSeoI18n instanceof PropelObjectCollection) {
            return $this
                ->useProductsSeoI18nQuery()
                ->filterByPrimaryKeys($productsSeoI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsSeoI18n() only accepts arguments of type ProductsSeoI18n or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsSeoI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function joinProductsSeoI18n($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsSeoI18n');

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
            $this->addJoinObject($join, 'ProductsSeoI18n');
        }

        return $this;
    }

    /**
     * Use the ProductsSeoI18n relation ProductsSeoI18n object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsSeoI18nQuery A secondary query class using the current class as primary query
     */
    public function useProductsSeoI18nQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsSeoI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsSeoI18n', '\Hanzo\Model\ProductsSeoI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Languages $languages Object to remove from the list of results
     *
     * @return LanguagesQuery The current query, for fluid interface
     */
    public function prune($languages = null)
    {
        if ($languages) {
            $this->addUsingAlias(LanguagesPeer::ID, $languages->getId(), Criteria::NOT_EQUAL);
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

}

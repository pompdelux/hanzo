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
use Hanzo\Model\Cms;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nPeer;
use Hanzo\Model\CmsI18nQuery;

/**
 * @method CmsI18nQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CmsI18nQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 * @method CmsI18nQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method CmsI18nQuery orderByPath($order = Criteria::ASC) Order by the path column
 * @method CmsI18nQuery orderByOldPath($order = Criteria::ASC) Order by the old_path column
 * @method CmsI18nQuery orderByContent($order = Criteria::ASC) Order by the content column
 * @method CmsI18nQuery orderBySettings($order = Criteria::ASC) Order by the settings column
 * @method CmsI18nQuery orderByIsRestricted($order = Criteria::ASC) Order by the is_restricted column
 * @method CmsI18nQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 * @method CmsI18nQuery orderByOnMobile($order = Criteria::ASC) Order by the on_mobile column
 *
 * @method CmsI18nQuery groupById() Group by the id column
 * @method CmsI18nQuery groupByLocale() Group by the locale column
 * @method CmsI18nQuery groupByTitle() Group by the title column
 * @method CmsI18nQuery groupByPath() Group by the path column
 * @method CmsI18nQuery groupByOldPath() Group by the old_path column
 * @method CmsI18nQuery groupByContent() Group by the content column
 * @method CmsI18nQuery groupBySettings() Group by the settings column
 * @method CmsI18nQuery groupByIsRestricted() Group by the is_restricted column
 * @method CmsI18nQuery groupByIsActive() Group by the is_active column
 * @method CmsI18nQuery groupByOnMobile() Group by the on_mobile column
 *
 * @method CmsI18nQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CmsI18nQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CmsI18nQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CmsI18nQuery leftJoinCms($relationAlias = null) Adds a LEFT JOIN clause to the query using the Cms relation
 * @method CmsI18nQuery rightJoinCms($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Cms relation
 * @method CmsI18nQuery innerJoinCms($relationAlias = null) Adds a INNER JOIN clause to the query using the Cms relation
 *
 * @method CmsI18n findOne(PropelPDO $con = null) Return the first CmsI18n matching the query
 * @method CmsI18n findOneOrCreate(PropelPDO $con = null) Return the first CmsI18n matching the query, or a new CmsI18n object populated from the query conditions when no match is found
 *
 * @method CmsI18n findOneById(int $id) Return the first CmsI18n filtered by the id column
 * @method CmsI18n findOneByLocale(string $locale) Return the first CmsI18n filtered by the locale column
 * @method CmsI18n findOneByTitle(string $title) Return the first CmsI18n filtered by the title column
 * @method CmsI18n findOneByPath(string $path) Return the first CmsI18n filtered by the path column
 * @method CmsI18n findOneByOldPath(string $old_path) Return the first CmsI18n filtered by the old_path column
 * @method CmsI18n findOneByContent(string $content) Return the first CmsI18n filtered by the content column
 * @method CmsI18n findOneBySettings(string $settings) Return the first CmsI18n filtered by the settings column
 * @method CmsI18n findOneByIsRestricted(boolean $is_restricted) Return the first CmsI18n filtered by the is_restricted column
 * @method CmsI18n findOneByIsActive(boolean $is_active) Return the first CmsI18n filtered by the is_active column
 * @method CmsI18n findOneByOnMobile(boolean $on_mobile) Return the first CmsI18n filtered by the on_mobile column
 *
 * @method array findById(int $id) Return CmsI18n objects filtered by the id column
 * @method array findByLocale(string $locale) Return CmsI18n objects filtered by the locale column
 * @method array findByTitle(string $title) Return CmsI18n objects filtered by the title column
 * @method array findByPath(string $path) Return CmsI18n objects filtered by the path column
 * @method array findByOldPath(string $old_path) Return CmsI18n objects filtered by the old_path column
 * @method array findByContent(string $content) Return CmsI18n objects filtered by the content column
 * @method array findBySettings(string $settings) Return CmsI18n objects filtered by the settings column
 * @method array findByIsRestricted(boolean $is_restricted) Return CmsI18n objects filtered by the is_restricted column
 * @method array findByIsActive(boolean $is_active) Return CmsI18n objects filtered by the is_active column
 * @method array findByOnMobile(boolean $on_mobile) Return CmsI18n objects filtered by the on_mobile column
 */
abstract class BaseCmsI18nQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCmsI18nQuery object.
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
            $modelName = 'Hanzo\\Model\\CmsI18n';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CmsI18nQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CmsI18nQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CmsI18nQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CmsI18nQuery) {
            return $criteria;
        }
        $query = new CmsI18nQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$id, $locale]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CmsI18n|CmsI18n[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CmsI18nPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CmsI18nPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CmsI18n A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `locale`, `title`, `path`, `old_path`, `content`, `settings`, `is_restricted`, `is_active`, `on_mobile` FROM `cms_i18n` WHERE `id` = :p0 AND `locale` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CmsI18n();
            $obj->hydrate($row);
            CmsI18nPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return CmsI18n|CmsI18n[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CmsI18n[]|mixed the list of results, formatted by the current formatter
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
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CmsI18nPeer::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CmsI18nPeer::LOCALE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CmsI18nPeer::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CmsI18nPeer::LOCALE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByCms()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CmsI18nPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CmsI18nPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsI18nPeer::ID, $id, $comparison);
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
     * @return CmsI18nQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CmsI18nPeer::LOCALE, $locale, $comparison);
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
     * @return CmsI18nQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CmsI18nPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the path column
     *
     * Example usage:
     * <code>
     * $query->filterByPath('fooValue');   // WHERE path = 'fooValue'
     * $query->filterByPath('%fooValue%'); // WHERE path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $path The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByPath($path = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($path)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $path)) {
                $path = str_replace('*', '%', $path);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CmsI18nPeer::PATH, $path, $comparison);
    }

    /**
     * Filter the query on the old_path column
     *
     * Example usage:
     * <code>
     * $query->filterByOldPath('fooValue');   // WHERE old_path = 'fooValue'
     * $query->filterByOldPath('%fooValue%'); // WHERE old_path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $oldPath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByOldPath($oldPath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($oldPath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $oldPath)) {
                $oldPath = str_replace('*', '%', $oldPath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CmsI18nPeer::OLD_PATH, $oldPath, $comparison);
    }

    /**
     * Filter the query on the content column
     *
     * Example usage:
     * <code>
     * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
     * $query->filterByContent('%fooValue%'); // WHERE content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $content The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $content)) {
                $content = str_replace('*', '%', $content);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CmsI18nPeer::CONTENT, $content, $comparison);
    }

    /**
     * Filter the query on the settings column
     *
     * Example usage:
     * <code>
     * $query->filterBySettings('fooValue');   // WHERE settings = 'fooValue'
     * $query->filterBySettings('%fooValue%'); // WHERE settings LIKE '%fooValue%'
     * </code>
     *
     * @param     string $settings The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterBySettings($settings = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($settings)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $settings)) {
                $settings = str_replace('*', '%', $settings);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CmsI18nPeer::SETTINGS, $settings, $comparison);
    }

    /**
     * Filter the query on the is_restricted column
     *
     * Example usage:
     * <code>
     * $query->filterByIsRestricted(true); // WHERE is_restricted = true
     * $query->filterByIsRestricted('yes'); // WHERE is_restricted = true
     * </code>
     *
     * @param     boolean|string $isRestricted The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByIsRestricted($isRestricted = null, $comparison = null)
    {
        if (is_string($isRestricted)) {
            $isRestricted = in_array(strtolower($isRestricted), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CmsI18nPeer::IS_RESTRICTED, $isRestricted, $comparison);
    }

    /**
     * Filter the query on the is_active column
     *
     * Example usage:
     * <code>
     * $query->filterByIsActive(true); // WHERE is_active = true
     * $query->filterByIsActive('yes'); // WHERE is_active = true
     * </code>
     *
     * @param     boolean|string $isActive The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByIsActive($isActive = null, $comparison = null)
    {
        if (is_string($isActive)) {
            $isActive = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CmsI18nPeer::IS_ACTIVE, $isActive, $comparison);
    }

    /**
     * Filter the query on the on_mobile column
     *
     * Example usage:
     * <code>
     * $query->filterByOnMobile(true); // WHERE on_mobile = true
     * $query->filterByOnMobile('yes'); // WHERE on_mobile = true
     * </code>
     *
     * @param     boolean|string $onMobile The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function filterByOnMobile($onMobile = null, $comparison = null)
    {
        if (is_string($onMobile)) {
            $onMobile = in_array(strtolower($onMobile), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CmsI18nPeer::ON_MOBILE, $onMobile, $comparison);
    }

    /**
     * Filter the query by a related Cms object
     *
     * @param   Cms|PropelObjectCollection $cms The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsI18nQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCms($cms, $comparison = null)
    {
        if ($cms instanceof Cms) {
            return $this
                ->addUsingAlias(CmsI18nPeer::ID, $cms->getId(), $comparison);
        } elseif ($cms instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CmsI18nPeer::ID, $cms->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCms() only accepts arguments of type Cms or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Cms relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function joinCms($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Cms');

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
            $this->addJoinObject($join, 'Cms');
        }

        return $this;
    }

    /**
     * Use the Cms relation Cms object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CmsQuery A secondary query class using the current class as primary query
     */
    public function useCmsQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCms($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Cms', '\Hanzo\Model\CmsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CmsI18n $cmsI18n Object to remove from the list of results
     *
     * @return CmsI18nQuery The current query, for fluid interface
     */
    public function prune($cmsI18n = null)
    {
        if ($cmsI18n) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CmsI18nPeer::ID), $cmsI18n->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CmsI18nPeer::LOCALE), $cmsI18n->getLocale(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}

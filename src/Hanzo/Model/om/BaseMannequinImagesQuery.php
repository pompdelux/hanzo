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
use Hanzo\Model\MannequinImages;
use Hanzo\Model\MannequinImagesPeer;
use Hanzo\Model\MannequinImagesQuery;
use Hanzo\Model\Products;

/**
 * @method MannequinImagesQuery orderByMaster($order = Criteria::ASC) Order by the master column
 * @method MannequinImagesQuery orderByColor($order = Criteria::ASC) Order by the color column
 * @method MannequinImagesQuery orderByLayer($order = Criteria::ASC) Order by the layer column
 * @method MannequinImagesQuery orderByImage($order = Criteria::ASC) Order by the image column
 * @method MannequinImagesQuery orderByIcon($order = Criteria::ASC) Order by the icon column
 * @method MannequinImagesQuery orderByWeight($order = Criteria::ASC) Order by the weight column
 * @method MannequinImagesQuery orderByIsMain($order = Criteria::ASC) Order by the is_main column
 *
 * @method MannequinImagesQuery groupByMaster() Group by the master column
 * @method MannequinImagesQuery groupByColor() Group by the color column
 * @method MannequinImagesQuery groupByLayer() Group by the layer column
 * @method MannequinImagesQuery groupByImage() Group by the image column
 * @method MannequinImagesQuery groupByIcon() Group by the icon column
 * @method MannequinImagesQuery groupByWeight() Group by the weight column
 * @method MannequinImagesQuery groupByIsMain() Group by the is_main column
 *
 * @method MannequinImagesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method MannequinImagesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method MannequinImagesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method MannequinImagesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method MannequinImagesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method MannequinImagesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method MannequinImages findOne(PropelPDO $con = null) Return the first MannequinImages matching the query
 * @method MannequinImages findOneOrCreate(PropelPDO $con = null) Return the first MannequinImages matching the query, or a new MannequinImages object populated from the query conditions when no match is found
 *
 * @method MannequinImages findOneByMaster(string $master) Return the first MannequinImages filtered by the master column
 * @method MannequinImages findOneByColor(string $color) Return the first MannequinImages filtered by the color column
 * @method MannequinImages findOneByLayer(int $layer) Return the first MannequinImages filtered by the layer column
 * @method MannequinImages findOneByImage(string $image) Return the first MannequinImages filtered by the image column
 * @method MannequinImages findOneByIcon(string $icon) Return the first MannequinImages filtered by the icon column
 * @method MannequinImages findOneByWeight(int $weight) Return the first MannequinImages filtered by the weight column
 * @method MannequinImages findOneByIsMain(boolean $is_main) Return the first MannequinImages filtered by the is_main column
 *
 * @method array findByMaster(string $master) Return MannequinImages objects filtered by the master column
 * @method array findByColor(string $color) Return MannequinImages objects filtered by the color column
 * @method array findByLayer(int $layer) Return MannequinImages objects filtered by the layer column
 * @method array findByImage(string $image) Return MannequinImages objects filtered by the image column
 * @method array findByIcon(string $icon) Return MannequinImages objects filtered by the icon column
 * @method array findByWeight(int $weight) Return MannequinImages objects filtered by the weight column
 * @method array findByIsMain(boolean $is_main) Return MannequinImages objects filtered by the is_main column
 */
abstract class BaseMannequinImagesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseMannequinImagesQuery object.
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
            $modelName = 'Hanzo\\Model\\MannequinImages';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
    }

    /**
     * Returns a new MannequinImagesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   MannequinImagesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return MannequinImagesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof MannequinImagesQuery) {
            return $criteria;
        }
        $query = new MannequinImagesQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$master, $color]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   MannequinImages|MannequinImages[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MannequinImagesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(MannequinImagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 MannequinImages A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `master`, `color`, `layer`, `image`, `icon`, `weight`, `is_main` FROM `mannequin_images` WHERE `master` = :p0 AND `color` = :p1';
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
            $obj = new MannequinImages();
            $obj->hydrate($row);
            MannequinImagesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return MannequinImages|MannequinImages[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|MannequinImages[]|mixed the list of results, formatted by the current formatter
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
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(MannequinImagesPeer::MASTER, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(MannequinImagesPeer::COLOR, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(MannequinImagesPeer::MASTER, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(MannequinImagesPeer::COLOR, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the master column
     *
     * Example usage:
     * <code>
     * $query->filterByMaster('fooValue');   // WHERE master = 'fooValue'
     * $query->filterByMaster('%fooValue%'); // WHERE master LIKE '%fooValue%'
     * </code>
     *
     * @param     string $master The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByMaster($master = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($master)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $master)) {
                $master = str_replace('*', '%', $master);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MannequinImagesPeer::MASTER, $master, $comparison);
    }

    /**
     * Filter the query on the color column
     *
     * Example usage:
     * <code>
     * $query->filterByColor('fooValue');   // WHERE color = 'fooValue'
     * $query->filterByColor('%fooValue%'); // WHERE color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $color The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByColor($color = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($color)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $color)) {
                $color = str_replace('*', '%', $color);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MannequinImagesPeer::COLOR, $color, $comparison);
    }

    /**
     * Filter the query on the layer column
     *
     * Example usage:
     * <code>
     * $query->filterByLayer(1234); // WHERE layer = 1234
     * $query->filterByLayer(array(12, 34)); // WHERE layer IN (12, 34)
     * $query->filterByLayer(array('min' => 12)); // WHERE layer >= 12
     * $query->filterByLayer(array('max' => 12)); // WHERE layer <= 12
     * </code>
     *
     * @param     mixed $layer The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByLayer($layer = null, $comparison = null)
    {
        if (is_array($layer)) {
            $useMinMax = false;
            if (isset($layer['min'])) {
                $this->addUsingAlias(MannequinImagesPeer::LAYER, $layer['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($layer['max'])) {
                $this->addUsingAlias(MannequinImagesPeer::LAYER, $layer['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MannequinImagesPeer::LAYER, $layer, $comparison);
    }

    /**
     * Filter the query on the image column
     *
     * Example usage:
     * <code>
     * $query->filterByImage('fooValue');   // WHERE image = 'fooValue'
     * $query->filterByImage('%fooValue%'); // WHERE image LIKE '%fooValue%'
     * </code>
     *
     * @param     string $image The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByImage($image = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($image)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $image)) {
                $image = str_replace('*', '%', $image);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MannequinImagesPeer::IMAGE, $image, $comparison);
    }

    /**
     * Filter the query on the icon column
     *
     * Example usage:
     * <code>
     * $query->filterByIcon('fooValue');   // WHERE icon = 'fooValue'
     * $query->filterByIcon('%fooValue%'); // WHERE icon LIKE '%fooValue%'
     * </code>
     *
     * @param     string $icon The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByIcon($icon = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($icon)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $icon)) {
                $icon = str_replace('*', '%', $icon);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MannequinImagesPeer::ICON, $icon, $comparison);
    }

    /**
     * Filter the query on the weight column
     *
     * Example usage:
     * <code>
     * $query->filterByWeight(1234); // WHERE weight = 1234
     * $query->filterByWeight(array(12, 34)); // WHERE weight IN (12, 34)
     * $query->filterByWeight(array('min' => 12)); // WHERE weight >= 12
     * $query->filterByWeight(array('max' => 12)); // WHERE weight <= 12
     * </code>
     *
     * @param     mixed $weight The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByWeight($weight = null, $comparison = null)
    {
        if (is_array($weight)) {
            $useMinMax = false;
            if (isset($weight['min'])) {
                $this->addUsingAlias(MannequinImagesPeer::WEIGHT, $weight['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($weight['max'])) {
                $this->addUsingAlias(MannequinImagesPeer::WEIGHT, $weight['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MannequinImagesPeer::WEIGHT, $weight, $comparison);
    }

    /**
     * Filter the query on the is_main column
     *
     * Example usage:
     * <code>
     * $query->filterByIsMain(true); // WHERE is_main = true
     * $query->filterByIsMain('yes'); // WHERE is_main = true
     * </code>
     *
     * @param     boolean|string $isMain The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function filterByIsMain($isMain = null, $comparison = null)
    {
        if (is_string($isMain)) {
            $isMain = in_array(strtolower($isMain), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(MannequinImagesPeer::IS_MAIN, $isMain, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MannequinImagesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(MannequinImagesPeer::MASTER, $products->getSku(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MannequinImagesPeer::MASTER, $products->toKeyValue('PrimaryKey', 'Sku'), $comparison);
        } else {
            throw new PropelException('filterByProducts() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Products relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function joinProducts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Products');

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
            $this->addJoinObject($join, 'Products');
        }

        return $this;
    }

    /**
     * Use the Products relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProducts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Products', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   MannequinImages $mannequinImages Object to remove from the list of results
     *
     * @return MannequinImagesQuery The current query, for fluid interface
     */
    public function prune($mannequinImages = null)
    {
        if ($mannequinImages) {
            $this->addCond('pruneCond0', $this->getAliasedColName(MannequinImagesPeer::MASTER), $mannequinImages->getMaster(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(MannequinImagesPeer::COLOR), $mannequinImages->getColor(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
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

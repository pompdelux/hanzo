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
use Hanzo\Model\Products;
use Hanzo\Model\Wishlists;
use Hanzo\Model\WishlistsLines;
use Hanzo\Model\WishlistsLinesPeer;
use Hanzo\Model\WishlistsLinesQuery;

/**
 * @method WishlistsLinesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method WishlistsLinesQuery orderByWishlistsId($order = Criteria::ASC) Order by the wishlists_id column
 * @method WishlistsLinesQuery orderByProductsId($order = Criteria::ASC) Order by the products_id column
 * @method WishlistsLinesQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 *
 * @method WishlistsLinesQuery groupById() Group by the id column
 * @method WishlistsLinesQuery groupByWishlistsId() Group by the wishlists_id column
 * @method WishlistsLinesQuery groupByProductsId() Group by the products_id column
 * @method WishlistsLinesQuery groupByQuantity() Group by the quantity column
 *
 * @method WishlistsLinesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method WishlistsLinesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method WishlistsLinesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method WishlistsLinesQuery leftJoinWishlists($relationAlias = null) Adds a LEFT JOIN clause to the query using the Wishlists relation
 * @method WishlistsLinesQuery rightJoinWishlists($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Wishlists relation
 * @method WishlistsLinesQuery innerJoinWishlists($relationAlias = null) Adds a INNER JOIN clause to the query using the Wishlists relation
 *
 * @method WishlistsLinesQuery leftJoinProducts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Products relation
 * @method WishlistsLinesQuery rightJoinProducts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Products relation
 * @method WishlistsLinesQuery innerJoinProducts($relationAlias = null) Adds a INNER JOIN clause to the query using the Products relation
 *
 * @method WishlistsLines findOne(PropelPDO $con = null) Return the first WishlistsLines matching the query
 * @method WishlistsLines findOneOrCreate(PropelPDO $con = null) Return the first WishlistsLines matching the query, or a new WishlistsLines object populated from the query conditions when no match is found
 *
 * @method WishlistsLines findOneByWishlistsId(string $wishlists_id) Return the first WishlistsLines filtered by the wishlists_id column
 * @method WishlistsLines findOneByProductsId(int $products_id) Return the first WishlistsLines filtered by the products_id column
 * @method WishlistsLines findOneByQuantity(int $quantity) Return the first WishlistsLines filtered by the quantity column
 *
 * @method array findById(int $id) Return WishlistsLines objects filtered by the id column
 * @method array findByWishlistsId(string $wishlists_id) Return WishlistsLines objects filtered by the wishlists_id column
 * @method array findByProductsId(int $products_id) Return WishlistsLines objects filtered by the products_id column
 * @method array findByQuantity(int $quantity) Return WishlistsLines objects filtered by the quantity column
 */
abstract class BaseWishlistsLinesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseWishlistsLinesQuery object.
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
            $modelName = 'Hanzo\\Model\\WishlistsLines';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new WishlistsLinesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   WishlistsLinesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return WishlistsLinesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof WishlistsLinesQuery) {
            return $criteria;
        }
        $query = new WishlistsLinesQuery(null, null, $modelAlias);

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
     * @return   WishlistsLines|WishlistsLines[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = WishlistsLinesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(WishlistsLinesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 WishlistsLines A model object, or null if the key is not found
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
     * @return                 WishlistsLines A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `wishlists_id`, `products_id`, `quantity` FROM `wishlists_lines` WHERE `id` = :p0';
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
            $obj = new WishlistsLines();
            $obj->hydrate($row);
            WishlistsLinesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return WishlistsLines|WishlistsLines[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|WishlistsLines[]|mixed the list of results, formatted by the current formatter
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
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(WishlistsLinesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(WishlistsLinesPeer::ID, $keys, Criteria::IN);
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
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(WishlistsLinesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(WishlistsLinesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WishlistsLinesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the wishlists_id column
     *
     * Example usage:
     * <code>
     * $query->filterByWishlistsId('fooValue');   // WHERE wishlists_id = 'fooValue'
     * $query->filterByWishlistsId('%fooValue%'); // WHERE wishlists_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $wishlistsId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function filterByWishlistsId($wishlistsId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($wishlistsId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $wishlistsId)) {
                $wishlistsId = str_replace('*', '%', $wishlistsId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WishlistsLinesPeer::WISHLISTS_ID, $wishlistsId, $comparison);
    }

    /**
     * Filter the query on the products_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductsId(1234); // WHERE products_id = 1234
     * $query->filterByProductsId(array(12, 34)); // WHERE products_id IN (12, 34)
     * $query->filterByProductsId(array('min' => 12)); // WHERE products_id >= 12
     * $query->filterByProductsId(array('max' => 12)); // WHERE products_id <= 12
     * </code>
     *
     * @see       filterByProducts()
     *
     * @param     mixed $productsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function filterByProductsId($productsId = null, $comparison = null)
    {
        if (is_array($productsId)) {
            $useMinMax = false;
            if (isset($productsId['min'])) {
                $this->addUsingAlias(WishlistsLinesPeer::PRODUCTS_ID, $productsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productsId['max'])) {
                $this->addUsingAlias(WishlistsLinesPeer::PRODUCTS_ID, $productsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WishlistsLinesPeer::PRODUCTS_ID, $productsId, $comparison);
    }

    /**
     * Filter the query on the quantity column
     *
     * Example usage:
     * <code>
     * $query->filterByQuantity(1234); // WHERE quantity = 1234
     * $query->filterByQuantity(array(12, 34)); // WHERE quantity IN (12, 34)
     * $query->filterByQuantity(array('min' => 12)); // WHERE quantity >= 12
     * $query->filterByQuantity(array('max' => 12)); // WHERE quantity <= 12
     * </code>
     *
     * @param     mixed $quantity The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function filterByQuantity($quantity = null, $comparison = null)
    {
        if (is_array($quantity)) {
            $useMinMax = false;
            if (isset($quantity['min'])) {
                $this->addUsingAlias(WishlistsLinesPeer::QUANTITY, $quantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantity['max'])) {
                $this->addUsingAlias(WishlistsLinesPeer::QUANTITY, $quantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WishlistsLinesPeer::QUANTITY, $quantity, $comparison);
    }

    /**
     * Filter the query by a related Wishlists object
     *
     * @param   Wishlists|PropelObjectCollection $wishlists The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WishlistsLinesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByWishlists($wishlists, $comparison = null)
    {
        if ($wishlists instanceof Wishlists) {
            return $this
                ->addUsingAlias(WishlistsLinesPeer::WISHLISTS_ID, $wishlists->getId(), $comparison);
        } elseif ($wishlists instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WishlistsLinesPeer::WISHLISTS_ID, $wishlists->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByWishlists() only accepts arguments of type Wishlists or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Wishlists relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function joinWishlists($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Wishlists');

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
            $this->addJoinObject($join, 'Wishlists');
        }

        return $this;
    }

    /**
     * Use the Wishlists relation Wishlists object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\WishlistsQuery A secondary query class using the current class as primary query
     */
    public function useWishlistsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWishlists($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Wishlists', '\Hanzo\Model\WishlistsQuery');
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WishlistsLinesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProducts($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(WishlistsLinesPeer::PRODUCTS_ID, $products->getId(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WishlistsLinesPeer::PRODUCTS_ID, $products->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function joinProducts($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useProductsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProducts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Products', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   WishlistsLines $wishlistsLines Object to remove from the list of results
     *
     * @return WishlistsLinesQuery The current query, for fluid interface
     */
    public function prune($wishlistsLines = null)
    {
        if ($wishlistsLines) {
            $this->addUsingAlias(WishlistsLinesPeer::ID, $wishlistsLines->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

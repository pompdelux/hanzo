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
use Hanzo\Model\MannequinImages;
use Hanzo\Model\OrdersLines;
use Hanzo\Model\Products;
use Hanzo\Model\ProductsDomainsPrices;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsPeer;
use Hanzo\Model\ProductsQuantityDiscount;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStock;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsWashingInstructions;
use Hanzo\Model\RelatedProducts;
use Hanzo\Model\SearchProductsTags;

/**
 * @method ProductsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method ProductsQuery orderBySku($order = Criteria::ASC) Order by the sku column
 * @method ProductsQuery orderByMaster($order = Criteria::ASC) Order by the master column
 * @method ProductsQuery orderBySize($order = Criteria::ASC) Order by the size column
 * @method ProductsQuery orderByColor($order = Criteria::ASC) Order by the color column
 * @method ProductsQuery orderByUnit($order = Criteria::ASC) Order by the unit column
 * @method ProductsQuery orderByWashing($order = Criteria::ASC) Order by the washing column
 * @method ProductsQuery orderByHasVideo($order = Criteria::ASC) Order by the has_video column
 * @method ProductsQuery orderByIsOutOfStock($order = Criteria::ASC) Order by the is_out_of_stock column
 * @method ProductsQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 * @method ProductsQuery orderByIsVoucher($order = Criteria::ASC) Order by the is_voucher column
 * @method ProductsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method ProductsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method ProductsQuery groupById() Group by the id column
 * @method ProductsQuery groupBySku() Group by the sku column
 * @method ProductsQuery groupByMaster() Group by the master column
 * @method ProductsQuery groupBySize() Group by the size column
 * @method ProductsQuery groupByColor() Group by the color column
 * @method ProductsQuery groupByUnit() Group by the unit column
 * @method ProductsQuery groupByWashing() Group by the washing column
 * @method ProductsQuery groupByHasVideo() Group by the has_video column
 * @method ProductsQuery groupByIsOutOfStock() Group by the is_out_of_stock column
 * @method ProductsQuery groupByIsActive() Group by the is_active column
 * @method ProductsQuery groupByIsVoucher() Group by the is_voucher column
 * @method ProductsQuery groupByCreatedAt() Group by the created_at column
 * @method ProductsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method ProductsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ProductsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ProductsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ProductsQuery leftJoinProductsRelatedByMaster($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsRelatedByMaster relation
 * @method ProductsQuery rightJoinProductsRelatedByMaster($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsRelatedByMaster relation
 * @method ProductsQuery innerJoinProductsRelatedByMaster($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsRelatedByMaster relation
 *
 * @method ProductsQuery leftJoinProductsWashingInstructions($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsWashingInstructions relation
 * @method ProductsQuery rightJoinProductsWashingInstructions($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsWashingInstructions relation
 * @method ProductsQuery innerJoinProductsWashingInstructions($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsWashingInstructions relation
 *
 * @method ProductsQuery leftJoinMannequinImages($relationAlias = null) Adds a LEFT JOIN clause to the query using the MannequinImages relation
 * @method ProductsQuery rightJoinMannequinImages($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MannequinImages relation
 * @method ProductsQuery innerJoinMannequinImages($relationAlias = null) Adds a INNER JOIN clause to the query using the MannequinImages relation
 *
 * @method ProductsQuery leftJoinProductsRelatedBySku($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsRelatedBySku relation
 * @method ProductsQuery rightJoinProductsRelatedBySku($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsRelatedBySku relation
 * @method ProductsQuery innerJoinProductsRelatedBySku($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsRelatedBySku relation
 *
 * @method ProductsQuery leftJoinProductsDomainsPrices($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsDomainsPrices relation
 * @method ProductsQuery rightJoinProductsDomainsPrices($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsDomainsPrices relation
 * @method ProductsQuery innerJoinProductsDomainsPrices($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsDomainsPrices relation
 *
 * @method ProductsQuery leftJoinProductsImages($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImages relation
 * @method ProductsQuery rightJoinProductsImages($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImages relation
 * @method ProductsQuery innerJoinProductsImages($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImages relation
 *
 * @method ProductsQuery leftJoinProductsImagesCategoriesSort($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImagesCategoriesSort relation
 * @method ProductsQuery rightJoinProductsImagesCategoriesSort($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImagesCategoriesSort relation
 * @method ProductsQuery innerJoinProductsImagesCategoriesSort($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImagesCategoriesSort relation
 *
 * @method ProductsQuery leftJoinProductsImagesProductReferences($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsImagesProductReferences relation
 * @method ProductsQuery rightJoinProductsImagesProductReferences($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsImagesProductReferences relation
 * @method ProductsQuery innerJoinProductsImagesProductReferences($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsImagesProductReferences relation
 *
 * @method ProductsQuery leftJoinProductsQuantityDiscount($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsQuantityDiscount relation
 * @method ProductsQuery rightJoinProductsQuantityDiscount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsQuantityDiscount relation
 * @method ProductsQuery innerJoinProductsQuantityDiscount($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsQuantityDiscount relation
 *
 * @method ProductsQuery leftJoinProductsStock($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsStock relation
 * @method ProductsQuery rightJoinProductsStock($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsStock relation
 * @method ProductsQuery innerJoinProductsStock($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsStock relation
 *
 * @method ProductsQuery leftJoinProductsToCategories($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsToCategories relation
 * @method ProductsQuery rightJoinProductsToCategories($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsToCategories relation
 * @method ProductsQuery innerJoinProductsToCategories($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsToCategories relation
 *
 * @method ProductsQuery leftJoinOrdersLines($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrdersLines relation
 * @method ProductsQuery rightJoinOrdersLines($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrdersLines relation
 * @method ProductsQuery innerJoinOrdersLines($relationAlias = null) Adds a INNER JOIN clause to the query using the OrdersLines relation
 *
 * @method ProductsQuery leftJoinRelatedProductsRelatedByMaster($relationAlias = null) Adds a LEFT JOIN clause to the query using the RelatedProductsRelatedByMaster relation
 * @method ProductsQuery rightJoinRelatedProductsRelatedByMaster($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RelatedProductsRelatedByMaster relation
 * @method ProductsQuery innerJoinRelatedProductsRelatedByMaster($relationAlias = null) Adds a INNER JOIN clause to the query using the RelatedProductsRelatedByMaster relation
 *
 * @method ProductsQuery leftJoinRelatedProductsRelatedBySku($relationAlias = null) Adds a LEFT JOIN clause to the query using the RelatedProductsRelatedBySku relation
 * @method ProductsQuery rightJoinRelatedProductsRelatedBySku($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RelatedProductsRelatedBySku relation
 * @method ProductsQuery innerJoinRelatedProductsRelatedBySku($relationAlias = null) Adds a INNER JOIN clause to the query using the RelatedProductsRelatedBySku relation
 *
 * @method ProductsQuery leftJoinSearchProductsTagsRelatedByMasterProductsId($relationAlias = null) Adds a LEFT JOIN clause to the query using the SearchProductsTagsRelatedByMasterProductsId relation
 * @method ProductsQuery rightJoinSearchProductsTagsRelatedByMasterProductsId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SearchProductsTagsRelatedByMasterProductsId relation
 * @method ProductsQuery innerJoinSearchProductsTagsRelatedByMasterProductsId($relationAlias = null) Adds a INNER JOIN clause to the query using the SearchProductsTagsRelatedByMasterProductsId relation
 *
 * @method ProductsQuery leftJoinSearchProductsTagsRelatedByProductsId($relationAlias = null) Adds a LEFT JOIN clause to the query using the SearchProductsTagsRelatedByProductsId relation
 * @method ProductsQuery rightJoinSearchProductsTagsRelatedByProductsId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SearchProductsTagsRelatedByProductsId relation
 * @method ProductsQuery innerJoinSearchProductsTagsRelatedByProductsId($relationAlias = null) Adds a INNER JOIN clause to the query using the SearchProductsTagsRelatedByProductsId relation
 *
 * @method ProductsQuery leftJoinProductsI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductsI18n relation
 * @method ProductsQuery rightJoinProductsI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductsI18n relation
 * @method ProductsQuery innerJoinProductsI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductsI18n relation
 *
 * @method Products findOne(PropelPDO $con = null) Return the first Products matching the query
 * @method Products findOneOrCreate(PropelPDO $con = null) Return the first Products matching the query, or a new Products object populated from the query conditions when no match is found
 *
 * @method Products findOneBySku(string $sku) Return the first Products filtered by the sku column
 * @method Products findOneByMaster(string $master) Return the first Products filtered by the master column
 * @method Products findOneBySize(string $size) Return the first Products filtered by the size column
 * @method Products findOneByColor(string $color) Return the first Products filtered by the color column
 * @method Products findOneByUnit(string $unit) Return the first Products filtered by the unit column
 * @method Products findOneByWashing(int $washing) Return the first Products filtered by the washing column
 * @method Products findOneByHasVideo(boolean $has_video) Return the first Products filtered by the has_video column
 * @method Products findOneByIsOutOfStock(boolean $is_out_of_stock) Return the first Products filtered by the is_out_of_stock column
 * @method Products findOneByIsActive(boolean $is_active) Return the first Products filtered by the is_active column
 * @method Products findOneByIsVoucher(boolean $is_voucher) Return the first Products filtered by the is_voucher column
 * @method Products findOneByCreatedAt(string $created_at) Return the first Products filtered by the created_at column
 * @method Products findOneByUpdatedAt(string $updated_at) Return the first Products filtered by the updated_at column
 *
 * @method array findById(int $id) Return Products objects filtered by the id column
 * @method array findBySku(string $sku) Return Products objects filtered by the sku column
 * @method array findByMaster(string $master) Return Products objects filtered by the master column
 * @method array findBySize(string $size) Return Products objects filtered by the size column
 * @method array findByColor(string $color) Return Products objects filtered by the color column
 * @method array findByUnit(string $unit) Return Products objects filtered by the unit column
 * @method array findByWashing(int $washing) Return Products objects filtered by the washing column
 * @method array findByHasVideo(boolean $has_video) Return Products objects filtered by the has_video column
 * @method array findByIsOutOfStock(boolean $is_out_of_stock) Return Products objects filtered by the is_out_of_stock column
 * @method array findByIsActive(boolean $is_active) Return Products objects filtered by the is_active column
 * @method array findByIsVoucher(boolean $is_voucher) Return Products objects filtered by the is_voucher column
 * @method array findByCreatedAt(string $created_at) Return Products objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Products objects filtered by the updated_at column
 */
abstract class BaseProductsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseProductsQuery object.
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
            $modelName = 'Hanzo\\Model\\Products';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ProductsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ProductsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ProductsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ProductsQuery) {
            return $criteria;
        }
        $query = new ProductsQuery(null, null, $modelAlias);

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
     * @return   Products|Products[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ProductsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Products A model object, or null if the key is not found
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
     * @return                 Products A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `sku`, `master`, `size`, `color`, `unit`, `washing`, `has_video`, `is_out_of_stock`, `is_active`, `is_voucher`, `created_at`, `updated_at` FROM `products` WHERE `id` = :p0';
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
            $obj = new Products();
            $obj->hydrate($row);
            ProductsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Products|Products[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Products[]|mixed the list of results, formatted by the current formatter
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
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProductsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProductsPeer::ID, $keys, Criteria::IN);
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
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProductsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProductsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the sku column
     *
     * Example usage:
     * <code>
     * $query->filterBySku('fooValue');   // WHERE sku = 'fooValue'
     * $query->filterBySku('%fooValue%'); // WHERE sku LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sku The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterBySku($sku = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sku)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sku)) {
                $sku = str_replace('*', '%', $sku);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductsPeer::SKU, $sku, $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ProductsPeer::MASTER, $master, $comparison);
    }

    /**
     * Filter the query on the size column
     *
     * Example usage:
     * <code>
     * $query->filterBySize('fooValue');   // WHERE size = 'fooValue'
     * $query->filterBySize('%fooValue%'); // WHERE size LIKE '%fooValue%'
     * </code>
     *
     * @param     string $size The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterBySize($size = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($size)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $size)) {
                $size = str_replace('*', '%', $size);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductsPeer::SIZE, $size, $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ProductsPeer::COLOR, $color, $comparison);
    }

    /**
     * Filter the query on the unit column
     *
     * Example usage:
     * <code>
     * $query->filterByUnit('fooValue');   // WHERE unit = 'fooValue'
     * $query->filterByUnit('%fooValue%'); // WHERE unit LIKE '%fooValue%'
     * </code>
     *
     * @param     string $unit The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByUnit($unit = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($unit)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $unit)) {
                $unit = str_replace('*', '%', $unit);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProductsPeer::UNIT, $unit, $comparison);
    }

    /**
     * Filter the query on the washing column
     *
     * Example usage:
     * <code>
     * $query->filterByWashing(1234); // WHERE washing = 1234
     * $query->filterByWashing(array(12, 34)); // WHERE washing IN (12, 34)
     * $query->filterByWashing(array('min' => 12)); // WHERE washing >= 12
     * $query->filterByWashing(array('max' => 12)); // WHERE washing <= 12
     * </code>
     *
     * @see       filterByProductsWashingInstructions()
     *
     * @param     mixed $washing The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByWashing($washing = null, $comparison = null)
    {
        if (is_array($washing)) {
            $useMinMax = false;
            if (isset($washing['min'])) {
                $this->addUsingAlias(ProductsPeer::WASHING, $washing['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($washing['max'])) {
                $this->addUsingAlias(ProductsPeer::WASHING, $washing['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsPeer::WASHING, $washing, $comparison);
    }

    /**
     * Filter the query on the has_video column
     *
     * Example usage:
     * <code>
     * $query->filterByHasVideo(true); // WHERE has_video = true
     * $query->filterByHasVideo('yes'); // WHERE has_video = true
     * </code>
     *
     * @param     boolean|string $hasVideo The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByHasVideo($hasVideo = null, $comparison = null)
    {
        if (is_string($hasVideo)) {
            $hasVideo = in_array(strtolower($hasVideo), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ProductsPeer::HAS_VIDEO, $hasVideo, $comparison);
    }

    /**
     * Filter the query on the is_out_of_stock column
     *
     * Example usage:
     * <code>
     * $query->filterByIsOutOfStock(true); // WHERE is_out_of_stock = true
     * $query->filterByIsOutOfStock('yes'); // WHERE is_out_of_stock = true
     * </code>
     *
     * @param     boolean|string $isOutOfStock The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByIsOutOfStock($isOutOfStock = null, $comparison = null)
    {
        if (is_string($isOutOfStock)) {
            $isOutOfStock = in_array(strtolower($isOutOfStock), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ProductsPeer::IS_OUT_OF_STOCK, $isOutOfStock, $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByIsActive($isActive = null, $comparison = null)
    {
        if (is_string($isActive)) {
            $isActive = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ProductsPeer::IS_ACTIVE, $isActive, $comparison);
    }

    /**
     * Filter the query on the is_voucher column
     *
     * Example usage:
     * <code>
     * $query->filterByIsVoucher(true); // WHERE is_voucher = true
     * $query->filterByIsVoucher('yes'); // WHERE is_voucher = true
     * </code>
     *
     * @param     boolean|string $isVoucher The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByIsVoucher($isVoucher = null, $comparison = null)
    {
        if (is_string($isVoucher)) {
            $isVoucher = in_array(strtolower($isVoucher), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ProductsPeer::IS_VOUCHER, $isVoucher, $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ProductsPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ProductsPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsPeer::CREATED_AT, $createdAt, $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ProductsPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ProductsPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductsPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsRelatedByMaster($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsPeer::MASTER, $products->getSku(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsPeer::MASTER, $products->toKeyValue('PrimaryKey', 'Sku'), $comparison);
        } else {
            throw new PropelException('filterByProductsRelatedByMaster() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsRelatedByMaster relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsRelatedByMaster($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsRelatedByMaster');

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
            $this->addJoinObject($join, 'ProductsRelatedByMaster');
        }

        return $this;
    }

    /**
     * Use the ProductsRelatedByMaster relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsRelatedByMasterQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductsRelatedByMaster($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsRelatedByMaster', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Filter the query by a related ProductsWashingInstructions object
     *
     * @param   ProductsWashingInstructions|PropelObjectCollection $productsWashingInstructions The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsWashingInstructions($productsWashingInstructions, $comparison = null)
    {
        if ($productsWashingInstructions instanceof ProductsWashingInstructions) {
            return $this
                ->addUsingAlias(ProductsPeer::WASHING, $productsWashingInstructions->getCode(), $comparison);
        } elseif ($productsWashingInstructions instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductsPeer::WASHING, $productsWashingInstructions->toKeyValue('PrimaryKey', 'Code'), $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsWashingInstructions($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useProductsWashingInstructionsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductsWashingInstructions($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsWashingInstructions', '\Hanzo\Model\ProductsWashingInstructionsQuery');
    }

    /**
     * Filter the query by a related MannequinImages object
     *
     * @param   MannequinImages|PropelObjectCollection $mannequinImages  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMannequinImages($mannequinImages, $comparison = null)
    {
        if ($mannequinImages instanceof MannequinImages) {
            return $this
                ->addUsingAlias(ProductsPeer::SKU, $mannequinImages->getMaster(), $comparison);
        } elseif ($mannequinImages instanceof PropelObjectCollection) {
            return $this
                ->useMannequinImagesQuery()
                ->filterByPrimaryKeys($mannequinImages->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMannequinImages() only accepts arguments of type MannequinImages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MannequinImages relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinMannequinImages($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MannequinImages');

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
            $this->addJoinObject($join, 'MannequinImages');
        }

        return $this;
    }

    /**
     * Use the MannequinImages relation MannequinImages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\MannequinImagesQuery A secondary query class using the current class as primary query
     */
    public function useMannequinImagesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMannequinImages($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MannequinImages', '\Hanzo\Model\MannequinImagesQuery');
    }

    /**
     * Filter the query by a related Products object
     *
     * @param   Products|PropelObjectCollection $products  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsRelatedBySku($products, $comparison = null)
    {
        if ($products instanceof Products) {
            return $this
                ->addUsingAlias(ProductsPeer::SKU, $products->getMaster(), $comparison);
        } elseif ($products instanceof PropelObjectCollection) {
            return $this
                ->useProductsRelatedBySkuQuery()
                ->filterByPrimaryKeys($products->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsRelatedBySku() only accepts arguments of type Products or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsRelatedBySku relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsRelatedBySku($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsRelatedBySku');

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
            $this->addJoinObject($join, 'ProductsRelatedBySku');
        }

        return $this;
    }

    /**
     * Use the ProductsRelatedBySku relation Products object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsQuery A secondary query class using the current class as primary query
     */
    public function useProductsRelatedBySkuQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductsRelatedBySku($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsRelatedBySku', '\Hanzo\Model\ProductsQuery');
    }

    /**
     * Filter the query by a related ProductsDomainsPrices object
     *
     * @param   ProductsDomainsPrices|PropelObjectCollection $productsDomainsPrices  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsDomainsPrices($productsDomainsPrices, $comparison = null)
    {
        if ($productsDomainsPrices instanceof ProductsDomainsPrices) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsDomainsPrices->getProductsId(), $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
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
     * Filter the query by a related ProductsImages object
     *
     * @param   ProductsImages|PropelObjectCollection $productsImages  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImages($productsImages, $comparison = null)
    {
        if ($productsImages instanceof ProductsImages) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsImages->getProductsId(), $comparison);
        } elseif ($productsImages instanceof PropelObjectCollection) {
            return $this
                ->useProductsImagesQuery()
                ->filterByPrimaryKeys($productsImages->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsImages() only accepts arguments of type ProductsImages or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsImages relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsImages($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsImages');

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
            $this->addJoinObject($join, 'ProductsImages');
        }

        return $this;
    }

    /**
     * Use the ProductsImages relation ProductsImages object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsImagesQuery A secondary query class using the current class as primary query
     */
    public function useProductsImagesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsImages($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsImages', '\Hanzo\Model\ProductsImagesQuery');
    }

    /**
     * Filter the query by a related ProductsImagesCategoriesSort object
     *
     * @param   ProductsImagesCategoriesSort|PropelObjectCollection $productsImagesCategoriesSort  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImagesCategoriesSort($productsImagesCategoriesSort, $comparison = null)
    {
        if ($productsImagesCategoriesSort instanceof ProductsImagesCategoriesSort) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsImagesCategoriesSort->getProductsId(), $comparison);
        } elseif ($productsImagesCategoriesSort instanceof PropelObjectCollection) {
            return $this
                ->useProductsImagesCategoriesSortQuery()
                ->filterByPrimaryKeys($productsImagesCategoriesSort->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsImagesCategoriesSort() only accepts arguments of type ProductsImagesCategoriesSort or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsImagesCategoriesSort relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsImagesCategoriesSort($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsImagesCategoriesSort');

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
            $this->addJoinObject($join, 'ProductsImagesCategoriesSort');
        }

        return $this;
    }

    /**
     * Use the ProductsImagesCategoriesSort relation ProductsImagesCategoriesSort object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsImagesCategoriesSortQuery A secondary query class using the current class as primary query
     */
    public function useProductsImagesCategoriesSortQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsImagesCategoriesSort($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsImagesCategoriesSort', '\Hanzo\Model\ProductsImagesCategoriesSortQuery');
    }

    /**
     * Filter the query by a related ProductsImagesProductReferences object
     *
     * @param   ProductsImagesProductReferences|PropelObjectCollection $productsImagesProductReferences  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsImagesProductReferences($productsImagesProductReferences, $comparison = null)
    {
        if ($productsImagesProductReferences instanceof ProductsImagesProductReferences) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsImagesProductReferences->getProductsId(), $comparison);
        } elseif ($productsImagesProductReferences instanceof PropelObjectCollection) {
            return $this
                ->useProductsImagesProductReferencesQuery()
                ->filterByPrimaryKeys($productsImagesProductReferences->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsImagesProductReferences() only accepts arguments of type ProductsImagesProductReferences or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsImagesProductReferences relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsImagesProductReferences($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsImagesProductReferences');

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
            $this->addJoinObject($join, 'ProductsImagesProductReferences');
        }

        return $this;
    }

    /**
     * Use the ProductsImagesProductReferences relation ProductsImagesProductReferences object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsImagesProductReferencesQuery A secondary query class using the current class as primary query
     */
    public function useProductsImagesProductReferencesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsImagesProductReferences($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsImagesProductReferences', '\Hanzo\Model\ProductsImagesProductReferencesQuery');
    }

    /**
     * Filter the query by a related ProductsQuantityDiscount object
     *
     * @param   ProductsQuantityDiscount|PropelObjectCollection $productsQuantityDiscount  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsQuantityDiscount($productsQuantityDiscount, $comparison = null)
    {
        if ($productsQuantityDiscount instanceof ProductsQuantityDiscount) {
            return $this
                ->addUsingAlias(ProductsPeer::SKU, $productsQuantityDiscount->getProductsMaster(), $comparison);
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
     * @return ProductsQuery The current query, for fluid interface
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
     * Filter the query by a related ProductsStock object
     *
     * @param   ProductsStock|PropelObjectCollection $productsStock  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsStock($productsStock, $comparison = null)
    {
        if ($productsStock instanceof ProductsStock) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsStock->getProductsId(), $comparison);
        } elseif ($productsStock instanceof PropelObjectCollection) {
            return $this
                ->useProductsStockQuery()
                ->filterByPrimaryKeys($productsStock->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsStock() only accepts arguments of type ProductsStock or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsStock relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsStock($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsStock');

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
            $this->addJoinObject($join, 'ProductsStock');
        }

        return $this;
    }

    /**
     * Use the ProductsStock relation ProductsStock object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsStockQuery A secondary query class using the current class as primary query
     */
    public function useProductsStockQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsStock($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsStock', '\Hanzo\Model\ProductsStockQuery');
    }

    /**
     * Filter the query by a related ProductsToCategories object
     *
     * @param   ProductsToCategories|PropelObjectCollection $productsToCategories  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsToCategories($productsToCategories, $comparison = null)
    {
        if ($productsToCategories instanceof ProductsToCategories) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsToCategories->getProductsId(), $comparison);
        } elseif ($productsToCategories instanceof PropelObjectCollection) {
            return $this
                ->useProductsToCategoriesQuery()
                ->filterByPrimaryKeys($productsToCategories->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsToCategories() only accepts arguments of type ProductsToCategories or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsToCategories relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsToCategories($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsToCategories');

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
            $this->addJoinObject($join, 'ProductsToCategories');
        }

        return $this;
    }

    /**
     * Use the ProductsToCategories relation ProductsToCategories object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsToCategoriesQuery A secondary query class using the current class as primary query
     */
    public function useProductsToCategoriesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductsToCategories($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsToCategories', '\Hanzo\Model\ProductsToCategoriesQuery');
    }

    /**
     * Filter the query by a related OrdersLines object
     *
     * @param   OrdersLines|PropelObjectCollection $ordersLines  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByOrdersLines($ordersLines, $comparison = null)
    {
        if ($ordersLines instanceof OrdersLines) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $ordersLines->getProductsId(), $comparison);
        } elseif ($ordersLines instanceof PropelObjectCollection) {
            return $this
                ->useOrdersLinesQuery()
                ->filterByPrimaryKeys($ordersLines->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrdersLines() only accepts arguments of type OrdersLines or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrdersLines relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinOrdersLines($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrdersLines');

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
            $this->addJoinObject($join, 'OrdersLines');
        }

        return $this;
    }

    /**
     * Use the OrdersLines relation OrdersLines object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\OrdersLinesQuery A secondary query class using the current class as primary query
     */
    public function useOrdersLinesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinOrdersLines($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrdersLines', '\Hanzo\Model\OrdersLinesQuery');
    }

    /**
     * Filter the query by a related RelatedProducts object
     *
     * @param   RelatedProducts|PropelObjectCollection $relatedProducts  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRelatedProductsRelatedByMaster($relatedProducts, $comparison = null)
    {
        if ($relatedProducts instanceof RelatedProducts) {
            return $this
                ->addUsingAlias(ProductsPeer::SKU, $relatedProducts->getMaster(), $comparison);
        } elseif ($relatedProducts instanceof PropelObjectCollection) {
            return $this
                ->useRelatedProductsRelatedByMasterQuery()
                ->filterByPrimaryKeys($relatedProducts->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRelatedProductsRelatedByMaster() only accepts arguments of type RelatedProducts or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the RelatedProductsRelatedByMaster relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinRelatedProductsRelatedByMaster($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('RelatedProductsRelatedByMaster');

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
            $this->addJoinObject($join, 'RelatedProductsRelatedByMaster');
        }

        return $this;
    }

    /**
     * Use the RelatedProductsRelatedByMaster relation RelatedProducts object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\RelatedProductsQuery A secondary query class using the current class as primary query
     */
    public function useRelatedProductsRelatedByMasterQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRelatedProductsRelatedByMaster($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RelatedProductsRelatedByMaster', '\Hanzo\Model\RelatedProductsQuery');
    }

    /**
     * Filter the query by a related RelatedProducts object
     *
     * @param   RelatedProducts|PropelObjectCollection $relatedProducts  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRelatedProductsRelatedBySku($relatedProducts, $comparison = null)
    {
        if ($relatedProducts instanceof RelatedProducts) {
            return $this
                ->addUsingAlias(ProductsPeer::SKU, $relatedProducts->getSku(), $comparison);
        } elseif ($relatedProducts instanceof PropelObjectCollection) {
            return $this
                ->useRelatedProductsRelatedBySkuQuery()
                ->filterByPrimaryKeys($relatedProducts->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRelatedProductsRelatedBySku() only accepts arguments of type RelatedProducts or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the RelatedProductsRelatedBySku relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinRelatedProductsRelatedBySku($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('RelatedProductsRelatedBySku');

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
            $this->addJoinObject($join, 'RelatedProductsRelatedBySku');
        }

        return $this;
    }

    /**
     * Use the RelatedProductsRelatedBySku relation RelatedProducts object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\RelatedProductsQuery A secondary query class using the current class as primary query
     */
    public function useRelatedProductsRelatedBySkuQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRelatedProductsRelatedBySku($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RelatedProductsRelatedBySku', '\Hanzo\Model\RelatedProductsQuery');
    }

    /**
     * Filter the query by a related SearchProductsTags object
     *
     * @param   SearchProductsTags|PropelObjectCollection $searchProductsTags  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySearchProductsTagsRelatedByMasterProductsId($searchProductsTags, $comparison = null)
    {
        if ($searchProductsTags instanceof SearchProductsTags) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $searchProductsTags->getMasterProductsId(), $comparison);
        } elseif ($searchProductsTags instanceof PropelObjectCollection) {
            return $this
                ->useSearchProductsTagsRelatedByMasterProductsIdQuery()
                ->filterByPrimaryKeys($searchProductsTags->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySearchProductsTagsRelatedByMasterProductsId() only accepts arguments of type SearchProductsTags or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SearchProductsTagsRelatedByMasterProductsId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinSearchProductsTagsRelatedByMasterProductsId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SearchProductsTagsRelatedByMasterProductsId');

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
            $this->addJoinObject($join, 'SearchProductsTagsRelatedByMasterProductsId');
        }

        return $this;
    }

    /**
     * Use the SearchProductsTagsRelatedByMasterProductsId relation SearchProductsTags object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\SearchProductsTagsQuery A secondary query class using the current class as primary query
     */
    public function useSearchProductsTagsRelatedByMasterProductsIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSearchProductsTagsRelatedByMasterProductsId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SearchProductsTagsRelatedByMasterProductsId', '\Hanzo\Model\SearchProductsTagsQuery');
    }

    /**
     * Filter the query by a related SearchProductsTags object
     *
     * @param   SearchProductsTags|PropelObjectCollection $searchProductsTags  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySearchProductsTagsRelatedByProductsId($searchProductsTags, $comparison = null)
    {
        if ($searchProductsTags instanceof SearchProductsTags) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $searchProductsTags->getProductsId(), $comparison);
        } elseif ($searchProductsTags instanceof PropelObjectCollection) {
            return $this
                ->useSearchProductsTagsRelatedByProductsIdQuery()
                ->filterByPrimaryKeys($searchProductsTags->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySearchProductsTagsRelatedByProductsId() only accepts arguments of type SearchProductsTags or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SearchProductsTagsRelatedByProductsId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinSearchProductsTagsRelatedByProductsId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SearchProductsTagsRelatedByProductsId');

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
            $this->addJoinObject($join, 'SearchProductsTagsRelatedByProductsId');
        }

        return $this;
    }

    /**
     * Use the SearchProductsTagsRelatedByProductsId relation SearchProductsTags object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\SearchProductsTagsQuery A secondary query class using the current class as primary query
     */
    public function useSearchProductsTagsRelatedByProductsIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSearchProductsTagsRelatedByProductsId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SearchProductsTagsRelatedByProductsId', '\Hanzo\Model\SearchProductsTagsQuery');
    }

    /**
     * Filter the query by a related ProductsI18n object
     *
     * @param   ProductsI18n|PropelObjectCollection $productsI18n  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ProductsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByProductsI18n($productsI18n, $comparison = null)
    {
        if ($productsI18n instanceof ProductsI18n) {
            return $this
                ->addUsingAlias(ProductsPeer::ID, $productsI18n->getId(), $comparison);
        } elseif ($productsI18n instanceof PropelObjectCollection) {
            return $this
                ->useProductsI18nQuery()
                ->filterByPrimaryKeys($productsI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductsI18n() only accepts arguments of type ProductsI18n or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductsI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function joinProductsI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductsI18n');

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
            $this->addJoinObject($join, 'ProductsI18n');
        }

        return $this;
    }

    /**
     * Use the ProductsI18n relation ProductsI18n object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\ProductsI18nQuery A secondary query class using the current class as primary query
     */
    public function useProductsI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinProductsI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsI18n', '\Hanzo\Model\ProductsI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Products $products Object to remove from the list of results
     *
     * @return ProductsQuery The current query, for fluid interface
     */
    public function prune($products = null)
    {
        if ($products) {
            $this->addUsingAlias(ProductsPeer::ID, $products->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ProductsQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'da_DK', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'ProductsI18n';

        return $this
            ->joinProductsI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ProductsQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'da_DK', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('ProductsI18n');
        $this->with['ProductsI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ProductsI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'da_DK', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductsI18n', 'Hanzo\Model\ProductsI18nQuery');
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ProductsQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ProductsPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ProductsQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProductsPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ProductsQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProductsPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ProductsQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ProductsPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     ProductsQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ProductsPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ProductsQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ProductsPeer::CREATED_AT);
    }
}

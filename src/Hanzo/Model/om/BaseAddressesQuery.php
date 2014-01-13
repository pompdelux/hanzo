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
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesPeer;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\Countries;
use Hanzo\Model\Customers;

/**
 * @method AddressesQuery orderByCustomersId($order = Criteria::ASC) Order by the customers_id column
 * @method AddressesQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method AddressesQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method AddressesQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method AddressesQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 * @method AddressesQuery orderByAddressLine1($order = Criteria::ASC) Order by the address_line_1 column
 * @method AddressesQuery orderByAddressLine2($order = Criteria::ASC) Order by the address_line_2 column
 * @method AddressesQuery orderByPostalCode($order = Criteria::ASC) Order by the postal_code column
 * @method AddressesQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method AddressesQuery orderByCountry($order = Criteria::ASC) Order by the country column
 * @method AddressesQuery orderByCountriesId($order = Criteria::ASC) Order by the countries_id column
 * @method AddressesQuery orderByStateProvince($order = Criteria::ASC) Order by the state_province column
 * @method AddressesQuery orderByCompanyName($order = Criteria::ASC) Order by the company_name column
 * @method AddressesQuery orderByExternalAddressId($order = Criteria::ASC) Order by the external_address_id column
 * @method AddressesQuery orderByLatitude($order = Criteria::ASC) Order by the latitude column
 * @method AddressesQuery orderByLongitude($order = Criteria::ASC) Order by the longitude column
 * @method AddressesQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method AddressesQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method AddressesQuery groupByCustomersId() Group by the customers_id column
 * @method AddressesQuery groupByType() Group by the type column
 * @method AddressesQuery groupByTitle() Group by the title column
 * @method AddressesQuery groupByFirstName() Group by the first_name column
 * @method AddressesQuery groupByLastName() Group by the last_name column
 * @method AddressesQuery groupByAddressLine1() Group by the address_line_1 column
 * @method AddressesQuery groupByAddressLine2() Group by the address_line_2 column
 * @method AddressesQuery groupByPostalCode() Group by the postal_code column
 * @method AddressesQuery groupByCity() Group by the city column
 * @method AddressesQuery groupByCountry() Group by the country column
 * @method AddressesQuery groupByCountriesId() Group by the countries_id column
 * @method AddressesQuery groupByStateProvince() Group by the state_province column
 * @method AddressesQuery groupByCompanyName() Group by the company_name column
 * @method AddressesQuery groupByExternalAddressId() Group by the external_address_id column
 * @method AddressesQuery groupByLatitude() Group by the latitude column
 * @method AddressesQuery groupByLongitude() Group by the longitude column
 * @method AddressesQuery groupByCreatedAt() Group by the created_at column
 * @method AddressesQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method AddressesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AddressesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AddressesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AddressesQuery leftJoinCustomers($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customers relation
 * @method AddressesQuery rightJoinCustomers($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customers relation
 * @method AddressesQuery innerJoinCustomers($relationAlias = null) Adds a INNER JOIN clause to the query using the Customers relation
 *
 * @method AddressesQuery leftJoinCountries($relationAlias = null) Adds a LEFT JOIN clause to the query using the Countries relation
 * @method AddressesQuery rightJoinCountries($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Countries relation
 * @method AddressesQuery innerJoinCountries($relationAlias = null) Adds a INNER JOIN clause to the query using the Countries relation
 *
 * @method Addresses findOne(PropelPDO $con = null) Return the first Addresses matching the query
 * @method Addresses findOneOrCreate(PropelPDO $con = null) Return the first Addresses matching the query, or a new Addresses object populated from the query conditions when no match is found
 *
 * @method Addresses findOneByCustomersId(int $customers_id) Return the first Addresses filtered by the customers_id column
 * @method Addresses findOneByType(string $type) Return the first Addresses filtered by the type column
 * @method Addresses findOneByTitle(string $title) Return the first Addresses filtered by the title column
 * @method Addresses findOneByFirstName(string $first_name) Return the first Addresses filtered by the first_name column
 * @method Addresses findOneByLastName(string $last_name) Return the first Addresses filtered by the last_name column
 * @method Addresses findOneByAddressLine1(string $address_line_1) Return the first Addresses filtered by the address_line_1 column
 * @method Addresses findOneByAddressLine2(string $address_line_2) Return the first Addresses filtered by the address_line_2 column
 * @method Addresses findOneByPostalCode(string $postal_code) Return the first Addresses filtered by the postal_code column
 * @method Addresses findOneByCity(string $city) Return the first Addresses filtered by the city column
 * @method Addresses findOneByCountry(string $country) Return the first Addresses filtered by the country column
 * @method Addresses findOneByCountriesId(int $countries_id) Return the first Addresses filtered by the countries_id column
 * @method Addresses findOneByStateProvince(string $state_province) Return the first Addresses filtered by the state_province column
 * @method Addresses findOneByCompanyName(string $company_name) Return the first Addresses filtered by the company_name column
 * @method Addresses findOneByExternalAddressId(string $external_address_id) Return the first Addresses filtered by the external_address_id column
 * @method Addresses findOneByLatitude(double $latitude) Return the first Addresses filtered by the latitude column
 * @method Addresses findOneByLongitude(double $longitude) Return the first Addresses filtered by the longitude column
 * @method Addresses findOneByCreatedAt(string $created_at) Return the first Addresses filtered by the created_at column
 * @method Addresses findOneByUpdatedAt(string $updated_at) Return the first Addresses filtered by the updated_at column
 *
 * @method array findByCustomersId(int $customers_id) Return Addresses objects filtered by the customers_id column
 * @method array findByType(string $type) Return Addresses objects filtered by the type column
 * @method array findByTitle(string $title) Return Addresses objects filtered by the title column
 * @method array findByFirstName(string $first_name) Return Addresses objects filtered by the first_name column
 * @method array findByLastName(string $last_name) Return Addresses objects filtered by the last_name column
 * @method array findByAddressLine1(string $address_line_1) Return Addresses objects filtered by the address_line_1 column
 * @method array findByAddressLine2(string $address_line_2) Return Addresses objects filtered by the address_line_2 column
 * @method array findByPostalCode(string $postal_code) Return Addresses objects filtered by the postal_code column
 * @method array findByCity(string $city) Return Addresses objects filtered by the city column
 * @method array findByCountry(string $country) Return Addresses objects filtered by the country column
 * @method array findByCountriesId(int $countries_id) Return Addresses objects filtered by the countries_id column
 * @method array findByStateProvince(string $state_province) Return Addresses objects filtered by the state_province column
 * @method array findByCompanyName(string $company_name) Return Addresses objects filtered by the company_name column
 * @method array findByExternalAddressId(string $external_address_id) Return Addresses objects filtered by the external_address_id column
 * @method array findByLatitude(double $latitude) Return Addresses objects filtered by the latitude column
 * @method array findByLongitude(double $longitude) Return Addresses objects filtered by the longitude column
 * @method array findByCreatedAt(string $created_at) Return Addresses objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Addresses objects filtered by the updated_at column
 */
abstract class BaseAddressesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAddressesQuery object.
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
            $modelName = 'Hanzo\\Model\\Addresses';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AddressesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AddressesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AddressesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AddressesQuery) {
            return $criteria;
        }
        $query = new AddressesQuery(null, null, $modelAlias);

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
                         A Primary key composition: [$customers_id, $type]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Addresses|Addresses[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AddressesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AddressesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Addresses A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `customers_id`, `type`, `title`, `first_name`, `last_name`, `address_line_1`, `address_line_2`, `postal_code`, `city`, `country`, `countries_id`, `state_province`, `company_name`, `external_address_id`, `latitude`, `longitude`, `created_at`, `updated_at` FROM `addresses` WHERE `customers_id` = :p0 AND `type` = :p1';
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
            $obj = new Addresses();
            $obj->hydrate($row);
            AddressesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return Addresses|Addresses[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Addresses[]|mixed the list of results, formatted by the current formatter
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
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(AddressesPeer::CUSTOMERS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(AddressesPeer::TYPE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(AddressesPeer::CUSTOMERS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(AddressesPeer::TYPE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the customers_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomersId(1234); // WHERE customers_id = 1234
     * $query->filterByCustomersId(array(12, 34)); // WHERE customers_id IN (12, 34)
     * $query->filterByCustomersId(array('min' => 12)); // WHERE customers_id >= 12
     * $query->filterByCustomersId(array('max' => 12)); // WHERE customers_id <= 12
     * </code>
     *
     * @see       filterByCustomers()
     *
     * @param     mixed $customersId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByCustomersId($customersId = null, $comparison = null)
    {
        if (is_array($customersId)) {
            $useMinMax = false;
            if (isset($customersId['min'])) {
                $this->addUsingAlias(AddressesPeer::CUSTOMERS_ID, $customersId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customersId['max'])) {
                $this->addUsingAlias(AddressesPeer::CUSTOMERS_ID, $customersId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AddressesPeer::CUSTOMERS_ID, $customersId, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::TYPE, $type, $comparison);
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
     * @return AddressesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AddressesPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%'); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $firstName)) {
                $firstName = str_replace('*', '%', $firstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByLastName('%fooValue%'); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByLastName($lastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lastName)) {
                $lastName = str_replace('*', '%', $lastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::LAST_NAME, $lastName, $comparison);
    }

    /**
     * Filter the query on the address_line_1 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddressLine1('fooValue');   // WHERE address_line_1 = 'fooValue'
     * $query->filterByAddressLine1('%fooValue%'); // WHERE address_line_1 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $addressLine1 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByAddressLine1($addressLine1 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($addressLine1)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $addressLine1)) {
                $addressLine1 = str_replace('*', '%', $addressLine1);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::ADDRESS_LINE_1, $addressLine1, $comparison);
    }

    /**
     * Filter the query on the address_line_2 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddressLine2('fooValue');   // WHERE address_line_2 = 'fooValue'
     * $query->filterByAddressLine2('%fooValue%'); // WHERE address_line_2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $addressLine2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByAddressLine2($addressLine2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($addressLine2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $addressLine2)) {
                $addressLine2 = str_replace('*', '%', $addressLine2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::ADDRESS_LINE_2, $addressLine2, $comparison);
    }

    /**
     * Filter the query on the postal_code column
     *
     * Example usage:
     * <code>
     * $query->filterByPostalCode('fooValue');   // WHERE postal_code = 'fooValue'
     * $query->filterByPostalCode('%fooValue%'); // WHERE postal_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $postalCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByPostalCode($postalCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($postalCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $postalCode)) {
                $postalCode = str_replace('*', '%', $postalCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::POSTAL_CODE, $postalCode, $comparison);
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
     * @return AddressesQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AddressesPeer::CITY, $city, $comparison);
    }

    /**
     * Filter the query on the country column
     *
     * Example usage:
     * <code>
     * $query->filterByCountry('fooValue');   // WHERE country = 'fooValue'
     * $query->filterByCountry('%fooValue%'); // WHERE country LIKE '%fooValue%'
     * </code>
     *
     * @param     string $country The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByCountry($country = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($country)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $country)) {
                $country = str_replace('*', '%', $country);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::COUNTRY, $country, $comparison);
    }

    /**
     * Filter the query on the countries_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCountriesId(1234); // WHERE countries_id = 1234
     * $query->filterByCountriesId(array(12, 34)); // WHERE countries_id IN (12, 34)
     * $query->filterByCountriesId(array('min' => 12)); // WHERE countries_id >= 12
     * $query->filterByCountriesId(array('max' => 12)); // WHERE countries_id <= 12
     * </code>
     *
     * @see       filterByCountries()
     *
     * @param     mixed $countriesId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByCountriesId($countriesId = null, $comparison = null)
    {
        if (is_array($countriesId)) {
            $useMinMax = false;
            if (isset($countriesId['min'])) {
                $this->addUsingAlias(AddressesPeer::COUNTRIES_ID, $countriesId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($countriesId['max'])) {
                $this->addUsingAlias(AddressesPeer::COUNTRIES_ID, $countriesId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AddressesPeer::COUNTRIES_ID, $countriesId, $comparison);
    }

    /**
     * Filter the query on the state_province column
     *
     * Example usage:
     * <code>
     * $query->filterByStateProvince('fooValue');   // WHERE state_province = 'fooValue'
     * $query->filterByStateProvince('%fooValue%'); // WHERE state_province LIKE '%fooValue%'
     * </code>
     *
     * @param     string $stateProvince The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByStateProvince($stateProvince = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($stateProvince)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $stateProvince)) {
                $stateProvince = str_replace('*', '%', $stateProvince);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::STATE_PROVINCE, $stateProvince, $comparison);
    }

    /**
     * Filter the query on the company_name column
     *
     * Example usage:
     * <code>
     * $query->filterByCompanyName('fooValue');   // WHERE company_name = 'fooValue'
     * $query->filterByCompanyName('%fooValue%'); // WHERE company_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $companyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByCompanyName($companyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($companyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $companyName)) {
                $companyName = str_replace('*', '%', $companyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::COMPANY_NAME, $companyName, $comparison);
    }

    /**
     * Filter the query on the external_address_id column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalAddressId('fooValue');   // WHERE external_address_id = 'fooValue'
     * $query->filterByExternalAddressId('%fooValue%'); // WHERE external_address_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $externalAddressId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByExternalAddressId($externalAddressId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($externalAddressId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $externalAddressId)) {
                $externalAddressId = str_replace('*', '%', $externalAddressId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AddressesPeer::EXTERNAL_ADDRESS_ID, $externalAddressId, $comparison);
    }

    /**
     * Filter the query on the latitude column
     *
     * Example usage:
     * <code>
     * $query->filterByLatitude(1234); // WHERE latitude = 1234
     * $query->filterByLatitude(array(12, 34)); // WHERE latitude IN (12, 34)
     * $query->filterByLatitude(array('min' => 12)); // WHERE latitude >= 12
     * $query->filterByLatitude(array('max' => 12)); // WHERE latitude <= 12
     * </code>
     *
     * @param     mixed $latitude The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByLatitude($latitude = null, $comparison = null)
    {
        if (is_array($latitude)) {
            $useMinMax = false;
            if (isset($latitude['min'])) {
                $this->addUsingAlias(AddressesPeer::LATITUDE, $latitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($latitude['max'])) {
                $this->addUsingAlias(AddressesPeer::LATITUDE, $latitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AddressesPeer::LATITUDE, $latitude, $comparison);
    }

    /**
     * Filter the query on the longitude column
     *
     * Example usage:
     * <code>
     * $query->filterByLongitude(1234); // WHERE longitude = 1234
     * $query->filterByLongitude(array(12, 34)); // WHERE longitude IN (12, 34)
     * $query->filterByLongitude(array('min' => 12)); // WHERE longitude >= 12
     * $query->filterByLongitude(array('max' => 12)); // WHERE longitude <= 12
     * </code>
     *
     * @param     mixed $longitude The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByLongitude($longitude = null, $comparison = null)
    {
        if (is_array($longitude)) {
            $useMinMax = false;
            if (isset($longitude['min'])) {
                $this->addUsingAlias(AddressesPeer::LONGITUDE, $longitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($longitude['max'])) {
                $this->addUsingAlias(AddressesPeer::LONGITUDE, $longitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AddressesPeer::LONGITUDE, $longitude, $comparison);
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
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(AddressesPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(AddressesPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AddressesPeer::CREATED_AT, $createdAt, $comparison);
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
     * @return AddressesQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(AddressesPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(AddressesPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AddressesPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related Customers object
     *
     * @param   Customers|PropelObjectCollection $customers The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AddressesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCustomers($customers, $comparison = null)
    {
        if ($customers instanceof Customers) {
            return $this
                ->addUsingAlias(AddressesPeer::CUSTOMERS_ID, $customers->getId(), $comparison);
        } elseif ($customers instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AddressesPeer::CUSTOMERS_ID, $customers->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCustomers() only accepts arguments of type Customers or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Customers relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function joinCustomers($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Customers');

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
            $this->addJoinObject($join, 'Customers');
        }

        return $this;
    }

    /**
     * Use the Customers relation Customers object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Hanzo\Model\CustomersQuery A secondary query class using the current class as primary query
     */
    public function useCustomersQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomers($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customers', '\Hanzo\Model\CustomersQuery');
    }

    /**
     * Filter the query by a related Countries object
     *
     * @param   Countries|PropelObjectCollection $countries The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AddressesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCountries($countries, $comparison = null)
    {
        if ($countries instanceof Countries) {
            return $this
                ->addUsingAlias(AddressesPeer::COUNTRIES_ID, $countries->getId(), $comparison);
        } elseif ($countries instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AddressesPeer::COUNTRIES_ID, $countries->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return AddressesQuery The current query, for fluid interface
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
     * @param   Addresses $addresses Object to remove from the list of results
     *
     * @return AddressesQuery The current query, for fluid interface
     */
    public function prune($addresses = null)
    {
        if ($addresses) {
            $this->addCond('pruneCond0', $this->getAliasedColName(AddressesPeer::CUSTOMERS_ID), $addresses->getCustomersId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(AddressesPeer::TYPE), $addresses->getType(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    // geocodable behavior
    /**
     * Filters objects by distance from a given origin.
     *
     * @param	double $latitude       The latitude of the origin point.
     * @param	double $longitude      The longitude of the origin point.
     * @param	double $distance       The distance between the origin and the objects to find.
     * @param	$unit                  The unit measure.
     * @param	Criteria $comparison   Comparison sign (default is: `<`).
     *
     * @return	AddressesQuery The current query, for fluid interface
     */
    public function filterByDistanceFrom($latitude, $longitude, $distance, $unit = AddressesPeer::KILOMETERS_UNIT, $comparison = Criteria::LESS_THAN)
    {
        if (AddressesPeer::MILES_UNIT === $unit) {
            $earthRadius = 3959;
        } elseif (AddressesPeer::NAUTICAL_MILES_UNIT === $unit) {
            $earthRadius = 3440;
        } else {
            $earthRadius = 6371;
        }

        $sql = 'ABS(%s * ACOS(%s * COS(RADIANS(%s)) * COS(RADIANS(%s) - %s) + %s * SIN(RADIANS(%s))))';
        $preparedSql = sprintf($sql,
            $earthRadius,
            cos(deg2rad($latitude)),
            $this->getAliasedColName(AddressesPeer::LATITUDE),
            $this->getAliasedColName(AddressesPeer::LONGITUDE),
            deg2rad($longitude),
            sin(deg2rad($latitude)),
            $this->getAliasedColName(AddressesPeer::LATITUDE)
        );

        return $this
            ->withColumn($preparedSql, 'Distance')
            ->where(sprintf('%s %s ?', $preparedSql, $comparison), $distance, PDO::PARAM_STR)
            ;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     AddressesQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(AddressesPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     AddressesQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(AddressesPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     AddressesQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(AddressesPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     AddressesQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(AddressesPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     AddressesQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(AddressesPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     AddressesQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(AddressesPeer::CREATED_AT);
    }
}

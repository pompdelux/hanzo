<?php

namespace Hanzo\Bundle\StockBundle;

use Pompdelux\PHPRedisBundle\Client\PHPRedis;
use Hanzo\Core\PropelReplicator;
use Hanzo\Core\Tools;

/**
 * Class Warehouse
 *
 * @package Hanzo\Bundle\StockBundle
 */
class Warehouse
{
    /**
     * @var PHPRedis
     */
    private $redis;
    private $basePrefix;

    /**
     * @var array
     */
    private $warehouseCountryMap = [];
    private $countryWarehouseMap = [];

    /**
     * @var string|false
     */
    private $locationSetTo = false;

    /**
     * @var \Hanzo\Core\PropelReplicator
     */
    private $replicator;

    /**
     * @param PHPRedis         $redis
     * @param array            $warehouses
     * @param PropelReplicator $replicator
     */
    public function __construct(PHPRedis $redis, array $warehouses, PropelReplicator $replicator = null)
    {
        $this->redis      = $redis;
        $this->basePrefix = $redis->getPrefix();
        $this->replicator = $replicator;

        $this->setWarehouses($warehouses);

        // debugging ...
        if (!$replicator instanceof PropelReplicator) {
            Tools::log('$replicator not set..: ', 0, true);
        }
    }


    /**
     * Set the required warehouse location by locale.
     *
     * @param string $locale
     *
     * @return Warehouse
     * @throws \InvalidArgumentException
     */
    public function setLocation($locale)
    {
        if (isset($this->countryWarehouseMap[$locale])) {
            $this->locationSetTo = $this->countryWarehouseMap[$locale];

            $p = trim($this->basePrefix, ':');
            $this->redis->setPrefix($p.'.'.$this->locationSetTo.':');

            return $this;
        }

        throw new \InvalidArgumentException("'{$locale}' not a known warehouse location.");
    }


    /**
     * Get inventory status for a range of products.
     *
     * @param array $productIds Array of product id's
     *
     * @return array
     * @throws \OutOfBoundsException
     */
    public function getInventory($productIds)
    {
        if (!$this->locationSetTo) {
            throw new \OutOfBoundsException("Missing call to setLocation. Warehouse location must be set to get status.");
        }

        $stock = [];
        $this->redis->multi();

        foreach ($productIds as $id) {
            $this->redis->hGetAll('products_id.'.$id);

            $stock[$id] = ['total' => 0];
        }

        foreach ($this->redis->exec() as $product) {
            if (empty($product)) {
                continue;
            }

            $count = 1;
            $id    = $product['id'];

            // if not unset it will pollude the data array
            unset ($product['id']);

            foreach ($product as $date => $quantity) {
                $stock[$id][str_replace('-', '', $date)] = [
                    'id'       => $count++,
                    'date'     => $date,
                    'quantity' => $quantity,
                ];
                $stock[$id]['total'] += $quantity;
            }
        }

        return $stock;
    }


    /**
     * Update or set an inventory record.
     *
     * @param int    $productId
     * @param string $date
     * @param int    $quantity
     *
     * @return mixed
     */
    public function setInventoryRecord($productId, $date, $quantity = 0)
    {
        return $this->redis
            ->multi()
                ->hSet('products_id.'.$productId, $date, $quantity)
                ->hSet('products_id.'.$productId, 'id', $productId)
            ->exec();
    }


    /**
     * Update or set inventory records.
     *
     * @param int   $productId
     * @param array $data
     *
     * @return mixed
     */
    public function setInventoryRecords($productId, $data)
    {
        $multi = $this->redis->multi();

        // start by deleting all existing records
        // this should be safe as long as it is done in a "multi" session, which creates a lock on the items in redis.
        $multi->delete('products_id.'.$productId);

        $recordCount = 0;

        foreach ($data as $record) {
            if (empty($record['date']) ||
                empty($record['quantity'])
            ) {
                continue;
            }

            // only add records with actual data
            $multi->hSet('products_id.'.$productId, $record['date'], $record['quantity']);
            $recordCount++;
        }

        // re-add the product_id key to handle lookups.
        if ($recordCount) {
            $multi->hSet('products_id.'.$productId, 'id', $productId);
        }

        return $multi->exec();
    }


    /**
     * Delete an inventory record.
     *
     * @param int    $productId
     * @param string $date
     *
     * @return mixed
     */
    public function deleteInventoryRecord($productId, $date)
    {
        return $this->redis->hDel('products_id.'.$productId, $date);
    }


    /**
     * Remove a full style from the inventory.
     *
     * @param int $productId
     *
     * @return mixed
     */
    public function removeProductFromInventory($productId)
    {
        return $this->redis->del('products_id.'.$productId);
    }


    /**
     * Returns an array of database connection names mapped against the warehouse list.
     *
     * @return array
     * @throws \OutOfBoundsException
     */
    public function getRelatedDatabases()
    {
        if (!$this->locationSetTo) {
            throw new \OutOfBoundsException("Missing call to setLocation. Warehouse location must be set to get status.");
        }

        static $relations;

        if (isset($relations[$this->locationSetTo])) {
            return $relations[$this->locationSetTo];
        }

        $relations[$this->locationSetTo] = [];
        $map = [];

        foreach ($this->warehouseCountryMap[$this->locationSetTo] as $locale) {
            $v = 'pdldb'.strtolower(substr($locale, -2)).'1';
            $map[$v] = $v;
        }

        foreach ($this->replicator->getConnectionNames() as $name) {
            if (isset($map[$name])) {
                $relations[$this->locationSetTo][] = $name;
            }
        }

        if (empty($relations[$this->locationSetTo])) {
            $relations[$this->locationSetTo] = ['default'];
        }

        return $relations[$this->locationSetTo];
    }


    /**
     * Set known warehouses.
     * The method is called from the constructor with data from the service configuration.
     *
     * @param array $map
     */
    private function setWarehouses(array $map)
    {
        foreach ($map as $warehouse => $locales) {
            $this->warehouseCountryMap[$warehouse] = $locales;

            foreach ($locales as $locale) {
                $this->countryWarehouseMap[$locale] = $warehouse;
            }
        }
    }
}

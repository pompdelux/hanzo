<?php

namespace Hanzo\Bundle\StockBundle;

use Hanzo\Bundle\RedisBundle\Client\Redis as RedisClient;
use Hanzo\Core\PropelReplicator;
use Hanzo\Core\Tools;

class Warehouse
{
    /**
     * @var \Hanzo\Bundle\RedisBundle\Client\Redis
     */
    private $redis;

    /**
     * @var array
     */
    private $warehouse_country_map = [];
    private $country_warehouse_map = [];

    /**
     * @var boolean
     */
    private $is_location_set = false;

    /**
     * @var \Hanzo\Core\PropelReplicator
     */
    private $replicator;

    /**
     * @param RedisClient      $redis
     * @param array            $warehouses
     * @param PropelReplicator $replicator
     */
    public function __construct(RedisClient $redis, array $warehouses, PropelReplicator $replicator = null)
    {
        $this->redis = $redis;
        $this->setWarehouses($warehouses);
        $this->replicator = $replicator;

        if (is_null($replicator)) {
            Tools::log('$replicator not set..: ', 0, true);
        }
    }


    /**
     * Set the required warehouse location by locale.
     *
     * @param  $locale
     * @return Warehouse
     * @throws \InvalidArgumentException
     */
    public function setLocation($locale)
    {
        if (isset($this->country_warehouse_map[$locale])) {
            $this->is_location_set = $this->country_warehouse_map[$locale];

            $p = trim($this->redis->getPrefix(), ':');
            $this->redis->setPrefix($p.'.'.$this->is_location_set.':');

            return $this;
        }

        throw new \InvalidArgumentException("'{$locale}' not a known warehouse location.");
    }


    /**
     * Get inventory status for a range of products.
     *
     * @param $product_ids Array of product id's
     * @return array
     * @throws \OutOfBoundsException
     */
    public function getInventory($product_ids)
    {
        if (!$this->is_location_set) {
            throw new \OutOfBoundsException("Missing call to setLocation. Warehouse location must be set to get status.");
        }

        $stock = [];

        $this->redis->multi();
        foreach ($product_ids as $id) {
            $this->redis->hGetAll('products_id.'.$id);

            $stock[$id] = ['total' => 0];
        }

        foreach ($this->redis->exec() as $product) {
            if (empty($product)) {
                continue;
            }

            $count = 1;
            $id = $product['id'];
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
     * @param  int    $product_id
     * @param  string $date
     * @param  int    $quantity
     * @return mixed
     */
    public function setInventoryRecord($product_id, $date, $quantity = 0)
    {
        return $this->redis
            ->multi()
                ->hSet('products_id.'.$product_id, $date, $quantity)
                ->hSet('products_id.'.$product_id, 'id', $product_id)
            ->exec()
        ;
    }


    /**
     * Update or set inventory records.
     *
     * @param $product_id
     * @param $data
     * @return mixed
     */
    public function setInventoryRecords($product_id, $data)
    {
        $multi = $this->redis->multi();

        // start by deleting all existing records
        // this should be safe as long as it is done in a "multi" session, which creates a lock on the items in redis.
        $multi->delete('products_id.'.$product_id);

        $record_count = 0;
        foreach ($data as $record) {
            if (empty($record['date']) ||
                empty($record['quantity'])
            ) {
                continue;
            }

            // only add records with actual data
            $multi->hSet('products_id.'.$product_id, $record['date'], $record['quantity']);
            $record_count++;
        }

        // re-add the product_id key to handle lookups.
        if ($record_count) {
            $multi->hSet('products_id.'.$product_id, 'id', $product_id);
        }

        return $multi->exec();
    }


    /**
     * Delete an inventory record.
     *
     * @param  int    $product_id
     * @param  string $date
     * @return mixed
     */
    public function deleteInventoryRecord($product_id, $date)
    {
        return $this->redis->hDel('products_id.'.$product_id, $date);
    }


    /**
     * Remove a full style from the inventory.
     *
     * @param $product_id
     * @return mixed
     */
    public function removeProductFromInventory($product_id)
    {
        return $this->redis->del('products_id.'.$product_id);
    }


    /**
     * Returns an array of database connection names mapped against the warehouse list.
     *
     * @return array
     * @throws \OutOfBoundsException
     */
    public function getRelatedDatabases()
    {
        if (!$this->is_location_set) {
            throw new \OutOfBoundsException("Missing call to setLocation. Warehouse location must be set to get status.");
        }

        static $relations;

        if (isset($relations[$this->is_location_set])) {
            return $relations[$this->is_location_set];
        }

        $relations[$this->is_location_set] = [];

        if (1 == count($this->warehouse_country_map[$this->is_location_set])) {
            $relations[$this->is_location_set] = ['default'];
        } else {
            $map = ['default' => 'default'];
            foreach ($this->warehouse_country_map[$this->is_location_set] as $locale) {
                $v = 'pdldb'.strtolower(substr($locale, -2)).'1';
                $map[$v] = $v;
            }

            foreach ($this->replicator->getConnectionNames() as $name) {
                if (isset($map[$name])) {
                    $relations[$this->is_location_set][] = $name;
                }
            }
        }

        return $relations[$this->is_location_set];
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
            $this->warehouse_country_map[$warehouse] = $locales;

            foreach ($locales as $locale) {
                $this->country_warehouse_map[$locale] = $warehouse;
            }
        }
    }
}

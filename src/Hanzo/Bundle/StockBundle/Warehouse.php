<?php

namespace Hanzo\Bundle\StockBundle;

use Hanzo\Bundle\RedisBundle\Client\Redis as RedisClient;
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
     * @param RedisClient $redis
     * @param array       $warehouses
     */
    public function __construct(RedisClient $redis, array $warehouses)
    {
        $this->redis = $redis;
        $this->setWarehouses($warehouses);
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
    public function getStatus($product_ids)
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

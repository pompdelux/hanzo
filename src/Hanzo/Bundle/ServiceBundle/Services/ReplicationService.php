<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Propel;
use PropelConfiguration;
use Hanzo\Core\Tools;

use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;

class ReplicationService
{
    protected $propel_configuration;
    protected $master_connection = 'default';
    protected $replicated_connections = array();

    protected $settings;
    protected $connections;

    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof PropelConfiguration) {
            throw new \InvalidArgumentException('PropelConfiguration expected as first parameter.');
        }

        $this->propel_configuration = $parameters[0];
        $this->settings = $settings;

        $this->mapReplicatedConnections();
    }


    /**
     * Syncronize style guides across databases
     *
     * @param  string $action     add/delete supported
     * @param  int    $image_id
     * @param  int    $product_id
     */
    public function syncStyleGuide($action = 'add', $image_id, $product_id)
    {
        foreach ($this->replicated_connections as $name) {
            $conn = $this->getConnection($name);
            switch ($action) {
                case 'add':
                    try {
                        $img = new ProductsImagesProductReferences();
                        $img->setProductsImagesId($image_id);
                        $img->setProductsId($product_id);
                        $img->save($conn);
                    } catch (Exception $e) {}
                    break;
                case 'delete':
                    try {
                        $conn->query("
                            DELETE FROM
                                products_images_product_references
                            WHERE
                                products_images_id = {$image_id}
                                AND
                                    products_id = {$product_id}
                        ");
                    } catch (Exception $e) {}
                    break;
            }
        }
    }


    /**
     * Build replication server map
     */
    protected function mapReplicatedConnections()
    {
        foreach ($this->propel_configuration->getFlattenedParameters() as $key => $value) {
            list($namespace, $name, $rest) = explode('.', $key, 3);

            // only add one connection, and only if the user is set
            if (($name != 'default') &&
                ($rest == 'connection.user') &&
                ($namespace == 'datasources')
            ) {
                $value = trim($value);
                if (!empty($value) && empty($this->replicated_connections[$name])) {
                    $this->replicated_connections[$name] = $name;
                    continue;
                }
            }
        }
    }

    /**
     * Get Propel connection object
     *
     * @param  string $name name of the Propel connection to retrive
     * @return Propel
     */
    protected function getConnection($name)
    {
        if (empty($this->connections[$name])) {
            $this->connections[$name] = Propel::getConnection($name, Propel::CONNECTION_WRITE);
        }

        return $this->connections[$name];
    }
}

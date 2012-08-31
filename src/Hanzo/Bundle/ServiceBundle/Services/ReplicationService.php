<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Propel;
use PropelConfiguration;
use Hanzo\Core\Tools;

use Hanzo\Model\ProductsImages;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;

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
            throw new \InvalidArgumentException('PropelConfiguration object expected as first parameter.');
        }

        $this->propel_configuration = $parameters[0];
        $this->settings = $settings;

        $this->mapReplicatedConnections();
    }


    /**
     * Syncronize images across databases
     */
    public function syncProductsImages()
    {
        $images = ProductsImagesQuery::create()->find();

        foreach ($this->replicated_connections as $name) {
            $conn = $this->getConnection($name);

            // delete all images in replicated table
            ProductsImagesQuery::create()
                ->filterById(0, \Criteria::GREATER_THAN)
                ->delete($conn);

            // loop all image into the replicated table
            foreach ($images as $image) {
                $i = new ProductsImages();
                $i->setId($image->getId());
                $i->setProductsId($image->getProductsId());
                $i->setImage($image->getImage());
                $i->save($conn);
            }
        }
    }


    /**
     * Syncronize style guides across databases
     */
    public function syncStyleGuide()
    {
        $guides = ProductsImagesProductReferencesQuery::create()->find();

        foreach ($this->replicated_connections as $name) {
            $conn = $this->getConnection($name);

            ProductsImagesProductReferencesQuery::create()
                ->filterByProductsId(0, \Criteria::GREATER_THAN)
                ->delete($conn);

            foreach ($guides as $guide) {
                $g = new ProductsImagesProductReferences();
                $g->setProductsImagesId($guide->getProductsImagesId());
                $g->setProductsId($guide->getProductsId());
                $g->save($conn);
            }
        }
    }


    /**
     * Syncronize image sorting across databases
     */
    public function syncImageSorting()
    {
        $images = ProductsImagesCategoriesSortQuery::create()->find();

        foreach ($this->replicated_connections as $name) {
            $conn = $this->getConnection($name);

            ProductsImagesCategoriesSortQuery::create()
                ->filterByProductsId(0, \Criteria::GREATER_THAN)
                ->delete($conn);

            foreach ($images as $image) {
                $s = new ProductsImagesCategoriesSort();
                $s->setProductsId($image->getProductsId());
                $s->setCategoriesId($image->getCategoriesId());
                $s->setProductsImagesId($image->getProductsImagesId());
                $s->setSort($image->getSort());
                $s->save($conn);
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

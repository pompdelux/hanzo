<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\LanguagesQuery;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class ProductAndCategoryIndexBuilder
{
    private $propel_configuration;
    private $connections;

    private $translation_dir;
    private $cache_dir;

    /**
     * TODO: Move to BaseIndexBuilder
     * @return array
     */
    public function __construct(\PropelConfiguration $propel_configuration, Router $router, $kernel_root_dir, $kernel_cache_dir)
    {
        $this->propel_configuration = $propel_configuration;
        $this->router               = $router;
        $this->translation_dir      = $kernel_root_dir.'/Resources/translations/';
        $this->cache_dir            = $kernel_cache_dir.'/';
    }


    public function build()
    {
        $category_map = [];

        foreach ($this->router->getRouteCollection()->all() as $key => $route) {
            if (preg_match('/^category_[0-9]+/')) {
                list ($junk, $id, $locale) = explode('_', $key, 3);


            }
        }


        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            foreach ($this->getLocales($connection) as $locale) {
                $file = $this->translation_dir.'category.'.$locale.'.xliff';
                if (!is_file($file)) {
                    continue;
                }

                $parser = new XliffFileLoader();
                $catalogue = $parser->load($file, $locale, 'category');

                foreach($catalogue->all()['category'] as $key => $text) {
                    if ('headers.category-' !== substr($key, 0, 17)) {
                        continue;
                    }


                }

            }
        }



    }

    /**
     * TODO: Move to BaseIndexBuilder
     *
     * @return array
     */
    protected function getConnections()
    {
        if (!$this->connections) {
            $this->findConnections();
        }

        return $this->connections;
    }

    /**
     * TODO: Move to BaseIndexBuilder
     *
     * @param  string $name Name of connection to retrive
     * @return PropelPDO    Propel connection object
     */
    protected function getConnection($name = 'default')
    {
        if (!$this->connections) {
            $this->findConnections();
        }

        if (empty($this->connections[$name])) {
            $this->connections[$name] = \Propel::getConnection($name);
        }

        return isset($this->connections[$name])
            ? $this->connections[$name]
            : null
        ;
    }

    /**
     * TODO: Move to BaseIndexBuilder
     */
    private function findConnections()
    {
        foreach ($this->propel_configuration->getFlattenedParameters() as $key => $value) {
            list($namespace, $name, $rest) = explode('.', $key, 3);

            // only add one connection, and only if the user is set
            if (($rest      == 'connection.user') &&
                ($namespace == 'datasources')
            ) {
                $value = trim($value);

                if (!empty($value) && empty($this->connections[$name])) {
                    $this->connections[$name] = null;
                    continue;
                }
            }
        }
    }

    /**
     * TODO: Move to BaseIndexBuilder
     *
     * @return array Array of active languages
     */
    protected function getLocales($connection)
    {
        return LanguagesQuery::create()
            ->select('locale')
            ->find($connection)
        ;
    }
}

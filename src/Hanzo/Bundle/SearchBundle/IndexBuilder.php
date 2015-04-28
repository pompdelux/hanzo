<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\LanguagesQuery;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class IndexBuilder
{
    private $connections;
    private $propel_configuration;

    protected $translation_dir;
    protected $cache_dir;

    /**
     * TODO: Move to BaseIndexBuilder
     *
     * @param \PropelConfiguration $propel_configuration Propel configuration object
     * @param Router               $router               The router object
     * @param string               $kernel_root_dir      Kernel root dir
     * @param string               $kernel_cache_dir     Kernel cache dir
     */
    public function __construct(\PropelConfiguration $propel_configuration, Router $router, $kernel_root_dir, $kernel_cache_dir)
    {
        $this->propel_configuration = $propel_configuration;
        $this->router               = $router;
        $this->translation_dir      = $kernel_root_dir.'/Resources/translations/';
        $this->cache_dir            = $kernel_cache_dir.'/';
    }


    /**
     * Return a list of locales based on the connection.
     *
     * @param PropelConnection $connection Connection to use in the lookup.
     * @return Array Array of active languages
     */
    protected function getLocales($connection)
    {
        return LanguagesQuery::create()
            ->select('locale')
            ->find($connection)
        ;
    }

    /**
     * Get translations from Catalogue
     *
     * @param $type
     * @param $locale
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    protected function getTranslationCatalogue($type, $locale)
    {
        $file = $this->translation_dir.$type.'.'.$locale.'.xliff';
        if (!is_file($file)) {
            return;
        }

        $parser = new XliffFileLoader();
        return $parser->load($file, $locale, $type);
    }


    /**
     * Find active propel connections.
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
     * Get named Propel connection
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
     * Parse Propel configuration and find connections.
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
}

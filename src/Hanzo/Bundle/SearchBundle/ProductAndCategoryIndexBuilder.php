<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Core\Tools;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\SearchFulltext;
use Hanzo\Model\SearchFulltextQuery;
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
     *
     * @params PropelConfiguration $propel_configuration Propel configuration object
     * @params Router              $router               The router object
     * @params string              $kernel_root_dir      Kernel root dir
     * @params string              $kernel_cache_dir     Kernel cache dir
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
            if (preg_match('/^(category|look)_[0-9]+/', $key)) {
                $info = $route->getDefaults();
                $category_map[$info['category_id']] = $key;
            }
        }

        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            SearchFulltextQuery::create()->deleteAll($connection);

            foreach ($this->getLocales($connection) as $locale) {
                // category indexing
                $file = $this->translation_dir.'category.'.$locale.'.xliff';
                if (!is_file($file)) {
                    continue;
                }

                $parser = new XliffFileLoader();
                $catalogue = $parser->load($file, $locale, 'category');

                foreach($catalogue->all('category') as $key => $text) {
                    if (!preg_match('/headers.category-([0-9]+)/i', $key, $matches)) {
                        continue;
                    }

                    $index = new SearchFulltext();
                    $index->setLocale($locale);
                    $index->setTarget($this->router->generate($category_map[$matches[1]], ['_locale' => $locale]));
                    $index->setType('category');
                    $index->setContent(trim(Tools::stripTags($text)));
                    $index->save($connection);
                }

                // product indexing
                $file = $this->translation_dir.'products.'.$locale.'.xliff';
                if (!is_file($file)) {
                    continue;
                }

                $parser = new XliffFileLoader();
                $catalogue = $parser->load($file, $locale, 'products');

                foreach ($catalogue->all('products') as $key => $text) {
                    $key = explode('.', $key);
                    $key = str_replace('_', ' ', $key[1]);

                    $record = ProductsQuery::create()
                        ->select(['Id', 'ProductsToCategories.categories_id'])
                        ->joinWithProductsToCategories()
                        ->findOneBySku($key, $connection)
                    ;

                    if ($record) {
                        $route = str_replace(['category', 'look'], 'product', $category_map[$record['ProductsToCategories.categories_id']]);
                        $index = new SearchFulltext();
                        $index->setLocale($locale);
                        $index->setTarget($this->router->generate($route, [
                            'product_id' => $record['Id'],
                            'title'      => Tools::stripText($key),
                            '_locale'    => $locale,
                        ]));
                        $index->setType('product');
                        $index->setContent(trim(Tools::stripTags($text)));
                        $index->save($connection);
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
     * @params PropelConnection $connection Connection to use in the lookup.
     *
     * @return Array Array of active languages
     */
    protected function getLocales($connection)
    {
        return LanguagesQuery::create()
            ->select('locale')
            ->find($connection)
        ;
    }
}

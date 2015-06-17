<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Core\Tools;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\ProductsI18n;
use Hanzo\Model\ProductsI18nQuery;
use Hanzo\Model\ProductsQuery;

class ProductAndCategoryIndexBuilder extends IndexBuilder
{
    /**
     * The builder routine, calls sub methods to update various parts.
     */
    public function build()
    {
        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            foreach ($this->getLocales($connection) as $locale) {
                //$this->updateCategoryIndex($locale, $connection);
                $this->updateProductIndex($locale, $connection);
            }
        }
    }

    /**
     * Get Category to CMS map from the routing service.
     *
     * @return array
     */
    private function getCategoryCmsMapping()
    {
        $category_map = [];
        foreach ($this->router->getRouteCollection()->all() as $key => $route) {
            if (preg_match('/^(category|look)_[0-9]+/', $key)) {
                $info = $route->getDefaults();
                $category_map[$info['category_id']] = $info['cms_id'];
            }
        }

        return $category_map;
    }


    /**
     * Update category cms nodes with content from translation files.
     *
     * @param string    $locale
     * @param PropelPDO $connection
     */
    private function updateCategoryIndex($locale, $connection)
    {
        static $category_map;

        if (!$catalogue = $this->getTranslationCatalogue('category', $locale)) {
            return;
        }

        if (empty($category_map)) {
            $category_map = $this->getCategoryCmsMapping();
        }

        foreach($catalogue->all('category') as $key => $text) {
            if (!preg_match('/headers.category-([0-9]+)/i', $key, $matches)) {
                continue;
            }

            $cms = CmsI18nQuery::create()
                ->filterByLocale($locale)
                ->findOneById($category_map[$matches[1]], $connection)
            ;

            if (!$cms instanceof CmsI18n) {
                continue;
            }

            $cms->setContent(trim(Tools::stripTags($text)));
            $cms->save($connection);
        }
    }


    /**
     * Update product descriptions with content from translation files.
     *
     * @param string    $locale
     * @param PropelPDO $connection
     */
    private function updateProductIndex($locale, $connection)
    {
        // product indexing
        if (!$catalogue = $this->getTranslationCatalogue('products', $locale)) {
            return;
        }

        foreach ($catalogue->all('products') as $key => $text) {
            $key = explode('.', $key);
            $key = str_replace('_', ' ', $key[1]);

            $record = ProductsQuery::create()
                ->select(['Id', 'ProductsToCategories.categories_id'])
                ->joinWithProductsToCategories()
                ->findOneBySku($key, $connection)
            ;

            if ($record) {
                $product = ProductsI18nQuery::create()
                    ->filterByLocale($locale)
                    ->findOneById($record['Id'], $connection)
                ;

                if (!$product instanceof ProductsI18n) {
                    continue;
                }

                $product->setContent(trim(Tools::stripTags($text)));
                $product->save($connection);
            }
        }
    }
}

<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\SearchProductsTagsQuery,
    Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsDomainsPricesPeer,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools
    ;

class ProductIndexBuilder extends IndexBuilder
{
    /**
     * Inserts data, remeber to ->clear() first
     *
     * @param Array $indexesToUpdate
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function build(Array $indexesToUpdate = [])
    {
        if (empty($indexesToUpdate)) {
            // Changes here should also be updated in buildProductSearchIndexAction
            $indexesToUpdate = [
                'product_index'      => true,
                'category_index'     => true,
                'custom_token_index' => true,
                'discount_index'     => true,
                ];
        }

        foreach (array_keys($this->getConnections()) as $name) {
            $connection = $this->getConnection($name);

            foreach ($indexesToUpdate as $index => $enabled) {
                if ($enabled === true) {
                    $this->performUpdate($index, $connection);
                }
            }
        }
    }

    /**
     * @param string $index
     */
    protected function performUpdate($index, $connection)
    {
        foreach ($this->getLocales($connection) as $locale) {
            switch ($index)
            {
                case 'product_index':
                    $this->updateProductIndex($locale, $connection);
                    break;
                case 'category_index':
                    $this->updateCategoryIndex($locale, $connection);
                    break;
                case 'custom_token_index':
                    $this->updateCustomTokensIndex($locale, $connection);
                    break;
                case 'discount_index':
                    $this->updateDiscountIndex($locale, $connection);
                    break;
            }
        }
    }

    /**
     * clear
     * Truncates the search_products_tags table for each connection
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function clear()
    {
        foreach (array_keys($this->getConnections()) as $name) {
            $connection = $this->getConnection($name);

            foreach ($this->getLocales($connection) as $locale) {
                $this->truncate($locale, $connection);
            }
        }
    }

    /**
     * truncate
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function truncate($locale, $connection)
    {
        $sql = "TRUNCATE TABLE search_products_tags";
        $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute();
    }

    /**
     * Update product search tags
     *
     * @param string    $locale
     * @param PropelPDO $connection
     */
    private function updateProductIndex($locale, $connection)
    {
        // index colors and sizes
        $sql = "
            INSERT INTO
                search_products_tags (
                    master_products_id,
                    products_id,
                    token,
                    type,
                    locale
                )
            SELECT
                p1.id,
                p2.id,
                p2.color,
                'product',
                '".$locale."'
            FROM
                products AS p1
            JOIN
                products AS p2
                ON (
                    p2.master = p1.sku
                )
            UNION SELECT
                p1.id,
                p2.id,
                p2.size,
                'product',
                '".$locale."'
            FROM
                products AS p1
            JOIN
                products AS p2
                ON (
                    p2.master = p1.sku
                )
        ";

        $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute();
    }

    /**
     * Update category search tags.
     *
     * @param string    $locale
     * @param PropelPDO $connection
     */
    private function updateCategoryIndex($locale, $connection)
    {
        // index categories
        $sql = "
            INSERT INTO
                search_products_tags (
                    master_products_id,
                    products_id,
                    token,
                    type,
                    locale
                )
            SELECT
                p1.id,
                p2.id,
                c.title,
                'category',
                c.locale
            FROM
                products AS p1
            JOIN
                products AS p2
                ON (
                    p2.master = p1.sku
                )
            JOIN
                products_to_categories AS p2c
                ON (
                    p1.id = p2c.products_id
                )
            JOIN
                categories_i18n AS c
                ON (
                    c.id = p2c.categories_id
                    AND
                    c.locale = '".$locale."'
                )
            UNION SELECT
                p1.id,
                p2.id,
                c.context,
                'category',
                '".$locale."'
            FROM
                products AS p1
            JOIN
                products AS p2
                ON (
                    p2.master = p1.sku
                )
            JOIN
                products_to_categories AS p2c
                ON (
                    p1.id = p2c.products_id
                )
            JOIN
                categories AS c
                ON (
                    c.id = p2c.categories_id
                )
            WHERE
                c.context IS NOT NULL
        ";

        $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute();
    }

    /**
     * updateCustomTokensIndex
     * - Some products are tagged with some custom tokens, i.e. eco
     * - This will find all products in the configured categories @see getCustomTokensForCategories and add them to the search table
     *
     * @param string $locale
     * @param PropelPDO
     *
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function updateCustomTokensIndex($locale, $connection)
    {
        $tokensToCategories = $this->getCustomTokensForCategories($locale);

        foreach ($tokensToCategories as $token => $categories)
        {
            // Only master products are in this table
            $masterProducts = ProductsQuery::create()
                ->useProductsToCategoriesQuery()
                    ->filterByCategoriesId($categories)
                ->endUse()
                ->find();

            foreach ($masterProducts as $masterProduct)
            {
                $products = ProductsQuery::create()
                    ->filterByMaster($masterProduct->getSku())
                    ->find();

                foreach ($products as $product)
                {
                    $tokenValue = Tools::stripText($token);

                    $sql = sprintf("
                        INSERT INTO
                            search_products_tags (
                                master_products_id,
                                products_id,
                                token,
                                type,
                                locale
                            )
                        VALUES(%d, %d, '%s', '%s', '%s')
                    ",
                    $masterProduct->getId(),
                    $product->getId(),
                    $tokenValue,
                    'tag',
                    $locale);

                    $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
                    $query->execute();
                }
            }
        }
    }

    /**
     * getCustomTokensForCategories
     *
     * Based on code from Model/CmsI18n and Hanzo\Bundle\SearchBundle\ProductAndCategoryIndexBuilder
     *
     * The following has to be added to the cms.$locale.xliff file om the XXX.settings block
     * Each number refers to a category
     *
     * "tokens": {
     *   "Gots": [
     *     "212",
     *     "214"
     *   ],
     *   "Oekotex": [
     *     "212"
     *   ]
     * }
     *
     * @param string $locale
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function getCustomTokensForCategories($locale)
    {
        $tokensToCategories = [];

        $catalog = $this->getTranslationCatalogue('cms', $locale);

        foreach ($catalog->all('cms') as $key => $msg)
        {
            if (!preg_match('/([0-9]+).settings/i', $key, $matches))
            {
                continue;
            }

            $msg = trim($msg);

            if (is_scalar($msg) && substr($msg, 0, 1) == '{')
            {
                $settings = json_decode(stripcslashes($msg));
                if (isset($settings->tokens)) {
                    $tokens = (array) $settings->tokens;
                    foreach ($tokens as $key => $categories)
                    {
                        if (!isset($tokensToCategories[$key]))
                        {
                            $tokensToCategories[$key] = [];
                        }
                        $tokensToCategories[$key] = array_merge($tokensToCategories[$key], $categories);
                    }
                }
            }
        }


        return $tokensToCategories;
    }

    /*
     * Index all discounted products
     *
     */
    private function updateDiscountIndex($locale, $connection)
    {
        $hanzo    = Hanzo::getInstance();
        $range    = $hanzo->container->get('hanzo_product.range')->getCurrentRange();
        $domainId = $hanzo->get('core.domain_id');

        $masterProducts = ProductsQuery::create()
            ->useProductsI18nQuery()
                ->filterByLocale($locale)
            ->endUse()
            ->filterByIsActive(true)
            ->filterByRange($range)
            ->useProductsDomainsPricesQuery()
                ->filterByDomainsId($domainId)
                ->condition('c1', ProductsDomainsPricesPeer::FROM_DATE . ' <= NOW()')
                ->condition('c2', ProductsDomainsPricesPeer::TO_DATE . ' >= NOW()')
                ->where(array('c1', 'c2'), 'AND')
            ->endUse()
            ->joinWithProductsImages()
            ->find($connection)
        ;

        $product_ids = [];
        $records     = [];

        foreach ($masterProducts as $masterProduct)
        {
            // Find all styles
            $sql = "SELECT id FROM products WHERE master = :sku";
            $stmt = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $stmt->execute(['sku' => $masterProduct->getSku()]);

            $styles = [];
            while ($style = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $styles[] = $style['id'];
            }

            $product_ids[] = $masterProduct->getId();
            $records[] = ['master_id' => $masterProduct->getId(), 'styles' => $styles];
        }

        // get product prices
        $prices = ProductsDomainsPricesPeer::getProductsPrices($product_ids);

        // attach the prices to the products
        foreach ($records as $data) {
            if (isset($prices[$data['id']]) && isset($prices[$data['id']]['sales'])) {

                $discountPct = $prices[$data['id']]['sales']['sales_pct'];

                foreach ($data['styles'] as $style)
                {
                    $sql = sprintf("
                        INSERT INTO
                            search_products_tags (
                                master_products_id,
                                products_id,
                                token,
                                type,
                                locale
                            )
                        VALUES(%d, %d, '%s', '%s', '%s')
                    ",
                    $data['master_id'],
                    $style,
                    $discountPct,
                    'discount',
                    $locale);

                    $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
                    $query->execute();
                }
            }
        }

    }
}

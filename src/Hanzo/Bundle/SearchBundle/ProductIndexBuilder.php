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
            // default has been replaced by pdldbdk1
            if ($name == 'default') {
                continue;
            }
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
     * Index all products with a discount
     *
     */
    private function updateDiscountIndex($locale, $connection)
    {
        /*
         * As we can't use hanzo here, because it depends on the enviroment, and we try to update all databases
         * we need to look up active product range here.
         *
         * NOTE: This has to be updated when #927 is implemented.
         */
        $sql = "SELECT c_value FROM settings WHERE c_key = 'active_product_range' AND ns = 'core'";
        $stmt = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $stmt->execute();

        $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        $range = $record['c_value'];

        // Get all master products
        $sql = "SELECT
                products.id,
                products.sku
            FROM
                `products`
            LEFT JOIN
                `products_i18n` ON (products.id=products_i18n.id)
            INNER JOIN
                `products_domains_prices` ON (products.id=products_domains_prices.products_id)
            INNER JOIN
                `products_images` ON (products.id=products_images.products_id)
            WHERE
                products_i18n.locale=:locale
            AND
                products.is_active=1
            AND
                products.range=:range
            AND
                (products_domains_prices.from_date <= NOW()
            AND
                products_domains_prices.to_date >= NOW())
            GROUP BY products.sku";

        $stmt = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $stmt->execute([
            'locale' => $locale,
            'range'  => $range,
            ]);

        $masterProducts = [];
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $masterProducts[] = $record;
        }

        // Now that we have the master products we have to find all style ids
        $records = [];
        foreach ($masterProducts as $masterProduct) {
            // Find all styles
            $sql = "SELECT id FROM products WHERE master = :sku";
            $stmt = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $stmt->execute(['sku' => $masterProduct['sku']]);

            $styles = [];
            while ($style = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $styles[] = $style['id'];
            }

            $records[] = ['master_id' => $masterProduct['id'], 'styles' => $styles];
        }

        /*
         * Find prices.
         * We need the domain_id so that we don't calculate the discount based on prices from 2 different domains
         */
        $sql = "SELECT
                products_domains_prices.products_id,
                products_domains_prices.price,
                products_domains_prices.domains_id,
                products_domains_prices.from_date,
                products_domains_prices.to_date
            FROM
                `products_domains_prices`
            WHERE
                products_domains_prices.from_date<=:time
            AND
                (products_domains_prices.to_date>=:time
                OR products_domains_prices.to_date IS NULL )
            ORDER BY
                products_domains_prices.products_id ASC,
                products_domains_prices.from_date DESC,
                products_domains_prices.to_date DESC";

        $stmt = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $stmt->execute(['time' => date('Y-m-d H:i:s')]);

        $prices = [];
        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $key = is_null($record['to_date']) ? 'normal' : 'sales';
            $prices[$record['domains_id']][$record['products_id']][$key] = ['price' => $record['price'], 'sales_pct' => 0.00 ];
        }

        // Calculate all discounts and remember which domain ids there are
        $domainIds = [];
        foreach ($prices as $domainId => $domainPrices) {
            $domainIds[$domainId] = $domainId;
            foreach ($domainPrices as $productId => $price) {
                if (isset($price['sales'])) {
                    // Runding is normaly done in the template, but the filter uses whole numbers
                    $prices[$domainId][$productId]['sales']['sales_pct'] = round((($price['normal']['price'] - $price['sales']['price']) / $price['normal']['price']) * 100);
                }
            }
        }

        $index = [];

        foreach ($records as $data) {
            foreach ($domainIds as $domainId) {
                if (isset($prices[$domainId][$data['master_id']]) && isset($prices[$domainId][$data['master_id']]['sales'])) {
                    $discountPct = $prices[$domainId][$data['master_id']]['sales']['sales_pct'];

                    foreach ($data['styles'] as $style) {
                        $key = $data['master_id'].'-'.$style.'-'.$discountPct.'-'.$domainId;
                        // Avoid duplicated entries
                        if (isset($index[$key])) {
                            continue;
                        }

                        $index[$key] = true;

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
}

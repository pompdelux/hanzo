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
     * indexes which actions are performed on
     *
     * @var array
     */
    private $indexes = [
                'product'      => false,
                'category'     => false,
                'tag'          => false,
                'discount'     => false,
                ];

    /**
     * Inserts data
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function build()
    {
        foreach (array_keys($this->getConnections()) as $name) {
            // default has been replaced by pdldbdk1
            if ($name == 'default') {
                continue;
            }
            $connection = $this->getConnection($name);

            foreach ($this->getLocales($connection) as $locale) {
                $this->performUpdate($locale, $connection);
            }
        }
    }

    /**
     * setIndexes sets which indexes should be worked on, ALL for... all
     *
     * @param Array $indexes
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function setIndexes(Array $indexes)
    {
        $this->resetIndexes();

        if (in_array("ALL", $indexes)) {
            foreach ($this->indexes as $index => $value) {
                $this->indexes[$index] = true;
            }
        } else {
            foreach ($indexes as $index) {
                if (isset($this->indexes[$index])) {
                    $this->indexes[$index] = true;
                }
            }
        }
    }

    /**
     * resetIndexes
     * Needed because we might be running in a loop via beanstalk
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    private function resetIndexes()
    {
        foreach ($this->indexes as $index => $value) {
            $this->indexes[$index] = false;
        }
    }

    /**
     * getActiveIndexes
     *
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function getActiveIndexes()
    {
        $strict      = true;
        $searchValue = true;

        return array_keys($this->indexes, $searchValue, $strict);
    }

    /**
     * performUpdate
     *
     * @param string $locale
     * @param PDO $connection
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function performUpdate($locale, $connection)
    {
        foreach ($this->getActiveIndexes() as $index) {
            switch ($index)
            {
                case 'product':
                    $this->updateProductIndex($locale, $connection);
                    break;
                case 'category':
                    $this->updateCategoryIndex($locale, $connection);
                    break;
                case 'tag':
                    $this->updateCustomTokensIndex($locale, $connection);
                    break;
                case 'discount':
                    $this->updateDiscountIndex($locale, $connection);
                    break;
            }
        }
    }

    /**
     * clear
     * Deletes a specific type
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function clear()
    {
        $sql = "DELETE FROM search_products_tags WHERE type = :type";

        foreach (array_keys($this->getConnections()) as $name) {
            $connection = $this->getConnection($name);
            $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

            foreach ($this->getActiveIndexes() as $index) {
                $query->execute(['type' => $index]);
            }
        }
    }

    /**
     * truncate
     * Truncates the search_products_tags table for each connection
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function truncate()
    {
        $sql = "TRUNCATE TABLE search_products_tags";

        foreach (array_keys($this->getConnections()) as $name) {
            $connection = $this->getConnection($name);
            $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute();
        }
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

        foreach ($tokensToCategories as $token => $categories) {
            // Only master products are in this table
            $masterProducts = ProductsQuery::create()
                ->useProductsToCategoriesQuery()
                    ->filterByCategoriesId($categories)
                ->endUse()
                ->find();

            foreach ($masterProducts as $masterProduct) {
                $products = ProductsQuery::create()
                    ->filterByMaster($masterProduct->getSku())
                    ->find();

                foreach ($products as $product) {
                    $tokenValue = Tools::stripText($token);

                    $sql = sprintf(
                        "INSERT INTO
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
                        $locale
                    );

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

        foreach ($catalog->all('cms') as $key => $msg) {
            if (!preg_match('/([0-9]+).settings/i', $key, $matches)) {
                continue;
            }

            $msg = trim($msg);

            if (is_scalar($msg) && substr($msg, 0, 1) == '{') {
                $settings = json_decode(stripcslashes($msg));
                if (isset($settings->tokens)) {
                    $tokens = (array) $settings->tokens;
                    foreach ($tokens as $key => $categories) {
                        if (!isset($tokensToCategories[$key])) {
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
                (products_domains_prices.from_date <= NOW()
            AND
                products_domains_prices.to_date >= NOW())
            GROUP BY products.sku";

        $stmt = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $stmt->execute([
            'locale' => $locale,
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

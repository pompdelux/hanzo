<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\SearchProductsTagsQuery,
    Hanzo\Model\ProductsQuery
    ;

class ProductIndexBuilder extends IndexBuilder
{
    public function build()
    {
        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            foreach ($this->getLocales($connection) as $locale) {
                $this->updateProductIndex($locale, $connection);
                $this->updateCategoryIndex($locale, $connection);
                $this->updateCustomTokensIndex($locale, $connection);
            }
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
                    locale
                )
            SELECT
                p1.id,
                p2.id,
                p2.color,
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
                    locale
                )
            SELECT
                p1.id,
                p2.id,
                c.title,
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
        $tokensToCategories = $this->getCustomTokensForCategories($locale, $connection);

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
                    $sql = sprintf("
                        INSERT INTO
                            search_products_tags (
                                master_products_id,
                                products_id,
                                token,
                                locale
                            )
                        VALUES(%d, %d, '%s', '%s')
                    ",
                    $masterProduct->getId(),
                    $product->getId(),
                    $token,
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
     * The following has to be added to the cms.$locale.xliff file om the XXX.settings block
     * Each number refers to a category
     *
     * "tokens": {
     *   "gots": [
     *     "212",
     *     "214"
     *   ],
     *   "oekotex": [
     *     "212"
     *   ]
     * }
     * @param string $locale
     * @param PropelPDO $connection
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    private function getCustomTokensForCategories($locale, $connection)
    {
        $tokensToCategories = [];

        $sql = sprintf("
            SELECT
                i18n.settings
            FROM
                cms_i18n i18n,
                cms
            WHERE
                cms.type = 'heading'
            AND
                i18n.locale = '%s'
            AND
                i18n.settings IS NOT NULL
            AND
                cms.id = i18n.id
            AND
                i18n.is_active = 1
            ",
            $locale);

        $query = $connection->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_ASSOC);

        while ($record = $query->fetch()) {
            $settings = json_decode($record['settings']);
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

        return $tokensToCategories;
    }
}

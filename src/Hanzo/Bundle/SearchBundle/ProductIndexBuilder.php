<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\SearchProductsTagsQuery,
    Hanzo\Model\ProductsQuery,
    Hanzo\Core\Hanzo
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
     * clear
     * Truncates the search_products_tags table for each connection
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function clear()
    {
        foreach ($this->getConnections() as $name => $x) {
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
     * - The value is stored in the db prefixed with 'token-' to avoid clash with category names
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
                    $tokenValue = 'token-'.$token;

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
                    $tokenValue,
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
}

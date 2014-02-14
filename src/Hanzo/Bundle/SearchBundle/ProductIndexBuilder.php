<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\SearchProductsTagsQuery;

class ProductIndexBuilder extends IndexBuilder
{
    public function build()
    {
        foreach ($this->getConnections() as $name => $x) {
            $connection = $this->getConnection($name);

            foreach ($this->getLocales($connection) as $locale) {
                $this->updateProductIndex($locale, $connection);
                $this->updateCategoryIndex($locale, $connection);
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
}

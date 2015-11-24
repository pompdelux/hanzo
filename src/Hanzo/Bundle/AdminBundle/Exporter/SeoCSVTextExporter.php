<?php

namespace Hanzo\Bundle\AdminBundle\Exporter;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\CmsQuery;

/**
 * Class LanguageExporter
 *
 * @package Hanzo\Bundle\AdminBundle\Exporter
 */
class SeoCSVTextExporter extends \PropelCSVParser
{
    /**
     * SeoCSVTextExporter constructor.
     *
     * @param string $delimiter
     */
    public function __construct($delimiter = ',')
    {
        $this->quoting   = self::QUOTE_NONNUMERIC;
        $this->delimiter = $delimiter;
    }

    /**
     * @param \PropelPDO|\Propel $connection
     */
    public function setDBConnection($connection)
    {
        $this->dbConnection = $connection;
    }

    /**
     * @param string $locale
     * @param mixed  $exportTypes
     *
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getData($locale, $exportTypes)
    {
        if (is_null($this->getDBConnection())) {
            throw new \OutOfBoundsException("Database connection needs to be set.");
        }

        return $this->listFromArray($this->build($locale, $exportTypes));
    }

    /**
     * exportProducts
     *
     * @param string $locale
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function exportProducts($locale)
    {
        $data = [];
        // We only export Master products, when imported the data is copied to all varients
        $products = ProductsQuery::create()
            ->filterByMaster(null, \Criteria::ISNULL)
            ->joinWithI18n($locale)
            ->find($this->getDBConnection());

        // Numbers are removed from key when using listFromArray
        $i = 0;
        foreach ($products as $product) {
            $seo = $product->getProductsSeoI18ns(null, $this->getDBConnection());

            $title       = '';
            $description = '';

            if ($seo->count()) {
                $seo = $seo->first();
                $title       = $seo->getMetaTitle();
                $description = $seo->getMetaDescription();
            }

            $data['product'.$i++] = [
                'id'               => $product->getId(),
                'sku'              => $product->getSku(),
                'meta_title'       => $title,
                'meta_description' => $description
            ];
        }

        return $data;
    }

    /**
     * exportCms
     *
     * @param string $locale
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function exportCms($locale)
    {
        $data = [];
        $cmsPages = CmsQuery::create()
            ->joinWithI18n($locale)
            ->useCmsI18nQuery()
                ->filterByTitle('', \Criteria::NOT_EQUAL)
            ->endUse()
            ->find($this->getDBConnection());

        $i = 0;
        /** @var \Hanzo\Model\CmsI18n $page */
        foreach ($cmsPages as $page) {
            $data['cmspage'.$i++] = [
                'id'               => $page->getId(),
                'title'            => str_replace('&nbsp;', '', $page->getTitle()),
                'path'             => '/'.$locale.'/'.$page->getPath(),
                'meta_title'       => $page->getMetaTitle(),
                'meta_description' => $page->getMetaDescription(),
            ];
        }

        return $data;
    }

    /**
     * @return \PDO|\PropelPDO
     */
    private function getDBConnection()
    {
        return $this->dbConnection;
    }

    /**
     * build
     *
     * @param string $locale
     * @param array  $exportTypes
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    private function build($locale, $exportTypes)
    {
        if (!is_array($exportTypes)) {
            $exportTypes = [$exportTypes];
        }

        $data = ['locale' => $locale];

        if (in_array('products', $exportTypes)) {
            return $this->exportProducts($locale);
        }

        if (in_array('cms', $exportTypes)) {
            return $this->exportCms($locale);
        }

        return $data;
    }
}

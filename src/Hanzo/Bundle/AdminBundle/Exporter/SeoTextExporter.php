<?php

namespace Hanzo\Bundle\AdminBundle\Exporter;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\CmsQuery;

/**
 * Class LanguageExporter
 *
 * @package Hanzo\Bundle\AdminBundle\Exporter
 */
class SeoTextExporter extends \PropelXMLParser
{
    /**
     * @param \PropelPDO|\PDO $connection
     */
    public function setDBConnection($connection)
    {
        $this->dbConnection = $connection;
    }

    /**
     * @param string $locale
     * @param Array  $exportTypes
     *
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getDataAsXML($locale, Array $exportTypes)
    {
        if (is_null($this->getDBConnection())) {
            throw new \OutOfBoundsException("Database connection needs to be set.");
        }

        return $this->listFromArray($this->build($locale, $exportTypes), 'seo_texts');
    }

    /**
     * @param array      $array
     * @param DOMElement $rootElement
     * @param string     $charset
     * @param boolean    $removeNumbersFromKeys
     *
     * @return DOMElement
     */
    protected function arrayToDOM($array, $rootElement, $charset = null, $removeNumbersFromKeys = false)
    {
        foreach ($array as $key => $value) {
            if ($removeNumbersFromKeys) {
                // hf@bellcom.dk: Notice: this removed everything from the key that is not a-z, e.g. _ and numbers
                $key = preg_replace('/[^a-z]/i', '', $key);
            }
            $element = $rootElement->ownerDocument->createElement($key);
            if (is_array($value)) {
                if (!empty($value)) {
                    // hf@bellcom.dk: Added $removeNumbersFromKeys
                    $element = $this->arrayToDOM($value, $element, $charset, $removeNumbersFromKeys);
                }
            } elseif (is_string($value)) {
                $charset = $charset ? $charset : 'utf-8';
                if (function_exists('iconv') && strcasecmp($charset, 'utf-8') !== 0 && strcasecmp($charset, 'utf8') !== 0) {
                    $value = iconv($charset, 'UTF-8', $value);
                }
                // hf@bellcom.dk: don't convert
                // $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
                $child = $element->ownerDocument->createCDATASection($value);
                $element->appendChild($child);
            } else {
                $child = $element->ownerDocument->createTextNode($value);
                $element->appendChild($child);
            }
            $rootElement->appendChild($element);
        }

        return $rootElement;
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
        $products = ProductsQuery::create()
            ->filterByMaster(null, \Criteria::ISNOTNULL)
            ->joinWithI18n($locale)
            ->find($this->getDBConnection());

        // Numbers are removed from key when using listFromArray
        $i = 0;
        foreach ($products as $product) {
            $seo = $product->getProductsSeoI18ns(null, $this->getDBConnection());
            $seo = $seo->toArray();

            $title       = '';
            $description = '';
            if (!empty($seo)) {
                $title       = $seo[0]['MetaTitle'];
                $description = $seo[0]['MetaDescription'];
            }

            $data['product'.$i++] = ['id' => $product->getId(), 'sku' => $product->getSku(), 'metatitle' => $title, 'metadescription' => $description];
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
            ->find($this->getDBConnection());

        $i = 0;
        foreach ($cmsPages as $page) {
            $title       = $page->getMetaTitle();
            $description = $page->getMetaDescription();
            // Only used to make a page easier to identify in the xml
            $name        = $page->getTitle();
            $path        = $page->getPath();

            // Numbers are removed from key when using listFromArray
            $data['cmspage'.$i++] = ['id' => $page->getId(), 'title' => $name, 'path' => $path, 'metatitle' => $title, 'metadescription' => $description];
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
        $data = [
            'locale' => $locale,
        ];

        if (in_array('products', $exportTypes)) {
            $data['products'] = $this->exportProducts($locale);
        }

        if (in_array('cms', $exportTypes)) {
            $data['cmspages'] = $this->exportCms($locale);
        }

        return $data;
    }
}

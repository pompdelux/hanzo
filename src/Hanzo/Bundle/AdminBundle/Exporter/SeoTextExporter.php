<?php

namespace Hanzo\Bundle\AdminBundle\Exporter;

use Hanzo\Model\ProductsQuery;

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
     * @return \PDO|\PropelPDO
     */
    private function getDBConnection()
    {
        return $this->dbConnection;
    }

    /**
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getDataAsXML()
    {
        if (is_null($this->getDBConnection())) {
            throw new \OutOfBoundsException("Database connection needs to be set.");
        }

        return $this->listFromArray($this->build(), 'seo_texts');
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
                $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
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
     * @return array
     */
    private function build()
    {
        $locale = 'da_DK';
        $data = [
            'locale'   => $locale,
            'products' => [],
        ];

        $products = ProductsQuery::create()
            ->joinProductsSeoI18n(null, \Criteria::LEFT_JOIN)
            ->joinWithI18n($locale)
            ->find($this->getDBConnection());

        // Numbers are removed from key when using listFromArray
        $i = 0;
        foreach ($products as $product) {
            $seo = $product->getProductsSeoI18ns();
            $data['products']['product'.$i++] = ['sku' => $product->getSku(), 'title' => $seo->getMetaTitle(), 'description' => $seo->getMetaDescription()];
        }

        return $data;
    }
}

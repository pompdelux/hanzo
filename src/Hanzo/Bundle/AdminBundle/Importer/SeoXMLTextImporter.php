<?php

namespace Hanzo\Bundle\AdminBundle\Importer;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class SeoXMLTextImporter
 *
 * @package Hanzo\Bundle\AdminBundle\Importer
 */
class SeoXMLTextImporter extends BaseSeoTextImporter implements SeoTextImporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(File $file)
    {
        $xml = simplexml_load_file($file->getRealPath());

        if ($xml === false) {
            throw new SeoTextImporterException('Some errors happened: Could not load xml file or file contains errors');
        }

        $cmsPages
            = $locale
            = $products
            = false;

        foreach ($xml->children() as $child) {
            switch ($child->getName())
            {
                case 'locale':
                    $locale = (string) $child;
                    break;

                case 'products':
                    $products = $child;
                    break;

                case 'cmspages':
                    $cmsPages = $child;
                    break;

                default:
                    $errors[] = 'Unknown tag "'.$child->getName().'"';
                    break;
            }
        }

        if ($locale === false) {
            throw new SeoTextImporterException('Some errors happened: Missing <locale> tag');
        }

        if ($this->locale !== $locale) {
            throw new SeoTextImporterException('Some errors happened: Uploaded fields <locale> tag does not match the selected database: "'.$locale.'" vs. "'.$this->locale.'"');
        }

        $productsErrors = [];
        if ($products !== false) {
            $productsErrors = $this->importProductsTexts($products, $locale);
        }

        $cmsPagesErrors = [];
        if ($cmsPages !== false) {
            $cmsPagesErrors = $this->importCMSPagesTexts($cmsPages, $locale);
        }

        $errors = array_merge($productsErrors, $cmsPagesErrors);

        if (!empty($errors)) {
            throw new SeoTextImporterException('Some errors happened: '.implode("<br>", $errors));
        }

        return true;
    }

    /**
     * importProductsTexts
     *
     * @param \SimpleXMLElement $products
     * @param string            $locale
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function importProductsTexts(\SimpleXMLElement $products, $locale)
    {
        $errors = [];

        foreach ($products->children() as $product) {
            $productId   = (int) $product->id;
            $title       = (string) $product->metatitle;
            $description = (string) $product->metadescription;
            $sku         = (string) $product->sku;

            if (empty($title) && empty($description)) {
                continue;
            }

            if (strlen($title) > 255) {
                $errors[] = 'Title is more than 255 chars (Truncated in database):<br>'.$title.'<br>';
            }

            if (strlen($description) > 255) {
                $errors[] = 'Description is more than 255 chars (Truncated in database):<br>'.$description.'<br>';
            }

            $this->updateProductSeo($locale, $sku, $productId, $title, $description);
        }

        return $errors;
    }

    /**
     * importCMSPagesTexts
     *
     * @param \SimpleXMLElement $pages
     *
     * @return array
     * @author Henrik Farre <hf@bellcom.dk>
     */
    protected function importCMSPagesTexts(\SimpleXMLElement $pages, $locale)
    {
        $errors = [];

        foreach ($pages->children() as $page) {
            $pageId      = (int) $page->id;
            $title       = (string) $page->metatitle;
            $description = (string) $page->metadescription;

            if (empty($title) && empty($description)) {
                continue;
            }

            if (strlen($title) > 255) {
                $errors[] = 'Title is more than 255 chars (Truncated in database):<br>'.$title.'<br>';
            }

            if (strlen($description) > 255) {
                $errors[] = 'Description is more than 255 chars (Truncated in database):<br>'.$description.'<br>';
            }

            $this->updateCmsSeo($locale, $pageId, $title, $description);
        }

        return $errors;
    }
}

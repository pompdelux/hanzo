<?php

namespace Hanzo\Bundle\AdminBundle\Importer;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class SeoCsvTextImporter
 *
 * @package Hanzo\Bundle\AdminBundle\Importer
 */
class SeoCsvTextImporter extends BaseSeoTextImporter implements SeoTextImporterInterface
{
    public function handle(File $file)
    {
        // The filename format matters: seo-texts-(products|cms)-(locale).csv
        list ($part, $locale) = explode('-', str_replace(['seo-texts-'], '', $file->getBasename('.csv')));

        // Only accept locales in the current locale scope.
        if ($this->locale !== $locale) {
            throw new SeoTextImporterException('Some errors happened: Uploaded fields <locale> tag does not match the selected database: "'.$locale.'" vs. "'.$this->locale.'"');
        }

        $handler = $file->openFile();
        $handler->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $handler->setCsvControl(';');

        $errors = [];

        $i = 0;
        foreach ($handler as $row) {
            ++$i;

            // skip header line
            if (1 === $i) {
                continue;
            }

            if ('cms' == $part) {
                // Extract cms page is, meta title and description.
                list($id, , , $title, $description) = $row;
            } elseif ('products' == $part) {
                // Extract product id, sku, meta title and description.
                list($id, $sku, $title, $description) = $row;
            }

            // For some reason Propel dumps null values as "N;" - and I cannot figure out how to remove these from the export.
            $title = trim($title, 'N;');
            $description = trim($description, 'N;');

            if (strlen($title) > 255) {
                $errors[] = 'Title is more than 255 chars (Truncated in database):<br>'.$title.'<br>';
            }

            if (strlen($description) > 255) {
                $errors[] = 'Description is more than 255 chars (Truncated in database):<br>'.$description.'<br>';
            }

            if ('cms' == $part) {
                $this->updateCmsSeo($locale, $id, $title, $description);
            } elseif ('products' == $part) {
                $this->updateProductSeo($locale, $sku, $id, $title, $description);
            }
        }

        if (count($errors)) {
            throw new SeoTextImporterException('Some errors happened: '.implode("<br>", $errors));
        }

        return true;
    }
}

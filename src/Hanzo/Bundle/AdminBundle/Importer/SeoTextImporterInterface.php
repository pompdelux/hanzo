<?php
/**
 * This file is part of the hanzo package.
 *
 * (c) ${COPYRIGHT}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AdminBundle\Importer;

use Symfony\Component\HttpFoundation\File\File;

interface SeoTextImporterInterface
{
    /**
     * SeoTextImporterInterface constructor.
     *
     * @param string $locale       Current locale to import into.
     * @param string $dbConnection DB connection
     */
    public function __construct($locale, $dbConnection);

    /**
     * Handles the reading and parsing of the SEO import file.
     *
     * @param File $file
     *
     * @return bool
     * @throws SeoTextImporterException
     */
    public function handle(File $file);
}

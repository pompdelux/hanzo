<?php

namespace Hanzo\Bundle\AdminBundle\Importer;

use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsSeoI18n;
use Hanzo\Model\ProductsSeoI18nQuery;

/**
 * Class BaseSeoTextImporter
 *
 * @package Hanzo\Bundle\AdminBundle\Importer
 */
class BaseSeoTextImporter
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \PDO
     */
    protected $dbConnection;

    /**
     * {@inheritdoc}
     */
    public function __construct($locale, $dbConnection)
    {
        $this->locale = $locale;
        $this->dbConnection = $dbConnection;
    }

    /**
     * @param string $locale
     * @param string $sku
     * @param int    $productId
     * @param string $title
     * @param string $description
     *
     * @throws \Exception
     * @throws \PropelException
     */
    protected function updateProductSeo($locale, $sku, $productId, $title, $description)
    {
        // Master product:
        $seoText = ProductsSeoI18nQuery::create()
            ->filterByLocale($locale)
            ->filterByProductsId($productId)
            ->findOne($this->dbConnection);

        if (!$seoText) {
            $seoText = new ProductsSeoI18n();
        }

        $seoText->setProductsId($productId);
        $seoText->setMetaTitle($title);
        $seoText->setMetaDescription($description);
        $seoText->setLocale($locale);
        $seoText->save($this->dbConnection);

        unset($seoText);

        // Copy text to all variants
        $variants = ProductsQuery::create()
            ->filterByMaster($sku)
            ->find($this->dbConnection);

        foreach ($variants as $variant) {
            $seoText = ProductsSeoI18nQuery::create()
                ->filterByLocale($locale)
                ->filterByProductsId($variant->getId())
                ->findOne($this->dbConnection);

            if (!$seoText) {
                $seoText = new ProductsSeoI18n();
            }

            $seoText->setProductsId($variant->getId());
            $seoText->setMetaTitle($title);
            $seoText->setMetaDescription($description);
            $seoText->setLocale($locale);
            $seoText->save($this->dbConnection);
        }
    }

    /**
     * @param string $locale
     * @param int    $pageId
     * @param string $title
     * @param string $description
     *
     * @return bool
     * @throws \Exception
     */
    protected function updateCmsSeo($locale, $pageId, $title, $description)
    {
        // we set empty to null to enable pdl to "delete" meta data.
        $title = $title ?: null;
        $description = $description ?: null;

        $seoText = CmsI18nQuery::create()
            ->filterByLocale($locale)
            ->filterById($pageId)
            ->findOne($this->dbConnection);

        // we do not add new pages.
        if (!$seoText instanceof CmsI18n) {
            return false;
        }

        if ($title) {
            error_log(mb_detect_encoding($title));
            error_log(utf8_decode($title));
        }

        $seoText->setMetaTitle($title);
        $seoText->setMetaDescription($description);
        $seoText->save($this->dbConnection);

        return true;
    }
}

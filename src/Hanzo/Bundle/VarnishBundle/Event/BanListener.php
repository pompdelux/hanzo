<?php

namespace Hanzo\Bundle\VarnishBundle\Event;

use Symfony\Component\EventDispatcher\Event as FilterEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Hanzo\Bundle\VarnishBundle\Varnish;

use Hanzo\Core\Tools;
use Hanzo\Core\PropelReplicator;

use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\Cms;

class BanListener
{
    /**
     * @var Varnish
     */
    protected $varnish;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $cache_dir;

    /**
     * @var PropelReplicator
     */
    protected $replicator;

    /**
     * @param Varnish          $varnish
     * @param Router           $router
     * @param string           $cache_dir
     * @param PropelReplicator $replicator
     */
    public function __construct(Varnish $varnish, Router $router, $cache_dir, PropelReplicator $replicator)
    {
        $this->varnish    = $varnish;
        $this->router     = $router;
        $this->cache_dir  = $cache_dir;
        $this->replicator = $replicator;
    }

    /**
     * send banUrl requests to varnish when changing cms pages
     *
     * @param FilterEvent $event
     */
    public function onBanCmsNode(FilterEvent $event)
    {
        $item = $event->getData();

        if ($item instanceof Cms) {
            $item = $item->getTranslation($event->getLocale(), $event->getConnection());
        }

        $path = '^/'.$item->getLocale('.*').'/'.$item->getPath();

        $settings = $item->getSettings(null, false);
        if ($settings && isset($settings->is_frontpage) && $settings->is_frontpage == 1) {
            $path = '^/'.$item->getLocale('.*').'/$';
        }

        try {
            $this->varnish->banUrl($path);
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }
    }

    /**
     * purge varnish urls on deleting categories
     *
     * @param FilterEvent $event
     */
    public function onCategoryDeleteNode(FilterEvent $event)
    {
        $this->purgeUrlsBasedOnCategory($event->getData());
    }


    /**
     * purge varnish urls on out-of-stock products
     *
     * @param FilterEvent $event
     */
    public function onProductSoldOut(FilterEvent $event)
    {
        $item = $event->getData();

        $categories = CategoriesQuery::create()
            ->useProductsToCategoriesQuery()
                ->filterByProductsId($item->getId())
            ->endUse()
            ->find()
        ;

        foreach ($categories as $category) {
            $this->purgeUrlsBasedOnCategory($category);
        }

        try {
            $this->varnish->banUrl('^/.*/product/view/'.$item->getId());
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }
    }


    /**
     * do the actual category loockup and send purges
     *
     * @param Categories $category
     */
    protected function purgeUrlsBasedOnCategory($category)
    {
        static $category_map;

        if (!$category instanceof Categories) {
            return;
        }

        if (empty($category_map)) {
            $category_map = $this->getCategoryMapping();
        }

        if (empty($category_map[$category->getId()])) {
            return;
        }

        $items = $category_map[$category->getId()];

        try {
            foreach ($items as $path) {
                $path = '^/'.$path.'.*';
                $this->varnish->banUrl($path);
            }

            if ($category->getContext()) {
                $context = strtoupper(substr($category->getContext(), 0, 2));
                $this->varnish->banUrl('^/.*/products/list/context/'.$context.'.*');
            }

        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }
    }


    /**
     * Fetches array of category paths to clear
     *
     * @return array
     */
    protected function getCategoryMapping()
    {
        $results = $this->replicator->executeQuery("
            SELECT
                cms_i18n.settings,
                CONCAT(cms_i18n.locale, '/', cms_i18n.path) as path
            FROM
                cms
            JOIN
                cms_i18n
            ON (
                cms.id = cms_i18n.id
            )
            WHERE
                cms.type = 'category'
        ");

        $category_map = [];
        foreach ($results as $name => $sth) {
            while ($record = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $settings = json_decode($record['settings']);
                if (empty($category_map[$settings->category_id])) {
                    $category_map[$settings->category_id] = [];
                }

                if (!in_array($record['path'], $category_map[$settings->category_id])) {
                    $category_map[$settings->category_id][] = $record['path'];
                }
            }
        }

        return $category_map;
    }

}

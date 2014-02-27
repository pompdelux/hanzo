<?php

namespace Hanzo\Bundle\VarnishBundle\Event;

use Hanzo\Model\Categories;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\Event as FilterEvent;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Hanzo\Bundle\VarnishBundle\Varnish;

use Hanzo\Core\Tools;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CategoriesQuery;

class BanListener
{
    protected $varnish;
    protected $router;
    protected $cache_dir;
    protected $translator;

    public function __construct(Varnish $varnish, Router $router, $cache_dir, Translator $translator)
    {
        $this->varnish    = $varnish;
        $this->router     = $router;
        $this->cache_dir  = $cache_dir;
        $this->translator = $translator;
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
                Tools::log($path);
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


    protected function getCategoryMapping()
    {
        $pages = CmsQuery::create()
            ->filterByType('category')
            ->find()
        ;

        $category_map = [];
        foreach ($pages as $page) {
            /** @var Cms $page */
            $settings = $page->getSettings(null, false);

            if (isset($settings->category_id)) {
                foreach ($page->getCmsI18ns() as $i18n) {
                    $category_map[$settings->category_id][] = $i18n->getPath();
                }
            }
        }

        return $category_map;
    }

}

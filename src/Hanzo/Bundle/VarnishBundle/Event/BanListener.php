<?php

namespace Hanzo\Bundle\VarnishBundle\Event;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\Event as FilterEvent;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Hanzo\Bundle\VarnishBundle\Varnish;

use Hanzo\Core\Tools;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\CategoriesQuery;

class BanListener
{
    protected $varnish;
    protected $router;
    protected $cache_dir;

    public function __construct(Varnish $varnish, Router $router, $cache_dir)
    {
        $this->varnish = $varnish;
        $this->router = $router;
        $this->cache_dir = $cache_dir;
    }

    /**
     * send banUrl requests to varnish when changing cms pages
     *
     * @param FilterEvent $event
     */
    public function onBanCmsNode(FilterEvent $event)
    {
        $path = '';
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
            $this->purgeUrlsBasedOnCategory($category, $event->getLocale());
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
     * @param Hanzo\Model\Categories $category
     */
    protected function purgeUrlsBasedOnCategory($category, $locale = null)
    {
        if (!$category instanceof Categories) {
            return;
        }

        $query = CmsI18nQuery::create()
            ->select(['Path', 'Locale'])
            ->filterBySettings('%category%')
            ->filterBySettings('%'.$category->getId().'%')
        ;

        if ($locale) {
            $query->filterByLocale($locale);
        }

        // g/b filter, should not be here - but for now it works...
        $context = '';
        if ($category->getContext()) {
            $context = strtoupper(substr($category->getContext(), 0, 2));
            switch ($context) {
                case 'G_':
                    $query->filterByPath('%/girl%');
                    break;
                case 'B_':
                    $query->filterByPath('%/boy%');
                    break;
                case 'LG':
                    $query->filterByPath('%/little-girl%');
                    break;
                case 'LB':
                    $query->filterByPath('%/little-boy%');
                    break;
            }
        }

        $items = $query->find();

        try {
            foreach ($items as $index => $item) {
                $path = '^/.*/'.$item['Path'].'.*';
                $this->varnish->banUrl($path);
            }

            if ($context) {
                $this->varnish->banUrl('^/.*/products/list/context/'.$context.'.*');
            }

        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }
    }
}

<?php /* vim: set sw=4: */
/**
 * generating routes from cms pages pr. locale.
 *
 * @see http://php-and-symfony.matthiasnoback.nl/2012/01/symfony2-dynamically-add-routes/
 */

namespace Hanzo\Bundle\CMSBundle;

use Hanzo\Core\Tools;
use Hanzo\Model\CmsI18nQuery;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CMSRouterLoader
 *
 * @package Hanzo\Bundle\CMSBundle
 */
class CMSRouterLoader implements LoaderInterface
{
    private $loaded = false;
    private $locale;
    private $cacheDir;

    /**
     * setup required variables
     *
     * @param string $locale   active locale
     * @param string $cacheDir Path to filesystem cache
     */
    public function __construct($locale, $cacheDir)
    {
        $this->locale = $locale;
        $this->cacheDir = $cacheDir;
    }


    /**
     * load the routes
     *
     * @param string $resource unused, but required
     * @param string $type     unused, but required
     *
     * @return RouteCollection
     * @throws \RuntimeException
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $categories = [];
        $routes = new RouteCollection();

        $pages = CmsI18nQuery::create()
            ->rightJoinWithCms()
            ->orderByPath()
            ->findByLocale($this->locale);

        /** @var \Hanzo\Model\CmsI18n $page */
        foreach ($pages as $page) {
            $id          = $page->getId();
            $locale      = trim($page->getLocale());
            $localeLower = strtolower($locale);
            $path        = '{_locale}/' . trim($page->getPath());
            $type        = trim($page->getCms()->getType());
            $title       = trim($page->getTitle());

            $isRestricted = 0;
            if (!$page->getIsActive()) {
                $isRestricted = (int) $page->getIsRestricted();
            }

            if (('' == $title) || isset($processed[$path])) {
                continue;
            }

            $processed[$path] = $path;

            $settings = trim($page->getSettings());
            if (substr($settings, 0, 2) == 'a:') {
                // serialized settings
                $settings = unserialize(stripslashes($settings));
            } elseif (substr($settings, 0, 1) == '{') {
                // Json encoded settings
                $settings = json_decode($settings);
            }

            switch ($type) {
                case 'category':
                    //test ... we should never enter this if tho...
                    if (!isset($settings->category_id)) {
                        Tools::log('Missing category id on page id: ' . $id);
                        continue;
                    }

                    $categoryKey  = '_' . $localeLower . '_' . $settings->category_id;
                    $categoryPath = 'category_' . $id . '_' . $localeLower;
                    $productPath  = 'product_' . $id . '_' . $localeLower;

                    $categories[$categoryKey] = $productPath;

                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", [
                            '_controller'   => 'ProductBundle:Default:view',
                            '_format'       => 'html',
                            'cms_id'        => $id,
                            'category_id'   => $settings->category_id,
                            'title'         => '',
                            'ip_restricted' => true,
                        ], [
                            'product_id' => '\d+',
                            '_format'    => 'html|json',
                    ]);

                    $routes->add($productPath, $route);

                    // category route
                    $route = new Route("/{$path}/{show}/{pager}", [
                            '_controller'   => 'CategoryBundle:Default:view',
                            '_format'       => 'html',
                            'cms_id'        => $id,
                            'category_id'   => $settings->category_id,
                            'pager'         => 1,
                            'show'          => 'overview',
                            'ip_restricted' => true,
                        ], [
                            '_format' => 'html|json',
                    ]);
                    $routes->add($categoryPath, $route);

                    break;

                case 'look':
                    //test ... we should never enter this if tho...
                    if ((!$settings instanceof \stdClass) || !isset($settings->category_id)) {
                        Tools::log('Missing category id on CMS #' . $page->getId());
                        continue;
                    }

                    $lookKey     = '_' . $localeLower . '_' . $settings->category_id;
                    $lookPath    = 'look_' . $id . '_' . $localeLower;
                    $productPath = 'product_' . $id . '_' . $localeLower;

                    $categories[$lookKey] = $productPath;

                    // look route
                    $route = new Route("/{$path}/{pager}", [
                            '_controller'   => 'CategoryBundle:ByLook:view',
                            '_format'       => 'html',
                            'cms_id'        => $id,
                            'category_id'   => $settings->category_id,
                            'pager'         => 1,
                            'ip_restricted' => true,
                        ], [
                            'pager'   => '\d+',
                            '_format' => 'html|json',
                    ]);
                    $routes->add($lookPath, $route);

                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", [
                            '_controller'   => 'ProductBundle:Default:view',
                            '_format'       => 'html',
                            'product_id'    => 0,
                            'cms_id'        => $id,
                            'look_id'       => $settings->category_id,
                            'title'         => '',
                            'ip_restricted' => true,
                        ], [
                            'product_id' => '\d+',
                            '_format'    => 'html|json',
                    ]);
                    $routes->add($productPath, $route);

                    break;

                case 'bycolour':
                    $route = new Route("/{$path}/{show}", [
                            '_controller'   => 'CategoryBundle:ByColour:view',
                            'id'            => $id,
                            'show'          => 'overview',
                            'ip_restricted' => true,
                        ]);
                    $routes->add('bycolour_'.$id . '_' . $localeLower, $route);

                    $productPath = 'product_' . $id . '_' . $localeLower ;
                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", [
                            '_controller'   => 'ProductBundle:Default:view',
                            '_format'       => 'html',
                            'product_id'    => 0,
                            'cms_id'        => $id,
                            'title'         => '',
                            'ip_restricted' => true,
                        ], [
                            'product_id' => '\d+',
                            '_format'    => 'html|json',
                        ]);
                    $routes->add($productPath, $route);

                    break;

                case 'page':
                    $route = new Route("/".$path, [
                        '_controller'   => 'CMSBundle:Default:view',
                        'id'            => $id,
                        'ip_restricted' => $isRestricted,
                    ]);
                    $routes->add('page_'.$id, $route);

                    break;

                case 'mannequin':
                    $route = new Route("/".$path, [
                        '_controller' => 'MannequinBundle:Default:view',
                        'id'          => $id,
                    ]);
                    $routes->add('mannequin_'.$id, $route);

                    break;

                case 'newsletter':
                    $route = new Route("/".$path, [
                        '_controller' => 'NewsletterBundle:Default:view',
                        'id'          => $id,
                    ]);
                    $routes->add('newsletter_'.$id, $route);

                    break;

                case 'search':
                    if ((!$settings instanceof \stdClass) || !isset($settings->type)) {
                        continue;
                    }

                    $method     = $settings->type;
                    $restricted = (($settings->type == 'category') ? 'true' : 'false');

                    $route = new Route("/".$path, [
                            '_controller'   => "SearchBundle:Default:{$method}",
                            '_format'       => 'html',
                            'id'            => $id,
                            'ip_restricted' => $restricted,
                        ], [
                            '_format' => 'html|json'
                    ]);
                    $routes->add($settings->type.'_search_'.$id, $route);

                    break;

                case 'advisor_map':
                case 'advisor_open_house':
                    $method = lcfirst(str_replace(' ', '', trim(ucwords(str_replace(['advisor', '_'], ' ', $type)))));

                    $route = new Route("/".$path, [
                            '_controller'   => "EventBundle:Advisor:{$method}",
                            'id'            => $id,
                            'ip_restricted' => $isRestricted,
                        ], [
                            '_format' => 'html|json'
                        ]);
                    $routes->add($type.'_'.$id, $route);

                    break;
            }
        }

        // cache the category map
        $categoryMapOut = '<?php # -:[' . date('Y-m-d H:i:s') . ']:-'."\nreturn ". var_export($categories, 1) . ";\n";
        file_put_contents($this->cacheDir.'/category_map.php', $categoryMapOut);

        return $routes;
    }


    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        return 'cms' === $type;
    }


    /**
     * {@inheritDoc}
     */
    public function getResolver()
    {
    }


    /**
     * {@inheritDoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}

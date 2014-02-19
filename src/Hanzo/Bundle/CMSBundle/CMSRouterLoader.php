<?php /* vim: set sw=4: */
/**
 * generating routes from cms pages pr. locale.
 *
 * @see http://php-and-symfony.matthiasnoback.nl/2012/01/symfony2-dynamically-add-routes/
 */

namespace Hanzo\Bundle\CMSBundle;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsI18nQuery;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CMSRouterLoader implements LoaderInterface
{
    private $loaded = false;
    private $locale;
    private $cache_dir;

    /**
     * setup required variables
     *
     * @param string $locale    active locale
     * @param string $cache_dir Path to filesystem cache
     */
    public function __construct($locale, $cache_dir)
    {
        $this->locale = $locale;
        $this->cache_dir = $cache_dir;
    }


    /**
     * load the routes
     *
     * @param  string $resource unused, but required
     * @param  string $type     unused, but required
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $categories = array();
        $routes = new RouteCollection();

        $pages = CmsI18nQuery::create()
            ->filterByIsActive(true)
            ->rightJoinWithCms()
            ->orderByPath()
            ->findByLocale($this->locale)
        ;

        foreach ($pages as $page) {
            $id = $page->getId();
            $locale = trim($page->getLocale());
            $locale_lower = strtolower($locale);
            $path = '{_locale}/'.trim($page->getPath());
            $type = trim($page->getCms()->getType());
            $title = trim($page->getTitle());
            $is_restricted = (int) $page->getIsRestricted();

            if (('' == $title) || isset($processed[$path])) {
                continue;
            }

            $processed[$path] = $path;

            $settings = trim($page->getSettings());
            if (substr($settings, 0, 2) == 'a:') {
                // serialized settings
                $settings = unserialize(stripslashes($settings));
            } else if(substr($settings, 0, 1) == '{') {
                // Json encoded settings
                $settings = json_decode($settings);
            }

            switch ($type) {
                case 'category':
                    //test ... we should never enter this if tho...
                    if (!$settings instanceof \stdClass || !isset($settings->category_id)) {
                        Tools::log($page->toArray());
                        continue;
                    }

                    $category_key = '_' . $locale_lower . '_' . $settings->category_id;
                    $category_path = 'category_' . $id . '_' . $locale_lower;
                    $product_path = 'product_' . $id . '_' . $locale_lower ;

                    $categories[$category_key] = $product_path;

                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", array(
                        '_controller' => 'ProductBundle:Default:view',
                        '_format' => 'html',
                        'cms_id' => $id,
                        'category_id' => $settings->category_id,
                        'title' => '',
                        'ip_restricted' => true,
                    ), array(
                        'product_id' => '\d+',
                        '_format' => 'html|json',
                    ));
                    $routes->add($product_path, $route);
                    // category route
                    $route = new Route("/{$path}/{show}/{pager}", array(
                        '_controller' => 'CategoryBundle:Default:view',
                        '_format' => 'html',
                        'cms_id' => $id,
                        'category_id' => $settings->category_id,
                        'pager' => 1,
                        'show' => 'overview',
                        'ip_restricted' => true,
                    ), array(
                        '_format' => 'html|json',
                    ));
                    $routes->add($category_path, $route);
                    break;

                case 'look':
                    //test ... we should never enter this if tho...
                    if (!$settings instanceof \stdClass || !isset($settings->category_id)) {
                        Tools::log('Missing category id on CMS #' . $page->getId());
                        continue;
                    }

                    $look_key = '_' . $locale_lower . '_' . $settings->category_id;
                    $look_path = 'look_' . $id . '_' . $locale_lower;
                    $product_path = 'product_' . $id . '_' . $locale_lower ;

                    $categories[$look_key] = $product_path;

                    // look route
                    $route = new Route("/{$path}/{pager}", array(
                        '_controller' => 'CategoryBundle:ByLook:view',
                        '_format' => 'html',
                        'cms_id' => $id,
                        'category_id' => $settings->category_id,
                        'pager' => 1,
                        'ip_restricted' => true,
                    ), array(
                        'pager' => '\d+',
                        '_format' => 'html|json',
                    ));
                    $routes->add($look_path, $route);

                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", array(
                        '_controller' => 'ProductBundle:Default:view',
                        '_format' => 'html',
                        'product_id' => 0,
                        'cms_id' => $id,
                        'look_id' => $settings->category_id,
                        'title' => '',
                        'ip_restricted' => true,
                    ), array(
                        'product_id' => '\d+',
                        '_format' => 'html|json',
                    ));
                    $routes->add($product_path, $route);
                    break;

                case 'page':
                    $route = new Route("/".$path, array(
                        '_controller' => 'CMSBundle:Default:view',
                        'id' => $id,
                        'ip_restricted' => $is_restricted,
                    ));
                    $routes->add('page_'.$id, $route);
                    break;

                case 'mannequin':
                    $route = new Route("/".$path, array(
                        '_controller' => 'MannequinBundle:Default:view',
                        'id' => $id,
                    ));
                    $routes->add('mannequin_'.$id, $route);
                    break;

                case 'bycolour':
                    $route = new Route("/{$path}/{show}", array(
                        '_controller' => 'CategoryBundle:ByColour:view',
                        'id' => $id,
                        'show' => 'overview',
                        'ip_restricted' => true,
                    ));
                    $routes->add('bycolour_'.$id . '_' . $locale_lower, $route);

                    $product_path = 'product_' . $id . '_' . $locale_lower ;
                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", array(
                        '_controller' => 'ProductBundle:Default:view',
                        '_format' => 'html',
                        'product_id' => 0,
                        'cms_id' => $id,
                        'title' => '',
                        'ip_restricted' => true,
                    ), array(
                        'product_id' => '\d+',
                        '_format' => 'html|json',
                    ));
                    $routes->add($product_path, $route);
                    break;

                case 'newsletter':
                    $route = new Route("/".$path, array(
                        '_controller' => 'NewsletterBundle:Default:view',
                        'id' => $id,
                    ));
                    $routes->add('newsletter_'.$id, $route);
                    break;

                case 'search':
                    if (!$settings instanceof \stdClass || !isset($settings->type)) {
                        continue;
                    }
                    $method = $settings->type;
                    $restricted = (($settings->type == 'category') ? 'true' : 'false');

                    $route = new Route("/".$path, array(
                        '_controller' => "SearchBundle:Default:{$method}",
                        '_format' => 'html',
                        'id' => $id,
                        'ip_restricted' => $restricted,
                    ), array(
                        '_format' => 'html|json'
                    ));
                    $routes->add($settings->type.'_search_'.$id, $route);
                    break;
            }
        }

        // cache the category map
        $category_map_out = '<?php # -:[' . date('Y-m-d H:i:s') . ']:-'."\nreturn ". var_export($categories, 1) . ";\n";
        file_put_contents($this->cache_dir.'/category_map.php', $category_map_out);

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
    public function getResolver(){}


    /**
     * {@inheritDoc}
     */
    public function setResolver(LoaderResolverInterface $resolver){}
}

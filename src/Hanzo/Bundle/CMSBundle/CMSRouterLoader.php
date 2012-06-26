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

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CMSRouterLoader implements LoaderInterface
{
    private $loaded = false;
    private $session;
    private $cache_dir;

    /**
     * setup required variables
     *
     * @param Session $session   The current session object
     * @param string  $cache_dir Path to filesystem cache
     */
    public function __construct(Session $session, $cache_dir)
    {
        $this->session = $session;
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
            ->rightJoinWithCms()
            ->orderByPath()
            ->findByLocale($this->session->getLocale())
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

            $settings = $page->getSettings();
            if (substr($settings, 0, 2) == 'a:') {
                // serialized settings
                $settings = unserialize(stripslashes($settings));
            } else if(substr($settings, 0, 1) == '{') {
                // Json encoded settings
                $settings = json_decode($settings);
            }

            switch ($type) {
                case 'category':

                    // test ... we should never enter this if tho...
                    if (!$settings instanceof \stdClass) {
                        Tools::log($page->toArray());
                        continue;
                    }

                    $category_key = '_' . $locale_lower . '_' . $settings->category_id;
                    $category_path = 'category_' . $id . '_' . $locale_lower;
                    $product_path = 'product_' . $id . '_' . $locale_lower ;

                    $categories[$category_key] = $product_path;

                    // category route
                    $route = new Route("/{$path}/{pager}", array(
                        '_controller' => 'HanzoCategoryBundle:Default:view',
                        '_format' => 'html',
                        'cms_id' => $id,
                        'category_id' => $settings->category_id,
                        'pager' => 1,
                        'ip_restricted' => true,
                    ), array(
                        'pager' => '\d+',
                        '_format' => 'html|json',
                    ));
                    $routes->add($category_path, $route);

                    // product route
                    $route = new Route("/{$path}/{product_id}/{title}", array(
                        '_controller' => 'HanzoProductBundle:Default:view',
                        '_format' => 'html',
                        'product_id' => 0,
                        'cms_id' => $id,
                        'category_id' => $settings->category_id,
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
                        '_controller' => 'HanzoCMSBundle:Default:view',
                        'id' => $id,
                        'ip_restricted' => $is_restricted,
                    ));
                    $routes->add('page_'.$id, $route);
                    break;

                case 'mannequin':
                    $route = new Route("/".$path, array(
                        '_controller' => 'HanzoMannequinBundle:Default:view',
                        'id' => $id,
                    ));
                    $routes->add('mannequin_'.$id, $route);
                    break;

                case 'newsletter':
                    $route = new Route("/".$path, array(
                        '_controller' => 'NewsletterBundle:Default:view',
                        'id' => $id,
                    ));
                    $routes->add('newsletter_'.$id, $route);
                    break;

                case 'search':
                    $method = $settings->type;
                    $restricted = (($settings->type == 'category') ? 'true' : 'false');

                    $route = new Route("/".$path, array(
                        '_controller' => "HanzoSearchBundle:Default:{$method}",
                        '_format' => 'html',
                        'id' => $id,
                        'ip_restricted' => $restricted,
                    ), array(
                        '_format' => 'html|json'
                    ));
                    $routes->add('newsletter_'.$id, $route);
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
    public function setResolver(LoaderResolver $resolver){}
}

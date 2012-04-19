<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsQuery;

use Predis\Client AS PredisClient;

class CacheService
{
    protected $redis;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->redis = $parameters[0];
        $this->settings = $settings;

        if (!$this->redis instanceof PredisClient) {
            throw new \InvalidArgumentException('Predis\Client expected as first parameter.');
        }
    }

    /**
     * Build router files and category maps from the cms tables.
     *
     * @return mixed array on success otherwise false.
     */
    public function routerBuilder()
    {
        $result = CmsQuery::create()
            ->joinCmsI18n(NULL, 'INNER JOIN')
            ->orderByParentId()
            ->useCmsI18nQuery()
              ->orderByLocale()
            ->endUse()
            ->find()
        ;

        $buffer = array();
        $counter = 1;
        $processed = array();
        $categories = array();
        foreach ($result as $record) {
            foreach ($record->getCmsI18ns() as $item){

                $id = $item->getId();
                $path = trim($item->getPath());
                $locale = strtolower(trim($item->getLocale()));
                $type = trim($record->getType());
                $title = trim($item->getTitle());
                $is_restricted = (int) $item->getIsRestricted();

                if ('' == $title) {
                    continue;
                }

                if (isset($processed[$path.'.'.$locale])) {
                    continue;
                }
                $processed[$path.'.'.$locale] = $path;

                if (!isset($buffer[$locale])) {
                    $buffer[$locale] = '';
                }

                $settings = $item->getSettings();
                if (substr($settings, 0, 2) == 'a:') {
                    $settings = unserialize(stripslashes($settings));
                }else if(substr($settings, 0, 1) == '{') { // Json encoded settings
                    $settings = json_decode($settings, true);
                }

                switch ($type) {
                    case 'category':

                        $category_key = '_' . $locale . '_' . $settings['category_id'];
                        $category_path = 'category_' . $id . '_' . $locale;
                        $product_path = 'product_' . $id . '_' . $locale ;

                        $categories[$category_key] = $product_path;

                        $buffer[$locale] .= trim("
{$category_path}:
    pattern: /{$path}/{pager}
    defaults:
        _controller: HanzoCategoryBundle:Default:view
        _format: html
        cms_id: {$id}
        category_id: {$settings['category_id']}
        pager: 1
        ip_restricted: true
    requirements:
        pager: \d+
        _format: html|json
{$product_path}:
    pattern: /{$path}/{product_id}/{title}
    defaults:
        _controller: HanzoProductBundle:Default:view
        _format: html
        product_id: 0
        cms_id: {$id}
        category_id: {$settings['category_id']}
        title: ''
        ip_restricted: true
    requirements:
        product_id: \d+
        _format: html|json
")."\n";
                        break;
                    case 'page':
                        $buffer[$locale] .= trim("
page_" . $id . "_" . $locale . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoCMSBundle:Default:view
        id: {$id}
        ip_restricted: {$is_restricted}
")."\n";
                        break;
                    case 'system':
                        switch ($settings['type']) {
                            case 'mannequin':
                            $buffer[$locale] .= trim("
system_" . $id . "_" . $locale . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoMannequinBundle:Default:view
        id: {$id}
")."\n";
                            break;
                        case 'newsletter':
                            $buffer[$locale] .= trim("
newsletter_" . $id . "_" . $locale . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoNewsletterBundle:Default:view
        id: {$id}
")."\n";
                            break;
                        case 'category_search':
                        case 'advanced_search':
                            $method = explode('_', $settings['type']);
                            $method = array_shift($method);
                            $restricted = (($settings['type'] == 'category_search') ? 'true' : 'false');
                            $buffer[$locale] .= trim("
search_" . $id . "_" . $locale . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoSearchBundle:Default:{$method}
        _format: html
        id: {$id}
        ip_restricted: {$restricted}
    requirements:
        _format: html|json
")."\n";
                            break;
                        default:
                            print_r($settings);
                    }
                    break;
                case 'url': // ignore
                    continue;
                    break;
                }

            $counter++;
            }
        }

        $cms_routing_out = '';
        $date = date('Y-m-d H:i:s');
        foreach ($buffer as $locale => $routers) {
            $cms_routing_out .= "# -:[{$locale} : {$date}]:-

" . trim($routers) . "

# --------------------------------------------
";
        }

        $cms_routing_file = __DIR__ . '/../../CMSBundle/Resources/config/cms_routing.yml';
        if (file_put_contents($cms_routing_file, $cms_routing_out)) {
            $category_map_out = '<?php # -:[' . $date . ']:-
return '. var_export($categories, 1) . "
;
    ";

            $category_map_file = __DIR__ . '/../../BasketBundle/Resources/config/category_map.php';
            if (file_put_contents($category_map_file, $category_map_out)) {
                return array(
                    $cms_routing_file,
                    $category_map_file
                );
            }
        }

        return false;
    }

    /**
     * clear symfony's file cache
     *
     * @return array of status messages from the different servers.
     */
    public function clearFileCache()
    {
        set_time_limit(0);

        // note, this is not optimal, but easy tho...
        // TODO populate list from the settings table
        $servers = array(
            $_SERVER['HTTP_HOST'],
        );

        $status = array();
        foreach ($servers as $server) {
            $status[$server] = file_get_contents('http://'.$server.'/cc.php?run=1');
        }

        return $status;
    }

    /**
     * This will clear the redis cache.
     *
     * @return boolean predis command state.
     */
    public function clearRedisCache()
    {
        return (bool) $this->redis->flushdb();
    }
}

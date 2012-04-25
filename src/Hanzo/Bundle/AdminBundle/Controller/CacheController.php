<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

class CacheController extends CoreController
{
    
    public function clearAction($js_css = FALSE, $router = FALSE, $redis = FALSE, $file = FALSE)
    {
        $cache = $this->get('cache_manager');

        if($js_css)
        {
        	// TODO do something with assetic. (Or does it do it by itself?)
        }

        if($router)
        {
        	$cache->routerBuilder();
        }

        if($redis){
        	$cache->clearRedisCache();
        }

        if($file){
        	$cache->clearFileCache();
		}

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('cache.cleared', array(), 'admin'),
            ));
        }
    }
}

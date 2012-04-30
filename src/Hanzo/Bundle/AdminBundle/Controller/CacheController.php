<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Bundle\DataIOBundle\Events,
    Hanzo\Bundle\DataIOBundle\FilterUpdateEvent;

class CacheController extends CoreController
{
    
    public function clearAction($jscss = FALSE, $router = FALSE, $redis = FALSE, $file = FALSE)
    {

        $cache = $this->get('cache_manager');

        if($jscss)
        {
            try{
                $event = new FilterUpdateEvent();
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(Events::incrementAssetsVersion, $event);
            }catch(Exception $e){
                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => FALSE,
                        'message' => $this->get('translator')->trans('cache.clear.failed.' . $e, array(), 'admin'),
                    ));
                }

                return $this->response($e);
            }
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

        return $this->response($this->get('translator')->trans('cache.cleared', array(), 'admin'));
    }
}

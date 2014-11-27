<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Bundle\DataIOBundle\Events;
use Hanzo\Bundle\DataIOBundle\FilterUpdateEvent;
use Hanzo\Core\CoreController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CacheController
 *
 * @package Hanzo\Bundle\AdminBundle
 */
class CacheController extends CoreController
{
    /**
     * @param bool $jscss
     * @param bool $router
     * @param bool $redis
     * @param bool $file
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearAction($jscss = false, $router = false, $redis = false, $file = false)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $cache = $this->get('cache_manager');

        if ($jscss) {
            try {
                $event = new FilterUpdateEvent();
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(Events::incrementAssetsVersion, $event);
            } catch (Exception $e) {
                if ($this->getFormat() == 'json') {
                    return $this->json_response([
                        'status'  => false,
                        'message' => $this->get('translator')->trans('cache.clear.failed.' . $e, [], 'admin'),
                    ]);
                }

                return $this->response($e);
            }
        }

        if ($router) {
            $cache->routerBuilder();
        }

        if ($redis) {
            $cache->clearRedisCache();
        }

        if ($file) {
            $cache->clearFileCache();
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->get('translator')->trans('cache.cleared', [], 'admin'),
            ]);
        }

        return $this->response($this->get('translator')->trans('cache.cleared', [], 'admin'));
    }
}

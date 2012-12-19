<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;

class MenuController extends CoreController
{
    protected $locale = NULL;
    protected $trail = array();
    protected $path = NULL;

    protected $cms_thread = 20;
    protected $menu = array();

    public function menuAction($type, $thread = NULL, $from = NULL, $offset = NULL)
    {
        $request = $this->get('request');

        $cache_id = func_get_args();
        array_unshift($cache_id, 'menu');
        array_push($cache_id, $request->getPathInfo());

        $html = $this->getCache($cache_id);
        /**


        **/
        $html = NULL; // FIXME: redis overwrite
        if (!$html) {
            $hanzo = Hanzo::getInstance();

            $this->locale = $hanzo->get('core.locale');
            $this->base_url = $request->getBaseUrl();

            if ($thread) {
                $this->cms_thread = $thread;
            } else {
                $this->cms_thread = $hanzo->get('core.main_menu_thread');
            }

            if (empty($this->path)) {
                $this->path = str_replace($this->locale, '', $request->getPathInfo());

                // NICETO this could be done better, but how ?
                if (preg_match('~(?:/[0-9]+/?([a-z0-9\-]+)?)~', $this->path, $matches)) {
                    $this->path = str_replace($matches[0], '', $this->path);
                }
                // home does not have a trail.
                if ($this->path != '/') {
                    $this->generateTrail();
                }
            }

            // now we have a trail to the current page, lets generate the requested menu.

            $html = '';
            switch($type) {
                case 'main':
                    if (empty($this->menu['main'])) {
                        $this->menu['main'] = '';
                        // generate
                        $this->generateFull(null, $type, $this->cms_thread);
                    }
                    $html = $this->menu['main'];
                    break;

                case 'sub':
                    if (empty($this->menu[$type]) && $offset) {
                        $this->menu[$type] = '';

                        // generate
                        if ($from && !isset($this->trail[$from])) {
                            break;
                        }
                        $this->generateFlat($offset, $type);
                    }
                    $html = $this->menu[$type];
                    break;

                case 'breadcrumb':
                    if (empty($this->menu[$type])) {
                        $this->menu[$type] = '';

                        $this->generateBreadcrumb();
                    }
                    $html = $this->menu[$type];
                    break;
                default:
                    if (empty($this->menu[$type])) {
                        $this->menu[$type] = '';

                        $this->generateFull($offset, $type);
                    }
                    $html = $this->menu[$type];
                    break;
            }

            $this->setCache($cache_id, $html);
        }

        return new Response(($html ?: ''), 200);
    }


    protected function generateTrail($parent_id = NULL)
    {
        if (empty($parent_id)) {
            $item = CmsQuery::create()
                ->filterByCmsThreadId($this->cms_thread)
                ->useCmsI18nQuery('i18n')
                    ->filterByLocale($this->locale)
                    ->filterByPath(trim($this->path, '/'))
                ->endUse()
                ->findOne()
            ;
        }
        else {
            $item = CmsQuery::create()
                ->findOneById($parent_id)
            ;
        }

        if ($item instanceof Cms) {
            $this->trail[$item->getId()] = $item;

            if ($item->getParentId()) {
                $this->generateTrail($item->getParentId());
            }
        }

    }


    protected function generateTree($parent_id = NULL, $type = 'main')
    {
        $query = CmsQuery::create()
            ->joinWithI18n($this->locale)
            ->filterByCmsThreadId($this->cms_thread)
            ->filterByIsActive(TRUE)
            ->orderBySort()
        ;

        if (empty($parent_id)) {
            $query->filterByParentId(NULL, \Criteria::ISNULL);
        }
        else {
            $query->filterByParentId($parent_id);
        }

        $result = $query->find();

        if ($result->count()) {
            $this->menu[$type] .= '<ul>';

            foreach($result as $record) {

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                if ($record->getTitle()) {
                    $class = 'inactive';
                    if ((isset($this->trail[$record->getId()])) ||
                        ($path == $this->path)
                    ) {
                        $class = 'active';
                    }

                    if (in_array($record->getType(), array('page', 'url'))) {
                        $params = $record->getSettings(null, false);

                        if (isset($params->class)) {
                            $class .= ' ' . $params->class;
                        } elseif (isset($params->is_frontpage)) {
                            if ($this->path == '/') {
                                $class = 'active';
                            }
                            $path = '';
                        }
                    }

                    if (preg_match('~^(f|ht)tps?://~', $path)) {
                        $uri = $path;
                    } else {
                        $uri = $this->base_url . '/' . $this->locale . '/' . $path;
                    }

                    $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $uri . '" class="page-'.$record->getId().'">' . $record->getTitle() . '</a>';

                    if (isset($this->trail[$record->getId()])) {
                        $this->generateTree($record->getId(), $type);
                    }

                    $this->menu[$type] .= '</li>';
                }
            }

            $this->menu[$type] .= '</ul>';
        }
    }


    protected function generateFlat($parent_id, $type)
    {
        $result = CmsQuery::create()
            ->joinWithI18n($this->locale)
            ->filterByCmsThreadId($this->cms_thread)
            ->filterByIsActive(TRUE)
            ->orderBySort()
            ->filterByParentId($parent_id)
            ->find()
        ;

        if ($result->count()) {
            $this->menu[$type] .= '<nav class="'.$type.'"><ul>';

            foreach($result as $record) {

                $class = 'inactive';
                if ((isset($this->trail[$record->getId()])) ||
                    ($record->getPath() == $this->path)
                ) {
                    $class = 'active';
                }
                if (in_array($record->getType(), array('page', 'url'))) {
                    $params = $record->getSettings(null, false);
                    if ($params && isset($params->class)) {
                        $class .= ' ' . $params->class;
                    }
                }

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $this->base_url . '/' . $this->locale . '/' . $path . '">' . $record->getTitle() . '</a></li>';
            }

            $this->menu[$type] .= '</ul></nav>';
        }
    }

    protected function generateFull($parent_id = NULL, $type, $from = NULL)
    {
        $query = CmsQuery::create()
            ->joinWithI18n($this->locale)
            ->filterByIsActive(TRUE)
            ->orderBySort()
            ->filterByParentId($parent_id)
        ;
        if(!empty($from)){
            $query->filterByCmsThreadId($from);
        }
        $result = $query->find();
        if ($result->count()) {
            $this->menu[$type] .= '<ul>';

            foreach($result as $record) {

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                if ($record->getTitle()) {
                    $class = 'inactive';
                    if ((isset($this->trail[$record->getId()])) ||
                        ($path == $this->path)
                    ) {
                        $class = 'active';
                    }

                    if (in_array($record->getType(), array('page', 'url'))) {
                        $params = $record->getSettings(null, false);

                        if (isset($params->class)) {
                            $class .= ' ' . $params->class;
                        } elseif (isset($params->is_frontpage)) {
                            if ($this->path == '/') {
                                $class = 'active';
                            }
                            $path = '';
                        }
                    }

                    if (preg_match('~^(f|ht)tps?://~', $path)) {
                        $uri = $path;
                    } else {
                        $uri = $this->base_url . '/' . $this->locale . '/' . $path;
                    }

                    if($record->getType() !== 'heading'){
                        $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $uri . '" class="page-'.$record->getId().' '.$record->getType().'">' . $record->getTitle() . '</a>';
                    }else{
                        $this->menu[$type] .= '<li class="' . $class . ' heading"><span>' . $record->getTitle() . '</span>';
                    }
                    $this->generateFull($record->getId(), $type);

                    $this->menu[$type] .= '</li>';
                }
            }

            $this->menu[$type] .= '</ul>';
        }
    }

    protected function generateBreadcrumb($type = 'breadcrumb')
    {
        if($count = count($this->trail)){
            $this->trail = array_reverse($this->trail);
            $this->menu[$type] .= '<ul class="breadcrumb">';

            foreach ($this->trail as $record) {

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                if ($record->getTitle()) {
                    $class = 'inactive';
                    if ((isset($this->trail[$record->getId()])) ||
                        ($path == $this->path)
                    ) {
                        $class = 'active';
                    }
                }
                
                $class .= ($record === reset($this->trail))?' first':'';
                $class .= ($record === end($this->trail))?' last':'';

                if (preg_match('~^(f|ht)tps?://~', $path)) {
                    $uri = $path;
                } else {
                    $uri = $this->base_url . '/' . $this->locale . '/' . $path;
                }

                if($record->getType() !== 'heading'){
                    $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $uri . '" class="page-'.$record->getId().' '.$record->getType().'">' . $record->getTitle() . '</a></li>';    
                }
                
            }

            $this->menu[$type] .= '</ul>';
        }
    }
}

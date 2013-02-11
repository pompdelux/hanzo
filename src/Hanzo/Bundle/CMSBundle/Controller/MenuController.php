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

    public function menuAction($type, $thread = NULL, $from = NULL, $offset = NULL, $css_id = null, $include_self = null)
    {
        $request = $this->get('request');

        $cache_id = [
            'menu',
            $type,
            $request->getRequestUri()
        ];
        $html = $this->getCache($cache_id);

        if (!$html) {
            $hanzo = Hanzo::getInstance();

            $this->locale = $hanzo->get('core.locale');
            $this->base_url = $request->getBaseUrl();

            if ($thread) {
                $this->cms_thread = $thread;
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
                        $this->generateFull(null, $type, $this->cms_thread, $css_id);
                    }
                    $html = $this->menu['main'];
                    break;

                case 'sub':
                    if (empty($this->menu[$type])) {
                        $this->menu[$type] = '';

                        // generate
                        if ($from && !isset($this->trail[$from])) {
                            break;
                        }
                        $this->generateFlat($offset, $type, $include_self);
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

                        $this->generateFull($offset, $type, null, $css_id);
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
    /**
     * Find the top node in the active trail. Doing it this way saves a call to db.
     *
     * @return int top nodes id
     */
    protected function getTopIdFromTrail()
    {
        foreach ($this->trail as $top) {
            if(!$top->getParentId())
                return $top->getId();
        }
        return null;
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

                    if($record->isFirst()){
                        $class .= ' first';
                    }

                    if($result->isLast()){
                        $class .= ' last';
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

    // Used for left menu, in new design
    /**
     * Generates a menu with sub levels.
     *
     * @param int       $parent_id     The node to start from, if null the id of top is used
     * @param string    $type          the name of the menu
     * @param boolean   $include_self  If parent is set, set this to include the parent into the menu
     *
     * @return void
     */
    protected function generateFlat($parent_id = null, $type, $include_self = false)
    {
        $query = CmsQuery::create()
            ->joinWithI18n($this->locale)
            ->filterByCmsThreadId($this->cms_thread)
            ->filterByIsActive(TRUE)
            ->orderBySort()
        ;

        if ($include_self) {
            if ($parent_id) {
                $query = $query->filterById($parent_id);
            } else {
                $query = $query->filterById($this->getTopIdFromTrail());
            }
        } else {
            if ($parent_id) {
                $query = $query->filterByParentId($parent_id);
            } else {
                $query = $query->filterByParentId($this->getTopIdFromTrail());
            }
        }

        $result = $query->find();
        if ($result->count()) {
            $ul = '<ul class="'.$type.'">';
            $this->menu[$type] .= $ul;

            foreach($result as $record) {

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                if ($record->getTitle()) {
                    $class = 'inactive';
                    if($path == trim($this->path, '/')) {
                        $class = 'active';
                    }elseif ((isset($this->trail[$record->getId()]))){
                        $class = 'active-trail';
                    }

                    if($result->isFirst()){
                        $class .= ' first';
                    }

                    if($result->isLast()){
                        $class .= ' last';
                    }

                    if($include_self && $record->getId() === $parent_id){
                        $class .= ' self-included';
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

                    if($record->getType() === 'heading'){
                        $uri = '#';
                        $class .= ' heading';
                    }elseif (preg_match('~^(f|ht)tps?://~', $path)) {
                        $uri = $path;
                    } else {
                        $uri = $this->base_url . '/' . $this->locale . '/' . $path;
                    }

                    $this->menu[$type] .= '<li class="' . $class . ' item"><a href="'. $uri . '" class="page-'.$record->getId().' '.$record->getType().'">' . $record->getTitle() . '</a>';

                    $this->generateFlat($record->getId(), $type, false);

                    $this->menu[$type] .= '</li>';
                }
            }

            $this->menu[$type] .= '</ul>';
        }
    }

    protected function generateFull($parent_id = NULL, $type, $from = NULL, $css_id = null)
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

            $ul = '<ul>';
            if ($css_id) {
                $ul = '<ul id="'.$css_id.'">';
            }
            $this->menu[$type] .= $ul;

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

                    if($result->isFirst()){
                        $class .= ' first';
                    }

                    if($result->isLast()){
                        $class .= ' last';
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

                    if($record->getType() === 'heading'){
                        $uri = '#';
                        $class .= ' heading';
                    }elseif (preg_match('~^(f|ht)tps?://~', $path)) {
                        $uri = $path;
                    } else {
                        $uri = $this->base_url . '/' . $this->locale . '/' . $path;
                    }

                    $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $uri . '" class="page-'.$record->getId().' '.$record->getType().'">' . $record->getTitle() . '</a>';

                    $this->generateFull($record->getId(), $type);

                    $this->menu[$type] .= '</li>';
                }
            }

            $this->menu[$type] .= '</ul>';
        }
    }

    protected function generateBreadcrumb($type = 'breadcrumb')
    {
        if($count = count($this->trail) > 1){
            $this->trail = array_reverse($this->trail);
            $this->menu[$type] .= '<ul class="breadcrumb">';

            foreach ($this->trail as $record) {
                $class = '';
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
                if($record === reset($this->trail)){ // First
                    $class .= ' first';
                }elseif($record === end($this->trail)){ // Last
                    $class .= ' last';
                }

                if($record->getType() === 'heading'){
                    $uri = '#';
                    $class .= ' heading';
                }elseif (preg_match('~^(f|ht)tps?://~', $path)) {
                    $uri = $path;
                } else {
                    $uri = $this->base_url . '/' . $this->locale . '/' . $path;
                }

                $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $uri . '" class="page-'.$record->getId().' '.$record->getType().'">' . $record->getTitle() . '<i class="sprite arrow-right"></i></a></li>';

            }

            $this->menu[$type] .= '</ul>';
        }
    }
}

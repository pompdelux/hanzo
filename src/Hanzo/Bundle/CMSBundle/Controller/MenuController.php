<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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

    protected $device = 'pc';

    public function menuAction($type, $thread = NULL, $from = NULL, $offset = NULL, $css_id = null, $include_self = null)
    {
        $request = $this->get('request');
        $this->device = $request->attributes->get('_x_device');

        // note, due to the fact that we cannot in sf 2.3.x get the master request, we hack a little.
        $request_url = explode('?', $_SERVER['REQUEST_URI']);
        $request_url = array_shift($request_url);

        $stripped_uri = explode($request->getLocale().'/', $request_url);
        $stripped_uri = array_pop($stripped_uri);

        $cache_id = $this->getCacheId($request, 'menu', $type, $stripped_uri);
        $html     = $this->getCache($cache_id);

        if (!$html) {
            $hanzo = Hanzo::getInstance();

            $this->locale = $hanzo->get('core.locale');
            $this->base_url = $request->getBaseUrl();
            if ($thread) {
                $this->cms_thread = $thread;
            }

            $generate_trail = false;
            if (empty($this->path)) {
                $this->path = $stripped_uri;

                // NICETO: this could be done better, but how ?
                if (preg_match('~(?:/(?:overview|look|[0-9]+)/?([a-z0-9\-]+)?)~', $this->path, $matches)) {
                    $this->path = str_replace($matches[0], '', $this->path);
                }

                $generate_trail = true;
            }

            // never ovreride the main thread, it will break stuff...
            if ($type != 'main') {
                $this->cms_thread = CmsI18nQuery::create()
                    ->join('Cms')
                    ->select('Cms.CmsThreadId')
                    ->filterByPath($this->path)
                    ->findOne()
                ;
            }

            if ($generate_trail) {
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
            if (!$top->getParentId()) {
                return $top->getId();
            }
        }
        return null;
    }


    protected function generateTree($parent_id = NULL, $type = 'main')
    {
        if ('pc' == $this->device) {
            $query = CmsQuery::create()
                ->joinWithI18n($this->locale)
                ->filterByCmsThreadId($this->cms_thread)
                ->orderBySort()
            ;

            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByOnlyMobile(false)->filterByIsActive(true)->endUse();
            }
        } else {
            $query = CmsQuery::create()
                ->useCmsI18nQuery()
                    ->filterByOnMobile(true)
                ->endUse()
                ->joinWithI18n($this->locale)
                ->filterByCmsThreadId($this->cms_thread)
                ->orderBySort()
            ;

            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByIsActive(true)->endUse();
            }
        }

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
        if ('pc' == $this->device) {
            $query = CmsQuery::create()
                ->joinWithI18n($this->locale)
                ->filterByCmsThreadId($this->cms_thread)
                ->orderBySort()
            ;

            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByOnlyMobile(false)->filterByIsActive(true)->endUse();
            }
        } else {
            $query = CmsQuery::create()
                ->useCmsI18nQuery()
                    ->filterByOnMobile(true)
                ->endUse()
                ->joinWithI18n($this->locale)
                ->filterByCmsThreadId($this->cms_thread)
                ->orderBySort()
            ;

            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByIsActive(true)->endUse();
            }
        }

        if (!$parent_id) {
            $parent_id = $this->getTopIdFromTrail();
        }

        if ($include_self) {
            $query = $query->filterById($parent_id);
        } else {
            $query = $query->filterByParentId($parent_id);
        }

        $result = $query->find();
        if ($result->count()) {
            $ul = '<ul class="'.$type.'">';
            $this->menu[$type] .= $ul;

            foreach($result as $record) {
                $path = $record->getPath();

                // Only for type URL
                if ($record->getType() == 'url') {

                    // If URL is a absolute URL (containing http://, not http://www only, since our local environments, doesnt nescessarily contain www)
                    if ( strpos($path, 'http://') !== false ) {

                        // Split path - remove locale (da_DK)
                        $path = substr(parse_url($path, PHP_URL_PATH), 7);
                    }
                }

                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                if ($record->getTitle()) {
                    $class = 'inactive';

                    // Made the last if,
                    // for fixing absolute URLs by URL CMS types.
                    // Since there was no trail (upwards) and the URL (absolute) doesnt match the pattern of $this->path (ex. pige)
                    // since it contains full path (http://www.***.xx/pige)
                    if ((count($this->trail) === 1) && ($path == trim($this->path, '/'))) {
                        $class = 'active-trail';
                    }elseif ($path == trim($this->path, '/')) {
                        $class = 'active';
                    }elseif ((isset($this->trail[$record->getId()]))){
                        $class = 'active-trail';
                    }

                    if ($result->isFirst()){
                        $class .= ' first';
                    }

                    if ($result->isLast()){
                        $class .= ' last';
                    }

                    if ($include_self && $record->getId() === $parent_id){
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
        static $is_first = true;

        if ('pc' == $this->device) {
            $query = CmsQuery::create()
                ->joinWithI18n($this->locale)
                ->orderBySort()
                ->filterByParentId($parent_id)
            ;

            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByOnlyMobile(false)->filterByIsActive(true)->endUse();
            }
        } else {
            $query = CmsQuery::create()
                ->useCmsI18nQuery()
                    ->filterByOnMobile(true)
                ->endUse()
                ->joinWithI18n($this->locale)
                ->orderBySort()
                ->filterByParentId($parent_id)
            ;

            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByIsActive(true)->endUse();
            }
        }

        if(!empty($from)){
            $query->filterByCmsThreadId($from);
        }

        $result = $query->find();

        if ($result->count()) {

            $css_class = '';
            if ($is_first) {
                $is_first = false;
                $css_class = ' class="outer"';
            }

            $ul = '<ul'.$css_class.'>';
            if ($css_id) {
                $ul = '<ul id="'.$css_id.'"'.$css_class.'>';
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


    /**
     * Build and return full tree fetched by thread title.
     *
     * @param  string $title
     * @param  integer $parent_id
     * @return Response
     */
    public function byTitleAction($title, $parent_id = null)
    {
        $request      = $this->get('request');
        $this->device = $request->attributes->get('_x_device');
        $uri          = $request->getRequestUri();

        $cache_id = $this->getCacheId($request, 'menu', $title, $uri);
        $html     = $this->getCache($cache_id);

        if (empty($html)) {
            $html = $this->byTitleBuilder($title);
        }

        $this->setCache($cache_id, $html);

        return new Response($html);
    }

    /**
     * Builder function used by the "byTitleAction" method.
     *
     * @todo replace generateFull with this method
     *
     * @param  string $title     [description]
     * @param  mixed $parent_id [description]
     *
     * @return string            [description]
     */
    protected function byTitleBuilder($title, $parent_id = null)
    {
        static $current_uri;
        static $locale;
        static $menu = '';

        if (!$locale) {
            $request     = $this->get('request');
            $current_uri = $request->getPathInfo();
            $locale      = $request->getLocale();
        }

        $query = CmsQuery::create()
            ->useCmsThreadQuery()
                ->useCmsThreadI18nQuery()
                    ->filterByTitle($title)
                ->endUse()
            ->endUse()
            ->joinWithI18n($locale)
            ->orderBySort()
            ->filterByParentId($parent_id)
            ->groupById()
        ;

        if ('pc' == $this->device) {
            if ($this->getRequest()->attributes->get('admin_enabled')) {
                $query->useCmsI18nQuery()->filterByIsActive(true)->_or()->filterByIsRestricted(true)->endUse();
            } else {
                $query->useCmsI18nQuery()->filterByOnlyMobile(false)->filterByIsActive(true)->endUse();
            }
        } else {
            $query->useCmsI18nQuery()
                ->filterByOnMobile(true)
                ->filterByIsActive(true)
                ->endUse()
                ;
        }

        $result = $query->find();

        if ($result->count()) {
            $menu .= '<ul class="'.($menu ? 'inner' : 'outer '.Tools::stripText($title)).'">';
            foreach ($result as $record) {

                if ($record->getTitle()) {
                    $class = '';
                    $path  = $record->getPath();

                    if ('frontpage' == $record->getType()) {
                        $path = '';
                    }

                    if ($result->isFirst()){
                        $class .= ' first';
                    } elseif ($result->isLast()){
                        $class .= ' last';
                    }

                    if ('heading' == $record->getType()) {
                        $uri = '';
                        $class .= ' heading';
                    } elseif (preg_match('~^(f|ht)tps?://~', $path)) {
                        $uri = $path;
                    } else {
                        $uri =  '/' . $locale . '/' . $path;
                    }

                    if ($current_uri == $uri) {
                        $class .= ' active';
                    } else {
                        $class .= ' inactive';
                    }

                    $menu .= '<li class="' . $class . '"><a href="'. $uri . '" class="page-'.$record->getId().' '.$record->getType().'">' . $record->getTitle() . '</a>';
                    $this->byTitleBuilder($title, $record->getId());
                    $menu .= '</li>';
                }
            }
            $menu .= '</ul>';
        }

        return $menu;
    }

    /**
     * Build Cache id the same way in different functions
     * @param Request $request
     */
    protected function getCacheId(Request $request, $type, $subtype, $uri)
    {
        $cache_id = [
            (int) $request->attributes->get('admin_enabled'),
            $type,
            $subtype,
            $uri,
            $this->device
        ];

        return $cache_id;
    }
}

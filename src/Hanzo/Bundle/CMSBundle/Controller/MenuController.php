<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\CoreController;

use Hanzo\Model\Cms,
    Hanzo\Model\CmsPeer,
    Hanzo\Model\CmsQuery,
    Hanzo\Model\CmsI18n,
    Hanzo\Model\CmsI18nQuery;

class MenuController extends CoreController
{
    protected $locale = NULL;
    protected $trail = array();
    protected $path = NULL;

    protected $cms_thread = 20;

    protected $menu = array();

    public function menuAction($type, $thread = NULL, $from = NULL, $offset = NULL)
    {
        $this->locale = $this->get('hanzo')->get('core.locale');

        $request = $this->get('request');
        $this->base_url = $request->getBaseUrl();


        if ($thread) {
            $this->cms_thread = $this->get('hanzo')->get('core.main_menu_thread');
        }

        if (empty($this->path)) {
            $this->path = $request->getPathInfo();

            // TODO this could be done better, but how ?
            if (preg_match('~(?:/[0-9]+/?([a-z0-9\-]+)?)~', $this->path, $matches)) {
                $this->path = str_replace($matches[0], '', $this->path);
            }

            // home does not have a trail.
            if ($this->path != '/') {
                $this->generateTrail();
            }
        }

        // now we have a trail to the current page, lets generate the requested menu.

        $return = '';
        switch($type) {
            case 'main':
                if (empty($this->menu['main'])) {
                    $this->menu['main'] = '';
                    // generate
                    $this->generateTree();
                }
                $return = $this->menu['main'];
                break;

            case 'sub':
                if (empty($this->menu['sub']) && $offset) {
                    $this->menu['sub'] = '';
                    // generate
                    if ($from && !isset($this->trail[$from])) {
                        break;
                    }
                    $this->generateFlat($offset, 'sub');
                }
                $return = $this->menu['sub'];
                break;
        }

        return new Response(($return ?: ''), 200);
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
        // TODO: allow offline views from selected ip addresses/users
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

                $class = 'inactive';
                if ((isset($this->trail[$record->getId()])) ||
                    ($record->getPath() == $this->path)
                ) {
                    $class = 'active';
                }
                if (in_array($record->getType(), array('page', 'url'))) {
                    $params = $record->getSettings('params');
                    if (is_array($params) && isset($params['class'])) {
                        $class .= ' ' . $params['class'];
                    }
                }

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $this->base_url . '/' . $path . '" rel="'.$record->getId().'">' . $record->getTitle() . '</a>';

                if (isset($this->trail[$record->getId()])) {
                    $this->generateTree($record->getId(), $type);
                }

                $this->menu[$type] .= '</li>';
            }

            $this->menu[$type] .= '</ul>';
        }
    }


    protected function generateFlat($parent_id, $type)
    {
        // TODO: allow offline views from selected ip addresses/users
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
                    $params = $record->getSettings('params');
                    if (is_array($params) && isset($params['class'])) {
                        $class .= ' ' . $params['class'];
                    }
                }

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = '';
                }

                $this->menu[$type] .= '<li class="' . $class . '"><a href="'. $this->base_url . '/' . $path . '">' . $record->getTitle() . '</a></li>';
            }

            $this->menu[$type] .= '</ul></nav>';
        }
    }

}

<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hanzo\Core\CoreController;
use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        $page = CmsPeer::getFrontpage($this->get('hanzo')->get('core.locale'));

        return $this->forward('HanzoCMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

    public function viewAction($id, Cms $page = NULL)
    {
        $locale = $this->get('hanzo')->get('core.locale');

        // handle forwards from frontpager
        if ($page instanceof Cms) {
            $cache_id = array('cms', $locale, 'frontpage');
        }
        else {
            $cache_id = array('cms', $locale, $id);
        }

        // if ($cache = $this->getCache($cache_id)) {
        //     return $this->response($cache);
        // }

        if ($page instanceof Cms) {
            $type = $page->getType();
        }
        else {
            $page = CmsPeer::getByPK($id, $locale);
            $type = 'pages';
            if (is_null($page)) {
                // TODO: 404 handeling...
            }
        }

        $this->get('twig')->addGlobal('page_type', $type);
        $html = $this->renderView('HanzoCMSBundle:Default:view.html.twig', array('page' => $page));
        $this->setCache($cache_id, $html);

        return $this->response($html);
    }
}
?>

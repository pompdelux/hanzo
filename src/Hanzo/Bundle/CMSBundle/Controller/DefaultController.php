<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

class DefaultController extends CoreController
{

    public function indexAction() {
        return $this->forward('HanzoCMSBundle:Default:view', array(
            'id'  => 1,
            'locale' => $this->get('session')->getLocale()
        ));
    }

    public function viewAction($id, $locale)
    {
        //$this->get('session')->getLocale();

        $cache = FALSE; //$this->cache->get(__METHOD__, $id);
        if ($cache) {
            return $cache;
        }


        $page = CmsPeer::getByPK($id, $locale);
        if (is_null($page)) {
            // TODO: 404 handeling...
        }

        return $this->render('HanzoCMSBundle:Default:view.html.twig', array('params' => $page));
    }
}

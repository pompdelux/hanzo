<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

class DefaultController extends Controller
{
    public function viewAction($id, $locale)
    {

        $this->get('session')->getLocale();

        $page = CmsPeer::getByPK($id, $locale);
        if (is_null($page)) {
            // TODO: 404 handeling...
        }

        return $this->render('HanzoCMSBundle:Default:view.html.twig', array('params' => $page));
    }
}

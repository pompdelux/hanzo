<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hanzo\Core\CoreController;

class HelperController extends CoreController
{
    public function menuAction($type)
    {
        switch ($type) {
            case 'main_nav':
                break;
            case 'sub_nav':
                break;
        }
        return $this->render('CMSBundle:Helper:' . $type . '.html.twig');
    }
}

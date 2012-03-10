<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SettingsController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }

    public function domainAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }
}

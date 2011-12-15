<?php

namespace Hanzo\Bundle\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('HanzoSearchBundle:Default:index.html.twig', array('name' => $name));
    }
}

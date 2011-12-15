<?php

namespace Hanzo\Bundle\MannequinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('HanzoMannequinBundle:Default:index.html.twig', array('name' => $name));
    }
}

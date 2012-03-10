<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CategoryController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:default.html.twig');
    }

}

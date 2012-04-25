<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DataIOBundle:Default:index.html.twig', array('name' => $name));
    }
}

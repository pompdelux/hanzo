<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('NewsletterBundle:Default:index.html.twig', array('page_type' => 'newsletter'));
    }

    /**
     * testAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function testAction()
    {
        return $this->render('NewsletterBundle:Default:test.html.twig',array('page_type' => 'newsletter'));
    }
}

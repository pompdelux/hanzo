<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController,
    Hanzo\Model\Customers,
    Hanzo\Model\CustomersPeer
    ;

class DefaultController extends CoreController
{
    public function indexAction()
    {
        return $this->render('NewsletterBundle:Default:index.html.twig', array(
            'page_type' => 'newsletter'
        ));
    }

    public function blockAction()
    {
        $customer = CustomersPeer::getCurrent();
        return $this->render('NewsletterBundle:Default:block.html.twig', array( 'customer' => $customer ));
    }
}

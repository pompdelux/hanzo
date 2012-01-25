<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\CoreController
    ;

class DefaultController extends Controller
{
    /**
     * blockAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        return $this->render('ShippingBundle:Default:block.html.twig');
    }

    public function indexAction($name)
    {
        return $this->render('ShippingBundle:Default:index.html.twig', array('name' => $name));
    }
}

<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ShippingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\CoreController,
    Hanzo\Model\ShippingMethods
    ;

class DefaultController extends CoreController
{
    /**
     * blockAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $api = $this->get('shipping.shippingapi');

        $methods = $api->getMethods();

        return $this->render('ShippingBundle:Default:block.html.twig', array( 'methods' => $methods ));
    }

    public function indexAction($name)
    {
        return $this->render('ShippingBundle:Default:index.html.twig', array('name' => $name));
    }
}

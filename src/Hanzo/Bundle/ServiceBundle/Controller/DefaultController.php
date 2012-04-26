<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\ZipToCity;
use Hanzo\Model\ZipToCityPeer;
use Hanzo\Model\ZipToCityQuery;

class DefaultController extends CoreController
{

    public function indexAction($name)
    {
        return $this->render('ServiceBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * getCityFromZipAction
     *
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getCityFromZipAction($zip_code)
    {
        $code = explode('.', $_SERVER['HTTP_HOST']);
        $code = array_pop($code);

        $query = ZipToCityQuery::create()
            ->filterByCountriesIso2($code)
            ->filterByZip( $zip_code )
            ->findOne();

        if ( !($query instanceOf ZipToCity) )
        {
            return $this->json_response(array(
                'status' => false,
                'message' => '',
            ));
        }

        return $this->json_response(array(
            'status' => true,
            'message' => '',
            'data' => array('city' => $query->getCity())
        ));
    }


    public function testAction()
    {
        // $cleanup = $this->get('cleanup_manager');
        // $cleanup->failedPaymentOrders();
        // $sms = $this->get('sms_manager');
        // $sms->eventReminder();
        $ax = $this->get('ax_manager');
        //$result = $ax->sendDebtor(\Hanzo\Model\CustomersQuery::create()->findPk(129798), true);
        $result = $ax->sendOrder(\Hanzo\Model\OrdersQuery::create()->findPk(569178), true);

Tools::log($result);

        return $this->render('ServiceBundle:Default:test.html.twig', array(
            'page_type' => 'test',
        ));
    }
}

<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Core\Tools
    ;

use Hanzo\Model\ZipToCity,
    Hanzo\Model\ZipToCityPeer,
    Hanzo\Model\ZipToCityQuery
    ;

class DefaultController extends CoreController
{

    public function indexAction($name)
    {
        return $this->render('ServiceBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * getCityFromZipAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getCityFromZipAction($zip_code)
    {
        $query = ZipToCityQuery::create()
            ->filterByCountriesIso2('DK')
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
}

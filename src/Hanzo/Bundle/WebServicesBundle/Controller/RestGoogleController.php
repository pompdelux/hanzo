<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Hanzo\Core\Tools;
use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;

use Hanzo\Model\CustomersQuery;
use Hanzo\Model\AddressesQuery;


/**
 * @see
 *  http://miller.limethinking.co.uk/2011/04/15/symfony2-controller-as-service/
 *  http://speakerdeck.com/u/hhamon/p/silex-meets-soap-rest
 */
class RestGoogleController extends CoreController
{
    /**
     * TODO: implement documentation in index actions.
     */
    public function indexAction($name = '') {

        return $this->render('WebServicesBundle:Default:index.html.twig', array(
            'page_type' => 'flaff',
            'name' => 'oskar'
        ));

    }

    /**
     * prozy function for getting zip code coordinates from google.
     *
     * TODO: implement caching, zip codes does not often change location
     *
     * @param string $query
     * @param string $country
     * @return object A Json encoded response object
     */
    public function proxyAction($query = null, $country = null)
    {
        $request = sprintf('http://maps.google.com/maps/geo?q=%s&output=json&oe=utf8', urlencode($query . ',' . $country));
        $response = array(
            'status' => TRUE,
            'data' => json_decode(file_get_contents($request))
        );

        return $this->json_response($response);
    }


    public function nearYouAction($type = 'near', $latitude = 0.00, $longitude = 0.00)
    {
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        // FIXME: un@bellcom.dk, fix sweedish koordinats for lerum - note this is a google maps issue!
        if ($latitude == 57.5752035 && $longitude == 17.1677372) {
          $latitude = 57.815504;
          $longitude = 12.268982;
        }

        $radius = 100;
        $num_items = 10;

        if ($type == 'hus') {
          $radius = 150;
          $num_items = 18;
        }

        $query = "
            SELECT
                customers_id AS id,
                (6371 * acos( cos(radians(".str_replace(',', '.', $latitude).")) * cos(radians(a.latitude)) * cos(radians(a.longitude) - radians(".str_replace(',', '.', $longitude).")) + sin(radians(".str_replace(',', '.', $latitude).")) * sin(radians(a.latitude)) )) AS distance
            FROM
                customers AS c
            JOIN
                addresses AS a
                ON (c.id = a.customers_id AND a.type = 'payment')
            WHERE
--                c.groups_id = 2
--                AND
                    c.is_active = 1
--                AND
--                    c.email NOT LIKE ('%@bellcom.dk')
                AND
                    c.email NOT IN ('hdkon@pompdelux.dk','mail@pompdelux.dk','hd@pompdelux.dk','kk@pompdelux.dk','sj@pompdelux.dk','ak@pompdelux.dk','test@pompdelux.dk')
            HAVING
                distance < {$radius}
            ORDER BY
                distance
            LIMIT
                {$num_items}
        ";
        $con = \Propel::getConnection();
        $result = $con->query($query);

        $ids = array();
        foreach ($result as $record) {
            $ids[] = $record['id'];
        }

        $customers = CustomersQuery::create()
            ->useAddressesQuery()
                ->filterByType('payment')
            ->endUse()
            ->filterById($ids)
            ->find()
        ;

        $data = array();
        foreach ($customers as $record) {

            $info = '<img src="http://cdn.ph.dk/images/consultantsDK/LineSkovsen.jpg" width="100" height="75"><i>Træffes bedst kl. 16 - 18</i>';
            if ($type == 'hus') {
                $info = '<b>Åbent hus arrangement</b><br>Mandag d. 20/2 kl. 12-22<br>Tirsdag d. 21/2 kl. 12-22<br>Haderslev Idrætscenter<br>Stadionvej 5<br>6100 Haderslev<br>Tilmelding nødvendig<br><i>Gerne mail eller sms 2627 7466</i>';
            }
//php app/console dataio:import customers dk
            $address = $record->getAddresses()->getFirst();

            $data[] = array(
                'id' => $record->getId(),
                'name' => $record->getFirstName(). ' ' . $record->getLastName(),
                'zip' => $address->getPostalCode(),
                'city' => $address->getCity(),
                'email' => $record->getEmail(),
                'phone' => $record->getPhone(),
                'info' => $info,
            );
        }


        $response = array(
            'status' => TRUE,
            'data' => $data
        );

        return $this->json_response($response);
    }
}

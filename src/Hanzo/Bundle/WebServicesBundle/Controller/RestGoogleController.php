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
    public function indexAction($name = '') {

        return $this->render('WebServicesBundle:Default:index.html.twig', array(
            'page_type' => 'flaff',
            'name' => 'oskar'
        ));

    }


    /**
     * This method handles "near you" pages.
     * Currrently two types are handled - the "near" and "hus" types.
     *   near: lists the consultants nearest you
     *   hus.: lists the events nearest you
     * in a 100km radius
     *
     * @param string $type
     * @param float $latitude
     * @param float $longitude
     * @return object json encoded Response object
     */
    public function nearYouAction($type = 'near', $latitude = 0.00, $longitude = 0.00)
    {
        if ((0 == $latitude) || (0 == $longitude)){
            $maxmind = $this->get('muneris.maxmind');
            $geoip = $maxmind->lookup($this->getRequest()->getClientIp());

            unset($response);
            $geoip = $geoip->city;

            if (0 == $latitude) {
                $latitude =  number_format($geoip->location->latitude, 8, '.', '');
            }
            if (0 == $longitude) {
                $longitude = number_format($geoip->location->longitude, 8, '.', '');
            }
        }


// TODO: switch the custom query to this:
// $lat = 55.494099;
// $lon = 9.459;
// $radius = 100;
// $exclude_pompdelux = array('hdkon@pompdelux.dk','mail@pompdelux.dk','hd@pompdelux.dk','kk@pompdelux.dk','sj@pompdelux.dk','ak@pompdelux.dk','test@pompdelux.dk');
// $exclude_bellcom = '%@bellcom.dk';

// $query = AddressesQuery::create()
//     ->filterByDistanceFrom($lat, $lon, $radius)
//     ->filterByType('payment')
//     ->useCustomersQuery('', 'JOIN')
//         ->filterByGroupsId(2)
//         ->filterByIsActive(true)
//         ->filterByEmail($exclude_pompdelux, \Criteria::NOT_IN)
//         ->filterByEmail($exclude_bellcom, \Criteria::NOT_LIKE)
//     ->endUse()
//     ->limit(10)
//     ->orderBy('Distance')
// ;
// $query
//     ->useCustomersQuery('', 'JOIN')
//         ->useConsultantsQuery('', 'JOIN')
//             ->filterByEventNotes('', \Criteria::NOT_EQUAL)
//         ->endUse()
//     ->endUse()
// ;
// $consultants = $query->find();

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        // un@bellcom.dk, fix sweedish koordinats for lerum - note this is a google maps issue!
        if ($latitude == 57.5752035 && $longitude == 17.1677372) {
          $latitude = 57.815504;
          $longitude = 12.268982;
        }

        // ab@bellcom.dk, fix danish koordinats for valby in KBH when searching for zip 2500 - note this is a google maps issue!
        if ($latitude == 56.05579 && $longitude == 12.20289) {
          $latitude = 55.661658;
          $longitude = 12.516775;
        }

        $radius = 100;
        $num_items = 12;

        $filter = '';
        if ($type == 'hus') {
          $radius = 150;
          $num_items = 18;
          $filter = "
            AND
                cn.event_notes != ''
            AND
                cn.hide_info = 0
          ";
        }

        $query = "
            SELECT
                c.id,
                (6371 * acos( cos(radians(".str_replace(',', '.', $latitude).")) * cos(radians(a.latitude)) * cos(radians(a.longitude) - radians(".str_replace(',', '.', $longitude).")) + sin(radians(".str_replace(',', '.', $latitude).")) * sin(radians(a.latitude)) )) AS distance,
                c.first_name,
                c.last_name,
                c.email,
                c.phone,
                a.postal_code,
                a.city,
                a.latitude,
                a.longitude,
                cn.info,
                cn.event_notes
            FROM
                customers AS c
            JOIN
                addresses AS a
                ON (
                    c.id = a.customers_id
                    AND
                    a.type = 'payment'
                )
            JOIN
                consultants AS cn
                ON (cn.id = c.id)
            WHERE
                c.groups_id = 2
                AND
                    c.is_active = 1
                AND
                    c.email NOT LIKE ('%@bellcom.dk')
                AND
                    c.email NOT IN ('hdkon@pompdelux.dk','mail@pompdelux.dk','hd@pompdelux.dk','kk@pompdelux.dk','sj@pompdelux.dk','ak@pompdelux.dk','test@pompdelux.dk','pd@pompdelux.dk')
            {$filter}
            HAVING
                distance < {$radius}
            ORDER BY
                distance
            LIMIT
                {$num_items}
        ";

        $cdn = Hanzo::getInstance()->get('core.cdn');
        $con = \Propel::getConnection();
        $result = $con->query($query);

        $data = array();
        foreach ($result as $record) {

            $info = $record['info'];
            if ($type == 'hus') {
                $info = str_replace("\n", "<br>", $record['event_notes']);
            }

            $info = str_replace('src="/', 'src="'.$cdn, $info);

            if ($info == 'null') {
                $info = '';
            }

            $data[] = array(
                'id' => $record['id'],
                'name' => $record['first_name']. ' ' . $record['last_name'],
                'zip' => $record['postal_code'],
                'city' => $record['city'],
                'email' => $record['email'],
                'phone' => $record['phone'],
                'notes' => '',
                'latitude' => $record['latitude'],
                'longitude' => $record['longitude'],
                'info' => $info,
            );
        }

        $response = array(
            'status' => TRUE,
            'data' => $data
        );

        return $this->json_response($response);
    }

    public function consultantsAction()
    {
        $consultants = CustomersQuery::create()
            ->filterByEmail('%@bellcom.dk', \Criteria::NOT_LIKE)
            ->filterByEmail(array('hdkon@pompdelux.dk','mail@pompdelux.dk','hd@pompdelux.dk','kk@pompdelux.dk','sj@pompdelux.dk','ak@pompdelux.dk','test@pompdelux.dk','pd@pompdelux.dk'), \Criteria::NOT_IN)
            ->filterByGroupsId(2)
            ->filterByIsActive(TRUE)
            ->useAddressesQuery()
                ->filterByType('payment')
            ->endUse()
            ->joinWithAddresses()
            ->find()
        ;

        $cdn = Hanzo::getInstance()->get('core.cdn');

        $data = array();
        foreach ($consultants as $consultant) {
            $address = $consultant->getAddresses()->getFirst();

            $info = $consultant->getInfo();
            $info = str_replace('src="/', 'src="'.$cdn, $info);

            $data[] = array(
                'name' => $consultant->getName(),
                'email' => $consultant->getEmail(),
                'phone' => $consultant->getPhone(),
                'info' => $info,
                'address' => $address->getAddressLine1(),
                'zip' => $address->getPostalCode(),
                'city' => $address->getCity(),
                'countryname' => $address->getCountry(),
                'latitude' => $address->getLatitude(),
                'longitude' => $address->getLongitude(),
            );
        }

        $response = array(
            'status' => TRUE,
            'data' => $data
        );

        return $this->json_response($response);
    }
}

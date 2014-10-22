<?php

namespace Hanzo\Bundle\WebServicesBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\EventsQuery;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestGoogleController
 *
 * @package Hanzo\Bundle\WebServicesBundle
 */
class RestGoogleController extends CoreController
{
    /**
     * This method handles "near you" pages.
     * Currrently two types are handled - the "near" and "hus" types.
     *   near: lists the consultants nearest you
     *   hus.: lists the events nearest you
     * in a 100km radius
     *
     * @param Request $request
     * @param string  $type
     * @param float   $latitude
     * @param float   $longitude
     * @param int     $showAll
     *
     * @throws \Exception
     * @return object json encoded Response object
     */
    public function nearYouAction(Request $request, $type = 'near', $latitude = 0.00, $longitude = 0.00, $showAll = 0)
    {
        if ((0 == $latitude) || (0 == $longitude)) {
            $maxmind = $this->get('muneris.maxmind');
            $geoip   = $maxmind->lookup($request->getClientIp());

            unset($response);
            $geoip = $geoip->city;

            if (0 == $latitude) {
                $latitude = number_format($geoip->location->latitude, 8, '.', '');
            }
            if (0 == $longitude) {
                $longitude = number_format($geoip->location->longitude, 8, '.', '');
            }
        }

        $latitude  = (float) $latitude;
        $longitude = (float) $longitude;

        // un@bellcom.dk, fix sweedish koordinats for lerum - note this is a google maps issue!
        if (($latitude == 57.5752035) && ($longitude == 17.1677372)) {
            $latitude  = 57.815504;
            $longitude = 12.268982;
        }

        // ab@bellcom.dk, fix danish koordinats for valby in KBH when searching for zip 2500 - note this is a google maps issue!
        if ($latitude == 56.05579 && $longitude == 12.20289) {
            $latitude  = 55.661658;
            $longitude = 12.516775;
        }

        $radius   = $showAll ? 20000 : 100;
        $numItems = $showAll ? 20000 : 12;

        $filter = '';
        if ($type == 'hus') {
            $radius   = $showAll ? 20000 : 150;
            $numItems = $showAll ? 20000 : 18;
            $filter    = "
            AND
                cn.hide_info = 0
          ";
        }

        $query = "
            SELECT
                c.id,
                (6371 * acos( cos(radians(" . str_replace(',', '.', $latitude) . ")) * cos(radians(a.latitude)) * cos(radians(a.longitude) - radians(" . str_replace(',', '.', $longitude) . ")) + sin(radians(" . str_replace(',', '.', $latitude) . ")) * sin(radians(a.latitude)) )) AS distance,
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
                {$numItems}
        ";

        $cdn    = preg_replace('/http[s]?:/', '', Hanzo::getInstance()->get('core.cdn'));
        $con    = \Propel::getConnection();
        $result = $con->query($query);

        $data = [];
        foreach ($result as $record) {
            $info   = $record['info'];
            $avatar = '';

            if ($type == 'hus') {
                preg_match('/\<img[^>]+\>/', $info, $matches);

                // do we need a fallback avatar ?
                if (empty($matches[0])) {
                    $matches[0] = '<img src="//cdn.pdl.un/images/consultantsDK/LineSkovsen.jpg" width="100" height="75">';
                }

                $avatar = $matches[0];
                $info   = str_replace("\n", "<br>", $record['event_notes']);
            }

            $info = str_replace('src="/', 'src="' . $cdn, $info);

            if ($info == 'null') {
                $info = '';
            }

            $data[$record['id']] = [
                'id'        => $record['id'],
                'name'      => $record['first_name'] . ' ' . $record['last_name'],
                'zip'       => $record['postal_code'],
                'city'      => $record['city'],
                'email'     => $record['email'],
                'phone'     => $record['phone'],
                'notes'     => '',
                'latitude'  => $record['latitude'],
                'longitude' => $record['longitude'],
                'info'      => $info,
                'avatar'    => $avatar,
            ];
        }

        if ('hus' == $type) {
            $openHouseEvents = EventsQuery::create()
                ->filterByRsvpType(null, \Criteria::ISNOTNULL)
                ->filterByEventDate(['min' => date('Y-m-d 00:00:01')])
                ->filterByConsultantsId(array_keys($data))
                ->orderByEventDate()
                ->find();

            $events = [];

            /** @var \Hanzo\Model\Events $event */
            foreach ($openHouseEvents as $event) {
                $start = $event->getEventDate('U');
                $end   = $event->getEventEndTime('U');
                $key   = $event->getPostalCode().$event->getAddressLine1();
                $cid   = $event->getConsultantsId();
                $note  = str_replace("\n", '<br>', Tools::stripTags($event->getPublicNote()));

                if (empty($events[$cid][$key])) {
                    $events[$cid][$key] = [
                        'address' => $event->getAddressLine1(),
                        'city'    => $event->getCity(),
                        'dates'   => [],
                        'host'    => $event->getHost(),
                        'notes'   => [],
                        'rsvp'    => $event->getRsvpType(),
                        'zip'     => $event->getPostalCode(),
                    ];
                }

                $events[$cid][$key]['dates'][] = [
                    'date' => strftime('%A %e/%m, %k:%M', $start) . ' - ' . strftime('%k:%M', $end),
                    'note' => $note,
                    'rswp' => $event->getRsvpType(),
                ];

                $events[$cid][$key]['notes'][] = $note;
            }

            foreach ($events as $consultantId => $items) {
                if (empty($data[$consultantId])) {
                    continue;
                }

                foreach ($items as $j => $event) {
                    $data[$consultantId]['events'][] = $event;
                }

                unset($data[$consultantId]['info']);
            }
        }

        $response = [
            'status' => true,
            'data'   => array_values($data)
        ];

        return $this->json_response($response);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function consultantsAction()
    {
        $ignore = [
            'hdkon@pompdelux.dk',
            'mail@pompdelux.dk',
            'hd@pompdelux.dk',
            'kk@pompdelux.dk',
            'sj@pompdelux.dk',
            'ak@pompdelux.dk',
            'test@pompdelux.dk',
            'pd@pompdelux.dk'
        ];

        $consultants = CustomersQuery::create()
            ->filterByEmail('%@bellcom.dk', \Criteria::NOT_LIKE)
            ->filterByEmail($ignore, \Criteria::NOT_IN)
            ->filterByGroupsId(2)
            ->filterByIsActive(true)
            ->useAddressesQuery()
            ->filterByType('payment')
            ->endUse()
            ->joinWithAddresses()
            ->find();

        $cdn = Hanzo::getInstance()->get('core.cdn');

        $data = [];
        foreach ($consultants as $consultant) {
            $address = $consultant->getAddresses()->getFirst();

            $info = $consultant->getInfo();
            $info = str_replace('src="/', 'src="' . $cdn, $info);

            $data[] = [
                'name'        => $consultant->getName(),
                'email'       => $consultant->getEmail(),
                'phone'       => $consultant->getPhone(),
                'info'        => $info,
                'address'     => $address->getAddressLine1(),
                'zip'         => $address->getPostalCode(),
                'city'        => $address->getCity(),
                'countryname' => $address->getCountry(),
                'latitude'    => $address->getLatitude(),
                'longitude'   => $address->getLongitude(),
            ];
        }

        $response = [
            'status' => true,
            'data'   => $data
        ];

        return $this->json_response($response);
    }
}

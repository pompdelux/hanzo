<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function lookupAction()
    {

$api = $this->get('hanzo_location_locator');
$response = $api->findByAddress([
    'countryCode'  => 'DK',
    'city'         => 'Kolding',
    'postalCode'   => '6000',
    'streetName'   => 'Thurasvej',
    'streetNumber' => '11',
]);

print_r($response);

        return $this->render('HanzoLocationLocatorBundle:Default:lookup.html.twig');
    }
}

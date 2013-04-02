<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class SearchForDropPoints
{
    public $productConceptID; // int
    public $installationID; // string
    public $country; // string
    public $address; // string
    public $postCode; // string
    public $city; // string
    public $limit; // int
}

<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class GetClosestDropPoint
{
    public $productConceptID; // int
    public $installationID; // string
    public $postCode; // string
    public $country; // string
}

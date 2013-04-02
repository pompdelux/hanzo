<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class GetChODCarrierDropPointsWithinBounds
{
    public $productConceptIds; // string
    public $installationId; // string
    public $ne_lat; // decimal
    public $ne_lng; // decimal
    public $sw_lat; // decimal
    public $sw_lng; // decimal
}

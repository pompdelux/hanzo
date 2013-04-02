<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class GetChODAllDropPointsWithinBounds
{
    public $webShopId; // int
    public $productConceptIds; // string
    public $webshopProductIds; // string
    public $installationId; // string
    public $ne_lat; // decimal
    public $ne_lng; // decimal
    public $sw_lat; // decimal
    public $sw_lng; // decimal
}

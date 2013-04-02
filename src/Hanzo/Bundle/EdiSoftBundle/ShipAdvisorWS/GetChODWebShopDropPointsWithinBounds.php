<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class GetChODWebShopDropPointsWithinBounds
{
    public $webShopId; // int
    public $webshopProductIds; // string
    public $installationId; // string
    public $ne_lat; // decimal
    public $ne_lng; // decimal
    public $sw_lat; // decimal
    public $sw_lng; // decimal
}

<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class GetChODWebShopDropPointsOnMap
{
    public $webShopId; // int
    public $webshopProductIds; // string
    public $installationId; // string
    public $postCode; // string
    public $country; // string
    public $mapWidth; // decimal
    public $mapHeight; // decimal
}

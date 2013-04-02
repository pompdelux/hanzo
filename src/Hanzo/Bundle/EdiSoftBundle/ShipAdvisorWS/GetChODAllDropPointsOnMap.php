<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class GetChODAllDropPointsOnMap
{
    public $webShopId; // int
    public $productConceptIds; // string
    public $webshopProductIds; // string
    public $installationId; // string
    public $postCode; // string
    public $country; // string
    public $mapWidth; // decimal
    public $mapHeight; // decimal
}

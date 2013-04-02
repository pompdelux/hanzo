<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class DropPointData
{
    public $ChodProductCodes; // string
    public $OriginalId; // string
    public $ESId; // string
    public $RoutingCode; // string
    public $Depot; // string
    public $Name1; // string
    public $Name2; // string
    public $Address1; // string
    public $Address2; // string
    public $PostalCode; // string
    public $City; // string
    public $CountryCode; // string
    public $Contact; // string
    public $Phone; // string
    public $Fax; // string
    public $Email; // string
    public $MapRefX; // double
    public $MapRefY; // double
    public $Distance; // double
    public $OpeningHoursList; // ArrayOfOpeningHours
    public $RatingDataList; // ArrayOfRatingData
    public $KeyValueList; // ArrayOfKeyValue
}

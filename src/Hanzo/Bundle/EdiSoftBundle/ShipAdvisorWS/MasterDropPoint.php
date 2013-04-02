<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class MasterDropPoint
{
  public $Id; // int
  public $RoutingCode; // string
  public $Name; // string
  public $Address; // string
  public $Address2; // string
  public $City; // string
  public $PostCode; // string
  public $Country; // string
  public $MapRefX; // decimal
  public $MapRefY; // decimal
  public $ValidTo; // dateTime
  public $UntrustFlags; // unsignedInt
  public $WebShopId; // int
  public $SupportedProducts; // string
  public $MasterBusinessHours; // ArrayOfMasterBusinessHour
  public $MDPXMPCs; // ArrayOfMDPXMPC
  public $MasterDropPointXRatings; // ArrayOfMasterDropPointXRating
}

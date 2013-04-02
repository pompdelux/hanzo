<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class MasterPostCode
{
    public $Id; // int
    public $Code; // string
    public $Country; // string
    public $MapRefX; // decimal
    public $MapRefY; // decimal
    public $UntrustFlags; // int
    public $ValidTo; // dateTime
    public $Zoom; // int
    public $MDPXMPCs; // ArrayOfMDPXMPC
}

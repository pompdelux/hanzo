<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class MDPXMPC
{
    public $Id; // int
    public $Distance; // decimal
    public $Type; // unsignedByte
    public $MasterDropPoint; // MasterDropPoint
    public $MasterPostCode; // MasterPostCode
}

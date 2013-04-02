<?php

namespace Hanzo\Bundle\EdiSoftBundle\ShipAdvisorWS;

class MasterBusinessHour
{
    public $Id; // int
    public $WeekDayStart; // unsignedByte
    public $WeekDayStop; // unsignedByte
    public $HoursStart; // short
    public $HoursStop; // short
}

<?php

namespace Hanzo\Model;

use Hanzo\Model\om\BaseEvents;

/**
 * Class Events
 *
 * @package Hanzo\Model
 */
class Events extends BaseEvents
{
    public static $eventRsvpMap = [
        '' => 'none.needed',
        0  => 'none.needed',
        1  => 'events.rsvp_type.choice.need_to',
        2  => 'events.rsvp_type.choice.nice_to',
        3  => 'events.rsvp_type.choice.sms_email',
    ];
}

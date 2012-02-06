<?php
namespace Hanzo\Bundle\NewsletterBundle;

use Symfony\Component\EventDispatcher\Event;

use Hanzo\Bundle\NewsletterBundle\FilterTestEvent;

class TestListener
{
    public function onTest(FilterTestEvent $event)
    {
        error_log(__LINE__.':'.__FILE__.' '); // hf@bellcom.dk debugging
    }
}

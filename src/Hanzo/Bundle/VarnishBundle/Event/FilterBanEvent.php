<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\VarnishBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class FilterBanEvent extends Event
{
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}

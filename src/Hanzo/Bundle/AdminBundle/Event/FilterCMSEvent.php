<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class FilterCMSEvent extends Event
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

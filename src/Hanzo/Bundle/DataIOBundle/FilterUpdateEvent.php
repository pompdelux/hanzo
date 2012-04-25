<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle;

use Symfony\Component\EventDispatcher\Event;

class FilterUpdateEvent extends Event
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

<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AdminBundle\Event;

use PropelPDO;
use Symfony\Component\EventDispatcher\Event;
use Hanzo\Core\Tools;

class FilterCMSEvent extends Event
{
    protected $data;
    protected $locale;
    protected $connection;

    public function __construct($data, $locale, PropelPDO $connection = null)
    {
        $this->data = $data;
        $this->locale = $locale;
        $this->connection = $connection;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getLocale($default = null)
    {
        return $this->locale ?: $default;
    }
}

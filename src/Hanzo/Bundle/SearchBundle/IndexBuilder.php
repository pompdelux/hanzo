<?php

namespace Hanzo\Bundle\SearchBundle;

use Hanzo\Model\LanguagesQuery;

class IndexBuilder
{
    private $connection;

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function getLocales()
    {
        return LanguagesQuery::create()
            ->select('locale')
            ->find($this->getConnection())
        ;
    }
}

<?php

namespace Hanzo\Bundle\AdminBundle\Entity;

class CmsNode
{
    protected $type;

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
}
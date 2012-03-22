<?php

namespace Hanzo\Bundle\AdminBundle\Entity\CmsNode;

class CmsNode
{
    protected $type;

    public function getType()
    {
        return $this->task;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
}
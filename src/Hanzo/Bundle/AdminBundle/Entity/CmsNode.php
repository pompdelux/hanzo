<?php

namespace Hanzo\Bundle\AdminBundle\Entity;

class CmsNode
{
    protected $type;
    protected $cms_thread_id;

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getCmsThreadId()
    {
        return $this->cms_thread_id;
    }
    public function setCmsThreadId($cms_thread_id)
    {
        $this->cms_thread_id = $cms_thread_id;
    }
}
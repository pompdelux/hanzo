<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services\Mappers;

/**
 * Class BaseMapper
 *
 * @package Hanzo\Bundle\AxBundle\Actions\Out\Services\Mappers
 */
class BaseMapper implements \ArrayAccess
{
    /**
     * Data container
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Getter implementation
     *
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        }

        return null;
    }

    /**
     * Setter implementation
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key] = $value;
        }

        throw new \OutOfBoundsException("Key '{$key}' not a valid parameter! Valid keys are: ".implode(', ', array_keys($this->fields)));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (array_key_exists($offset, $this->fields)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->fields[$offset] = null;
    }
}

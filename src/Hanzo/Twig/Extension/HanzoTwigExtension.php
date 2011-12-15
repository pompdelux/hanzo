<?php

namespace Hanzo\Twig\Extension;

use \Twig_Extension,
    \Twig_Function_Method;

class HanzoTwigExtension extends Twig_Extension
{
    protected $container;

    public function __construct($container, $cdn = '')
    {
        $this->container = $container;
        $this->cdn = $cdn;
    }

    public function getName()
    {
        return 'hanzo';
    }


    public function getFunctions()
    {
        return array(
            'image_tag' => new Twig_Function_Method($this, 'image_tag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'image_path' => new Twig_Function_Method($this, 'image_path', array()),
        );
    }

    /**
     * generates a formatted image tag.
     *
     * @see Functions::image_path()
     * @param string $src image source
     * @param string $preset the image preset to use - format heightXwidth
     * @param array $params
     * @return type
     */
    public function image_tag($src, $preset = '50x50', array $params = array())
    {
        if (empty($params['title']) && !empty($params['alt'])) {
            $params['title'] = $params['alt'];
        }
        if (empty($params['alt']) && !empty($params['title'])) {
            $params['alt'] = $params['title'];
        }

        $extra = '';

        foreach ($params as $key => $value) {
            $extra .= ' ' . $key . '="'.$value.'"';
        }

        return '<img src="' . $this->image_path($src, $preset) . '"' . $extra . '>';
    }


    /**
     * build image path based on source and preset
     *
     * @param string $src image source
     * @param string $preset the image preset to use - format heightXwidth
     * @throws InvalidArgumentException
     * @return string
     */
    public function image_path($src, $preset)
    {
        if (!preg_match('/[0-9]+x[0-9]+/i', $preset)) {
            throw new \InvalidArgumentException("Preset: {$preset} is not valid.");
        }

        $file = basename($src);
        $dir = '/images/products/';
        $src = $dir . $preset . ',' . $file;

        return $this->cdn . $src;
    }

}

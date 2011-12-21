<?php

namespace Hanzo\Twig\Extension;

use \Twig_Extension,
    \Twig_Function_Method,
    \Twig_Function_Function,
    \Twig_Filter_Method
;

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
            'product_image_tag' => new Twig_Function_Method($this, 'product_image_tag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'product_image_url' => new Twig_Function_Method($this, 'product_image_url'),
            'fx_image_tag' => new Twig_Function_Method($this, 'fx_image_tag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'image_path' => new Twig_Function_Method($this, 'image_path', array()),
            'print_r' => new Twig_Function_Function('print_r'),
        );
    }

    public function getFilters() {
        return array(
            'money' => new Twig_Filter_Method($this, 'hanzo_money_format'),
        );
    }


    public function hanzo_money_format($number, $format = '%i')
    {
        return money_format($format, $number);
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
    public function fx_image_tag($src, $preset = '50x50', array $params = array())
    {
        $src = $this->cdn . 'fx/' . $src;
        return $this->image_tag($this->image_path($src, $preset), $params);
    }

    public function product_image_tag($src, $preset = '50x50', array $params = array())
    {
        $src = $this->cdn . 'images/products/thumb/' . $src;
        return $this->image_tag($this->image_path($src, $preset), $params);
    }

    public function product_image_url($src, $preset = '50x50', array $params = array())
    {
        $src = $this->cdn . 'images/products/thumb/' . $src;
        return $this->image_path($src, $preset);
    }


    protected function image_tag($src, array $params = array())
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

        return '<img src="' . $src . '"' . $extra . '>';
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

        $url = parse_url($src);

        $file = basename($url['path']);
        $dir  = dirname($url['path']);
        $url['path'] = $dir . '/' . $preset . ',' . $file;
        $url['query'] = $this->container->get('hanzo')->get('core.cache_key', 'z3');

        if (empty($url['scheme'])) {
            $url['scheme'] = 'http';
            $url['host'] = $_SERVER['HTTP_HOST'];
        }

        return $url['scheme'].'://'.$url['host'].$url['path'].'?'.$url['query'];
    }
}

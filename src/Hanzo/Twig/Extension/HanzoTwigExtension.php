<?php

namespace Hanzo\Twig\Extension;

use \Twig_Extension,
    \Twig_Function_Method,
    \Twig_Function_Function,
    \Twig_Filter_Method;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

class HanzoTwigExtension extends Twig_Extension
{
    // protected $container;

    // public function __construct($container, $cdn = '')
    // {
    //     $this->container = $container;
    //     $this->cdn = $cdn;
    // }

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

    /**
     * @see Hanzo\Core\Tools\Tools::moneyFormat
     * @todo loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function hanzo_money_format($number, $format = '%i')
    {
        return Tools::moneyFormat($number, $format);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::fxImageTag
     * @todo loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function fx_image_tag($src, $preset = '', array $params = array())
    {
        return Tools::fxImageTag($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::productImageTag
     * @todo loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function product_image_tag($src, $preset = '50x50', array $params = array())
    {
        return Tools::productImageTag($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::productImageUrl
     * @todo loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function product_image_url($src, $preset = '50x50', array $params = array())
    {
        return Tools::productImageUrl($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::imageTag
     * @todo loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    protected function image_tag($src, array $params = array())
    {
        return Tools::imageTag($src, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::imagePath
     * @todo loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function image_path($src, $preset = '')
    {
        return Tools::imagePath($src, $preset);
    }
}

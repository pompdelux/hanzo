<?php

namespace Hanzo\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Twig_Function_Function;
use Twig_Filter_Method;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class AssetExtension extends Twig_Extension
{
    protected $twig_string;

    public function getName()
    {
        return 'asset';
    }


    public function getFunctions()
    {
        return array(
            'product_image_tag' => new Twig_Function_Method($this, 'product_image_tag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'product_image_url' => new Twig_Function_Method($this, 'product_image_url'),
            'fx_image_tag' => new Twig_Function_Method($this, 'fx_image_tag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'image_path' => new Twig_Function_Method($this, 'image_path', array()),
            'image_tag' => new Twig_Function_Method($this, 'image_tag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    /**
     * @see Hanzo\Core\Tools\Tools::fxImageTag
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function fx_image_tag($src, $preset = '', array $params = array())
    {
        return Tools::fxImageTag($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::productImageTag
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function product_image_tag($src, $preset = '50x50', array $params = array())
    {
        return Tools::productImageTag($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::productImageUrl
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function product_image_url($src, $preset = '50x50', array $params = array())
    {
        return Tools::productImageUrl($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::imageTag
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function image_tag($src, array $params = array())
    {
        return Tools::imageTag($src, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::imagePath
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function image_path($src, $preset = '')
    {
        return Tools::imagePath($src, $preset);
    }
}

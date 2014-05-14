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

    /**
     * @return string
     */
    public function getName()
    {
        return 'asset';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'product_image_tag' => new Twig_Function_Method($this, 'product_image_tag', ['pre_escape' => 'html', 'is_safe' => ['html']]),
            'product_image_url' => new Twig_Function_Method($this, 'product_image_url'),
            'fx_image_tag'      => new Twig_Function_Method($this, 'fx_image_tag', ['pre_escape' => 'html', 'is_safe' => ['html']]),
            'fx_image_url'      => new Twig_Function_Method($this, 'fx_image_url'),
            'image_path'        => new Twig_Function_Method($this, 'image_path', []),
            'image_tag'         => new Twig_Function_Method($this, 'image_tag', ['pre_escape' => 'html', 'is_safe' => ['html']]),
            'asset_url'         => new Twig_Function_Method($this, 'asset_url', ['pre_escape' => 'html', 'is_safe' => ['html']]),
            'asset_embed'       => new Twig_Function_Method($this, 'asset_embed', ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    /**
     * @see Hanzo\Core\Tools\Tools::fxImageTag
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function fx_image_tag($src, $preset = '', array $params = [])
    {
        return Tools::fxImageTag($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::fxImageUrl
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function fx_image_url($src, $preset = '50x50', array $params = [])
    {
        return Tools::fxImageUrl($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::productImageTag
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function product_image_tag($src, $preset = '50x50', array $params = [])
    {
        return Tools::productImageTag($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::productImageUrl
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function product_image_url($src, $preset = '50x50', array $params = [])
    {
        return Tools::productImageUrl($src, $preset, $params);
    }

    /**
     * @see Hanzo\Core\Tools\Tools::imageTag
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function image_tag($src, array $params = [])
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

    /**
     * @param $path
     * @return string
     */
    public function asset_url($path)
    {
        $hanzo = Hanzo::getInstance();
        return str_replace(['http:', 'https:'], '', $hanzo->get('core.cdn')).$path.'?'.$hanzo->get('core.assets_version', 'z4');
    }

    /**
     * do we need to ?
     *
     * @param  string $path
     * @return string
     */
    public function asset_embed($path)
    {
        $path = 'http:'.$this->asset_url($path);
        return file_get_contents($path);
    }
}

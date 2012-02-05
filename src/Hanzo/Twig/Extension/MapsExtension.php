<?php

namespace Hanzo\Twig\Extension;

use \Twig_Extension;
use \Twig_Function_Method;
use \Twig_Function_Function;
use \Twig_Filter_Method;

use Symfony\Component\Finder\Finder;
use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class MapsExtension extends Twig_Extension
{
    protected $template_dir;
    protected $twig_string;

    public function __construct(TwigStringService $twig_string)
    {
        $this->twig_string = $twig_string;
        $this->template_dir = realpath(__DIR__ . '/../../Bundle/CMSBundle/Resources/views/Twig/') . '/';
    }

    /**
     * @inherit
     */
    public function getName()
    {
        return 'maps';
    }

    /**
     * @inherit
     */
    public function getFunctions()
    {
        return array(
            'geo_zip_code_form' => new Twig_Function_Method($this, 'geo_zip_code_form', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'geo_consultants_map' => new Twig_Function_Method($this, 'geo_consultants_map', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function geo_zip_code_form($type = 'near-you', $country = 'Denmark')
    {
        $template = file_get_contents($this->template_dir . 'zip_form.html.twig');
        return $this->twig_string->parse($template, array(
            'type' => $type,
            'country' => $country
        ));
    }

    public function geo_consultants_map($type = 'near-you')
    {
        $template = file_get_contents($this->template_dir . 'consultants_map.html.twig');
        return $this->twig_string->parse($template, array(
            'type' => $type,
        ));
    }
}

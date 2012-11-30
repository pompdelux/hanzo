<?php

namespace Hanzo\Twig\Extension;

use \Twig_Extension;
use \Twig_Environment;
use \Twig_Function_Method;
use \Twig_Function_Function;
use \Twig_Filter_Method;

use Symfony\Component\Finder\Finder;
use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class MapsExtension extends Twig_Extension
{
    protected $twig_string;

    public function __construct(TwigStringService $twig_string)
    {
        $this->twig_string = $twig_string;
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
            'geo_zip_code_form' => new Twig_Function_Method($this, 'zip_code_form', array('pre_escape' => 'html', 'is_safe' => array('html'), 'needs_environment' => true)),
            'consultants_near_you' => new Twig_Function_Method($this, 'consultants_near_you', array('pre_escape' => 'html', 'is_safe' => array('html'), 'needs_environment' => true)),
            'consultants_map' => new Twig_Function_Method($this, 'consultants_map', array('pre_escape' => 'html', 'is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    public function zip_code_form(Twig_Environment $env, $type = 'near', $country = 'Denmark')
    {
        return $env->render('CMSBundle:Twig:zip_form.html.twig', array(
            'type' => $type,
            'country' => $country
        ));
    }

    public function consultants_map(Twig_Environment $env)
    {
        $hanzo = Hanzo::getInstance();
        $config = $hanzo->getByNs('maps');

        return $env->render('CMSBundle:Twig:consultants_map.html.twig', array(
            'settings' => 'lat:' . $config['consultants_map.lat'] . ',lng:' . $config['consultants_map.lng'].', zoom:' . $config['consultants_map.zoom'],
            'language' => $config['consultants_map.language'],
            'height'   => $config['consultants_map.height'],
        ));
    }

    public function consultants_near_you(Twig_Environment $env, $type = 'near')
    {
        $geoip = Hanzo::getInstance()->container->get('geoip_manager');
        $result = $geoip->lookup();

        return $env->render('CMSBundle:Twig:consultants_near_you.html.twig', array(
            'type' => $type,
            'lat' => number_format((double) $result['lat'], 8, '.', ''),
            'lon' => number_format((double) $result['lon'], 8, '.', ''),
        ));
    }
}

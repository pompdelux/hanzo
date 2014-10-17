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

/**
 * Class MapsExtension
 *
 * @package Hanzo\Twig\Extension
 */
class MapsExtension extends Twig_Extension
{
    protected $twigString;
    protected $maxmind;

    /**
     * @param TwigStringService $twigString
     * @param object            $maxmind
     */
    public function __construct(TwigStringService $twigString, $maxmind)
    {
        $this->twigString = $twigString;
        $this->maxmind    = $maxmind;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'maps';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'geo_zip_code_form' => new Twig_Function_Method(
                $this,
                'zip_code_form',
                ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_environment' => true]
            ),
            'consultants_near_you' => new Twig_Function_Method(
                $this,
                'consultants_near_you',
                ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_environment' => true]
            ),
            'consultants_map' => new Twig_Function_Method(
                $this,
                'consultants_map',
                ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param Twig_Environment $env
     * @param string           $type
     * @param string           $country
     *
     * @return string
     */
    public function zip_code_form(Twig_Environment $env, $type = 'near', $country = 'Denmark')
    {
        return $env->render('CMSBundle:Twig:zip_form.html.twig', [
            'type'    => $type,
            'country' => $country
        ]);
    }

    /**
     * @param Twig_Environment $env
     *
     * @return string
     * @throws \Exception
     */
    public function consultants_map(Twig_Environment $env)
    {
        $hanzo = Hanzo::getInstance();
        $config = $hanzo->getByNs('maps');

        return $env->render('CMSBundle:Twig:consultants_map.html.twig', [
            'settings' => 'lat:' . $config['consultants_map.lat'] . ',lng:' . $config['consultants_map.lng'].', zoom:' . $config['consultants_map.zoom'],
            'language' => $config['consultants_map.language'],
            'height'   => $config['consultants_map.height'],
        ]);
    }

    /**
     * @param Twig_Environment $env
     * @param string           $type
     * @param string           $all
     *
     * @return string
     */
    public function consultants_near_you(Twig_Environment $env, $type = 'near', $all = 'false')
    {
        $result = $this->maxmind->lookup();

        return $env->render('CMSBundle:Twig:consultants_near_you.html.twig', [
            'all'  => $all,
            'type' => $type,
            'lat'  => number_format((double) $result->city->location->latitude, 8, '.', ''),
            'lon'  => number_format((double) $result->city->location->longitude, 8, '.', ''),
        ]);
    }
}

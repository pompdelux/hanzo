<?php

namespace Hanzo\Bundle\LocationLocatorBundle\Providers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bridge\Monolog\Logger;

/**
 * Location provider base class
 *
 * @author Ulrik Nielsen <un@bellcom.dk>
 */
abstract class BaseProvider
{
    /**
     * Find locations by address
     *
     * @param  array   $address_parts   Associative array of address parts to use in search
     * @param  integer $limit           Number of results to return
     * @throws ProviderException        If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array                    Array of results, empty array if none found
     */
    abstract public function findByAddress(array $address_parts = [], $limit = 5);

    /**
     * Find locations by postal code
     *
     * @param  string  $country_code    Country code
     * @param  mixed   $postal_code     The postal code "string" to lookup
     * @param  integer $limit           Number of results to return
     * @throws ProviderException        If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array                    Array of results, empty array if none found
     */
    abstract public function findByPostalCode($country_code, $postal_code, $limit = 5);

    /**
     * Find locations by coordinates
     *
     * @param  float   $latitude        Latitude point
     * @param  float   $longitude       Longitude point
     * @param  string  $country_code    ISO 3166-1, Alpha-2 Country code
     * @param  integer $limit           Number of results to return
     * @throws ProviderException        If there are problems with the lookup
     * @throws InvalidArgumentException If there are problems with the arguments
     * @return array                    Array of results, empty array if none found
     */
    abstract public function findByLocation($latitude, $longitude, $country_code, $limit = 5);

    /**
     * Must return the provider name
     *
     * @return string
     */
    abstract protected function getProviderName();


    /**
     * Translator instance
     *
     * @var object
     */
    protected $translator = null;

    /**
     * locator settings
     *
     * @var array
     */
    protected $settings = [];


    /**
     * Setup provider
     *
     * @param  Array      $settings     Array of settings
     * @param  Translator $translator   Translator object
     * @param  Logger     $logger       Logger object
     * @throws InvalidArgumentException If there are problems with the arguments
     */
    public function setup(array $settings = [], Translator $translator, Logger $logger, $environment)
    {
        foreach ($settings as $key => $value) {
            $this->settings[$key] = $value;
        }

        $this->translator  = $translator;
        $this->logger      = $logger;
        $this->environment = $environment;
    }


    /**
     * Build the form used to retrive the information needed for the lookup
     *
     * @param  FormBuilder $builder FormBuilder object
     * @param  Request     $request Request object
     * @throws ProviderException    If there are any logical problems
     * @return FormBuilder
     */
    public function getLookupForm(FormBuilder $builder, Request $request)
    {
        $builder = $builder
            ->add('q', 'text', [
                'label'              => 'lookup.label',
                'error_bubbling'     => true,
                'translation_domain' => 'locator',
                'attr' => [
                    'placeholder'    => $this->translator->trans('lookup.placeholder', [], 'locator'),
                ]
            ])
            ->add('countryCode', 'hidden', ['data' => substr($request->getLocale(), -2)])
            ->add('provider',    'hidden', ['data' => $this->getProviderName()])
            ->getForm()
        ;

        if (($request->getMethod() == 'POST') && !empty($_POST['builder']['q'])) {
            $builder->bind($request);
        }

        return $builder;
    }
}

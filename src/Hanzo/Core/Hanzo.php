<?php

namespace Hanzo\Core;

use Hanzo\Model;
use Hanzo\Model\SettingsQuery;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\DomainsSettingsQuery;

use Symfony\Component\Yaml\Yaml;

class Hanzo
{
    protected $domain;
    protected $settings = array();
    protected $ns_settings = array();

    public $cache = null;
    public $container;

    protected static $hanzo;

    public static function initialize($container = NULL, $environment = NULL)
    {
        if (empty(self::$hanzo)) {
            if (empty($container)) {
                throw new \InvalidArgumentException('Service Container object needed !', 100);
            }
            self::$hanzo = new Hanzo($container, $environment);
        }

        return self::$hanzo;
    }

    public static function getInstance()
    {
        if (self::$hanzo) {
            return self::$hanzo;
        }

        throw new \Exception('Hanzo not initializet', 100);
    }


    private function __construct($container, $environment = NULL)
    {
        $this->container = $container;
        $this->settings['core']['env'] = $environment;
        $session = $this->container->get('session');

        $domain = DomainsQuery::create()
            ->joinWithDomainsSettings()
            ->useDomainsSettingsQuery()
                ->filterByCKey('locale')
                ->filterByCValue($session->getLocale())
            ->endUse()
            ->findOne()
        ;

        $this->settings['core']['tld'] = strtolower($domain->getDomainKey());

        if ('cli' !== PHP_SAPI) {
            $this->cache = $this->container->get('hanzo.cache');
        }

        $config = Yaml::parse( __DIR__ . '/../../../app/config/hanzo.yml' );

        // translate locale dev tld's
        if (isset($config['core']['tld_map']) &&
            isset($config['core']['tld_map'][$this->settings['core']['tld']])
        ) {
            $this->settings['core']['tld'] = $config['core']['tld_map'][$this->settings['core.tld']];
        }

        if ($this->cache && ($cache = $this->cache->get($this->cache->id('core.settings')))) {
             $this->settings = $cache;
        }
        else {
            $this->initSettings();
            $this->initDomain($config['core']['default_tld'], $config['core']['tld_map']);

            if ($this->cache) {
                $this->cache->set($this->cache->id('core.settings'), $this->settings);
            }
        }

        unset($config['core']['tld_map'], $config['core']['default_tld']);

        $config['core']['cdn'] = $this->container->getParameter('cdn');

        foreach ($config as $section => $settings) {
            foreach ($settings as $key => $value) {
                $this->settings[$section][$key] = $value;
            }
        }

        foreach($this->settings as $key => $value) {
            foreach ($value as $ns => $data) {
                $this->ns_settings[$key.'.'.$ns] = $data;
            }
        }

        list($lang, ) = explode('_', $this->get('core.locale'), 2);
        $this->container->get('twig')->addGlobal('html_lang', $lang);
        //$this->container->get('translator')->setLocale($this->get('core.locale'));

        setLocale(LC_ALL, $session->getLocale().'.utf-8');

        // we piggybag on nl to show euros, even for none euro countries
        // note the locale has to be installed, and er need duch anyway, so...
        if ($this->get('core.currency') == 'EUR') {
            setlocale(LC_MONETARY, 'nl_NL.utf8');
        }
    }


    /**
     * initiate domain settings and locale setup
     *
     * @param string $default, the default domain to use
     * @param array $dev_map, optional array of locale tld's to map to "real" domains
     */
    protected function initDomain($default, array $dev_map = array())
    {
        $settings = DomainsSettingsQuery::create()
            ->joinWithDomains()
            ->findByDomainKey($this->settings['core']['tld'])
        ;

        foreach ($settings as $record) {
            $this->settings[$record->getNs()][$record->getCKey()] = $record->getCValue();
        }

        if ($record) {
            $language = LanguagesQuery::create()
                ->findOneByLocale($this->settings['core']['locale'])
            ;
            $this->settings['core']['language_id'] = $language->getId();

            $this->settings['core']['domain_id'] = $record->getDomains()->getId();
            $this->settings['core']['domain_key'] = $record->getDomainKey();
        }
    }


    /**
     * initialize settings from the database
     */
    protected function initSettings()
    {
        $settings = SettingsQuery::create()->find();

        foreach ($settings as $record) {

            // unserialize the cached data if needed
            $data = stripslashes($record->getCValue());
            if ($data && (substr($data, 0, 5) == ':[S]:')) {
                $data = unserialize(substr($data), 5);
            }

            $this->settings[$record->getNs()][$record->getCKey()] = $data;
        }
    }

    /**
     * getter method, returns data from the current settings scope.
     *
     * @param string $key, settings namespace and key
     * @param mixed $default, parameter to return as default (fallback) value
     * @return mixed
     */
    public function get($key, $default = NULL)
    {
        if ($key == 'ALL') {
            return $this->ns_settings;
        }

        if (isset($this->ns_settings[$key])) {
            return $this->ns_settings[$key];
        }

        return $default;
    }

    /**
     * Get all settings associated with a specific namespace
     *
     * @param string $ns
     * @return array
     */
    public function getByNs($ns)
    {
        if (isset($this->settings[$ns])) {
            return $this->settings[$ns];
        }
        return array();
    }


    /**
     * used by the GeocodableBehavior
     */
    public function getGoogleMapsKey()
    {
        return $this->get('google.maps');
    }

    /**
     * get session object wrapper
     */
    public function getSession()
    {
        return $this->container->get('request')->getSession();
    }
}

<?php

namespace Hanzo\Core;

use Hanzo\Model,
    Hanzo\Model\SettingsQuery,
    Hanzo\Model\LanguagesQuery,
    Hanzo\Model\DomainsSettingsQuery
;

use Symfony\Component\Yaml\Yaml;

class Hanzo
{
    protected $domain;
    protected $settings = array();

    public $cache;
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
        $this->settings['core.env'] = $environment;

        $this->domain = empty($_SERVER['HTTP_HOST']) ? 'localhost.locale' : $_SERVER['HTTP_HOST'];
        $this->domain_fragments = explode('.', $this->domain);
        $this->settings['core.tld'] = array_pop($this->domain_fragments);

        $this->cache = new RedisCache($this);

        $config = Yaml::parse( __DIR__ . '/../../../app/config/hanzo.yml' );

        if (isset($config['core']['cdn'])) {
            $this->settings['core.cdn'] = $config['core']['cdn'];
            $this->container->get('twig')->addGlobal('cdn', $config['core']['cdn']);
        }

        // translate locale dev tld's
        if (isset($config['core']['tld_map']) &&
            isset($config['core']['tld_map'][$this->settings['core.tld']])
        ) {
            $this->settings['core.tld'] = $config['core']['tld_map'][$this->settings['core.tld']];
        }

        if ($cache = $this->cache->get($this->cache->id('core.settings'))) {
             $this->settings = $cache;
        }
        else {
            $this->initSettings();
            $this->initDomain($config['core']['default_tld'], $config['core']['tld_map']);
            $this->cache->set($this->cache->id('core.settings'), $this->settings);
        }

        unset($config['core']['cdn'], $config['core']['tld_map'], $config['core']['default_tld']);

        foreach ($config as $section => $settings) {
            foreach ($settings as $key => $value) {
                $this->settings[$section.'.'.$key] = $value;
            }
        }

        $session = $this->container->get('session');
        if ($session->getLocale() != $this->settings['core.locale']) {
            $session->setLocale($this->settings['core.locale']);
        }

        list($lang, ) = explode('_', $this->settings['core.locale'], 2);
        $this->container->get('twig')->addGlobal('html_lang', $lang);

        setLocale(LC_ALL, $session->getLocale().'.utf-8');
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
            ->findByDomainKey($this->settings['core.tld'])
        ;

        if (0 == $settings->count()) {
            // TODO: should we redirect the user to the default domain ?
            // by dooing this, we just pretend that the user is on .com
            $settings = DomainsSettingsQuery::create()
                ->joinWithDomains()
                ->findByDomainKey($default)
            ;
        }

        foreach ($settings as $record) {
            $this->settings[$record->getNs() . '.' . $record->getCKey()] = $record->getCValue();
        }

        if ($record) {
            $language = LanguagesQuery::create()->findOneByLocale($this->settings['core.locale']);
            $this->settings['core.language_id'] = $language->getId();

            $this->settings['core.domain_id'] = $record->getDomains()->getId();
            $this->settings['core.domain_key'] = $record->getDomainKey();

            // handle sales donains
            if ((count($this->domain_fragments) > 1) &&
                (substr($this->domain_fragments[0], 0, 4) == 'kons')
            ) {
                $this->settings['core.domain_key'] = 'Sales' . $this->settings['core.domain_key'];
            }
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

            $this->settings[$record->getNs().'.'.$record->getCKey()] = $data;
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
            return $this->settings;
        }

        if (isset($this->settings[$key])) {
            return $this->settings[$key];
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
        $settings = array();
        foreach ($this->settings as $key => $value) {
            if (strpos($key, $ns.'.') === 0) {
                $key = substr($key, strlen($ns) +1);
                $settings[$key] = $value;
            }
        }

        return $settings;
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

// # da
//         $domain = new Domains();
//         $domain->setDomainName('dk');
//         $domain->setDomainKey(strtoupper($domain->getDomainName()));

//         $setting = new DomainsSettings();
//         $setting->setDomainKey($domain->getDomainKey());
//         $setting->setNs('core');
//         $setting->setCKey('currency');
//         $setting->setCValue('DKK');
//         $domain->addDomainsSettings($setting);

//         $setting = new DomainsSettings();
//         $setting->setDomainKey($domain->getDomainKey());
//         $setting->setNs('core');
//         $setting->setCKey('country');
//         $setting->setCValue('dk');
//         $domain->addDomainsSettings($setting);

//         $setting = new DomainsSettings();
//         $setting->setDomainKey($domain->getDomainKey());
//         $setting->setNs('core');
//         $setting->setCKey('locale');
//         $setting->setCValue('da_DK');
//         $domain->addDomainsSettings($setting);

//         $setting = new DomainsSettings();
//         $setting->setDomainKey($domain->getDomainKey());
//         $setting->setNs('core');
//         $setting->setCKey('language');
//         $setting->setCValue('da');
//         $domain->addDomainsSettings($setting);
//         $domain->save();

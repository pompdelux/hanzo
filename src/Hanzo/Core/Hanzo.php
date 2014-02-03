<?php

namespace Hanzo\Core;

use Hanzo\Core\Tools;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Request;
use Predis\Network\ConnectionException;

use Hanzo\Model;
use Hanzo\Model\SettingsQuery;
use Hanzo\Model\LanguagesQuery;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\DomainsSettingsQuery;
use Hanzo\Model\DomainsSettingsPeer;

use Symfony\Component\Yaml\Yaml;

class Hanzo
{
    protected $domain;
    protected $settings    = array();
    protected $ns_settings = array();

    public $cache = null;
    public $container;

    protected static $hanzo;

    public static function initialize($container = NULL)
    {
        if (empty(self::$hanzo)) {
            if (empty($container)) {
                throw new \InvalidArgumentException('Service Container object needed !', 100);
            }
            self::$hanzo = new Hanzo($container);
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


    private function __construct($container)
    {
        $this->container               = $container;
        $this->kernel                  = $container->get('kernel');
        $this->settings['core']['env'] = $this->kernel->getEnvironment();
        $this->cache                   = $this->container->get('redis.main');

        if (('cli' !== PHP_SAPI) && empty($_SERVER['HTTP_SOAPACTION'])) {

            try {
                $this->cache = $this->container->get('redis.main');
            } catch (ConnectionException $e) {
                $event = new GetResponseForExceptionEvent($container->get('kernel'), new Request(), HttpKernelInterface::MASTER_REQUEST, $e);
                $container->get('event_dispatcher')->dispatch(KernelEvents::EXCEPTION, $event);
                return;
            }
        }

        if ($this->cache) {
            $cache_id = $this->cache->generateKey($this->kernel->getSetting('domain_key'),'core.settings');
            if ($cache = $this->cache->get($cache_id)) {
                $this->settings = $cache;
            }
        }

        if (empty($cache)) {
            $this->initSettings();
            $this->initDomain();

            if ($this->cache) {
                $this->cache->setex($cache_id, 3600, $this->settings);
            }
        }

        $scheme = 'http:';
        if (Tools::isSecure()) {
            $scheme = 'https:';
        }

        $config['core']['cdn'] = str_replace('http:', $scheme, $this->container->getParameter('cdn'));
        if ($this->container->hasParameter('cdn2')) {
            $config['core']['cdn2'] = str_replace('http:', $scheme, $this->container->getParameter('cdn2'));
        } else {
            $config['core']['cdn2'] = $config['core']['cdn'];
        }

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

        $locale = $this->get('core.locale');
        list($lang, ) = explode('_', $locale, 2);
        $this->container->get('twig')->addGlobal('locale', $locale);
        $this->container->get('twig')->addGlobal('html_lang', $lang);

        setLocale(LC_ALL, $locale.'.utf-8');

        // we piggybag on nl to show euros, even for none euro countries
        // note the locale has to be installed, and er need duch anyway, so...
        if ($this->get('core.currency') == 'EUR') {
            setlocale(LC_MONETARY, 'nl_NL.utf8');
        }
    }


    /**
     * initiate domain settings and locale setup
     */
    protected function initDomain()
    {
        $check = false;
        $settings = [];

        // unittest hack
        if (isset($_SERVER['_']) && 'phpunit' === substr($_SERVER['_'], -7)) {
            return;
        }

        // if parent domain exists (consultant sites), load the parent settings first.
        if ($this->kernel->getSetting('parent_domain_key')) {
            $check             = true;
            $parent_domain_key = $this->kernel->getSetting('parent_domain_key');
            $domain_key        = $this->kernel->getSetting('domain_key');

            $settings = DomainsSettingsQuery::create()
                ->leftJoinWithDomains()
                ->filterByDomainKey($parent_domain_key)
                ->_or()
                ->filterByDomainKey($domain_key)
                ->addAscendingOrderByColumn(sprintf("FIELD(%s, '%s', '%s')", DomainsSettingsPeer::DOMAIN_KEY, $parent_domain_key, $domain_key))
                ->find()
            ;
        } else {
            $settings = DomainsSettingsQuery::create()
                ->joinWithDomains()
                ->findByDomainKey($this->kernel->getSetting('domain_key'))
            ;
        }

        foreach ($settings as $record) {
            $this->settings[$record->getNs()][$record->getCKey()] = $record->getCValue();
        }

        if (isset($record)) {
            $language = LanguagesQuery::create()
                ->findOneByLocale($this->settings['core']['locale'])
            ;

            $this->settings['core']['language_id'] = $language->getId();
            $this->settings['core']['domain_id']   = $record->getDomains()->getId();
            $this->settings['core']['domain_key']  = $record->getDomains()->getDomainKey();
        }

        if ($check) {
            if ($this->settings['core']['domain_key'] != $this->kernel->getSetting('domain_key')) {
                $this->settings = array();
                error_log($this->kernel->getSetting('domain_key') . ' not configured !');
                die('woops, see error log.');
            }
        }
    }


    /**
     * initialize settings from the database
     */
    protected function initSettings()
    {
        // unittest hack
        if (isset($_SERVER['_']) && 'phpunit' === substr($_SERVER['_'], -7)) {
            return;
        }

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

<?php

namespace Hanzo\Core;

use \Hanzo\Model;
use \Hanzo\Model\SettingsQuery;
use \Hanzo\Model\DomainsSettingsQuery;


class Hanzo
{
    protected $domain;
    protected $settings = array();
    protected $tld;

    protected $cache;

    public function __construct($container, $default = 'com', array $dev_map = array(), $main_menu_thread = NULL)
    {
        $this->container = $container;
        $this->cache = new RedisCache($this->container);

        if ($cache = $this->cache->get($this->cache->id('core.settings'))) {
             $this->settings = $cache;
        }
        else {
            $this->initSettings();
            $this->initDomain($default, $dev_map);
            $this->cache->set($this->cache->id('core.settings'), $this->settings);
        }

        $session = $this->container->get('session');
        if ($session->getLocale() != $this->settings['core.locale']) {
            $session->setLocale($this->settings['core.locale']);
        }

        $this->settings['core.main_menu_thread'] = $main_menu_thread;
    }


    /**
     * initiate domain settings and locale setup
     *
     * @param string $default, the default domain to use
     * @param array $dev_map, optional array of locale tld's to map to "real" domains
     */
    protected function initDomain($default, array $dev_map = array())
    {
        $this->domain = $this->container->get('request')->getHost();
        $domain_fragments = explode('.', $this->domain);
        $this->tld = array_pop($domain_fragments);

        // translate locale dev tld's
        if (isset($dev_map[$this->tld])) {
            $this->tld = $dev_map[$this->tld];
        }

        $settings = DomainsSettingsQuery::create()->findByDomainKey($this->tld);
        if (0 == $settings->count()) {
            // TODO: should we redirect the user to the default domain ?
            // by dooing this, we just pretend that the user is on .com
            $result = DomainsSettingsQuery::create()->findByDomainKey($default);
        }

        foreach ($settings as $record) {
            $this->settings[$record->getNs() . '.' . $record->getCKey()] = $record->getCValue();
        }

        if ($record) {
            $this->settings['core.domain_key'] = $record->getDomainKey();

            // handle sales donains
            if ((count($domain_fragments) > 1) &&
                (substr($domain_fragments[0], 0, 4) == 'kons')
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
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }

        return $default;
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

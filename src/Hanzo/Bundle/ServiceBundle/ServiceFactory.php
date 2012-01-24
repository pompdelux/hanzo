<?php
namespace Hanzo\Bundle\ServiceBundle;

use Hanzo\Bundle\ServiceBundle\Services;
use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools;

use Hanzo\Model\SettingsQuery;

class ServiceFactory
{
    public function get($service, $parameters = NULL, $class = NULL)
    {
        // figure out where to load the class from.
        if (!empty($class)) {
            $service = $class;
            $settings_key = preg_replace('/service$/', '', strtolower(basename('/'.str_replace('\\', '/', $class))));
        }
        else {
            $settings_key = strtolower($service);

            $service = ucfirst($settings_key) . 'Service';
            $service =  __NAMESPACE__ . '\\Services\\' . $service;
        }

        // load settings from the database - if any
        $result = SettingsQuery::create()
            ->filterByNs($settings_key)
            ->find();

        $settings = array();
        foreach ($result as $record) {
            $settings[$record->getCKey()] = $record->getCValue();
        }

        foreach (Hanzo::getInstance()->get('ALL') as $key => $value) {
            list($ns, $key) = explode('.', $key);
            if ($ns == $settings_key) {
                $settings[$key] = $value;
            }
        }

        // return the instance
        return new $service($parameters, $settings);
    }
}

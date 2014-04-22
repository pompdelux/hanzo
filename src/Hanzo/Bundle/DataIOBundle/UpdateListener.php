<?php /* vim: set sw=4: */
namespace Hanzo\Bundle\DataIOBundle;

use Symfony\Component\EventDispatcher\Event,
    Symfony\Component\Yaml\Parser,
    Symfony\Component\Yaml\Dumper,
    Symfony\Component\Yaml\Exception\ParseException
    ;

use Hanzo\Bundle\DataIOBundle\FilterUpdateEvent,
    Hanzo\Model\Settings,
    Hanzo\Model\SettingsQuery
    ;

use Exception;

class UpdateListener
{
    public function onIncrementAssetsVersion(FilterUpdateEvent $event)
    {
        $assetsVersion = SettingsQuery::create()
            ->filterByNs('core')
            ->findOneByCKey('assets_version')
            ;

        if (is_null( $assetsVersion )) {
            $assetsVersion = new Settings();
            $assetsVersion->setCKey('assets_version')
                ->setNs('core')
                ->setTitle('Assets version')
            ;
        }

        $assetsVersion->setCValue(time());
        $assetsVersion->save();
    }

    /**
     * onUpdateTranslations
     *
     * This triggers Capifony's "deploy:translations" task
     * For more info about this, check deploy.rb
     **/
    public function onUpdateTranslations(FilterUpdateEvent $event)
    {
        chdir(__DIR__.'/../../../../');
        $command = 'cap deploy:translations';
        exec($command, $out, $return);
    }
}

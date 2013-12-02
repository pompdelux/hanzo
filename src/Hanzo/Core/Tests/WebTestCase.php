<?php

namespace Hanzo\Core\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    private static $application;

    public static function setUpBeforeClass()
    {
        \Propel::disableInstancePooling();

        self::runCommand('propel:build --insert-sql');
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient(['environment' => 'test_dk']);

            self::$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new \Symfony\Component\Console\Input\StringInput($command));
    }
}

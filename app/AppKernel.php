<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

use Hanzo\Core AS C;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Propel\PropelBundle\PropelBundle(),
            new Snc\RedisBundle\SncRedisBundle(),

            new Hanzo\Bundle\CMSBundle\HanzoCMSBundle(),
            new Hanzo\Bundle\WebServicesBundle\WebServicesBundle(),
            new Hanzo\Bundle\CategoryBundle\HanzoCategoryBundle(),
            new Hanzo\Bundle\MannequinBundle\HanzoMannequinBundle(),
            new Hanzo\Bundle\NewsletterBundle\HanzoNewsletterBundle(),
            new Hanzo\Bundle\ProductBundle\HanzoProductBundle(),
            new Hanzo\Bundle\SearchBundle\HanzoSearchBundle(),
            new Hanzo\Bundle\BasketBundle\BasketBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function boot()
    {
        parent::boot();
        $twig = $this->container->get('twig'); // ->addGlobal('', '');
        $twig->addExtension(new Twig_Extension_Optimizer());

        // TODO: figure out if this is good or bad..
        if ('cli' !== php_sapi_name()) {
            $hanzo = C\Hanzo::initialize($this->container, $this->getEnvironment());
            $this->container->get('translator')->setLocale($hanzo->get('core.locale'));
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}


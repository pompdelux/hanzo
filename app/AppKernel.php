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
            new SimpleThings\FormExtraBundle\SimpleThingsFormExtraBundle(),

            new Hanzo\Bundle\CMSBundle\HanzoCMSBundle(),
            new Hanzo\Bundle\WebServicesBundle\WebServicesBundle(),
            new Hanzo\Bundle\CategoryBundle\HanzoCategoryBundle(),
            new Hanzo\Bundle\MannequinBundle\HanzoMannequinBundle(),
            new Hanzo\Bundle\ProductBundle\HanzoProductBundle(),
            new Hanzo\Bundle\SearchBundle\HanzoSearchBundle(),
            new Hanzo\Bundle\BasketBundle\BasketBundle(),
            new Hanzo\Bundle\PaymentBundle\PaymentBundle(),
            new Hanzo\Bundle\NewsletterBundle\NewsletterBundle(),
            new Hanzo\Bundle\CheckoutBundle\CheckoutBundle(),
            new Hanzo\Bundle\AccountBundle\AccountBundle(),
            new Hanzo\Bundle\ServiceBundle\ServiceBundle(),
            new Hanzo\Bundle\ShippingBundle\ShippingBundle(),
            new Hanzo\Bundle\DataIOBundle\DataIOBundle(),
            new Hanzo\Bundle\AdminBundle\AdminBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function boot()
    {
        parent::boot();
        // TODO: figure out if this is good or bad..
        if ('cli' !== php_sapi_name()) {
            $hanzo = C\Hanzo::initialize($this->container, $this->getEnvironment());
            $this->container->get('translator')->setLocale($hanzo->get('core.locale'));
        }

        $twig = $this->container->get('twig'); // ->addGlobal('', '');

        //$twig->addGlobal('layout', $this->container->get('request')->attributes->get('_x_device', 'pc').'.base.html.twig');
        $twig->addGlobal('store_mode', self::getStoreMode());
        $twig->addExtension(new Twig_Extension_Optimizer());
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // first we load "store mode" configs
        $loader->load(__DIR__.'/config/config_ws_'.self::getStoreMode().'.yml');
        // then we load env configs, these should always be loaded last.
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }


    protected static function getStoreMode()
    {
        $mode = 'webshop';
        // use strpos to capture test.kons and friends
        if (isset($_SERVER['HTTP_HOST']) && (false !== strpos($_SERVER['HTTP_HOST'], 'kons'))) {
            $mode = 'consultant';
        }

        return $mode;
    }
}


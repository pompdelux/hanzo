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
            new Bazinga\ExposeTranslationBundle\BazingaExposeTranslationBundle(),

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

        if (preg_match('/^(test|dev)_/', $this->getEnvironment())) {
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
        $twig->addGlobal('store_mode', $this->getStoreMode());
        $twig->addExtension(new Twig_Extension_Optimizer());
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // first we load "store mode" configs
        $loader->load(__DIR__.'/config/config_ws_'.$this->getStoreMode().'.yml');

        // then we load env configs, these should always be loaded last.
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }


    protected function getStoreMode()
    {
        $mode = 'webshop';

        // use strpos to capture test.kons and friends
        if (isset($_SERVER['HTTP_HOST']) && (false !== strpos($_SERVER['HTTP_HOST'], 'kons'))) {
            $mode = 'consultant';
        }

        return $mode;
    }

    public function humanReadableSize($size)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }


    /**
     * allow us to override cache dir for the consultants section
     *
     * @see http://blog.amalraghav.com/manipulating-config-files/
     * @return string
     */
    public function getCacheDir()
    {
        if (self::getStoreMode() == 'consultant') {
            $cacheDir = $this->rootDir.'/cache/consultant/'.$this->environment;
        } else {
            $cacheDir = $this->rootDir.'/cache/'.$this->environment;
        }

        return $cacheDir;
    }
}


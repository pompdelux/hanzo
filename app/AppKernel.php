<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core AS C;

class AppKernel extends Kernel
{
    protected $terminate_events = array();
    protected $settings = array();

    public $locale;

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
            new Hanzo\Bundle\WallBundle\WallBundle(),
            new Hanzo\Bundle\EventsBundle\EventsBundle(),
            new Hanzo\Bundle\QuickOrderBundle\QuickOrderBundle(),
            new Hanzo\Bundle\ConsultantNewsletterBundle\ConsultantNewsletterBundle(),
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

        $store_mode = $this->getStoreMode();
        list($dir, $lang,) = explode('_', $this->getEnvironment());

        $domain_key = strtoupper($lang);
        if ('consultant' == $store_mode) {
            $this->setSetting('parent_domain_key', $domain_key);
            $domain_key = 'Sales'.$domain_key;
        }

        $this->setSetting('store_mode', $store_mode);
        $this->setSetting('domain_key', $domain_key);

        $twig = $this->container->get('twig');
        $twig->addGlobal('cdn', $this->container->getParameter('cdn'));

        if (isset($_SERVER['HTTP_HOST'])) {
            $twig->addGlobal('baseurl', 'http://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] == 80 ? '' : $_SERVER['SERVER_PORT']).$_SERVER['SCRIPT_NAME']);
        }

        $twig_vars = $this->container->getParameter('hanzo_cms.twig');
        if (count($twig_vars)) {
            foreach ($twig_vars as $key => $value) {
                $twig->addGlobal($key, $value);
            }
        }

        //$twig->addGlobal('layout', $this->container->get('request')->attributes->get('_x_device', 'pc').'.base.html.twig');
        $twig->addGlobal('store_mode', $store_mode);
        $twig->addExtension(new Twig_Extension_Optimizer());

        if (preg_match('/^(test|dev)_/', $this->getEnvironment())) {
            $twig->addExtension(new Twig_Extension_Debug());
        }
    }


    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        list($dir, $lang,) = explode('_', $this->getEnvironment());

        $base_dir = __DIR__.'/config/';
        $config_dir = $base_dir.$dir.'/';

        $mode = $this->getStoreMode();

        $loader->load($base_dir.'firewall_'.$mode.'.yml');
        $loader->load($config_dir.'config.yml');
        $loader->load($config_dir.'_'.$lang.'.yml');

        if ('consultant' == $mode) {
            $file = $config_dir.'_consultant.yml';
            $loader->load($file);

            $file = $config_dir.'_'.$lang.'_consultant.yml';
            if (is_file($file)) {
                $loader->load($file);
            }
        }
    }


    public function getStoreMode()
    {
        $mode = 'webshop';

        // use strpos to capture test.kons and friends
        if (isset($_SERVER['HTTP_HOST']) && (false !== strpos($_SERVER['HTTP_HOST'], 'c.'))) {
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
        return parent::getCacheDir();
    }


    /**
     * let us hande stuff after connection to client is closed.
     *
     * @param string $event      event key
     * @param mixed  $parameters parameters to send to the event
     */
    public function setTerminateEvent($event, $parameters)
    {
        $this->terminate_events[$key] = $parameters;
    }


    /**
     * wrap kernel->send() method to allow us to handle onClose events
     *
     * @param  Response $handle Response object
     */
    public function terminate(Response $handle)
    {
        if (count($this->terminate_events)) {

            // add close headers.
            $handle->headers->add(array(
                'Content-Length' => mb_strlen($handle->getContent()),
                'Connection' => 'close',
            ));
            $handle->send();

            while (ob_get_length()) {
                ob_end_flush();
            }

            ignore_user_abort(true);
            flush();

            // connection should be closed, let's fire up the events
            $dispatcher = $this->container->get('event_dispatcher');
            foreach ($this->terminate_events as $event => $parameters) {
                $dispatcher->dispatch($event, $parameters);
            }

            return;
        }

        $handle->send();
    }

    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function getSetting($key, $default = null)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
}


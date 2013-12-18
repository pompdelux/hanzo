<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core AS C;
use Hanzo\Core\Tools;

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
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),

            new Propel\PropelBundle\PropelBundle(),
            new Bazinga\ExposeTranslationBundle\BazingaExposeTranslationBundle(),
            new Ekino\Bundle\NewRelicBundle\EkinoNewRelicBundle(),
            new Liip\ThemeBundle\LiipThemeBundle(),
            new Nelmio\SecurityBundle\NelmioSecurityBundle(),
            new Misd\GuzzleBundle\MisdGuzzleBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),

            new Hanzo\Bundle\AccountBundle\AccountBundle(),
            new Hanzo\Bundle\AdminBundle\AdminBundle(),
            new Hanzo\Bundle\BasketBundle\BasketBundle(),
            new Hanzo\Bundle\CMSBundle\CMSBundle(),
            new Hanzo\Bundle\CategoryBundle\CategoryBundle(),
            new Hanzo\Bundle\CheckoutBundle\CheckoutBundle(),
            new Hanzo\Bundle\ConsultantNewsletterBundle\ConsultantNewsletterBundle(),
            new Hanzo\Bundle\DataIOBundle\DataIOBundle(),
            new Hanzo\Bundle\DiscountBundle\DiscountBundle(),
            new Hanzo\Bundle\EventsBundle\EventsBundle(),
            new Hanzo\Bundle\MannequinBundle\MannequinBundle(),
            new Hanzo\Bundle\NewsletterBundle\NewsletterBundle(),
            new Hanzo\Bundle\PaymentBundle\PaymentBundle(),
            new Hanzo\Bundle\ProductBundle\ProductBundle(),
            new Hanzo\Bundle\QuickOrderBundle\QuickOrderBundle(),
            new Hanzo\Bundle\SearchBundle\SearchBundle(),
            new Hanzo\Bundle\ServiceBundle\ServiceBundle(),
            new Hanzo\Bundle\ShippingBundle\ShippingBundle(),
            new Hanzo\Bundle\WallBundle\WallBundle(),
            new Hanzo\Bundle\WebServicesBundle\WebServicesBundle(),
            new Hanzo\Bundle\VarnishBundle\VarnishBundle(),
            new Hanzo\Bundle\RedisBundle\RedisBundle(),
            new Hanzo\Bundle\LocationLocatorBundle\LocationLocatorBundle(),
            new Hanzo\Bundle\MunerisBundle\MunerisBundle(),
            new Hanzo\Bundle\AxBundle\AxBundle(),
            new Hanzo\Bundle\RetargetingBundle\RetargetingBundle(),
            new Hanzo\Bundle\RMABundle\RMABundle(),
            new Hanzo\Bundle\GoogleBundle\GoogleBundle(),
        );

        if (preg_match('/^(test|dev)_/', $this->getEnvironment())) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }


    /**
     * hook into getEnvironment to allow us a cleaner way to do cli work in dev.
     *
     * @return string
     */
    public function getEnvironment()
    {
        $env = parent::getEnvironment();
        if (false === strpos($env, '_')) {
            $env = $env.'_dk';
        }

        return $env;
    }

    /**
     * handle kernel boot
     */
    public function boot()
    {
        parent::boot();

        $store_mode = $this->getStoreMode();
        list($dir, $lang,) = explode('_', $this->getEnvironment());

        $domain_key = strtoupper($lang);
        if (('consultant' == $store_mode) || ( isset($_COOKIE['__ice']) && strtoupper( $_COOKIE['__ice'] ) == 'SALES' )) {
            $this->setSetting('parent_domain_key', $domain_key);
            $domain_key = 'Sales'.$domain_key;
        }

        $this->setSetting('store_mode', $store_mode);
        $this->setSetting('domain_key', $domain_key);

        $scheme = 'http';
        if (Tools::isSecure()) {
            $scheme = 'https';
        }

        $twig = $this->container->get('twig');
        $twig->addGlobal('cdn', str_replace('http', $scheme, $this->container->getParameter('cdn')));

        $theme = $this->container->get('liip_theme.active_theme');
        $twig->addGlobal('current_theme', $theme->getName());

        if (isset($_SERVER['HTTP_HOST'])) {
            $script = $_SERVER['SCRIPT_NAME'];
            if ('/app.php' == $script) {
                $script = '';
            }

            $twig->addGlobal('baseurl', $scheme.'://'.$_SERVER['HTTP_HOST'].$script);
        }

        $twig_vars = $this->container->getParameter('cms.twig');
        if (count($twig_vars)) {
            foreach ($twig_vars as $key => $value) {
                $twig->addGlobal($key, $value);
            }
        }

        $twig->addGlobal('store_mode', $store_mode);
        $twig->addExtension(new Twig_Extension_Optimizer());

        if (preg_match('/^(test|dev)_/', $this->getEnvironment())) {
            $twig->addExtension(new Twig_Extension_Debug());
        }
    }


    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        list($env, $lang,) = explode('_', $this->getEnvironment());

        $base_dir = __DIR__.'/config/';
        $config_dir = $base_dir.'/env/'.$env.'/';

        $mode = $this->getStoreMode();

        $loader->load($base_dir.'parameters.ini');
        $loader->load($base_dir.'parameters.php');
        $loader->load($base_dir.'firewall_'.$mode.'.yml');
        $loader->load($base_dir.'config.yml');
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

    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function getSetting($key, $default = null)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
}


<?php

namespace Hanzo\Bundle\LocationLocatorBundle;

use Hanzo\Core\hanzo;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class LocationLocator
{
    /**
     * Translator instance
     *
     * @var object
     */
    protected $translator;

    /**
     * locator settings
     *
     * @var array
     */
    protected $settings;

    /**
     * __construct
     *
     * @param string     $settings
     * @param Translator $translator
     */
    public function __construct($settings, Translator $translator)
    {
        $this->settings = $settings;
        $this->translator = $translator;
    }

    /**
     * Provider wrapper
     *
     * @param  string $name      Name of the method
     * @param  array  $arguments Method arguments
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        $settings = Hanzo::getInstance()->getByNs('locator');
    }
}

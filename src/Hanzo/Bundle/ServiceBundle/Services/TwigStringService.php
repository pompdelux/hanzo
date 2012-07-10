<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Symfony\Bundle\TwigBundle\TwigEngine;

use Hanzo\Core\Tools;

class TwigStringService
{
    protected $twig;
    protected $settings;
    protected $path;

    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof TwigEngine) {
            throw new \InvalidArgumentException('TwigEngine instance required.');
        }

        $this->twig = $parameters[0];
        $this->settings = $settings;

        $this->path = __DIR__ . '/../Resources/views/TS/';
    }

    /**
     * Parse a twig string template
     *
     * @param string $template the string to parse
     * @param array $parameters, array of parameters parsed to the template
     * @return string
     */
    public function parse($data, $parameters = array())
    {
        $template = md5($data).'.html.twig';

        $file = $this->path.$template;
        // file_put_contents($file, $data);

        // // cache ? if we do, we need some sort of cleanup thingy
        if (!file_exists($file)) {
            file_put_contents($file, $data);
        } else {
            touch($file);
        }

        $html = $this->twig->render('ServiceBundle:TS:'.$template, $parameters);
        //unlink($file);

        return $html;
    }
}

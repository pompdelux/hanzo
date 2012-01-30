<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use \Twig_Environment;
use \Twig_Loader_String;

class TwigStringService
{
    protected $twig;
    protected $settings;
    protected $loader;
    protected $overridden = FALSE;
    protected $auto_end = TRUE;

    public function __construct($parameters, $settings)
    {
        if (!$parameters[0] instanceof Twig_Environment) {
            throw new \InvalidArgumentException('TwigEngine instance required.');
        }

        $this->twig = $parameters[0];
        $this->loader = $this->twig->getLoader();
        $this->settings = $settings;
    }

    /**
     * Parse a twig string template
     *
     * @param string $template the string to parse
     * @param mixed $parameters array, string or object parsed to the template
     * @return string
     */
    public function parse($template, $parameters = NULL)
    {
        if (FALSE === $this->overridden) {
            $this->begin();
        }

        $result = $this->twig->render($template, $parameters);

        if ($this->auto_end) {
            $this->end();
        }

        return $result;
    }

    /**
     * Start a transaction, use in loops to save loader switching
     */
    public function startTransaction()
    {
        $this->auto_end = FALSE;
        $this->begin();
    }

    /**
     * End a transaction.
     *
     * Note: failing to end the transaction will mess up the rest of the request flow.
     */
    public function endTransaction()
    {
        $this->auto_end = TRUE;
        $this->end();
    }

    /**
     * We override the template loader so we can load strings from the database.
     */
    protected function begin()
    {
        $this->twig->setLoader(new Twig_Loader_String());
        $this->overridden = TRUE;
    }

    /**
     * End a twig string transaction
     * Reset the loader to it's default.
     */
    protected function end()
    {
        $this->twig->setLoader($this->loader);
        $this->overridden = FALSE;
    }
}

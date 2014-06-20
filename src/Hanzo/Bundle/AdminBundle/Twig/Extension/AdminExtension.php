<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AdminBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class AdminExtension extends \Twig_Extension
{
    private $locale;
    private $router;

    public function __construct($locale, Router $router)
    {
        $this->locale = $locale;
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin';
    }

    public function getFunctions()
    {
        return [
            'cms_admin_link' => new \Twig_Function_Method($this, 'cms_admin_link'),
        ];
    }

    public function cms_admin_link($route, $parameters)
    {
        // FIXME: we need to address all these locale/db/xxx fixes soon !!
        $db = 'pdldb'.strtolower(substr($this->locale, -2)).'1';
        if ('da_DK' === $this->locale) {
            $db = 'default';
        }

        $parameters['_locale'] = 'da_DK';
        return $this->router->generate('admin_database', [
            'goto'    => $this->router->generate($route, $parameters),
            'name'    => $db,
            '_locale' => 'da_DK'
        ]);
    }
}

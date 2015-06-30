<?php

namespace Hanzo\Twig\Extension;

use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;
use Liip\ThemeBundle\ActiveTheme;

use Twig_Environment;
use Twig_Extension;
use Twig_Function_Method;
use Twig_Function_Function;
use Twig_Filter_Method;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

/**
 * Class: MiscExtension
 *
 */
class CookieNoticeExtension extends Twig_Extension
{
    protected $twigString;
    protected $theme;
    protected $settings;

    /**
     * __construct
     *
     * @param TwigStringService $twigString
     * @param ActiveTheme       $theme
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function __construct(TwigStringService $twigString, ActiveTheme $theme)
    {
        $this->twigString = $twigString;
        $this->theme = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cookie_notice';
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'cookie_notice' => new Twig_Function_Method($this, 'cookieNotice', array('pre_escape' => 'html', 'is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    /**
     * Outputs html for cookie notice
     *
     * @param  Twig_Environment $env
     * @return string
     */
    public function cookieNotice(Twig_Environment $env)
    {
        $hanzo      = Hanzo::getInstance();
        $container  = $hanzo->container;
        $locale     = $hanzo->get('core.locale');
        $translator = $container->get('translator');

        $language = substr($locale, 0, 2);
        $text     = $translator->trans('cookie_notice.fdih.text', [], 'js');
        $link     = $translator->trans('cookie_notice.fdih.link', [], 'js');

        $html = '';

        // Default danish settings
        $parameters = [
            'memberid'   => 11241,
            'language'   => $language,
            'design'     => 'white',
            'position'   => 'leftbottom',
            'margin'     => 100,
            'link'       => $link,
            'text'       => $text,
            'newwindow'  => 'false',
            'cookiedays' => 30,
            'hidesecs'   => 10,
        ];

        // Override danish defaults, in switch so they can be changed later
        switch ($language)
        {
            case 'nl':
            case 'no':
            case 'fi':
            case 'en':
            case 'sv':
            case 'de':
                $parameters['memberid']   = 10750;
                $parameters['cookiedays'] = 14;
                break;
        }

        // Some sites use english for this message
        switch ($language)
        {
            case 'nl':
            case 'no':
            case 'fi':
                $parameters['language'] = 'en';
                break;
        }

        $template = 'TwigBundle:CookieNotice:fdih.html.twig';

        try {
            $html = $env->render($template, $parameters);
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }

        return $html;
    }
}

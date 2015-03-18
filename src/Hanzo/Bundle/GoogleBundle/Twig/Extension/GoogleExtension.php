<?php

namespace Hanzo\Bundle\GoogleBundle\Twig\Extension;

class GoogleExtension extends \Twig_Extension
{
    /**
     * @param array  $site_verification
     */
    public function __construct(array $site_verification)
    {
        $this->site_verification  = $site_verification;
    }

    /**
     * @inherit
     */
    public function getName()
    {
        return 'google';
    }

    public function setGoogleTagManager($service)
    {
        $this->gtm = $service;
    }

    /**
     * @inherit
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('google_site_verification_tag', [$this, 'getSiteVerificationTag'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
            new \Twig_SimpleFunction('google_tag_manager', [$this, 'getGoogleTagManager'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    /**
     * @return string
     */
    public function getSiteVerificationTag()
    {
        if (empty($this->site_verification)) {
            return '';
        }

        $html = '';
        foreach ($this->site_verification as $code) {
            $html .= '<meta name="google-site-verification" content="'.$code.'">'."\n";
        }

        return $html;
    }

    /**
     * getGoogleTagManager
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getGoogleTagManager($context)
    {
        $html = '';

        /**
         * Good stuff in $context
         * - locale / html_lang
         * - domain_key
         * - is_mobile_layout
         */
        $this->gtm->setContext($context);
        $this->gtm->setPageType($context['page_type']);
        $html = $this->gtm->getHtml();

        return $html;
    }
}

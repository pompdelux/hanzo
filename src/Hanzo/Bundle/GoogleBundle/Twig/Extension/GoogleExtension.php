<?php

namespace Hanzo\Bundle\GoogleBundle\Twig\Extension;

class GoogleExtension extends \Twig_Extension
{
    /**
     * @param string $analytics_code
     * @param string $conversion_id
     * @param array  $site_verification
     * @param string $google_tag_manager_id
     */
    public function __construct($analytics_code, $conversion_id, array $site_verification, $google_tag_manager_id)
    {
        $this->analytics_code        = $analytics_code;
        $this->conversion_id         = $conversion_id;
        $this->site_verification     = $site_verification;
        $this->google_tag_manager_id = $google_tag_manager_id;
    }

    /**
     * @inherit
     */
    public function getName()
    {
        return 'google';
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

        $gtm = $this->get('google.');
        $gtm->setContext($context);
        $gtm->setPageType('page_type');
        $html = $gtm->getHtml();

        return $html;
    }
}

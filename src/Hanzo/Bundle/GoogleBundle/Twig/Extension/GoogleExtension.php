<?php

namespace Hanzo\Bundle\GoogleBundle\Twig\Extension;

class GoogleExtension extends \Twig_Extension
{
    /**
     * @param string $analytics_code
     * @param string $conversion_id
     * @param array  $site_verification
     */
    public function __construct($analytics_code, $conversion_id, array $site_verification)
    {
        $this->analytics_code    = $analytics_code;
        $this->conversion_id     = $conversion_id;
        $this->site_verification = $site_verification;
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
            new \Twig_SimpleFunction('google_tags', [$this, 'getAllTags'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
            new \Twig_SimpleFunction('google_conversion_tag', [$this, 'getConversionTag'], ['needs_context' => true]),
            new \Twig_SimpleFunction('google_analytics_tag', [$this, 'getAnalyticsTag'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
            new \Twig_SimpleFunction('google_site_verification_tag', [$this, 'getSiteVerificationTag'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
        ];
    }


    /**
     * Google analytics tag, will only be displayed if a key is found
     *
     * @param array $context
     * @return string
     */
    public function getAnalyticsTag($context)
    {
        if (empty($this->analytics_code)) {
            return '';
        }

        $ecommerce = '';

        $context['page_type'] = empty($context['page_type'])
            ? ''
            : $context['page_type']
        ;

        /**
         * if we are on the checkout success page,
         * we will inject analytics/commerce tracking
         */
        if ('checkout-success' == $context['page_type']) {
            $order = $context['order'];
            $ecommerce .= "
_gaq.push(['_addTrans',
  '{$order['id']}',
  '{$order['store_name']}',
  '{$order['total']}',
  '{$order['tax']}',
  '{$order['shipping']}',
  '{$order['city']}',
  '{$order['state']}',
  '{$order['country']}'
]);
";
            foreach ($order['lines'] as $line) {
                $ecommerce .= "
_gaq.push(['_addItem',
  '{$order['id']}',
  '{$line['sku']}',
  '{$line['name']}',
  '{$line['variation']}',
  '{$line['price']}',
  '{$line['quantity']}'
]);
";
            }

            $ecommerce .= "
_gaq.push(['_trackTrans']);
";
        }

        $output = <<<DOC
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '{$this->analytics_code}']);
_gaq.push(['_trackPageview']);
{$ecommerce}
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
DOC;

        return $output;
    }


    /**
     * @return string
     */
    public function getConversionTag()
    {
        if (empty($this->conversion_id)) {
            return '';
        }

        $html = <<<DOC
<script type="text/javascript">
    var google_conversion_id = {$this->conversion_id};
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/{$this->conversion_id}/?value=0&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
DOC;

        return $html;
    }


    /**
     * @return string
     */
    public function getSiteVerificationTag()
    {
        if (empty($this->site_verification)) {
            return '';
        }

        return '<meta name="google-site-verification" content="'.$this->site_verification.'">';
    }


    /**
     * Combines Conversion and tracking tags.
     *
     * @param  array $context
     * @return string
     */
    public function getAllTags($context)
    {
        $out = '';
        $out .= $this->getAnalyticsTag($context);
        $out .= $this->getConversionTag();

        return $out;
    }
}

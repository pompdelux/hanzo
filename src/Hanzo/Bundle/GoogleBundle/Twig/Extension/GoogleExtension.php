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
     * @param $name
     * @param $service
     */
    public function serviceInjection($name, $service)
    {
        $this->$name = $service;
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
            new \Twig_SimpleFunction('google_conversion_tag', [$this, 'getConversionTag'], ['needs_context' => true, 'pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('google_addwords_conversion_tag', [$this, 'getAddWordsConversionTag'], ['needs_context' => true, 'pre_escape' => 'html', 'is_safe' => ['html']]),
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
ga('require', 'ecommerce');
ga('ecommerce:addTransaction', {
    'id'          : '{$order['id']}',
    'affiliation' : '{$order['store_name']}'',
    'revenue'     : '{$order['total']}',
    'shipping'    : '{$order['shipping']}',
    'tax'         : '{$order['tax']}',
    'currency'    : '{$order['currency']}'
});
";
            foreach ($order['lines'] as $line) {
                $ecommerce .= "
ga('ecommerce:addItem', {
    'id'       : '{$order['id']}',
    'name'     : '{$line['name']}',
    'sku'      : '{$line['sku']}',
    'category' : '{$line['variation']}',
    'price'    : '{$line['price']}',
    'quantity' : '{$line['quantity']}'
});
";
            }

            $ecommerce .= "
ga('ecommerce:send');
";
        }

        $output = <<<DOC
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '{$this->analytics_code}', 'auto');
ga('send', 'pageview');

{$ecommerce}
</script>
DOC;

        return $output;
    }


    /**
     * @param $context
     * @param $params
     * @return string
     */
    public function getAddWordsConversionTag($context, $params)
    {
        if (('checkout-success' !== $context['page_type']) || empty($this->addwords_conversion)) {
            return '';
        }

        /** @var \Hanzo\Bundle\GoogleBundle\Services\AddWords\AddWordsConversion $addwords */
        $addwords = $this->addwords_conversion;

        foreach ($params as $key => $value) {
            $addwords->setParameter($key, $value);
        }

        return $addwords->getHtml();
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

        $html = '';
        foreach ($this->site_verification as $code) {
            $html .= '<meta name="google-site-verification" content="'.$code.'">'."\n";
        }

        return $html;
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

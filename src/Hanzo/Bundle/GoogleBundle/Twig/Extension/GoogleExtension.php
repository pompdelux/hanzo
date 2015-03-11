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
            new \Twig_SimpleFunction('google_tag_manager', [$this, 'getGoogleTagManagerTag'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
            new \Twig_SimpleFunction('google_data_layer', [$this, 'getGoogleDataLayer'], ['pre_escape' => 'html', 'is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    /**
     * Google analytics tag, will only be displayed if a key is found
     *
     * @DEPRECATED Google Tag Manager loads this script
     *
     * @param array $context
     * @return string
     */
    public function getAnalyticsTag($context)
    {
        if (empty($this->analytics_code)) {
            return '';
        }

        $output = <<<DOC
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '{$this->analytics_code}', 'auto');
ga('require', 'displayfeatures');
ga('send', 'pageview');
</script>
DOC;

        return $output;
    }

    /**
     * @param $context
     * @param $params
     * @return string
     */
    public function getEcommerceCode($context)
    {
        $ecommerce = '';

        $context['page_type'] = empty($context['page_type'])
            ? ''
            : $context['page_type']
            ;

        /**
         * If we are on the checkout success page we will inject e-commerce tracking
         */
        if ('checkout-success' == $context['page_type']) {
            $order = $context['order'];
            $ecommerce .= "
ga('require', 'ecommerce');
ga('ecommerce:addTransaction', {
    'id'          : '{$order['id']}',
    'affiliation' : '{$order['store_name']}',
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

            $ecommerce = '<script>$( document ).ready(function() {'.
                $ecommerce.'
});</script>';
        }

        return $ecommerce;
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
        // $out .= $this->getAnalyticsTag($context);
        $out .= $this->getConversionTag();
        // $out .= $this->getEcommerceCode($context);

        return $out;
    }

    /**
     * getGoogleTagManagerTag
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function getGoogleTagManagerTag($context)
    {
        if (empty($this->google_tag_manager_id)) {
            return '';
        }

        $html = <<<DOC
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id={$this->google_tag_manager_id}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer',"{$this->google_tag_manager_id}");</script>
<!-- End Google Tag Manager -->
DOC;

        return $html;
    }

    /**
     * @param mixed $context
     * - Generates dataLayer for Google Tag Manager
     * https://developers.google.com/tag-manager/devguide#datalayer
     * https://support.google.com/tagmanager/answer/6107169?hl=en
     * https://support.google.com/ds/answer/6026116#JSON-format
     * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
     *
     * @return string
     */
    public function getGoogleDataLayer($context)
    {
        $html = '';
        $dataLayer = [];

        if ('checkout-success' == $context['page_type']) {
            $order = $context['order'];

            $purchase = [];
            $purchase['actionField'] = [
                'id'         =>  $order['id'],
                'affiliation'=>  $order['store_name'],
                'revenue'    =>  $order['total'],
                'shipping'   =>  $order['shipping'],
                'tax'        =>  $order['tax'],
                'currency'   =>  $order['currency'],
                ];

            $purchase['products'] = [];
            foreach ($order['lines'] as $line) {
                $product = [
                    'id'       => $order['id'],
                    'name'     => $line['name'],
                    'sku'      => $line['sku'],
                    'category' => $line['variation'],
                    'price'    => $line['price'],
                    'quantity' => $line['quantity'],
                    ];

                $purchase['products'][] = $product;
            }

            $dataLayer[] = ['ecommerce' => [ 'purchase' => $purchase ]];
        }

        if (!empty($dataLayer)) {
            error_log(__LINE__.':'.__FILE__.' '.print_r($dataLayer, 1)); // hf@bellcom.dk debugging
            $html = '<script>dataLayer = '.json_encode($dataLayer).'</script>';
        }

        return $html;
    }
}

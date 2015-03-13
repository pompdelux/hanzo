<?php

namespace Hanzo\Bundle\GoogleBundle\Services;

use Hanzo\Bundle\GoogleBundle\DataLayer\Ecommerce,
    Hanzo\Bundle\GoogleBundle\DataLayer\Remarketing
    ;

class GoogleTagManager
{
    protected $gtm_id = '';

    protected $context = [];

    protected $page_type = '';

    protected $data_layer = [];

    public function __construct($gtm_id)
    {
        $this->gtm_id = $gtm_id;
    }

    public function setContext(Array $context = [])
    {
        $this->context = $context;
    }

    /**
     * setPageType
     *
     * @param string $page_type
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function setPageType($page_type = '')
    {
        $this->page_type = $page_type;
    }

    /**
     * extractDataLayers
     * https://developers.google.com/tag-manager/devguide#datalayer
     * https://support.google.com/tagmanager/answer/6107169?hl=en
     * https://support.google.com/ds/answer/6026116#JSON-format
     * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
     *
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function extractDataLayers()
    {
    }

    /**
     * getHtml
     *
     * @return string
     * @author Henrik Farre <hf@bellcom.dk>
     */
    public function getHtml()
    {
        if (empty($this->gtm_id)) {
            return '';
        }

        $this->extractDataLayers();

        $html = '';

        if (!empty($this->data_layer))
        {
            $html .= '<script>dataLayer = '.json_encode($dataLayer).'</script>';
        }

        $html .= <<<DOC
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id={$this->gtm_id}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer',"{$this->gtm_id}");</script>
<!-- End Google Tag Manager -->
DOC;

        return $html;
    }
}

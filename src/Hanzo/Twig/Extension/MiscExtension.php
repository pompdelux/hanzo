<?php

namespace Hanzo\Twig\Extension;

use Hanzo\Bundle\ServiceBundle\Services\TwigStringService;

use Twig_Extension;
use Twig_Function_Method;
use Twig_Function_Function;
use Twig_Filter_Method;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsI18nQuery;

class MiscExtension extends Twig_Extension
{
    protected $twig_string;

    public function __construct(TwigStringService $twig_string)
    {
        $this->twig_string = $twig_string;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'misc';
    }


    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array(
            'layout' => $this->getLayout(),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'print_r' => new Twig_Function_Function('print_r'),
            'parse' => new Twig_Function_Method($this, 'parse', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'meta_tags' => new Twig_Function_Method($this, 'metaTags', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'google_analytics_tag' => new Twig_Function_Method($this, 'googleAnalyticsTag', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'front_page_teasers' => new Twig_Function_Method($this, 'frontPageTeasers', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
            'money' => new Twig_Filter_Method($this, 'moneyFormat'),
        );
    }


    /**
     * Set the global twig var "layout"
     *
     * @see Hanzo\Core\HanzoBoot::onKernelRequest()
     * @return string
     */
    public function getLayout()
    {
        $hanzo = Hanzo::getInstance();
        $device = $hanzo->container->get('request')->attributes->get('_x_device');

        // TODO: implement the mobile layout
        $device_map = array(
            'pc' => '::base.html.twig',
            // 'mobile' => '::base_mobile.html.twig',
        );

        return isset($device_map[$device]) ? $device_map[$device] : $device_map['pc'];
    }


    /**
     * @see Hanzo\Core\Tools\Tools::moneyFormat
     * TODO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function moneyFormat($number, $format = '%i')
    {
        return Tools::moneyFormat($number, $format);
    }


    /**
     * Returns a "template string" parsed by Twig_String
     *
     * @param string $string
     * @param array $parameters
     * @return string
     */
    public function parse($string, $parameters = array())
    {
        return $this->twig_string->parse($string, $parameters);
    }


    /**
     * Returns any meta data associated with this domain.
     *
     * @return string
     */
     public function metaTags()
     {
         $meta = Hanzo::getInstance()->getByNs('meta');

         $result = '';
         foreach ($meta as $key => $value) {
             $attr = 'name';
             if (0 === strpos($key, 'og:')) {
                 $attr = 'property';
             }
             $result .= '<meta '.$attr.'="'.$key.'" content="'.$value.'">'."\n";
         }

         return $result;
     }


     /**
      * Google analytics tag, will only be displayed if a key is found
      */
     public function googleAnalyticsTag()
     {
            $google = Hanzo::getInstance()->getByNs('google');
            if (!empty($google['analytics_id'])) {
        return <<<DOC
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '{$google['analytics_id']}']);
_gaq.push(['_trackPageview']);
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
DOC;
        }

        return '';
     }


     /**
      * Build and return frontpage teasers
      *
      * TODO:
      * - implement caching
      * - implement templating
      * - fix hardcoded id
      */
     public function frontPageTeasers()
     {
        $pages = CmsI18nQuery::create()
            ->useCmsQuery()
                ->filterByCmsThreadId(21) // FIXME!
            ->endUse()
            ->findByLocale(Hanzo::getInstance()->get('core.locale'))
        ;

        ob_start();
        if ($pages->count()) {
?>
  <aside id="teasers" role="teasers">
    <ul>
<?php $i=1; foreach ($pages as $page): ?>
      <li class="teaser-box-<?php echo $i ?>">
        <?php echo $this->parse($page->getContent()) ?>
      </li>
<?php $i++; endforeach; ?>
    </ul>
  </aside>
<?php
        }

        return ob_get_clean();
     }
}

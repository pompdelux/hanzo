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

use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\OrdersPeer;

class MiscExtension extends Twig_Extension
{
    protected $twig_string;
    protected $theme;
    protected $settings;

    public function __construct(TwigStringService $twig_string, ActiveTheme $theme)
    {
        $this->twig_string = $twig_string;
        $this->theme = $theme;
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
            'front_page_teasers' => new Twig_Function_Method($this, 'frontPageTeasers', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'parameter' => new Twig_Function_Method($this, 'parameter', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'embed' => new Twig_Function_Method($this, 'embed', array('pre_escape' => 'html', 'is_safe' => array('html'), 'needs_environment' => true)),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
            'money'  => new Twig_Filter_Method($this, 'moneyFormat'),
            'og_description' => new Twig_Filter_Method($this, 'ogDescription'),
            'strip_tags' => new Twig_Filter_Method($this, 'stripTags'),
            'tag_safe' => new Twig_Filter_Method($this, 'tagSafe'),
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
        $mode = $hanzo->container->get('kernel')->getStoreMode();

        if ('webshop' == $mode) {
            $mode = '';
        }

        // TODO: implement the mobile layout
        $device_map = array(
            'pc' => 'base.html.twig',
            // 'mobile' => '::base_mobile.html.twig',
        );

        $layout = isset($device_map[$device]) ? $device_map[$device] : $device_map['pc'];

        return '::'.$mode.$layout;
    }


    /**
     * @see Hanzo\Core\Tools\Tools::moneyFormat
     * NICETO: loose the wrapper, figure out how to use namespaces and load the Tools class in the getF*() methods
     */
    public function moneyFormat($number, $format = '%.2i')
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
        $find = '~(background|src)="(../|/)~';
        $replace = '$1="' . Hanzo::getInstance()->get('core.cdn');
        $string = preg_replace($find, $replace, $string);

        return $this->twig_string->parse($string, $parameters);
    }


    /**
    * Returns any meta data associated with this domain.
    *
    * @param string exclude specific tag names.
    * @return string
    */
    public function metaTags($exclude = '')
    {
        $exclude = explode(',', trim($exclude));

        // move to GoogleBundle
        array_unshift($exclude, 'google-site-verification');

        $meta = Hanzo::getInstance()->getByNs('meta');

        $result = '';
        foreach ($meta as $key => $value) {
            if (!in_array($key, $exclude)) {
                $attr = 'name';
                if (0 === strpos($key, 'og:')) {
                    $attr = 'property';
                }

                $result .= '<meta ' . $attr . '="' . $key . '" content="' . $value . '">' . "\n";
            }
        }

        return $result;
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
  <aside id="teasers" role="complementary">
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


     /**
      * get a settings parameter
      *
      * @param  string $name       settings identifier
      * @param  array  $parameters array of replacement parameters
      * @return string
      */
     public function parameter($name, $parameters = array())
     {
        if (empty($this->settings)) {
            $this->settings = Hanzo::getInstance()->get('ALL');
        }

        $out = '';
        if (strpos($name, '.') === false) {
            foreach ($this->settings as $key => $value) {
                if (preg_match('/.'.$name.'$/i', $key)) {
                    $out = $value;
                }
            }
        } else {
            if (isset($this->settings[$name])) {
                $out = $this->settings[$name];
            }
        }

        return strtr($out, $parameters);
     }


     /**
      * embed html elements in cms pages
      *
      * @param  Twig_Environment $env        [description]
      * @param  string           $name       [description]
      * @param  array            $parameters [description]
      * @return string
      */
     public function embed(Twig_Environment $env, $name, $parameters = array())
     {
        switch ($name) {
            default:
                return '';
                break;

            case 'newsletter_form':
                $view = '';
                $customer = null;
                if (isset($parameters['view']) && $parameters['view'] == 'simple') {
                    $view = 'simple-';
                } else {
                    $customer = \Hanzo\Model\CustomersPeer::getCurrent();
                }

                $template = 'NewsletterBundle:Default:'.$view.'block.html.twig';
                $parameters = array(
                    'customer' => $customer,
                    'listid' => Hanzo::getInstance()->container->get('newsletterapi')->getListIdAvaliableForDomain(),
                );

                break;

            // {{ embed('media_file', {
            //   'file': 'xhjkpiydjns/MissionVision.pdf',
            //   'date_format': 'long',
            //   'text': '» Mission, vision og idégrundlag (pdf)'
            // }) }}
            case 'media_file':
                $cdn = Hanzo::getInstance()->get('core.cdn');

                $parameters['file'] = isset($parameters['file']) ? $parameters['file'] : '';
                $parameters['text'] = isset($parameters['text']) ? $parameters['text'] : '';
                $parameters['date_format'] = isset($parameters['date_format']) ? $parameters['date_format'] : 'Y-m-d H:i';
                $parameters['date_label'] = isset($parameters['date_label']) ? $parameters['date_label'] : '';

                if (empty($parameters['file']) || empty($parameters['text'])) {
                    return '';
                }

                $ext = pathinfo($parameters['file'], PATHINFO_EXTENSION);

                if (empty($parameters['date_label'])) {
                    return '<a href="'.$cdn.'images/'.$parameters['file'].'" class="js-external media_file filetype-'.$ext.'">'.$parameters['text'].'</a>';
                }

                return '<a href="'.$cdn.'images/'.$parameters['file'].'" class="js-external media_file rewrite filetype-'.$ext.'" data-dateformat="'.$parameters['date_format'].'" data-datelabel="'.$parameters['date_label'].'">'.$parameters['text'].'</a> <em></em> ';

                break;

            case 'edit_warning':
                if (OrdersPeer::inEdit()) {
                    return Tools::getInEditWarning();
                }
                return '';

                break;

            // eks:
            // {{ embed("slideshow", {
            //     "2013s1": {
            //         "slides": [{
            //                 "href": "/da_DK/forside/home-shopping",
            //                 "src": "images/frontpage/Carousel05_uge7_SS13_ALL.jpg",
            //                 "alt": "alt image text"
            //             },{
            //                 "href": "/da_DK/forside/om-pompdelux",
            //                 "src": "images/frontpage/Carousel04_uge7_SS13_DK.jpg",
            //                 "alt": "alt image text"
            //             }
            //         ],
            //         "class": "grid_6 alpha"
            //     },
            //     "2013s1_mobile": {
            //         "slides": [{
            //                 "href": "/da_DK/forside/home-shopping/aabent-hus",
            //                 "src": "images/frontpage/Carousel01_uge8_SS13_DK.jpg",
            //                 "alt": "alt image text"
            //             },{
            //                 "href": "/da_DK/forside/om-pompdelux",
            //                 "src": "images/frontpage/Carousel04_uge7_SS13_DK.jpg",
            //                 "alt": "alt image text"
            //             }
            //         ],
            //         "class": "grid_6 alpha"
            //     }
            // }) }}
            case 'slideshow':
                // get slides
                $theme = $this->theme->getName();

                if (isset($parameters[$theme]['slides'])) {
                    $selected = $parameters[$theme];
                } elseif (isset($parameters['default']['slides'])) {
                    $selected = $parameters['default'];
                } else {
                    // old stuff
                    $class = (!empty($slides['class']))?' '.$parameters['class']:'';
                    $html = '<div class="cycle-slideshow '.$class.'" data-cycle-slides="> a" data-pause-on-hover="true">'."\n";

                    foreach ($parameters['slides'] as $slide) {
                        $html .= $slide."\n";
                    }

                    $html .= '<div class="cycle-pager"></div></div>'."\n";
                    return $html;
                }

                $html = '';
                foreach ($selected['slides'] as $slide) {
                    $params = '';
                    if (isset($slide['params'])) {
                        foreach ($slide['params'] as $k => $v) {
                            $params .= ' '.$k.'="'.$v.'"';
                        }
                    }

                    $attr = [];
                    if (isset($slide['alt']) && $slide['alt']) {
                        $attr['alt'] = $slide['alt'];
                    }

                    if (empty($slide['href'])) {
                        $slide['href'] = '';
                    }

                    $html .= '<a href="'.$slide['href'].'"'.$params.'>'.Tools::imageTag($slide['src'], $attr)."</a>\n";
                }

                $class = (!empty($selected['class']))?' '.$selected['class']:' ';

                return '<div class="cycle-slideshow '.$class.'" data-cycle-slides="> a" data-pause-on-hover="true">'."\n".$html.'<div class="cycle-pager"></div></div>'."\n";
                break;

            // {{ embed("image", {
            //    "2013s1": {
            //      "src": "path/to/image.jpg",
            //      "alt": "image alt text",
            //      "caption": "optional image caption"
            //    },
            //    "2013s1_mobile": {
            //      "src": "path/to/mobile/image.jpg",
            //      "alt": "image alt text",
            //      "caption": "optional image caption"
            //    }
            // }) }}
            case 'image':
                $theme = $this->theme->getName();
                if (isset($parameters[$theme])) {
                    $attr = ['class' => ''];

                    $block = $parameters[$theme];
                    if (!empty($block['alt'])) {
                        $attr['alt'] = $block['alt'];
                    }

                    if (!empty($block['class'])) {
                        $attr['class'] = $block['class'];
                    }

                    $attr['lazy'] = true;
                    $attr['noscript'] = false;
                    $html = Tools::imageTag($block['src'], $attr);

                    if (!empty($block['href'])) {
                      $params = '';
                      if (isset($block['params']) && is_array($block['params'])) {
                          foreach ($block['params'] as $k => $v) {
                              $params .= ' '.$k.'="'.$v.'"';
                          }
                      }

                      $html = '<a href="'.$block['href'].'"'.$params.'>'.$html.'</a>';
                    }

                    if (isset($block['caption']) && $block['caption']) {
                      $html = '<div class="image-caption ' . $attr['class'] . '">' . $html . '<span>' . $block['caption'] . '</span></div>';
                    }

                    return $html;
                }
                break;
        }

        $html = '';
        try {
            $html = $env->render($template, $parameters);
        } catch (\Exception $e) {
            Tools::log($e->getMessage());
        }

        return $html;
    }

    public function ogDescription($description)
    {
        $description = explode('<br>', $description);
        $description = trim($description[0]);

        return $description;
    }

    /**
     * Wrap the Tools::stripTags function to an twig function.
     *
     * @param string $value
     *   The value to strip
     * @return string
     */
    public function stripTags($value)
    {
      return Tools::stripTags($value);
    }

    public function tagSafe($value, $params = null)
    {
        if (!empty($params) && is_array($params)) {
            return strtr($value, $params);
        }

        return preg_replace('/[^a-z0-9_-]/i', '', $value);
    }
}

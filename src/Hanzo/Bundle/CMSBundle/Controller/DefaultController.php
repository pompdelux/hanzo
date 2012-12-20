<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;

use Hanzo\Model\CustomersPeer;
use Hanzo\Model\ProductsStockPeer;

class DefaultController extends CoreController
{
    /**
     * Cache cms page views for 24 hours
     * @Cache(smaxage="86400")
     */
    public function indexAction()
    {
        $hanzo = Hanzo::getInstance();
        $page = CmsPeer::getFrontpage($hanzo->get('core.locale'));

        return $this->forward('CMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

    /**
     * Cache cms page views for 24 hours
     * @Cache(smaxage="86400")
     */
    public function viewAction($id, $page = NULL)
    {
        $hanzo = Hanzo::getInstance();
        $locale = $hanzo->get('core.locale');

        if ($page instanceof Cms) {
            $type = $page->getType();
        } else {
            $page = CmsPeer::getByPK($id, $locale);
            $type = 'pages';

            if (is_null($page)) {
                throw $this->createNotFoundException('The page does not exist (id: '.$id.' )');
            }
        }

        $html = $page->getContent();
        $find = '~(background|src)="(../|/)~';
        $replace = '$1="' . $hanzo->get('core.cdn');
        $html = preg_replace($find, $replace, $html);
        $page->setContent($html);

        return $this->render('CMSBundle:Default:view.html.twig', array('page_type' => $type, 'page' => $page));
    }

    public function testAction()
    {
        // if all variants is out of stock, set it on the master product.
        $result = \Hanzo\Model\ProductsStockQuery::create()
          ->withColumn('SUM('.ProductsStockPeer::QUANTITY.')', 'total_stock')
          ->select(array('total_stock'))
          ->useProductsQuery()
            ->filterByMaster('Todd Little SLEEPER')
          ->endUse()
          ->findOne()
        ;
        Tools::log($result);

        return $this->response('1.2.3 ... test');
    }
}

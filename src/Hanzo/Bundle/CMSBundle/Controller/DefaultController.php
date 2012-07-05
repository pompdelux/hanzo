<?php

namespace Hanzo\Bundle\CMSBundle\Controller;

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
    public function indexAction()
    {
        $hanzo = Hanzo::getInstance();
        $page = CmsPeer::getFrontpage($hanzo->get('core.locale'));

        return $this->forward('HanzoCMSBundle:Default:view', array(
            'id'  => NULL,
            'page' => $page
        ));
    }

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

        $response = new Response();
        $response->setLastModified($page->getUpdatedAt(null));

        if ($response->isNotModified($this->getRequest())) {
            return $response;
        }

        $response->setMaxAge(60);
        $response->setSharedMaxAge(60);

        $html = $page->getContent();
        $find = '~(background|src)="(../|/)~';
        $replace = '$1="' . $hanzo->get('core.cdn');
        $html = preg_replace($find, $replace, $html);
        $page->setContent($html);

        return $this->render('HanzoCMSBundle:Default:view.html.twig', array('page_type' => $type, 'page' => $page), $response);
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

    public function splashAction()
    {
        $request = $this->get('request');

        return $this->render('HanzoCMSBundle:Default:splash.html.twig', array(
            'baseurl' => trim($request->getScheme().'://'.$request->getHost().$request->getRequestUri(), '/')
        ));
    }
}

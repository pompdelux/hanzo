<?php

namespace Hanzo\Bundle\GoogleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;

use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsImagesQuery;

class SitemapController extends Controller
{
  protected $locale;

  protected $base_url;

  protected $request;

  /**
   * sitemapAction
   * - Most of this is black magic composed from CMSBundle/Controller/MenuController.php and GoogleBundle/Controller/ProductFeedController.php
   *
   * https://support.google.com/webmasters/answer/183668?hl=en&ref_topic=6080646&rd=1
   *
   * @Route("/google/sitemap.xml")
   * @return Symfony\Component\HttpFoundation\Response
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function sitemapAction(Request $request)
  {
    $hanzo  = Hanzo::getInstance();

    $this->locale   = $hanzo->get('core.locale');
    $this->base_url = $request->getBaseUrl();
    $this->request  = $request;

    $cmsPages = $this->getCMS();
    $products = $this->getProducts();

    $items = [];
    $items = array_merge($items, $this->generateCMSItems($cmsPages));
    $items = array_merge($items, $this->generateProductItems($products));

    $response = new Response($this->renderView('GoogleBundle:Sitemap:sitemap.xml.twig', ['items' => $items]));
    $response->headers->set('Content-Type', 'application/xml');

    return $response;
  }

  /**
   * getProducts
   *
   * @return \PropelObjectCollection
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function getProducts()
  {
    return ProductsQuery::create()
      ->filterByMaster(null, \Criteria::ISNULL)
      ->joinWithProductsImages()
      ->joinWithProductsI18n()
      ->useProductsI18nQuery()
      ->filterByLocale($this->locale)
      ->endUse()
      ->find()
      ;
  }

  /**
   * getCMS
   *
   * @return \PropelObjectCollection
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  protected function getCMS()
  {
    return CmsQuery::create()
      ->useCmsI18nQuery()
      ->filterByOnMobile(true)
      ->endUse()
      ->joinWithI18n($this->locale)
      // FIXME:
      ->filterByCmsThreadId(20)
      ->orderBySort()
      ->find()
      ;
  }

  /**
   * generateProductItems
   *
   * @param \PropelObjectCollection $products
   *
   * @return Array
   * @author Henrik Farre <hf@bellcom.dk>
   */
  protected function generateProductItems(\PropelObjectCollection $products)
  {
    $router = $this->get('router');
    $router_keys = include $this->container->getParameter('kernel.cache_dir').'/category_map.php';

    $items = [];

    foreach ($products as $product)
    {
      $product_id           = $product->getId();
      $product_sku          = $product->getTitle();
      $product_sku_stripped = Tools::stripText($product_sku);

      $products2category = ProductsToCategoriesQuery::create()
        ->useProductsQuery()
        ->filterBySku($product->getSku())
        ->endUse()
        ->findOne();

      $key = '_' . strtolower($this->locale) . '_' . $products2category->getCategoriesId();

      // skip products without routes
      if (empty($router_keys[$key])) {
        continue;
      }

      $product_route = $router_keys[$key];

      $params = [
        'product_id' => $product_id,
        'title'      => $product_sku_stripped,
      ];

      $url = $router->generate($product_route, $params, true);

      $images_array = $this->getImagesForProduct($product_id);

      $urls = [
        'loc'    => $url,
        'images' => $images_array,
        ];

      $items[] = $urls;
    }

    return $items;
  }

  /**
   * generateCMSItems
   *
   * @param \PropelObjectCollection $cmsPages
   *
   * @return Array
   * @author Henrik Farre <hf@bellcom.dk>
   */
  protected function generateCMSItems(\PropelObjectCollection $cmsPages)
  {
    $items = [];

    foreach ($cmsPages as $record)
    {
      $type = $record->getType();

      if ( $type != 'page' )
      {
        continue;
      }

      // FIXME: prettier way?
      //$url = $router->generate($record->getTitle());
      $url = $this->request->getHttpHost() . $this->base_url . '/' . $this->locale . '/' . $record->getPath();

      $urls = [
        'loc'    => $url,
        'images' => [],
        ];

      $items[] = $urls;
    }

    return $items;
  }

  /**
   * getImagesForProduct
   * https://support.google.com/webmasters/answer/178636
   *
   * @param string $product_id
   *
   * @return Array
   * @author Henrik Farre <hf@bellcom.dk>
   */
  protected function getImagesForProduct($product_id)
  {
    $images_array = [];

    $images = ProductsImagesQuery::create()
      ->filterByProductsId($product_id)
      ->find();

    foreach ($images as $image)
    {
      $img = $image->getImage();
      // only show "overview" images, there are a limit to how many images google allows pr product.
      if (false === strpos($img, '_overview_')) {
        continue;
      }

      $images_array[] = ['image_loc' => Tools::productImageUrl($img, '0x0')];
    }

    return $images_array;
  }
}

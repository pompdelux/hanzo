<?php

namespace Hanzo\Bundle\GoogleBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Model\Cms;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsToCategoriesQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class: SitemapController
 *
 * @see Controller
 */
class SitemapController extends Controller
{
  protected $locale;

  protected $baseURL;

  protected $request;

  /**
   * @param Request $request
   *
   * @Route("/google/sitemap.xml")
   * @author Henrik Farre <hf@bellcom.dk>
   *
   * @return Response
   *
   * sitemapAction
   * - Most of this is black magic composed from CMSBundle/Controller/MenuController.php and GoogleBundle/Controller/ProductFeedController.php
   *
   * https://support.google.com/webmasters/answer/183668?hl=en&ref_topic=6080646&rd=1
   */
  public function sitemapAction(Request $request)
  {
    $hanzo  = Hanzo::getInstance();

    $this->locale   = $request->getLocale();
    $this->baseURL = $request->getBaseUrl();
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
   */
  protected function getProducts()
  {
    return ProductsQuery::create()
      ->filterByMaster(null, \Criteria::ISNULL)
      ->joinWithProductsImages()
      ->joinWithProductsI18n()
      ->useProductsI18nQuery()
      ->filterByLocale($this->locale)
      ->endUse()
      ->find();
  }

  /**
   * getCMS
   *
   * @return \PropelObjectCollection
   * @author Henrik Farre <hf@bellcom.dk>
   */
  protected function getCMS()
  {
    return CmsQuery::create()
      ->useCmsI18nQuery()
        ->filterByOnMobile(true)
      ->endUse()
      ->joinWithI18n($this->locale)
      ->filterByCmsThreadId(20)
      ->orderBySort()
      ->find();
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
    $router = $this->container->get('router');
    $routerKeys = include $this->container->getParameter('kernel.cache_dir').'/category_map.php';

    $items = [];

    foreach ($products as $product) {
      $productId          = $product->getId();
      $productSku         = $product->getTitle();
      $productSkuStripped = Tools::stripText($productSku);

      $products2category = ProductsToCategoriesQuery::create()
        ->useProductsQuery()
        ->filterBySku($product->getSku())
        ->endUse()
        ->findOne();

      $key = '_' . strtolower($this->locale) . '_' . $products2category->getCategoriesId();

      // skip products without routes
      if (empty($routerKeys[$key])) {
        continue;
      }

      $productRoute = $routerKeys[$key];

      $params = [
        'product_id' => $productId,
        'title'      => $productSkuStripped,
      ];

      $url = $router->generate($productRoute, $params, true);

      $imagesArray = $this->getImagesForProduct($productId);

      $urls = [
        'loc'    => $url,
        'images' => $imagesArray,
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
    $router = $this->container->get('router');
    $items = [];

    foreach ($cmsPages as $record) {
      $type = $record->getType();

      if ( $type != 'page' ) {
        continue;
      }

      $url = $router->generate('page_'.$record->getId(), [], true);

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
   * @param string $productId
   *
   * @return Array
   * @author Henrik Farre <hf@bellcom.dk>
   */
  protected function getImagesForProduct($productId)
  {
    $imagesArray = [];

    $images = ProductsImagesQuery::create()
      ->filterByProductsId($productId)
      ->find();

    foreach ($images as $image) {
      $img = $image->getImage();
      // only show "overview" images, there are a limit to how many images google allows pr product.
      if (false === strpos($img, '_overview_')) {
        continue;
      }

      $imagesArray[] = ['image_loc' => Tools::productImageUrl($img, '0x0')];
    }

    return $imagesArray;
  }
}

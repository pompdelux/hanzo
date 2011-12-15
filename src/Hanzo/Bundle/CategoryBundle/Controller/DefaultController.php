<?php

namespace Hanzo\Bundle\CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Response
;

use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsImagesCategoriesSortQuery,
    Hanzo\Model\ProductsImagesCategoriesSortPeer
;

class DefaultController extends CoreController
{

    /**
     * handle category listings
     *
     * @param $cms_id
     * @param $category_id
     * @param $pager
     */
    public function viewAction($cms_id, $category_id, $pager = 1)
    {
        $route = $this->get('request')->get('_route');

        $cache_id = explode('_', $route);
        $cache_id = array($cache_id[0], $cache_id[2], $cache_id[1]);

        $data = $this->getCache($cache_id);
        if (!$data) {

            $locale = $this->get('hanzo')->get('core.locale');
            $router = $this->get('router');

            $result = ProductsImagesCategoriesSortQuery::create()
                ->useProductsQuery()
                    ->filterByIsOutOfStock(FALSE)
                ->endUse()
                ->joinWithProducts()
                ->joinWithProductsImages()
                ->orderBySort()
                ->filterByCategoriesId($category_id)
                ->paginate($pager, 12)
            ;

            $product_route = str_replace('category_', 'product_', $route);

            $records = array();
            foreach ($result as $record) {
                $product = $record->getProducts();
                $records[] = array(
                    'sku' => $product->getSku(),
                    'id' => $product->getId(),
                    'name' => $product->getMaster(),
                    'image' => 'http://static.hanzo.bc/images/products/' . $record->getProductsImages()->getImage(),
                    'url' => $router->generate($product_route, array(
                        'product_id' => $product->getId(),
                        'title' => $this->stripText($product->getMaster()),
                    )),
                    // TODO prices ?
                );
            }

            $data = array(
                'title' => 'cat view',
                'products' => $records,
            );

            if ($result->haveToPaginate()) {

                $pages = array();
                foreach ($result->getLinks(20) as $page) {
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);
                }

                $data['paginate'] = array(
                    'next' => ($result->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getNextPage()), TRUE)),
                    'prew' => ($result->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $result->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            }

            // categories are cached 5 seconds - just to boost performance a bit.
            $this->setCache($cache_id, $data, 5);
        }

        if ($this->getFormat() == 'json') {
            $this->json_responce($data);
        }

        $this->get('twig')->addGlobal('page_type', 'category');
        $responce = $this->render('HanzoCategoryBundle:Default:view.html.twig', $data);

        return $responce;
    }
}

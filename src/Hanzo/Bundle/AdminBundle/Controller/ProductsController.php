<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\CategoriesQuery;

class ProductsController extends CoreController
{
    
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    public function sortAction($category_id = null)
    {

        $categories_result = CategoriesQuery::create()
    		->where('categories.PARENT_ID IS NOT NULL')
            ->joinWithI18n()
            ->orderByParentId()
            ->find()
        ;

        $categories = array();
        foreach ($categories_result as $category) {
            $tmp = explode('_', $category->getContext());
            $categories[$category->getContext()]['group'] = $tmp[0];
            $categories[$category->getContext()]['title']= $category->getTitle();
            $categories[$category->getContext()]['id']= $category->getId();
        }

        $products = ProductsImagesCategoriesSortQuery::create()
            ->useProductsQuery()
                ->where('products.MASTER IS NULL')
            ->endUse()
            ->joinWithProducts()
            ->useProductsImagesQuery()
                ->groupByImage()
            ->endUse()
            ->joinWithProductsImages()
            ->orderBySort()
            ->filterByCategoriesId($category_id)
            ->find()
        ;
		
		$records = array();
        foreach ($products as $record) {
            $product = $record->getProducts();
        
            $records[] = array(
                'sku' => $product->getSku(),
                'id' => $product->getId(),
                'title' => $product->getSku(),
                'image' => $record->getProductsImages()->getImage(),
                'is_active' => $product->getIsActive()
            );
        }

        return $this->render('AdminBundle:Products:sort.html.twig', array(
            'products'      => $records,
            'categories'    => $categories
        ));
    }

    public function updateSortAction()
    {
    	$requests = $this->get('request');
        $products = $requests->get('data');
        
        $sort = 0;
        foreach ($products as $product => $product_id) {

            // $result = ProductsImagesCategoriesSortQuery::create()
            // 	->findOneById($product_id);
            // 	->setSort($sort);
            // 	->save();

            $sort++;
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin'),
            ));
        }
    }
}

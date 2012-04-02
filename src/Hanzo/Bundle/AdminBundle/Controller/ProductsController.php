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
        $current_category = CategoriesQuery::create()
            ->joinWithI18n()
            ->findOneById($category_id)
        ;

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
                'image_id' => $record->getProductsImages()->getId(),
                'is_active' => $product->getIsActive()
            );
        }

        return $this->render('AdminBundle:Products:sort.html.twig', array(
            'products'          => $records,
            'current_category'  => $current_category,
            'categories'        => $categories
        ));
    }

    public function updateSortAction()
    {
    	$requests = $this->get('request');
        $products = $requests->get('data');
        $category_id = $requests->get('category_id'); // @todo Fjernes og cat id tages med som ÌD på hver item
        
        $sort = 0;
        foreach ($products as $product) {
            $item_parts = explode('-', substr($product, 5));
            $product_id = $item_parts[0];
            $picture_id = $item_parts[1];
            $result = ProductsImagesCategoriesSortQuery::create()
                ->filterByCategoriesId($category_id)
                //->useProductsImagesQuery()
            	   ->filterByProductsId($product_id)
                //->endUse()
                ->findOneByProductsImagesId($picture_id)
            	->setSort($sort)
            	->save()
            ;

            $sort++;
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin'),
                'catID' => $category_id,
                'productId' => $product_id,
                'productIds' => substr($product_id, 5),
            ));
        }

        $this->get('session')->setFlash('notice', 'product.sort.updated');
        return $this->redirect($this->generateUrl('admin_products_sort', 
            array(
                'category_id' => $category_id
            )
        ));

    }
}

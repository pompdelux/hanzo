<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\ProductsImagesCategoriesSortQuery,
    Hanzo\Model\ProductsImagesProductReferences,
    Hanzo\Model\ProductsImagesProductReferencesQuery,
    Hanzo\Model\ProductsToCategoriesQuery,
    Hanzo\Model\ProductsImagesQuery,
    Hanzo\Model\ProductsQuery,
    Hanzo\Model\CategoriesQuery;

class ProductsController extends CoreController
{
    
    public function indexAction($category_id, $subcategory_id)
    {
        $categories = null;
        $products = null;
        if (!$category_id){

            $categories = CategoriesQuery::create()
                ->where('categories.PARENT_ID IS NULL')
                ->joinWithI18n()
                ->orderById()
                ->find()
            ;
            $parent_category = null;

        } elseif (!$subcategory_id){

            $categories = CategoriesQuery::create()
                ->filterByParentId($category_id)
                ->joinWithI18n()
                ->orderById()
                ->find()
            ;

            $parent_category = CategoriesQuery::create()
                ->joinWithI18n()
                ->findOneById($category_id)
            ;

        } else { // Both $category_id and $subcategory_id are set. Show some products!

            $products = ProductsQuery::create()
                ->useProductsToCategoriesQuery()
                    ->filterByCategoriesId($subcategory_id)
                ->endUse()
                ->find()
            ;
            
            $parent_category = CategoriesQuery::create()
                ->joinWithI18n()
                ->findOneById($subcategory_id)
            ;
        }
        $categories_list = array();
        $products_list = array();
        if ($categories) {

            foreach ($categories as $category) {
                $categories_list[] = array(
                    'id' => $category->getId(),
                    'context' => $category->getContext(),
                    'is_active' => $category->getIsActive(),
                    'title' => $category->getTitle()
                );
            }

        }else if ($products) {

            foreach ($products as $product) {
                $products_list[] = array(
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'master' => $product->getMaster(),
                    'size' => $product->getSize(),
                    'color' => $product->getColor(),
                    'unit' => $product->getUnit(),
                    'is_out_of_stock' => $product->getIsOutOfStock(),
                    'is_active' => $product->getIsActive()
                );
            }

        }
        
        return $this->render('AdminBundle:Products:list.html.twig', array(
            'categories'        => $categories_list,
            'products'        => $products_list,
            'parent_category'   => $parent_category,
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id
        ));
    }

    public function viewAction($id)
    {
        /**
         * @todo Lav fint med CSS
         * 
         **/
        $current_product = ProductsQuery::create()
            ->findOneById($id)
        ;

        $all_products = ProductsQuery::create()
            ->filterByMaster(NULL)
            ->find()
        ;

        $product_images = ProductsImagesQuery::create()
            ->joinProducts()
            ->findByProductsId($id)
        ;

        $product_images_list = array();

        foreach ($product_images as $record) {

            $products_refs = ProductsImagesProductReferencesQuery::create()
                ->joinWithProducts()
                ->joinWithProductsImages()
                ->filterByProductsImagesId($record->getId())
                ->find()
            ;

            $products_refs_list = array();
            if($products_refs){
                foreach ($products_refs as $ref) {

                    $product_ref = $ref->getProducts();

                    $products_refs_list[] = array(
                        'id' => $product_ref->getId(),
                        'sku' => $product_ref->getSku()
                    );
                }
            }

            $product_images_list[] = array(
                'id' => $record->getProductsId(),
                'image' => $record->getImage(),
                'image_id' => $record->getId(),
                'product_ref_ids' => $products_refs_list
            );
        }
        return $this->render('AdminBundle:Products:viewImages.html.twig', array(
            'current_product' => $current_product,
            'product_images'    => $product_images_list,
            'products' => $all_products
        ));
    }

    public function addReferenceAction()
    {
        $requests = $this->get('request');
        $image_id = $requests->get('image');
        $product_id = $requests->get('product');

        $reference = new ProductsImagesProductReferences();
        $reference->setProductsImagesId($image_id);
        $reference->setProductsId($product_id);

        try {
            $reference->save();
        } catch (PropelException $e) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $this->get('translator')->trans('save.changes.failed', array(), 'admin')
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin')
            ));
        }
    }

    public function deleteReferenceAction($image_id, $product_id)
    {

        $product_ref = ProductsImagesProductReferencesQuery::create()
            ->filterByProductsImagesId($image_id)
            ->findOneByProductsId($product_id)
        ;

        if($product_ref)
            $product_ref->delete();

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.imageReference.success', array(), 'admin'),
            ));
        }
    }

    public function sortAction($category_id)
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

        $sort = 0;
        foreach ($products as $product) {
            $item_parts = explode('-', substr($product, 5));
            $product_id = $item_parts[0];
            $picture_id = $item_parts[1];
            $category_id = $item_parts[2];
            $result = ProductsImagesCategoriesSortQuery::create()
                ->filterByCategoriesId($category_id)
                ->filterByProductsId($product_id)
                ->findOneByProductsImagesId($picture_id)
            	->setSort($sort)
            	->save()
            ;

            $sort++;
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin')
            ));
        }
    }
}

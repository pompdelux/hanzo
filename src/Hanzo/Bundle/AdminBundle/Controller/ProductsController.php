<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\ProductsImagesCategoriesSortQuery,
    Hanzo\Model\ProductsImagesProductReferences,
    Hanzo\Model\ProductsImagesProductReferencesQuery,
    Hanzo\Model\ProductsToCategoriesQuery,
    Hanzo\Model\ProductsToCategories,
    Hanzo\Model\ProductsImagesQuery,
    Hanzo\Model\ProductsQuery,
    Hanzo\Model\ProductsStockQuery,
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
                ->joinWithI18n('en_GB')
                ->orderById()
                ->find()
            ;
            $parent_category = null;

        } elseif (!$subcategory_id){

            $categories = CategoriesQuery::create()
                ->filterByParentId($category_id)
                ->joinWithI18n('en_GB')
                ->orderById()
                ->find()
            ;

            $parent_category = CategoriesQuery::create()
                ->joinWithI18n('en_GB')
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
                ->joinWithI18n('en_GB')
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
                    'title' => $category->getTitle(),
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

        $categories = CategoriesQuery::create()
            ->where('categories.PARENT_ID IS NOT NULL')
            ->joinWithI18n('en_GB')
            ->orderByContext()
            ->find()
        ;

        $product_categories = CategoriesQuery::create()
            ->useProductsToCategoriesQuery()
                ->filterByProductsId($id)
            ->endUse()
            ->orderById()
            ->find()
        ;

        $current_product = ProductsQuery::create()
            ->findOneById($id)
        ;

        $styles = ProductsQuery::create()
            ->filterByMaster($current_product->getSku())
            ->orderBySku()
            ->find()
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
        return $this->render('AdminBundle:Products:view.html.twig', array(
            'styles'                => $styles,
            'product_categories'    => $product_categories,
            'categories'            => $categories,
            'current_product'       => $current_product,
            'product_images'        => $product_images_list,
            'products'              => $all_products
        ));
    }

    public function deleteStylesAction($id)
    {
        $master = ProductsQuery::create()
            ->findOneById($id)
        ;

        $styles = ProductsQuery::create()
            ->filterByMaster($master->getSku())
            ->find()
        ;

        if($styles instanceof \PropelObjectCollection){
            $styles->delete();

            $this->get('session')->setFlash('notice', 'delete.products.styles.success');

            return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));
        }

        $this->get('session')->setFlash('notice', 'delete.products.styles.failed');

        return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));


    }

    public function deleteStyleAction($id)
    {

        $style = ProductsQuery::create()
            ->findOneById($id)
        ;

        if($style instanceof ProductsQuery){
            $style->delete();

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('delete.products.style.success', array(), 'admin')
                ));
            }
            $this->get('session')->setFlash('notice', 'delete.products.style.success');

            return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.products.style.failed', array(), 'admin')
            ));
        }

        $this->get('session')->setFlash('notice', 'delete.products.style.failed');

        return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));


    }

    public function addCategoryAction()
    {
        $requests = $this->get('request');
        $category_id = $requests->get('category');
        $product_id = $requests->get('product');

        $category_to_product = new ProductsToCategories();
        $category_to_product->setCategoriesId($category_id);
        $category_to_product->setProductsId($product_id);

        try {
            $category_to_product->save();
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

    public function deleteCategoryAction($category_id, $product_id)
    {

        $category_to_product = ProductsToCategoriesQuery::create()
            ->filterByCategoriesId($category_id)
            ->findOneByProductsId($product_id)
        ;

        if($category_to_product)
            $category_to_product->delete();

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.changes.success', array(), 'admin'),
            ));
        }
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
            $this->get('replication_manager')->syncStyleGuide('add', $image_id, $product_id);

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
            $this->get('replication_manager')->syncStyleGuide('delete', $image_id, $product_id);

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

    public function stockAction()
    {
        $parser = new \PropelCSVParser();
        $parser->delimiter = ';';

        $stocks = ProductsStockQuery::create()
            ->useProductsQuery()
                ->orderBySku()
            ->endUse()
            ->joinWithProducts()
            ->withColumn('SUM(products_stock.quantity)', 'totalstock')
            ->groupByProductsId()
            ->find()
        ;

        $stock_data = array();
        $stock_data[0] = array('SKU','STOCK');

        foreach ($stocks as $stock) {
            $stock_data[] = array($stock->getProducts()->getSku(), $stock->getVirtualColumn('totalstock'));
        }

        return new Response(
            $parser->toCSV($stock_data, true, false),
            200,
            array(
                 'Content-Type' => 'text/csv',
                 'Content-Disposition' => sprintf('attachment; filename="stock_' . date('Y-m-d', time()) . '.csv"', 'stock_' . date('Y-m-d', time()) .'.csv')
            )
        );
    }
}

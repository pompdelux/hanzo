<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    Hanzo\Model\ProductsQuantityDiscountQuery,
    Hanzo\Model\ProductsQuantityDiscount,
    Hanzo\Model\DomainsQuery,
    Hanzo\Model\CategoriesQuery,
    Hanzo\Model\RelatedProducts,
    Hanzo\Model\RelatedProductsQuery;

class ProductsController extends CoreController
{

    public function indexAction($category_id, $subcategory_id)
    {
        $categories = null;
        $products = null;
        $q_clean = null;
        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';
            /**
             * @todo Lav søgning så man kan søge på hele navn. Sammenkobling på for og efternavn.
             */
            $products = ProductsQuery::create()
                ->filterBySku($q)
                ->_or()
                ->filterById($q_clean)
                ->find($this->getDbConnection())
            ;

            $parent_category = null;

        }elseif (!$category_id){

            $categories = CategoriesQuery::create()
                ->where('categories.PARENT_ID IS NULL')
                ->joinWithI18n('en_GB')
                ->orderById()
                ->find($this->getDbConnection())
            ;
            $parent_category = null;

        } elseif (!$subcategory_id){

            $categories = CategoriesQuery::create()
                ->filterByParentId($category_id)
                ->joinWithI18n('en_GB')
                ->orderById()
                ->find($this->getDbConnection())
            ;

            $parent_category = CategoriesQuery::create()
                ->joinWithI18n('en_GB')
                ->filterById($category_id)
                ->findOne($this->getDbConnection())
            ;

        } else { // Both $category_id and $subcategory_id are set. Show some products!

            $products = ProductsQuery::create()
                ->useProductsToCategoriesQuery()
                    ->filterByCategoriesId($subcategory_id)
                ->endUse()
                ->find($this->getDbConnection())
            ;

            $parent_category = CategoriesQuery::create()
                ->joinWithI18n('en_GB')
                ->filterById($subcategory_id)
                ->findOne($this->getDbConnection())
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
            'subcategory_id' => $subcategory_id,
            'search_query' => $q_clean,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function viewAction($id)
    {
        $categories = CategoriesQuery::create()
            ->where('categories.PARENT_ID IS NOT NULL')
            ->joinWithI18n('en_GB')
            ->orderByContext()
            ->find($this->getDbConnection())
        ;

        $product_categories = CategoriesQuery::create()
            ->useProductsToCategoriesQuery()
                ->filterByProductsId($id)
            ->endUse()
            ->orderById()
            ->find($this->getDbConnection())
        ;

        $current_product = ProductsQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        $styles = ProductsQuery::create()
            ->filterByMaster($current_product->getSku())
            ->orderBySku()
            ->find($this->getDbConnection())
        ;

        $all_products = ProductsQuery::create()
            ->filterByMaster(NULL)
            ->find($this->getDbConnection())
        ;

        $product_images = ProductsImagesQuery::create()
            ->joinProducts()
            ->filterByProductsId($id)
            ->find($this->getDbConnection())
        ;

        $related_products = RelatedProductsQuery::create()
            ->filterByMaster($current_product->getSku())
            ->find($this->getDbConnection())
        ;

        $product_images_list = array();

        foreach ($product_images as $record) {

            $products_refs = ProductsImagesProductReferencesQuery::create()
                ->joinWithProducts()
                ->joinWithProductsImages()
                ->filterByProductsImagesId($record->getId())
                ->find($this->getDbConnection())
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


        $form_hasVideo = $this->createFormBuilder($current_product)
            ->add('has_video', 'checkbox', array(
                    'label' => 'product.label.has_video',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form_hasVideo->bindRequest($request);

            if ($form_hasVideo->isValid()) {
                $current_product->save($this->getDbConnection());
            }
        }
        return $this->render('AdminBundle:Products:view.html.twig', array(
            'styles'                => $styles,
            'product_categories'    => $product_categories,
            'categories'            => $categories,
            'current_product'       => $current_product,
            'product_images'        => $product_images_list,
            'products'              => $all_products,
            'related_products'      => $related_products,
            'has_video_form'        => $form_hasVideo->createView(),
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function quantityDiscountsAction($product_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $current_product = ProductsQuery::create()
            ->filterById($product_id)
            ->findOne($this->getDbConnection())
        ;

        $domains_availible = DomainsQuery::Create()->find($this->getDbConnection());

        foreach ($domains_availible as $domain) {
            $domains_availible_data[$domain->getId()] = $domain->getDomainKey();
        }

        $quantity_discount = new ProductsQuantityDiscount();
        $quantity_discount->setProductsMaster($current_product->getSku());
        $form = $this->createFormBuilder($quantity_discount)
            ->add('domains_id', 'choice', array(
                    'label'     => 'admin.products.discount.domains_id',
                    'choices'   => $domains_availible_data,
                    'required'  => TRUE,
                    'translation_domain' => 'admin'
                )
            )->add('span', 'integer', array(
                    'label' => 'admin.products.discount.span',
                    'required' => TRUE,
                    'translation_domain' => 'admin'
                )
            )->add('discount', 'number', array(
                    'label' => 'admin.products.discount.discount',
                    'required' => TRUE,
                    'translation_domain' => 'admin'
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $duplicate = ProductsQuantityDiscountQuery::create()
                    ->filterByProductsMaster($quantity_discount->getProductsMaster())
                    ->filterBySpan($quantity_discount->getSpan())
                    ->filterByDomainsId($quantity_discount->getDomainsId())
                    ->findOne($this->getDbConnection())
                ;

                if ($duplicate instanceof ProductsQuantityDiscount) {

                    $duplicate->setDiscount($quantity_discount->getDiscount());
                    $duplicate->save($this->getDbConnection());

                }else{

                    $quantity_discount->save($this->getDbConnection());

                }

                $this->get('session')->setFlash('notice', 'admin.products.discount.saved');
            }
        }

        $quantity_discounts = ProductsQuantityDiscountQuery::create()
            ->joinWithDomains()
            ->filterByProductsMaster($current_product->getSku())
            ->orderByDomainsId()
            ->find($this->getDbConnection())
        ;

        return $this->render('AdminBundle:Products:discount.html.twig', array(
            'quantity_discounts'    => $quantity_discounts,
            'form'                  => $form->createView(),
            'current_product'       => $current_product,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function deleteQuantityDiscountAction($master, $domains_id, $span)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $discount = ProductsQuantityDiscountQuery::create()
            ->filterByProductsMaster($master)
            ->filterBySpan($span)
            ->filterByDomainsId($domains_id)
            ->findOne($this->getDbConnection())
        ;

        if($discount instanceof ProductsQuantityDiscount){
            $discount->delete($this->getDbConnection());

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('delete.products.discount.success', array(), 'admin')
                ));
            }

            $this->get('session')->setFlash('notice', 'delete.products.discount.success');

            return $this->redirect($this->generateUrl('admin_products_discount', array('product_id' => $master->getId())));
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.products.discount.failed', array(), 'admin')
            ));
        }

        $this->get('session')->setFlash('notice', 'delete.products.discount.failed');

        return $this->redirect($this->generateUrl('admin_products_discount', array('product_id' => $master->getId())));

    }

    public function deleteStylesAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $master = ProductsQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        $styles = ProductsQuery::create()
            ->filterByMaster($master->getSku())
            ->find($this->getDbConnection())
        ;

        if($styles instanceof \PropelObjectCollection){
            $styles->delete($this->getDbConnection());

            $this->get('session')->setFlash('notice', 'delete.products.styles.success');

            return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));
        }

        $this->get('session')->setFlash('notice', 'delete.products.styles.failed');

        return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));

    }

    public function deleteStyleAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $style = ProductsQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        if($style instanceof ProductsQuery){
            $style->delete($this->getDbConnection());

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
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.products.style.failed', array(), 'admin')
            ));
        }

        $this->get('session')->setFlash('notice', 'delete.products.style.failed');

        return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));


    }

    public function addCategoryAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $requests = $this->get('request');
        $category_id = $requests->get('category');
        $product_id = $requests->get('product');

        $category_to_product = new ProductsToCategories();
        $category_to_product->setCategoriesId($category_id);
        $category_to_product->setProductsId($product_id);

        try {
            $category_to_product->save($this->getDbConnection());
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $category_to_product = ProductsToCategoriesQuery::create()
            ->filterByCategoriesId($category_id)
            ->filterByProductsId($product_id)
            ->findOne($this->getDbConnection())
        ;

        if($category_to_product)
            $category_to_product->delete($this->getDbConnection());

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.changes.success', array(), 'admin'),
            ));
        }
    }

    public function addRelatedProductAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $requests = $this->get('request');
        $master = $requests->get('master');
        $sku = $requests->get('sku');

        $related_products = new RelatedProducts();
        $related_products->setMaster($master);
        $related_products->setSku($sku);

        try {
            $related_products->save($this->getDbConnection());
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

    public function deleteRelatedProductAction($master, $sku)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $related_product = RelatedProductsQuery::create()
            ->filterByMaster($master)
            ->filterBySku($sku)
            ->findOne($this->getDbConnection())
        ;

        if($related_product instanceof RelatedProducts)
            $related_product->delete($this->getDbConnection());

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
            $reference->save($this->getDbConnection());
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
            ->filterByProductsId($product_id)
            ->findOne($this->getDbConnection())
        ;

        if($product_ref)
            $product_ref->delete($this->getDbConnection());
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
            ->filterById($category_id)
            ->findOne($this->getDbConnection())
        ;

        $categories_result = CategoriesQuery::create()
            ->where('categories.PARENT_ID IS NOT NULL')
            ->joinWithI18n()
            ->orderByParentId()
            ->find($this->getDbConnection())
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
            ->find($this->getDbConnection())
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
            'categories'        => $categories,
            'database' => $this->getRequest()->getSession()->get('database')
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
                ->filterByProductsImagesId($picture_id)
                ->findOne($this->getDbConnection())
                ->setSort($sort)
                ->save($this->getDbConnection())
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
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $parser = new \PropelCSVParser();
        $parser->delimiter = ';';

        $stocks = ProductsStockQuery::create()
            ->useProductsQuery()
                ->orderBySku()
            ->endUse()
            ->joinWithProducts()
            ->withColumn('SUM(products_stock.quantity)', 'totalstock')
            ->groupByProductsId()
            ->find($this->getDbConnection())
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

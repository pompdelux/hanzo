<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\ProductsDomainsPricesQuery;
use Hanzo\Model\ProductsImagesCategoriesSortQuery;
use Hanzo\Model\ProductsImagesCategoriesSort;
use Hanzo\Model\ProductsImagesProductReferences;
use Hanzo\Model\ProductsImagesProductReferencesQuery;
use Hanzo\Model\ProductsToCategoriesQuery;
use Hanzo\Model\ProductsToCategories;
use Hanzo\Model\ProductsImagesQuery;
use Hanzo\Model\ProductsQuery;
use Hanzo\Model\ProductsStockQuery;
use Hanzo\Model\ProductsQuantityDiscountQuery;
use Hanzo\Model\ProductsQuantityDiscount;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\Categories;
use Hanzo\Model\CategoriesQuery;
use Hanzo\Model\RelatedProducts;
use Hanzo\Model\RelatedProductsQuery;

use Hanzo\Bundle\AdminBundle\Event\FilterCategoryEvent;

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

        } elseif (!$category_id) {

            $categories = CategoriesQuery::create()
                ->where('categories.PARENT_ID IS NULL')
                ->joinWithI18n('en_GB')
                ->orderById()
                ->find($this->getDbConnection())
            ;
            $parent_category = null;

        } elseif (!$subcategory_id) {

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

            $products = ProductsQuery::create()
                ->useProductsToCategoriesQuery()
                ->filterByCategoriesId($category_id)
                ->endUse()
                ->find($this->getDbConnection())
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
        $categories_list = [];
        $products_list = [];
        if ($categories) {
            foreach ($categories as $category) {
                $categories_list[] = array(
                    'id'        => $category->getId(),
                    'context'   => $category->getContext(),
                    'is_active' => $category->getIsActive(),
                    'title'     => $category->getTitle(),
                );
            }
        }

        if ($products) {
            foreach ($products as $product) {
                $products_list[] = array(
                    'id'              => $product->getId(),
                    'sku'             => $product->getSku(),
                    'master'          => $product->getMaster(),
                    'size'            => $product->getSize(),
                    'color'           => $product->getColor(),
                    'unit'            => $product->getUnit(),
                    'is_out_of_stock' => $product->getIsOutOfStock(),
                    'is_active'       => $product->getIsActive()
                );
            }

        }

        return $this->render('AdminBundle:Products:list.html.twig', array(
            'categories'      => $categories_list,
            'products'        => $products_list,
            'parent_category' => $parent_category,
            'category_id'     => $category_id,
            'subcategory_id'  => $subcategory_id,
            'search_query'    => $q_clean,
            'database'        => $this->getRequest()->getSession()->get('database')
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

        $product_images_list = [];

        foreach ($product_images as $record) {
            $product_image_in_categories = ProductsImagesCategoriesSortQuery::create()
                ->joinWithCategories()
                ->filterByProductsImagesId($record->getId())
                ->find($this->getDbConnection())
            ;


            $image_categories_list = [];
            foreach ($product_image_in_categories as $ref) {

                $image_categories_list[] = array(
                    'id'          => $record->getId(),
                    'category_id' => $ref->getCategoriesId(),
                    'title'       => $ref->getCategories()->getContext()
                );
            }

            $products_refs = ProductsImagesProductReferencesQuery::create()
                ->joinWithProducts()
                ->joinWithProductsImages()
                ->filterByProductsImagesId($record->getId())
                ->find($this->getDbConnection())
            ;

            $products_refs_list = [];
            if($products_refs){
                foreach ($products_refs as $ref) {

                    $product_ref = $ref->getProducts();

                    $products_refs_list[] = array(
                        'id'    => $product_ref->getId(),
                        'sku'   => $product_ref->getSku(),
                        'color' => $ref->getColor()
                    );
                }
            }

            $product_images_list[$record->getId()] = array(
                'id'               => $record->getProductsId(),
                'image'            => $record->getImage(),
                'image_id'         => $record->getId(),
                'product_ref_ids'  => $products_refs_list,
                'image_categories' => $image_categories_list
            );
        }


        $form_hasVideo = $this->createFormBuilder($current_product)
            ->add('has_video', 'checkbox', array(
                    'label'              => 'product.label.has_video',
                    'translation_domain' => 'admin',
                    'required'           => false
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form_hasVideo->bind($request);

            if ($form_hasVideo->isValid()) {
                $current_product->save($this->getDbConnection());
            }
        }

        $prices_result = ProductsDomainsPricesQuery::create()
            ->filterByProductsId($id)
            ->joinWithDomains()
            ->orderByProductsId()
            ->orderByFromDate()
            ->find($this->getDbConnection());

        $prices = [];
        foreach ($prices_result as $price) {
            $prices[] = [
                'domain'    => $price->getDomains()->getDomainKey(),
                'price'     => number_format($price->getPrice()+$price->getVat(), 2, ',', ''),
                'from_date' => $price->getFromDate('Y-m-d H:i'),
                'to_date'   => ($price->getToDate() ? $price->getToDate('Y-m-d H:i') : '-'),
            ];
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
            'prices'                => $prices,
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
                    'label'              => 'admin.products.discount.span',
                    'required'           => true,
                    'translation_domain' => 'admin'
                )
            )->add('discount', 'number', array(
                    'label'              => 'admin.products.discount.discount',
                    'required'           => true,
                    'translation_domain' => 'admin'
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

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

                $this->get('session')->getFlashBag()->add('notice', 'admin.products.discount.saved');
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
                    'message' => $this->get('translator')->trans('delete.products.discount.success', [], 'admin')
                ));
            }

            $this->get('session')->getFlashBag()->add('notice', 'delete.products.discount.success');

            return $this->redirect($this->generateUrl('admin_products_discount', array('product_id' => $master->getId())));
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.products.discount.failed', [], 'admin')
            ));
        }

        $this->get('session')->getFlashBag()->add('notice', 'delete.products.discount.failed');

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

            $this->get('session')->getFlashBag()->add('notice', 'delete.products.styles.success');

            return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('notice', 'delete.products.styles.failed');

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
                    'message' => $this->get('translator')->trans('delete.products.style.success', [], 'admin')
                ));
            }
            $this->get('session')->getFlashBag()->add('notice', 'delete.products.style.success');

            return $this->redirect($this->generateUrl('admin_product', array('id' => $id)));
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.products.style.failed', [], 'admin')
            ));
        }

        $this->get('session')->getFlashBag()->add('notice', 'delete.products.style.failed');

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
                    'message' => $this->get('translator')->trans('save.changes.failed', [], 'admin')
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', [], 'admin')
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

        if($category_to_product) {
            $category_to_product->delete($this->getDbConnection());
        }

        $node = new Categories();
        $node->setId($category_id);
        $this->get('event_dispatcher')->dispatch('category.node.deleted', new FilterCategoryEvent($node, null, $this->getDbConnection()));

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.changes.success', [], 'admin'),
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
                    'message' => $this->get('translator')->trans('save.changes.failed', [], 'admin')
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', [], 'admin')
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
                'message' => $this->get('translator')->trans('delete.changes.success', [], 'admin'),
            ));
        }
    }

    public function addReferenceAction()
    {
        $requests = $this->get('request');
        $image_id = $requests->get('image');
        $product_id = $requests->get('product');
        $color = $requests->get('color');

        $reference = new ProductsImagesProductReferences();
        $reference->setProductsImagesId($image_id);
        $reference->setProductsId($product_id);
        $reference->setColor($color);

        try {
            $reference->save($this->getDbConnection());
        } catch (PropelException $e) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $this->get('translator')->trans('save.changes.failed', [], 'admin')
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', [], 'admin')
            ));
        }
    }

    public function addReferenceGetColorsAction()
    {
        $requests = $this->get('request');
        $product_id = $requests->get('product');

        $images = ProductsImagesQuery::create()
            ->filterByProductsId($product_id)
            ->groupBy('Color')
            ->find($this->getDbConnection());

        $all_colors = [];

        foreach ($images as $image) {
            $all_colors[] = $image->getColor();
        }
        return $this->json_response(array(
            'status' => TRUE,
            'message' => $this->get('translator')->trans('save.changes.failed', [], 'admin'),
            'data' => $all_colors
        ));
    }

    public function deleteReferenceAction($image_id, $product_id)
    {
        $product_ref = ProductsImagesProductReferencesQuery::create()
            ->filterByProductsImagesId($image_id)
            ->filterByProductsId($product_id)
            ->findOne($this->getDbConnection())
        ;

        if($product_ref) {
            $product_ref->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.imageReference.success', [], 'admin'),
            ));
        }
    }

    public function addImageToCategoryAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $requests = $this->get('request');
        $image_id = $requests->get('image');
        $category_id = $requests->get('category');
        $image = ProductsImagesQuery::create()->findOneById($image_id);

        $reference = new ProductsImagesCategoriesSort();
        $reference->setProductsId($image->getProductsId());
        $reference->setProductsImagesId($image_id);
        $reference->setCategoriesId($category_id);

        $c = ProductsToCategoriesQuery::create()
            ->filterByProductsId($image->getProductsId())
            ->filterByCategoriesId($category_id)
            ->findOne($this->getDbConnection())
        ;

        if (!$c instanceof ProductsToCategories) {
            $c = new ProductsToCategories();
            $c->setProductsId($image->getProductsId());
            $c->setCategoriesId($category_id);
            $c->save($this->getDbConnection());
        }

        try {
            $reference->save($this->getDbConnection());

        } catch (PropelException $e) {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => $this->get('translator')->trans('save.changes.failed', [], 'admin')
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', [], 'admin')
            ));
        }
    }

    public function deleteImageFromCategoryAction($image_id, $category_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $ref = ProductsImagesCategoriesSortQuery::create()
            ->filterByProductsImagesId($image_id)
            ->filterByCategoriesId($category_id)
            ->findOne($this->getDbConnection())
        ;

        if ($ref) {
            $product_id = $ref->getProductsId();
            $ref->delete($this->getDbConnection());

            $image_count = ProductsImagesCategoriesSortQuery::create()
                ->filterByProductsId($product_id)
                ->filterByCategoriesId($category_id)
                ->count($this->getDbConnection())
            ;

            if (0 === $image_count) {
                ProductsToCategoriesQuery::create()
                    ->filterByProductsId($product_id)
                    ->filterByCategoriesId($category_id)
                    ->delete($this->getDbConnection())
                ;
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.success', [], 'admin'),
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

        $categories = [];
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

        $records = [];
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

            $node = new Categories();
            $node->setId($category_id);
            $this->get('event_dispatcher')->dispatch('category.product_sort.update', new FilterCategoryEvent($node, null, $this->getDbConnection()));

            $sort++;
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', [], 'admin')
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

        $stocks = ProductsQuery::create()
            ->leftJoinWithProductsStock()
            ->useProductsStockQuery(null, \Criteria::LEFT_JOIN)
                ->withColumn('SUM(products_stock.quantity)', 'totalstock')
            ->endUse()
            ->groupById()
            ->orderBySku()
            ->filterByMaster(null, \Criteria::ISNOTNULL)
            ->find($this->getDbConnection())
        ;

        $stock_data = [];
        $stock_data[0] = array('SKU','STOCK');

        foreach ($stocks as $stock) {
            $s = $stock->getVirtualColumn('totalstock') ?: 0;
            $stock_data[] = array($stock->getSku(), $s);
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

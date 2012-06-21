<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Model\CouponsQuery,
	Hanzo\Model\Coupons,
	Hanzo\Model\CouponsToCustomersQuery,
    Hanzo\Model\CouponsToCustomers,
	Hanzo\Model\CustomersQuery;
use Hanzo\Model\DomainsSettingsQuery;

class CouponsController extends CoreController
{
    public function indexAction($pager)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

    	$hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');

	    $coupons = CouponsQuery::create()
	    	// ->useCouponsToCustomersQuery()
	    	// 	->withColumn('SUM(coupons_to_customers.use_count)', 'usecount')
	    	// ->endUse()
	    	//->joinWithCouponsToCustomers('LEFT JOIN')
	    ;

    	if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

	    	$coupons = $coupons->filterByCode($q);
	    }

	    $coupons = $coupons->paginate($pager, 20, $this->getDbConnection());

		$paginate = null;
        if ($coupons->haveToPaginate()) {

            $pages = array();
            foreach ($coupons->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }

            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($coupons->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $coupons->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($coupons->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $coupons->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($coupons->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $coupons->getNextPage()), TRUE)),
                    'prew' => ($coupons->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $coupons->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        return $this->render('AdminBundle:Coupons:index.html.twig', array(
            'coupons'     => $coupons,
            'paginate'    => $paginate,
            'database'    => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function viewAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }
        
    	$coupon = null;

    	if ($id) {
    		$coupon = CouponsQuery::create()
    			->findOneById($id, $this->getDbConnection())
    		;
    	}else{
    		$coupon = new Coupons();
    	}

		$currencies_data = array();
    	$currencies = DomainsSettingsQuery::create()
    		->filterByNs('core')
    		->filterByCKey('currency')
    		->find($this->getDbConnection())
    	;

    	foreach ($currencies as $currency) {
    		$currencies_data[$currency->getCValue()] = $currency->getCValue();
    	}

    	$form = $this->createFormBuilder($coupon)
            ->add('code', 'text',
                array(
                    'label' => 'admin.coupons.code',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            )->add('amount', 'number',
                array(
                    'label' => 'admin.coupons.amount',
                    'translation_domain' => 'admin',
                    'required' => true
                )
            // )->add('vat', 'number',
            //     array(
            //         'label' => 'admin.coupons.vat',
            //         'translation_domain' => 'admin',
            //         'required' => false
            //     )
            )->add('currency_code', 'choice',
                array(
                	'choices' => $currencies_data,
                    'label' => 'admin.coupons.currency',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('uses_pr_coupon', 'integer',
                array(
                    'label' => 'admin.coupons.uses_pr_coupon',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('uses_pr_coustomer', 'integer',
                array(
                    'label' => 'admin.coupons.uses_pr_customer',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('active_from', 'date',
                array(
                	'input' => 'string',
                	'widget' => 'single_text',
                	'format' => 'dd-MM-yyyy',
                    'label' => 'admin.coupons.active_from',
                    'translation_domain' => 'admin',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                )
            )->add('active_to', 'date',
                array(
                	'input' => 'string',
                	'widget' => 'single_text',
                	'format' => 'dd-MM-yyyy',
                    'label' => 'admin.coupons.active_to',
                    'translation_domain' => 'admin',
                    'required' => false,
                    'attr' => array('class' => 'datepicker')
                )
            )->getForm()
        ;

        $customers = CustomersQuery::create()->find($this->getDbConnection());

        $couponstocustomers = CouponsToCustomersQuery::create()
        	->joinWithCustomers()
        	->findByCouponsId($coupon->getId(), $this->getDbConnection())
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $coupon->save($this->getDbConnection());

                $this->get('session')->setFlash('notice', 'admin.coupon.inserted');
            }
        }

        return $this->render('AdminBundle:Coupons:view.html.twig', array(
            'form' => $form->createView(),
            'couponstocustomers' => $couponstocustomers,
            'customers' => $customers,
            'coupon' => $coupon,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
    	$coupon = CouponsQuery::create()
            ->findOneById($id, $this->getDbConnection());

        if($coupon instanceof Coupons){
            $coupon->delete($this->getDbConnection());


            if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	                'status' => TRUE,
	                'message' => $this->get('translator')->trans('delete.coupon.success', array(), 'admin'),
	            ));
	        }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.coupon.failed', array(), 'admin'),
            ));
        }


    }

    public function addCustomerAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $requests = $this->get('request');
        $coupon_id = $requests->get('coupon');
        $customer_id = $requests->get('customer');

        $customer = CouponsToCustomersQuery::create()
            ->filterByCouponsId($coupon_id)
            ->filterByCustomersId($customer_id)
            ->findOne($this->getDbConnection())
        ;

        if (!($customer instanceof CouponsToCustomers)) {

            $customer = new CouponsToCustomers();
            $customer->setCouponsId($coupon_id);
            $customer->setCustomersId($customer_id);
            $customer->save($this->getDbConnection());

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('add.coupon.customer.success', array(), 'admin'),
                ));
            }

        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('add.coupon.customer.failed', array(), 'admin'),
            ));
        }
    }

    public function deleteCustomerAction($coupon_id, $customer_id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        
    	$coupon = CouponsToCustomersQuery::create()
    		->filterByCouponsId($coupon_id)
            ->filterByCustomersId($customer_id)
            ->findOne($this->getDbConnection());

        if($coupon instanceof CouponsToCustomers){
            $coupon->delete($this->getDbConnection());


	        if ($this->getFormat() == 'json') {
	            return $this->json_response(array(
	                'status' => TRUE,
	                'message' => $this->get('translator')->trans('delete.coupon.customer.success', array(), 'admin'),
	            ));
	        }

        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.coupon.customer.failed', array(), 'admin'),
            ));
        }
    }
}

<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Criteria;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Hanzo\Model\CouponsQuery;
use Hanzo\Model\Coupons;
use Hanzo\Model\CouponsToCustomersQuery;
use Hanzo\Model\OrdersToCouponsQuery;
use Hanzo\Model\CouponsToCustomers;
use Hanzo\Model\CustomersQuery;
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
            ->orderByActiveFrom(Criteria::DESC)
            ->orderByCreatedAt(Criteria::DESC)
        ;

        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

            $coupons->filterByCode($q);
        } else {
            $coupons->filterByIsActive(true);
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

        $request = $this->getRequest();
        $coupon = null;
        $coupons_history = null;
        if ($id) {
            $coupon = CouponsQuery::create()
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
            $coupons_history = OrdersToCouponsQuery::create()->filterByCouponsId($id)->find();
        } else {
            $coupon = new Coupons();

            if ('GET' === $request->getMethod()) {
                // make sure we have a uniq code for every coupon
                while (true) {
                    $code = substr(str_shuffle(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 2))), 0, 9);
                    $check = CouponsQuery::create()
                        ->filterByCode($code)
                        ->findOne()
                    ;

                    if (empty($check)) {
                        break;
                    }
                }
                $coupon->setCode($code);
            }
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
            ->add('code', 'text', array(
                'label' => 'admin.coupons.code',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('amount', 'text', array(
                'label' => 'admin.coupons.amount',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('currency_code', 'choice', array(
                'choices' => $currencies_data,
                'label' => 'admin.coupons.currency',
                'translation_domain' => 'admin',
                'required' => false
            ))->add('active_from', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.coupons.active_from',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker')
            ))->add('active_to', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.coupons.active_to',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker')
            ))->add('is_active', 'checkbox', array(
                'label' => 'admin.customer.is_active',
                'translation_domain' => 'admin',
                'required' => false,
            ))->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {

                $coupon->save($this->getDbConnection());

                $this->get('session')->setFlash('notice', 'admin.coupon.inserted');
            }
        }

        return $this->render('AdminBundle:Coupons:view.html.twig', array(
            'form' => $form->createView(),
            'coupon' => $coupon,
            'coupons_history' => $coupons_history,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $coupon = CouponsQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

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
}

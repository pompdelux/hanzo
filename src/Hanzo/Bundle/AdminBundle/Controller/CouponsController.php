<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Criteria;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Hanzo\Model\CouponsQuery;
use Hanzo\Model\Coupons;
use Hanzo\Model\CouponsPeer;
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
                if (isset($_GET['q'])) {
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                } else{
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);
                }
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
            $coupon->setActiveFrom($coupon->getActiveFrom());
            $coupon->setActiveTo($coupon->getActiveTo());
        } else {
            $coupon = new Coupons();

            if ('GET' === $request->getMethod()) {
                $coupon->setCode(CouponsPeer::generateCode(9, '', $this->getDbConnection()));
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
            ))->add('min_purchase_amount', 'text', array(
                'label' => 'admin.coupons.min.purchase.amount',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('currency_code', 'choice', array(
                'choices' => $currencies_data,
                'label' => 'admin.coupons.currency',
                'translation_domain' => 'admin',
                'required' => false
            ))->add('active_from', 'datetime', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.coupons.active_from',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker')
            ))->add('active_to', 'datetime', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.coupons.active_to',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker')
            ))->add('is_active', 'checkbox', array(
                'label' => 'admin.coupon.is_active',
                'translation_domain' => 'admin',
                'required' => false,
            ))->add('is_used', 'checkbox', array(
                'label' => 'admin.coupon.is_used',
                'translation_domain' => 'admin',
                'required' => false,
            ))
            ->getForm()
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

    public function batchAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $coupon = new Coupons();

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
                'label' => 'Prefix:',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('amount', 'text', array(
                'label' => 'Beløb:',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('currency_code', 'choice', array(
                'choices' => $currencies_data,
                'label' => 'Valuta:',
                'translation_domain' => 'admin',
                'required' => false
            ))->add('quantity', 'text', array(
                'label' => 'Antal rabatkoder:',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('min_purchase_amount', 'text', array(
                'label' => 'Mindstekøb:',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('active_from', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Aktiv fra den:',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker')
            ))->add('active_to', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Aktiv til den:',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker')
            ))->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $out      = [];
                $post     = $form->getData();
                $quantity = (int) $post->getQuantity();

                for ($i=0; $i<$quantity; $i++) {
                    $code = $post->getCode().CouponsPeer::generateCode(9, '', $this->getDbConnection());

                    $c = new Coupons();
                    $c->setCode($code);
                    $c->setAmount($post->getAmount());
                    $c->setMinPurchaseAmount($post->getMinPurchaseAmount());
                    $c->setActiveFrom($post->getActiveFrom());
                    $c->setActiveTo($post->getActiveTo());
                    $c->setCurrencyCode($post->getCurrencyCode());
                    $c->setIsActive(true);
                    $c->setIsUsed(false);
                    $c->save($this->getDbConnection());

                    $data = $c->toArray();
                    unset($data['Id'], $data['CreatedAt'], $data['UpdatedAt'], $data['IsActive'], $data['IsUsed']);
                    $out[] = '"'.implode('";"', $data).'"';
                }

                if (count($out)) {
                    $this->writeCouponFile($out);
                    $this->get('session')->setFlash('notice', 'Der er nu oprettet '.$quantity.' nye rabatkoder, filen kan downloades herunder.');
                }
            }

            return $this->redirect($this->generateUrl('admin_coupons_batch'));
        }

        return $this->render('AdminBundle:Coupons:batch.html.twig', array(
            'coupon'   => $coupon,
            'database' => $this->getRequest()->getSession()->get('database'),
            'files'    => $this->listCouponFiles(),
            'form'     => $form->createView(),
        ));
    }

    public function deleteCouponFileAction(Request $request)
    {
        $target = $this->getCouponsDir().basename($request->query->get('filename'));
        if (is_file($target)) {
            unlink($target);
        }

        return $this->redirect($this->generateUrl('admin_coupons_batch'));
    }


    /**
     * statsAction
     *
     * @param  Request $request
     * @return array
     *
     * @Template("AdminBundle:Coupons:stats.html.twig")
     */
    public function statsAction(Request $request)
    {
        $data = [];
        if ($request->query->get('start') && $request->query->get('end')) {

            $data['used'] = 0;
            $data['used_amount'] = 0;
            $data['unused'] = 0;
            $data['expired'] = 0;

            $coupons_total = CouponsQuery::create()
                ->find($this->getDbConnection())
            ;

            foreach ($coupons_total as $coupon) {
                if ((date('YmdHi') > $coupon->getActiveTo('YmdHi')) && (false === $coupon->getIsUsed())) {
                    $data['expired']++;
                }
            }

            $coupons_used = CouponsQuery::create()
                ->useOrdersToCouponsQuery()
                    ->join('Orders', Criteria::LEFT_JOIN)
                    ->useOrdersQuery()
                        ->filterByCreatedAt([
                            'max' => new \DateTime($request->query->get('end').' 23:59:59'),
                            'min' => new \DateTime($request->query->get('start')),
                        ])
                    ->endUse()
                ->endUse()
                ->with('Orders')
                ->filterByIsUsed(true)
                ->find($this->getDbConnection())
            ;

            foreach ($coupons_used as $coupon) {
                $data['used']++;
                $data['used_amount'] += $coupon
                    ->getOrdersToCouponss(null, $this->getDbConnection())
                    ->getFirst()
                    ->getOrders($this->getDbConnection())
                    ->getTotalPrice()
                ;
            }

            $data['total'] = $coupons_total->count();
            $data['unused'] = $data['total'] - ($data['used'] + $data['expired']);

            unset($coupon, $coupons_total, $coupons_used);
        }

        return [
            'database' => $this->getRequest()->getSession()->get('database'),
            'data'     => $data,
        ];
    }


    protected function writeCouponFile($data)
    {
        $target = $this->getCouponsDir().''.time().'.csv';
        return file_put_contents($target, implode("\r\n", $data));
    }

    protected function listCouponFiles()
    {
        $root = $this->get('request')->server->get('DOCUMENT_ROOT');

        $files = [];
        $finder = new Finder();
        $finder->files()->name('*.csv');

        foreach ($finder->in($this->getCouponsDir()) as $file) {
            $files[] = [
                'name' => date('Y-m-d H:i:s', $file->getBasename('.csv')),
                'path' => '/uploads/'.$file->getBasename(),
            ];
        }

        return $files;
    }

    protected function getCouponsDir()
    {
        return realpath($this->get('kernel')->getRootDir().'/../web/uploads/').'/';
    }
}

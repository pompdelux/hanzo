<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Criteria;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Core\Tools;

use Hanzo\Model\GiftCardsQuery;
use Hanzo\Model\GiftCards;
use Hanzo\Model\GiftCardsPeer;
use Hanzo\Model\GiftCardsToCustomersQuery;
use Hanzo\Model\OrdersToGiftCardsQuery;
use Hanzo\Model\GiftCardsToCustomers;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\DomainsSettingsQuery;

class GiftCardsController extends CoreController
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

        $gift_cards = GiftCardsQuery::create()
            ->orderByActiveFrom(Criteria::DESC)
            ->orderByCreatedAt(Criteria::DESC)
        ;

        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

            $gift_cards->filterByCode($q);
        } else {
            $gift_cards->filterByIsActive(true);
        }

        $gift_cards = $gift_cards->paginate($pager, 20, $this->getDbConnection());

        $paginate = null;
        if ($gift_cards->haveToPaginate()) {

            $pages = array();
            foreach ($gift_cards->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }

            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($gift_cards->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $gift_cards->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($gift_cards->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $gift_cards->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($gift_cards->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $gift_cards->getNextPage()), TRUE)),
                    'prew' => ($gift_cards->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $gift_cards->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        return $this->render('AdminBundle:GiftCards:index.html.twig', array(
            'gift_cards'     => $gift_cards,
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
        $gift_card = null;

        if ($id) {
            $gift_card = GiftCardsQuery::create()
                ->filterById($id)
                ->findOne($this->getDbConnection())
            ;
        } else {
            $gift_card = new GiftCards();

            if ('GET' === $request->getMethod()) {
                $gift_card->setCode(GiftCardsPeer::generateCode(9, '', $this->getDbConnection()));
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

        $form = $this->createFormBuilder($gift_card)
            ->add('code', 'text', array(
                'label' => 'admin.gift_cards.code',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('amount', 'text', array(
                'label' => 'admin.gift_cards.amount',
                'translation_domain' => 'admin',
                'required' => true
            ))->add('currency_code', 'choice', array(
                'choices' => $currencies_data,
                'label' => 'admin.gift_cards.currency',
                'translation_domain' => 'admin',
                'required' => false
            ))->add('active_from', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.gift_cards.active_from',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker'),
                'data' => $gift_card->getActiveFrom('Y-m-d'),
            ))->add('active_to', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.gift_cards.active_to',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => array('class' => 'datepicker'),
                'data' => $gift_card->getActiveTo('Y-m-d'),
            ))->add('is_active', 'checkbox', array(
                'label' => 'admin.customer.is_active',
                'translation_domain' => 'admin',
                'required' => false,
            ))->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $gift_card->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'admin.gift_card.inserted');
            }
        }

        return $this->render('AdminBundle:GiftCards:view.html.twig', array(
            'form' => $form->createView(),
            'gift_card' => $gift_card,
            'gift_cards_history' => null,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $gift_card = GiftCardsQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        if($gift_card instanceof GiftCards){
            $gift_card->delete($this->getDbConnection());


            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => TRUE,
                    'message' => $this->get('translator')->trans('delete.gift_card.success', array(), 'admin'),
                ));
            }
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => $this->get('translator')->trans('delete.gift_card.failed', array(), 'admin'),
            ));
        }


    }
}

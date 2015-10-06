<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Hanzo\Model\ShippingMethods;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\DomainsQuery;
use Hanzo\Model\FreeShipping;
use Hanzo\Model\FreeShippingQuery;
use Hanzo\Model\ShippingMethodsQuery;

use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

class ShippingController extends CoreController
{

    /**
     * List Free Shipping table
     *
     * @param Request $request
     * @Template()
     * @return array
     * @throws AccessDeniedException
     */
    public function listFreeBreaksAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")'))) {
            throw new AccessDeniedException();
        }

        $pager  = $request->query->get('pager', 0);
        $route  = $request->attributes->get('_route');
        $router = $this->get('router');

        $breaks = FreeShippingQuery::create()
            ->orderByValidFrom('DESC')
            ->orderByDomainKey('ASC')
            ->paginate($pager, 50, $this->getDbConnection())
        ;

        $paginate = null;
        if ($breaks->haveToPaginate()) {

            $pages = array();
            foreach ($breaks->getLinks(20) as $page) {
                $pages[$page] = $router->generate($route, ['pager' => $page]);
            }

            $paginate = [
                'index' => $pager,
                'next'  => ($breaks->getNextPage()     == $pager ? '' : $router->generate($route, ['pager' => $breaks->getNextPage()])),
                'pages' => $pages,
                'prew'  => ($breaks->getPreviousPage() == $pager ? '' : $router->generate($route, ['pager' => $breaks->getPreviousPage()])),
            ];
        }

        return [
            'breaks'   => $breaks,
            'database' => $this->getRequest()->getSession()->get('database'),
            'paginate' => $paginate,
        ];
    }

    /**
     * Add or edit breaks
     *
     * @Template()
     * @param  Request $request
     * @param  integet $id
     * @return array
     * @throws AccessDeniedException
     */
    public function editFreeBreaksAction(Request $request, $id = NULL)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        if ($id) {
            $break = FreeShippingQuery::create()->findOneById($id, $this->getDbConnection());
        } else {
            $break = new FreeShipping();
        }

        $domains = ['' => '- vælg -'];
        foreach (DomainsQuery::Create()->find($this->getDbConnection()) as $domain) {
            $domains[$domain->getDomainKey()] = $domain->getDomainKey();
        }

        $form = $this->createFormBuilder($break)
            ->add('domain_key', 'choice', [
                'choices' => $domains,
                'required' => true,
            ])
            ->add('break_at', 'text', [
                'required' => true,
            ])
            ->add('valid_from', 'date', [
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.coupons.active_from',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => ['class' => 'datepicker']
            ])
            ->add('valid_to', 'date', [
                'input' => 'string',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'admin.coupons.active_to',
                'translation_domain' => 'admin',
                'required' => false,
                'attr' => ['class' => 'datepicker']
            ])
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $break->save($this->getDbConnection());
                $this->get('session')->getFlashBag()->add('notice', 'Gemt!');
            }

            return $this->redirect($this->generateUrl('admin_shipping_breaks'));
        }

        return [
            'break_id' => $id,
            'database' => $this->getRequest()->getSession()->get('database'),
            'form'     => $form->createView(),
        ];
    }


    /**
     * @param  Request $request
     * @param  int     $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function deleteFreeBreaksAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        FreeShippingQuery::create()->filterById($id)->delete($this->getDbConnection());
        $this->get('session')->getFlashBag()->add('notice', 'Så er den slettet!');

        return $this->redirect($this->generateUrl('admin_shipping_breaks'));
    }


    /**
     * @Template()
     * @return array
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function listShippingModulesAction()
    {
        if (false === $this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")'))) {
            throw new AccessDeniedException();
        }

        return [
            'database' => $this->getRequest()->getSession()->get('database'),
            'modules'  => ShippingMethodsQuery::create()
                    ->orderByCarrier()
                    ->orderByMethod()
                    ->orderByExternalId()
                    ->find($this->getDbConnection())
        ];
    }


    /**
     * @param  ShippingMethods $module
     * @param  Request $request
     * @return array
     * @Template()
     * @ParamConverter("module", class="\Hanzo\Model\ShippingMethods")
     */
    public function editShippingModuleAction(ShippingMethods $module, Request $request)
    {
        $form = $this->createFormBuilder($module)
            ->add('carrier', 'text')
            ->add('method', 'text')
            ->add('external_id', 'integer')
            ->add('price', 'money', ['currency' => false])
            ->add('fee', 'money', ['currency' => false, 'required' => false])
            ->add('fee_external_id', 'integer', ['required' => false])
            ->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $module->save($this->getDbConnection());
                $this->get('session')->getFlashBag()->add('notice', 'Fragtprisen er nu opdateret, bemærk at Redis skal ryddes før ændringerne kan træde i kraft.');

                return $this->redirect($this->generateUrl('admin_shipping_index'));
            }
        }

        return [
            'database' => $this->getRequest()->getSession()->get('database'),
            'form'     => $form->createView()
        ];
    }
}

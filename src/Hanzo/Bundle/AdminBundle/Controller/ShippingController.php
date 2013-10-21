<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\DomainsQuery;
use Hanzo\Model\FreeShipping;
use Hanzo\Model\FreeShippingQuery;

class ShippingController extends CoreController
{

    /**
     * List Free Shipping table
     *
     * @Template()
     * @return array
     */
    public function listFreeBreaksAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
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
     */
    public function editFreeBreaksAction(Request $request, $id = 0)
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
            ->add('break_at', 'integer', [
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
            ->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $break->save($this->getDbConnection());
                $this->get('session')->setFlash('notice', 'Gemt!');
            }

            return $this->redirect($this->generateUrl('admin_shipping_index'));
        }

        return [
            'break_id' => ($break->getId() ?: 0),
            'database' => $this->getRequest()->getSession()->get('database'),
            'form'     => $form->createView(),
        ];
    }

    public function deleteFreeBreaksAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        FreeShippingQuery::create()->filterById($id)->delete($this->getDbConnection());
        $this->get('session')->setFlash('notice', 'Så er den slettet.');

        return $this->redirect($this->generateUrl('admin_shipping_index'));
    }
}

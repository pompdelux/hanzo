<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;

use Hanzo\Model\CustomersQuery;
use Hanzo\Model\Addresses;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\GroupsQuery;

use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

/**
 * Class CustomersController
 *
 * @package Hanzo\Bundle\AdminBundle\Controller
 */
class CustomersController extends CoreController
{
    /**
     * @param Request $request
     * @param string $domain_key
     * @param int    $pager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction(Request $request, $domain_key, $pager)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE") or hasRole("ROLE_LOGISTICS")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $route  = $request->get('_route');
        $router = $this->container->get('router');

        $customers = CustomersQuery::create();

        if ($request->query->has('debitor')) {
            $debitor   = $request->query->get('debitor');
            $customers = $customers->filterById($debitor);
        }

        $qClean = null;
        if ($request->query->has('q')) {
            $qClean = $request->query->get('q');
            $q = '%'.$qClean.'%';

            /**
             * @todo Lav søgning så man kan søge på hele navn. Sammenkobling på for og efternavn.
             */
            $customers = $customers
                ->filterByFirstname($q)
                ->_or()
                    ->filterByLastname($q)
                ->_or()
                    ->filterByEmail($q)
                ->_or()
                    ->filterByPhone($q)
                ->_or()
                    ->filterById($qClean);
        }

        if ($domain_key) {
            $customers = $customers
                ->useOrdersQuery()
                    ->useOrdersAttributesQuery()
                        ->filterByCKey('domain_key')
                        ->filterByCValue($domain_key)
                    ->endUse()
                    ->joinOrdersAttributes()
                ->endUse()
                ->groupById();
        }

        $customers = $customers
            ->orderByUpdatedAt('DESC')
            ->orderByFirstName()
            ->orderByLastName()
            ->paginate($pager, 50, $this->getDbConnection());

        $paginate = null;
        if ($customers->haveToPaginate()) {

            $pages = [];
            foreach ($customers->getLinks(10) as $page) {
                if ($qClean) {
                    $pages[$page] = $router->generate($route, ['pager' => $page, 'q' => $qClean], true);
                } else {
                    $pages[$page] = $router->generate($route, ['pager' => $page], true);
                }
            }

            // If search query, add it to the route
            if ($qClean) {
                $paginate = [
                    'next' => ($customers->getNextPage() == $pager ? '' : $router->generate($route, ['pager' => $customers->getNextPage(), 'q' => $qClean], true)),
                    'prew' => ($customers->getPreviousPage() == $pager ? '' : $router->generate($route, ['pager' => $customers->getPreviousPage(), 'q' => $qClean], true)),
                    'pages' => $pages,
                    'index' => $pager
                ];
            } else {
                $paginate = [
                    'next' => ($customers->getNextPage() == $pager ? '' : $router->generate($route, ['pager' => $customers->getNextPage()], true)),
                    'prew' => ($customers->getPreviousPage() == $pager ? '' : $router->generate($route, ['pager' => $customers->getPreviousPage()], true)),
                    'pages' => $pages,
                    'index' => $pager
                ];
            }
        }

        return $this->render('AdminBundle:Customers:list.html.twig', [
            'customers'         => $customers,
            'paginate'          => $paginate,
            'domain_key'        => $domain_key,
            'domains_availible' => DomainsQuery::Create()->find($this->getDbConnection()),
            'database'          => $this->getRequest()->getSession()->get('database')
        ]);

    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function viewAction(Request $request, $id)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE") or hasRole("ROLE_LOGISTICS")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $readOnly = !$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")'));
        $readOnlyEnabled = !$this->get('security.context')->isGranted(
            new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_SALES") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")')
        );

        $customer = CustomersQuery::create()
            ->filterById($id)
            ->findOne($this->getDbConnection());

        $addresses = AddressesQuery::create()
            ->filterByCustomersId($id)
            ->find($this->getDbConnection());

        $groups = GroupsQuery::create()->find($this->getDbConnection());

        $groupChoices = [];
        foreach ($groups as $group) {
            $groupChoices[$group->getId()] = $group->getName();
        }

        $form = $this->createFormBuilder($customer)
            ->add('first_name', 'text', [
                'label'              => 'admin.customer.first_name.label',
                'translation_domain' => 'admin',
                'disabled'           => $readOnly,
            ])->add('last_name', 'text', [
                'label'              => 'admin.customer.last_name.label',
                'translation_domain' => 'admin',
                'disabled'           => $readOnly,
            ])->add('groups_id', 'choice', [
                'choices'            => $groupChoices,
                'label'              => 'admin.customer.group.label',
                'translation_domain' => 'admin',
                'disabled'           => $readOnly,
            ])->add('email', 'text', [
                'label'              => 'admin.customer.email.label',
                'translation_domain' => 'admin',
                'disabled'           => $readOnly,
            ])->add('phone', 'text', [
                'label'              => 'admin.customer.phone.label',
                'translation_domain' => 'admin',
                'required'           => false,
                'disabled'           => $readOnly,
            ])->add('discount', 'text', [
                'label'              => 'admin.customer.discount.label',
                'translation_domain' => 'admin',
                'disabled'           => $readOnly,
            ])->add('password_clear', 'text', [
                'label'              => 'admin.customer.password_clear.label',
                'translation_domain' => 'admin'
            ])->add('is_active', 'checkbox', [
                'label'              => 'admin.customer.is_active.label',
                'translation_domain' => 'admin',
                'required'           => false,
                'disabled'           => $readOnlyEnabled,
            ])->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $customer->setPassword(sha1($customer->getPasswordClear()));
                $customer->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'customer.updated');
            }
        }

        return $this->render('AdminBundle:Customers:view.html.twig', [
                'form'      => $form->createView(),
                'customer'  => $customer,
                'addresses' => $addresses,
                'database'  => $request->getSession()->get('database')
        ]);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        CustomersQuery::create()
            ->filterById($id)
            ->delete($this->getDbConnection());

        if ($this->getFormat() == 'json') {
            return $this->json_response([
                'status'  => true,
                'message' => $this->get('translator')->trans('delete.customer.success', [], 'admin'),
            ]);
        }
    }

    /**
     * @param Request $request
     * @param int     $id
     * @param string  $type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \PropelException
     */
    public function editAddressAction(Request $request, $id, $type)
    {
        if (false === $this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $address = null;
        if ($type) {
            $address = AddressesQuery::create()
                ->filterByType($type)
                ->filterByCustomersId($id)
                ->findOne($this->getDbConnection());
        } else {
            $address = new Addresses();
        }

        $form = $this->createFormBuilder($address)
            ->add('first_name', 'text', [
                'label'              => 'admin.customers.addresses.first_name',
                'translation_domain' => 'admin'
            ])->add('last_name', 'text', [
                'label'              => 'admin.customers.addresses.last_name',
                'translation_domain' => 'admin'
            ])->add('address_line_1', 'text', [
                'label'              => 'admin.customers.addresses.address_line_1',
                'translation_domain' => 'admin'
            ])->add('address_line_2', 'text', [
                'label'              => 'admin.customers.addresses.address_line_2',
                'translation_domain' => 'admin',
                'required'           => false
            ])->add('postal_code', 'text', [
                'label'              => 'admin.customers.addresses.postal_code',
                'translation_domain' => 'admin'
            ])->add('city', 'text', [
                'label'              => 'admin.customers.addresses.city',
                'translation_domain' => 'admin'
            ])->add('country', 'text', [
                'label'              => 'admin.customers.addresses.country',
                'translation_domain' => 'admin'
            ])->add('state_province', 'text', [
                'label'              => 'admin.customers.addresses.state_province',
                'translation_domain' => 'admin',
                'required'           => false,
            ])->add('company_name', 'text', [
                'label'              => 'admin.customers.addresses.company_name',
                'translation_domain' => 'admin',
                'required'           => false
            ])->add('latitude', 'text', [
                'label'              => 'admin.customers.addresses.latitude',
                'translation_domain' => 'admin',
                'required'           => false
            ])->add('longitude', 'text', [
                'label'              => 'admin.customers.addresses.longitude',
                'translation_domain' => 'admin',
                'required'           => false
            ])->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $address->save($this->getDbConnection());
                $this->get('session')->getFlashBag()->add('notice', 'address.updated');
            }
        }

        return $this->render('AdminBundle:Customers:editAddress.html.twig', [
            'form'     => $form->createView(),
            'address'  => $address,
            'database' => $request->getSession()->get('database')
        ]);
    }
}

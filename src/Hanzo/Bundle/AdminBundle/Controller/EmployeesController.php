<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\CustomersQuery;

use Propel\Runtime\Parser\PropelCSVParser;

class EmployeesController extends CoreController
{
    
    public function indexAction($pager)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');
        $employees = null;

        // Search parameter
        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

            /**
             * @todo Lav søgning så man kan søge på hele navn. Sammenkobling på for og efternavn.
             */
            $employees = CustomersQuery::create()
                ->filterByFirstname($q)
                ->_or()
                ->filterByLastname($q)
                ->_or()
                ->filterByEmail($q)
                ->_or()
                ->filterByPhone($q)
                ->_or()
                ->filterById($q_clean)
                ->useGroupsQuery()
                    ->filterByName('employee')
                ->endUse()
                ->orderByIsActive('DESC')
                ->orderByFirstName()
                ->orderByLastName()
                ->paginate($pager, 50, $this->getDbConnection())
            ;
            
        } else {

            $employees = CustomersQuery::create()
                ->useGroupsQuery()
                    ->filterByName('employee')
                ->endUse()
                ->orderByIsActive('DESC')
                ->orderByFirstName()
                ->orderByLastName()
                ->paginate($pager, 50, $this->getDbConnection())
            ;

        }
        $paginate = null;
        if ($employees->haveToPaginate()) {

            $pages = array();
            foreach ($employees->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }

            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($employees->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $employees->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($employees->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $employees->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($employees->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $employees->getNextPage()), TRUE)),
                    'prew' => ($employees->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $employees->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        return $this->render('AdminBundle:Employees:list.html.twig', array(
            'employees'     => $employees,
            'paginate'      => $paginate,
            'database'      => $this->getRequest()->getSession()->get('database')
        ));
    }
}

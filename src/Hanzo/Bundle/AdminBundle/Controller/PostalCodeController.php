<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\ZipToCityQuery,
	Hanzo\Model\ZipToCity,
	Hanzo\Model\DomainsQuery,
	Hanzo\Model\CountriesQuery;


class PostalCodeController extends CoreController
{
    
    public function indexAction($locale, $pager)
    {
        $hanzo = Hanzo::getInstance();
        $container = $hanzo->container;
        $route = $container->get('request')->get('_route');
        $router = $container->get('router');
        
        $zip_to_city = ZipToCityQuery::create();
        if($locale)
        	$zip_to_city->filterByCountriesIso2($locale);

        if (isset($_GET['q'])) {
            $q_clean = $this->getRequest()->get('q', null);
            $q = '%'.$q_clean.'%';

            $zip_to_city = $zip_to_city
                ->filterByZip($q)
                ->_or()
                ->filterByCity($q)
                ->_or()
                ->filterByCountyName($q)
                ->_or()
                ->filterByComment($q)
                ->orderByZip()
                ->orderByCity()
                ->paginate($pager, 50)
            ;
        } else {

            $zip_to_city = $zip_to_city
                ->orderByZip()
                ->orderByCity()
                ->paginate($pager, 50)
            ;
        }
        $paginate = null;
        if ($zip_to_city->haveToPaginate()) {

            $pages = array();
            foreach ($zip_to_city->getLinks(20) as $page) {
                if (isset($_GET['q']))
                    $pages[$page] = $router->generate($route, array('pager' => $page, 'q' => $_GET['q']), TRUE);
                else
                    $pages[$page] = $router->generate($route, array('pager' => $page), TRUE);

            }
            
            if (isset($_GET['q'])) // If search query, add it to the route
                $paginate = array(
                    'next' => ($zip_to_city->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $zip_to_city->getNextPage(), 'q' => $_GET['q']), TRUE)),
                    'prew' => ($zip_to_city->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $zip_to_city->getPreviousPage(), 'q' => $_GET['q']), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
            else
                $paginate = array(
                    'next' => ($zip_to_city->getNextPage() == $pager ? '' : $router->generate($route, array('pager' => $zip_to_city->getNextPage()), TRUE)),
                    'prew' => ($zip_to_city->getPreviousPage() == $pager ? '' : $router->generate($route, array('pager' => $zip_to_city->getPreviousPage()), TRUE)),

                    'pages' => $pages,
                    'index' => $pager
                );
        }

        $domains_availible = ZipToCityQuery::Create()
            ->groupByCountriesIso2()
            ->find()
        ;

        return $this->render('AdminBundle:PostalCode:index.html.twig', array(
            'zip_to_city'     => $zip_to_city,
            'paginate'      => $paginate,
            'domains_availible' => $domains_availible
        ));

    }

    public function viewAction($id)
    {
    	$zip_to_city = null;
    	if($id)
			$zip_to_city = ZipToCityQuery::create()
	            ->findOneById($id)
	        ;
	    else
	    	$zip_to_city = new ZipToCity();

        $countries_availible = CountriesQuery::Create()
            ->find()
        ;
        $countries_availible_data = array();
        foreach ($countries_availible as $country) {
        	$countries_availible_data[$country->getIso2()] = $country->getName();
        }
        $form = $this->createFormBuilder($zip_to_city)
            ->add('zip', 'text',
                array(
                    'label' => 'admin.zip_to_city.zip.label',
                    'translation_domain' => 'admin'
                )
            )->add('countries_iso2', 'choice',
                array(
                	'choices' => $countries_availible_data,
                    'label' => 'admin.zip_to_city.countries_iso2.label',
                    'translation_domain' => 'admin'
                )
            )->add('city', 'text',
                array(
                    'label' => 'admin.zip_to_city.city.label',
                    'translation_domain' => 'admin'
                )
            )->add('county_id', 'text',
                array(
                    'label' => 'admin.zip_to_city.county_id.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('county_name', 'text',
                array(
                    'label' => 'admin.zip_to_city.county_name.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->add('comment', 'text',
                array(
                    'label' => 'admin.zip_to_city.comment.label',
                    'translation_domain' => 'admin',
                    'required' => false
                )
            )->getForm()
        ;

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
              
                $zip_to_city->save();

                $this->get('session')->setFlash('notice', 'zip_to_city.updated');
            }
        }

        return $this->render('AdminBundle:PostalCode:view.html.twig', array(
            'form'      => $form->createView(),
            'zip_to_city'  => $zip_to_city
        ));
    }

    public function deleteAction($id)
    {
        $zip_to_city = ZipToCityQuery::create()
        	->findOneById($id);

        if($zip_to_city instanceof ZipToCity){
            $zip_to_city->delete();
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.zip_to_city.success', array(), 'admin'),
            ));
        }
    }
}

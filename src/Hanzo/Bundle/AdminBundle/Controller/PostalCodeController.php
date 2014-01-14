<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\ZipToCityQuery;
use Hanzo\Model\ZipToCity;
use Hanzo\Model\DomainsQuery;
use Hanzo\Model\CountriesQuery;

class PostalCodeController extends CoreController
{

    public function indexAction($locale, $pager)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

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
                ->paginate($pager, 50, $this->getDbConnection())
            ;
        } else {

            $zip_to_city = $zip_to_city
                ->orderByZip()
                ->orderByCity()
                ->paginate($pager, 50, $this->getDbConnection())
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
            ->find($this->getDbConnection())
        ;

        return $this->render('AdminBundle:PostalCode:index.html.twig', array(
            'zip_to_city'     => $zip_to_city,
            'paginate'      => $paginate,
            'domains_availible' => $domains_availible,
            'locale' => $locale,
            'database' => $this->getRequest()->getSession()->get('database')
        ));

    }

    public function viewAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }

    	$zip_to_city = null;
    	if($id)
			$zip_to_city = ZipToCityQuery::create()
	            ->filterById($id)
                ->findOne($this->getDbConnection())
	        ;
	    else
	    	$zip_to_city = new ZipToCity();

        $countries_availible = CountriesQuery::Create()
            ->find($this->getDbConnection())
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
            $form->handleRequest($request);

            if ($form->isValid()) {

                $zip_to_city->save($this->getDbConnection());

                $this->get('session')->getFlashBag()->add('notice', 'zip_to_city.updated');
            }
        }

        return $this->render('AdminBundle:PostalCode:view.html.twig', array(
            'form'      => $form->createView(),
            'zip_to_city'  => $zip_to_city,
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function importAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $domains_availible = ZipToCityQuery::Create()
            ->select('CountriesIso2')
            ->groupByCountriesIso2()
            ->find($this->getDbConnection())
        ;

        $domains = [''=>''];
        foreach ($domains_availible as $domain) {
            $domains[$domain] = $domain;
        }

        $form = $this->createFormBuilder()
            ->add('domain', 'choice', [
                'choices' => $domains,
                'required' => true,
            ])
            ->add('attachment', 'file', [
                'label' => 'CSV Fil',
                'required' => true,
            ])
            ->getForm()
        ;

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            $form_data = $form->getData();

            if (empty($form_data['domain'])) {
                $form->addError(new FormError('Der skal vælges et domain!'));
            }

            $file = $form['attachment']->getData();
            if (('text/csv' !== $file->getClientMimeType()) ||
                ('.csv' !== substr($file->getClientOriginalName(), -4))
            ) {
                $form->addError(new FormError('Filen skal overholde det beskrevne format og være gemt som .csv fil!'));
            }

            $error = null;
            if ($form->isValid()) {
                $handle = $file->openFile();

                $loop = 0;
                while ($line = $handle->fgets()) {
                    $data = str_getcsv($line);
                    if (0 === $loop) {
                        $count = count($data);
                        if ($count > 5 || $count < 2) {
                            $error = 'invalid.file.format';
                            break;
                        }

                        ZipToCityQuery::create()
                            ->filterByCountriesIso2($form_data['domain'])
                            ->delete($this->getDbConnection())
                        ;
                    }

                    $code = new ZipToCity();
                    $code->fromArray([
                        'CountriesIso2' => $form_data['domain'],
                        'Zip'        => $data[0],
                        'City'       => $data[1],
                        'CountyId'   => @$data[2],
                        'CountyName' => @$data[3],
                        'Comment'    => @$data[4],
                    ]);
                    $code->save($this->getDbConnection());

                    $loop++;
                }

                $this->get('session')->getFlashBag()->add('notice', $loop.' postnumre er nu importeret til: '.$form_data['domain']);
                return $this->redirect($this->generateUrl('admin_postalcode_import'));
            }
        }

        return $this->render('AdminBundle:PostalCode:import.html.twig', array(
            'form'      => $form->createView(),
            'database' => $this->getRequest()->getSession()->get('database')
        ));
    }

    public function deleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $zip_to_city = ZipToCityQuery::create()
        	->filterById($id)
            ->findOne($this->getDbConnection())
        ;

        if($zip_to_city instanceof ZipToCity){
            $zip_to_city->delete($this->getDbConnection());
        }

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.zip_to_city.success', array(), 'admin'),
            ));
        }
    }
}

<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;

use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

class PostalCodeController extends CoreController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        $form = $this->getSearchForm();

        $search_result = [];
        $search_empty  = false;
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var Client $guzzle */
            $guzzle = $this->container->get('muneris.guzzle_postal_client');
            $data = $form->getData();

            try {
                $result = $guzzle->get('gpc/countries/'.$data['country'].'/fuzies/'.$data['q'].'', [
                    'Accept' => 'application/json'
                ])->send();
            } catch (\Exception $e) {}

            if (isset($result)) {
                $result = $result->json();

                if (!empty($result['postcodes'])) {
                    $search_result = $result['postcodes'];
                } else {
                    $search_empty = $data['country'].' -> '.$data['q'];
                }
            }
        }

        return $this->render('AdminBundle:PostalCode:index.html.twig', array(
            'search_form'   => $form->createView(),
            'search_result' => $search_result,
            'search_empty'  => $search_empty,
            'database'      => $request->getSession()->get('database')
        ));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function editAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        /** @var Client $guzzle */
        $guzzle = $this->container->get('muneris.guzzle_postal_client');

        $data = [];

        if ($request->query->has('id')) {
            $result = $guzzle->get('gpc/postcodes/'.$request->query->get('id'), ['Accept' => 'application/json'])->send();
            $data = $result->json();
            $data['lat'] = str_replace(',', '.', $data['lat']);
            $data['lng'] = str_replace(',', '.', $data['lng']);
        }

        $id = $request->query->get('id');
        if (empty($id) && isset($data['id'])) {
            $id = $data['id'];
        }

        $builder = $this->createFormBuilder($data);
        $builder->add('country', 'country', [
                'preferred_choices' => ['AD','AI','AT','AW','BE','BL','BM','BQ','BV','CH','CW','DE','DK','ES','FI','FK','FO','FR','GB','GG','GI','GL','GR','GS','IE','IM','IO','IS','IT','JE','KY','LI','LU','MC','MF','MS','MT','NC','NL','NO','PF','PM','PN','PT','SE','SH','SM','SX','TC','TF','VA','VG','WF'],
                'empty_value' => 'Vælg land',
            ])
            ->add('language', 'language', [
                'preferred_choices' => ['ca','da','de','el','en','es','fi','fr','is','it','mt','nl','no','pt','sv'],
                'empty_value' => 'Vælg sprog',
            ])
            ->add('city', 'text')
            ->add('zip_code', 'text')
            ->add('lat', 'text')
            ->add('lng', 'text')
            ->add('save', 'submit', [
                'attr' => ['class' => 'button btn btn-success']
            ])
            ->add('id', 'hidden', ['data' => $id, 'required' => false])
        ;

        if (isset($data['id'])) {
            $builder->add('id', 'hidden', ['data' => $data['id']]);
        }
        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $form_data = $form->getData();
            $data = [
                'country' => $form_data['country'],
                'language' => $form_data['language'],
                'zipCode' => $form_data['zip_code'],
                'city' => $form_data['city'],
                'lat'  => $form_data['lat'],
                'lng'  => $form_data['lng'],
            ];

            if (isset($form_data['id'])) {
                $data['id'] = $form_data['id'];

                try {
                    $result = $guzzle->put('gpc/postcodes/'.$form_data['id'], ['Accept' => 'application/json'], $data)->send();
                } catch (ClientErrorResponseException $e) {
                    Tools::log(get_class_methods($e));
                    Tools::log($e->getResponse());
                    throw $e;
                }
            } else {
                $result = $guzzle->post('gpc/postcodes', ['Accept' => 'application/json'], $data)->send();
            }

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Postnummer ændringerne er nu gemt.'
            );

            return $this->redirect($this->generateUrl('admin_postalcode'));
        }

        return $this->render('AdminBundle:PostalCode:edit.html.twig', array(
            'edit_form'   => $form->createView(),
            'search_form' => $this->getSearchForm()->createView(),
            'database'    => $request->getSession()->get('database')
        ));
    }


    /**
     * @param Request $request
     * @param         $country
     * @param null    $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $country, $id = null)
    {
        if (!$this->get('security.context')->isGranted(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_CUSTOMERS_SERVICE_EXTRA")'))) {
            return $this->redirect($this->generateUrl('admin'));
        }

        /** @var Client $guzzle */
        $guzzle = $this->container->get('muneris.guzzle_postal_client');
        try {
            $guzzle->delete('gpc/postcodes/'.$id, ['Accept' => 'application/json'])->send();
        } catch (\Exception $e) {}

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Postnummeret er nu blevet slettet.'
        );

        return $this->redirect($this->generateUrl('admin_postalcode'));
    }


    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function getSearchForm()
    {
        // note, these are the once provided by the data set in out GeoPostcode data.
        $preferred_choices = ['AD','AI','AT','AW','BE','BL','BM','BQ','BV','CH','CW','DE','DK','ES','FI','FK','FO','FR','GB','GG','GI','GL','GR','GS','IE','IM','IO','IS','IT','JE','KY','LI','LU','MC','MF','MS','MT','NC','NL','NO','PF','PM','PN','PT','SE','SH','SM','SX','TC','TF','VA','VG','WF'];
        return $this->createFormBuilder()
            ->add('country', 'country', [
                'preferred_choices' => $preferred_choices,
                'empty_value' => 'Vælg land',
            ])
            ->add('q', 'text', [
                'attr' => ['placeholder' => 'Postnummer ..']
            ])
            ->add('submit', 'submit', [
                'attr' => ['class' => 'button search btn btn-default'],
                'label' => 'Søg',
            ])
            ->getForm()
        ;
    }
}

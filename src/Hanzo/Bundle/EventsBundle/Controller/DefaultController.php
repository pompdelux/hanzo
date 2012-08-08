<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController,
    Hanzo\Core\Hanzo,
    Hanzo\Model\Customers,
    Hanzo\Model\Addresses,
    Hanzo\Model\CountriesPeer,
    Hanzo\Model\CustomersQuery,
    Hanzo\Bundle\AccountBundle\Form\Type\CustomersType,
    Hanzo\Bundle\AccountBundle\Form\Type\AddressesType
    ;

class DefaultController extends CoreController
{
    
    public function indexAction($name)
    {
        return $this->render('EventsBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * createCustomerAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function createCustomerAction()
    {
        $hanzo = Hanzo::getInstance();

        $domainKey = $hanzo->get('core.domain_key');

        $errors = '';

        $customer = new Customers();
        $addresses = new Addresses();

        $countries = CountriesPeer::getAvailableDomainCountries();

        if ( count( $countries ) == 1 ) // for .dk, .se, .no and maybe .nl
        {
            $addresses->setCountry( $countries[0]->getLocalName() );
            $addresses->setCountriesId( $countries[0]->getId() );
        }

        $customer->addAddresses($addresses);

        $form = $this->createForm(
            new CustomersType(true, new AddressesType($countries)),
            $customer,
            array('validation_groups' => 'customer')
        );

        return $this->render('EventsBundle:Default:create_customer.html.twig', array(
            'page_type' => 'events-create-customer',
            'form' => $form->createView(),
            'errors' => $errors,
            'domain_key' => $domainKey
            ));
    }

    /**
     * fetchCustomerAction
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function fetchCustomerAction($type)
    {
        $request = $this->getRequest();
        $value = $request->get('value');

        $error = false;

        switch ($type) 
        {
          case 'email':
              $customer = CustomersQuery::create()
                  ->findOneByEmail($value);

              if ($customer instanceof Customers) 
              {
                  $data = array(
                      'first_name' => $customer->getFirstName(),
                      'last_name'  => $customer->getLastName(),
                      );;
              }
              break;
          case 'phone':
              // code...
              break;
        }

        return $this->json_response(array(
            'error' => $error,
            'msg'   => '',
            'data'  => $data, 
        ));
    }
}

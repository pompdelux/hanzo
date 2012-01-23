<?php

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CustomersType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('first_name', null, array('translation_domain' => 'account'));
        $builder->add('last_name', null, array('translation_domain' => 'account'));
        $builder->add('initials', null, array('translation_domain' => 'account'));
        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'first_name' => 'password',
            'second_name' => 'password_repeated',
            'translation_domain' => 'account',
        ));

        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'invalid_message' => 'The email fields must match.',
            'first_name' => 'email',
            'second_name' => 'email_repeated',
            'translation_domain' => 'account',
        ));

        $builder->add('phone', null, array('translation_domain' => 'account'));

         $builder->add('addresses', 'collection', array(
             'type' => new AddressesType(),
             'translation_domain' => 'account'
         ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Hanzo\Model\Customers',
        );
    }

    public function getName()
    {
        return 'customers';
    }
}

<?php

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CustomersType extends AbstractType
{
    protected $is_new;

    public function __construct($is_new = TRUE)
    {
        $this->is_new = (boolean) $is_new;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('first_name', null, array('translation_domain' => 'account'));
        $builder->add('last_name', null, array('translation_domain' => 'account'));

        $builder->add('addresses', 'collection', array(
            'type' => new AddressesType(),
            'translation_domain' => 'account'
        ));

        $builder->add('phone', null, array(
            'required' => TRUE,
            'translation_domain' => 'account'
        ));

        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'invalid_message' => 'The email fields must match.',
            'first_name' => 'email_address',
            'second_name' => 'email_address_repeated',
            'translation_domain' => 'account',
        ));

        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'first_name' => 'pass',
            'second_name' => 'pass_repeated',
            'translation_domain' => 'account',
            'required' => $this->is_new,
        ));

        if ($this->is_new) {
            $builder->add('newsletter', 'checkbox', array(
                'label' => 'create.newsletter',
                'required' => false,
                'translation_domain' => 'account',
            ));

            $builder->add('accept', 'checkbox', array(
                'label' => 'create.accept',
                'required' => true,
                'translation_domain' => 'account',
            ));
        }
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

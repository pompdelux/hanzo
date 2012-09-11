<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CustomersType extends AbstractType
{
    protected $is_new;
    protected $addressType;

    public function __construct($is_new = TRUE, AddressesType $addressType)
    {
        $this->addressType = $addressType;
        $this->is_new = (boolean) $is_new;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('first_name', null, array('translation_domain' => 'account', 'trim' => true));
        $builder->add('last_name', null, array('translation_domain' => 'account', 'trim' => true));

        $builder->add('addresses', 'collection', array(
            'type' => $this->addressType,
            'translation_domain' => 'account',
            'error_bubbling' => true,
            'attr' => array('autocomplete' => 'off'),
        ));

        $builder->add('phone', null, array(
            'required' => TRUE,
            'translation_domain' => 'account',
            'error_bubbling' => true,
            'attr' => array('autocomplete' => 'off'),
        ));

        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'invalid_message' => 'email.invalid.match',
            'first_name' => 'email_address',
            'second_name' => 'email_address_repeated',
            'translation_domain' => 'account',
            'error_bubbling' => true,
            'options' => array('attr' => array('autocomplete' => 'off')),
        ));

        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'password.invalid.match',
            'first_name' => 'pass',
            'second_name' => 'pass_repeated',
            'translation_domain' => 'account',
            'required' => $this->is_new,
            'error_bubbling' => true,
            'options' => array('attr' => array('autocomplete' => 'off')),
        ));

        if ($this->is_new) {
            $builder->add('newsletter', 'checkbox', array(
                'label' => 'create.newsletter',
                'required' => false,
                'translation_domain' => 'account',
                'property_path' => false,
                'error_bubbling' => true,
                'attr' => array('autocomplete' => 'off'),
            ));

            $builder->add('accept', 'checkbox', array(
                'label' => 'create.accept',
                'required' => true,
                'translation_domain' => 'account',
                'property_path' => false,
                'error_bubbling' => true,
                'attr' => array('autocomplete' => 'off'),
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

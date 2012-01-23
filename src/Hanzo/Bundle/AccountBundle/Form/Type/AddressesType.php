<?php

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AddressesType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('type', 'hidden', array('translation_domain' => 'account'));
        $builder->add('address_line_1', null, array('translation_domain' => 'account'));
        $builder->add('postal_code', null, array('translation_domain' => 'account'));
        $builder->add('city', null, array('translation_domain' => 'account'));
        $builder->add('country', null, array('translation_domain' => 'account'));

        //$builder->add('address_line_2');
        // $builder->add('state_province');
        // $builder->add('company_name');
        // $builder->add('latitude', 'hidden');
        // $builder->add('longitude', 'hidden');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Hanzo\Model\Addresses',
        );
    }

    public function getName()
    {
        return 'addresses';
    }
}

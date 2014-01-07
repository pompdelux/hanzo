<?php

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Hanzo\Core\Hanzo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddressesType extends AbstractType
{
    private $choices;

    public function __construct($countries = null)
    {
        $this->countries = $countries;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $short_domain_key = substr(Hanzo::getInstance()->get('core.domain_key'), -2);

        $builder->setErrorBubbling(true);
        $builder->add('type', 'hidden', array('translation_domain' => 'account'));

        $builder->add('address_line_1', null, array(
            'required'           => TRUE,
            'translation_domain' => 'account',
            'error_bubbling'     => true,
            'max_length'         => 35
        ));

        $attr = [];
        if (in_array($short_domain_key, ['AT', 'CH', 'DE', 'DK', 'FI', 'NL', 'NO', 'SE'])) {
            $attr = ['class' => 'auto-city'];
        }

        $builder->add('postal_code', null, array(
            'required'           => TRUE,
            'translation_domain' => 'account',
            'error_bubbling'     => true,
            'attr'               => $attr,
        ));
        $builder->add('city', null, array(
            'required'           => TRUE,
            'translation_domain' => 'account',
            'error_bubbling'     => true,
        ));

        if ( count( $this->countries ) > 1 ) {
            $choices = array();
            foreach ($this->countries as $country) {
               $choices[$country->getId()] = $country->getLocalName();
            }

            $builder->add('countries_id', 'choice', array(
                'choices'            => $choices,
                'translation_domain' => 'account',
                'error_bubbling'     => true,
            ));
            $builder->add('country', 'hidden', array('translation_domain' => 'account'));
        } else {
            $builder->add('country', null, array(
                'translation_domain' => 'account',
                'read_only'          => TRUE,
                'error_bubbling'     => true,
            ));
            $builder->add('countries_id', 'hidden', array('translation_domain' => 'account'));
        }
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

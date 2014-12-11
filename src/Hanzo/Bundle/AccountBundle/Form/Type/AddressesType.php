<?php

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Hanzo\Core\Hanzo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AddressesType
 *
 * @package Hanzo\Bundle\AccountBundle
 */
class AddressesType extends AbstractType
{
    /**
     * @param null $countries
     */
    public function __construct($countries = null)
    {
        $this->countries = $countries;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $shortDomainKey = substr(Hanzo::getInstance()->get('core.domain_key'), -2);

        $builder->setErrorBubbling(true);
        $builder->add('type', 'hidden', ['translation_domain' => 'account']);

        $builder->add('address_line_1', null, [
            'required'           => true,
            'translation_domain' => 'account',
            'error_bubbling'     => true,
            'max_length'         => 35,
        ]);

        $attr = [];
        if (in_array($shortDomainKey, ['AT', 'CH', 'DE', 'DK', 'FI', 'NL', 'NO', 'SE'])) {
            $attr = ['class' => 'auto-city'];
        }

        $builder->add('postal_code', null, [
            'required'           => true,
            'translation_domain' => 'account',
            'error_bubbling'     => true,
            'attr'               => $attr,
        ]);
        $builder->add('city', null, [
            'required'           => true,
            'translation_domain' => 'account',
            'error_bubbling'     => true,
        ]);

        if (count($this->countries) > 1) {
            $choices = [];
            foreach ($this->countries as $country) {
                $choices[$country->getId()] = $country->getLocalName();
            }

            $builder->add('countries_id', 'choice', [
                'choices'            => $choices,
                'translation_domain' => 'account',
                'error_bubbling'     => true,
            ]);
            $builder->add('country', 'hidden', ['translation_domain' => 'account']);
        } else {
            $builder->add('country', null, [
                'translation_domain' => 'account',
                'read_only'          => true,
                'error_bubbling'     => true,
            ]);
            $builder->add('countries_id', 'hidden', ['translation_domain' => 'account']);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'account',
            'data_class'         => 'Hanzo\Model\Addresses',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'addresses';
    }
}

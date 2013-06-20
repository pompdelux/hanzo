<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Hanzo\Core\Hanzo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomersType extends AbstractType
{
    protected $is_new;
    protected $addressType;

    public function __construct($is_new = TRUE, AddressesType $addressType)
    {
        $this->addressType = $addressType;
        $this->is_new = (boolean) $is_new;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name', null, array('trim' => true));
        $builder->add('last_name', null, array('trim' => true));

        $builder->add('addresses', 'collection', array(
            'type' => $this->addressType,
            'attr' => array('autocomplete' => 'off'),
        ));

        $builder->add('phone', 'integer', array(
            'required' => TRUE,
            'attr' => array('autocomplete' => 'off'),
        ));

        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'invalid_message' => 'email.invalid.match',
            'first_name' => 'email_address',
            'second_name' => 'email_address_repeated',
            'options' => array('attr' => array('autocomplete' => 'off')),
        ));

        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'password.invalid.match',
            'first_name' => 'pass',
            'second_name' => 'pass_repeated',
            'required' => $this->is_new,
            'options' => array('attr' => array('autocomplete' => 'off')),
        ));

        if ($this->is_new) {
            $attr = [
                'autocomplete' => 'off',
                'checked' => 'checked'
            ];

            // ugly hack to disable default choice for NL
            // TODO: find a better solution
            if ('NL' == substr(Hanzo::getInstance()->get('core.domain_key'), -2)) {
                unset($attr['checked']);
            }

            $builder->add('newsletter', 'checkbox', array(
                'label' => 'create.newsletter',
                'required' => false,
                'property_path' => false,
                'attr' => $attr,
            ));

            $builder->add('accept', 'checkbox', array(
                'label' => 'create.accept',
                'required' => true,
                'property_path' => false,
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'account'
        ));
    }

    public function getName()
    {
        return 'customers';
    }
}

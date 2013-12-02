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

    public function __construct($is_new = true, AddressesType $addressType)
    {
        $this->addressType = $addressType;
        $this->is_new      = (boolean) $is_new;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $short_domain_key = substr(Hanzo::getInstance()->get('core.domain_key'), -2);

        if (in_array($short_domain_key, ['DE'])) {
            $builder->add('title', 'choice', [
                'choices'  => [
                    'female' => 'title.female',
                    'male'   => 'title.male',
                ],
                'label'    => 'title',
                'required' => true,
                'trim'     => true
            ]);
        }

        $builder->add('first_name', null, ['trim' => true]);
        $builder->add('last_name', null, ['trim' => true]);

        $builder->add('addresses', 'collection', [
            'type' => $this->addressType,
            'attr' => ['autocomplete' => 'off'],
        ]);

        $builder->add('phone', null, [
            'required' => true,
            'attr'     => ['autocomplete' => 'off'],
        ]);

        $builder->add('email', 'repeated', [
            'type'            => 'email',
            'invalid_message' => 'email.invalid.match',
            'first_name'      => 'email_address',
            'second_name'     => 'email_address_repeated',
            'options'         => ['attr' => ['autocomplete' => 'off']],
        ]);

        $builder->add('password', 'repeated', [
            'type'            => 'password',
            'invalid_message' => 'password.invalid.match',
            'first_name'      => 'pass',
            'second_name'     => 'pass_repeated',
            'required'        => $this->is_new,
            'options'         => ['attr' => ['autocomplete' => 'off']],
        ]);

        if ($this->is_new) {
            $attr = [
                'autocomplete' => 'off',
                'checked'      => 'checked'
            ];

            // ugly hack to disable default choice for NL
            // TODO: find a better solution
            if ('NL' == $short_domain_key) {
                unset($attr['checked']);
            }

            $builder->add('newsletter', 'checkbox', [
                'label'         => 'create.newsletter',
                'required'      => false,
                'property_path' => false,
                'attr'          => $attr,
            ]);

            $builder->add('accept', 'checkbox', [
                'label'         => 'create.accept',
                'required'      => true,
                'property_path' => false,
                'attr'          => ['autocomplete' => 'off'],
            ]);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'account',
            'data_class' => 'Hanzo\Model\Customers',
        ));
    }

    public function getName()
    {
        return 'customers';
    }
}

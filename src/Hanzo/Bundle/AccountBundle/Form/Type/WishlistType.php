<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WishlistType  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sku', 'text');
        $builder->add('size', 'choice', [
            'empty_value' => 'choose.size'
        ]);
        $builder->add('color', 'choice', [
            'empty_value' => 'choose.color'
        ]);
        $builder->add('quantity', 'choice', [
            'choices' => [1,2,3,4,5,6,7,8,9,10]
        ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'account',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wishlist';
    }
}

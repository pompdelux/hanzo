<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventsTellAFriendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text', [
                'label' => 'events.participants.first_name.label',
            ])->add('last_name', 'text', [
                'label' => 'events.participants.last_name.label',
            ])->add('email', 'email', [
                'label' => 'events.participants.email.label',
                'required' => false
            ])->add('phone', 'text', [
                'label' => 'events.participants.phone.label',
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Hanzo\Model\EventsParticipants',
            'translation_domain' => 'events',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'events_tell_a_friend';
    }
}

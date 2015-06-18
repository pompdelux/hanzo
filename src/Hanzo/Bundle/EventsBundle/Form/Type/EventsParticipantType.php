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

/**
 * Class EventsType
 *
 * @package Hanzo\Bundle\EventsBundle\Form\Type
 */
class EventsParticipantType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name', 'text', [
                'label' => 'events.participants.first_name.label',
            ])->add('last_name', 'text', [
                'label' => 'events.participants.last_name.label',
            ])->add('email', 'email', [
                'label'    => 'events.participants.email.label',
                'required' => false,
            ])->add('phone', 'text', [
                'label'    => 'events.participants.phone.label',
                'required' => false,
                'attr'     => ['class' => 'dk']
            ])->add('tell_a_friend', 'checkbox', [
                'label'    => 'events.participants.tell_a_friend.label',
                'required' => false,
            ])->add('comment', 'textarea', [
                'label'    => 'events.participants.comment.label',
                'required' => false,
                'mapped'   => false,
            ])->add('events_id', 'hidden', [
                'required' => false
            ]);

        if (!empty($options['events_id'])) {
            $builder->add('events_id', 'hidden');
        }
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'events',
            'data_class'         => 'Hanzo\Model\EventsParticipants',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'events_participant';
    }
}

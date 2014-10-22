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

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventsRsvpType extends AbstractType
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

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
            ])->add('phone', 'text', [
                'label'    => 'events.participants.phone.label',
                'required' => false
            ])->add('has_accepted', 'choice', [
                'choices' => [
                    '1' => $this->translator->trans('events.hasaccepted.yes', [], 'events'),
                    '0' => $this->translator->trans('events.hasaccepted.no', [], 'events')
                ],
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'label'    => 'events.participants.has_accepted.label',
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'events',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'events_rsvp';
    }
}

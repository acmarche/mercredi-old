<?php

namespace AcMarche\Mercredi\Admin\Form\Animateur;

use AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Form\Type\AnimateurSelectorType;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use AcMarche\Mercredi\Plaine\Entity\Plaine;

class AnimateurPlainesType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plaine',
                EntityType::class,
                array(
                    'required' => false,
                    'placeholder' => 'Choisissez une plaine',
                    'label' => 'Plaine',
                    'class' => Plaine::class,
                    'query_builder' => function (PlaineRepository $cr) {
                        return $cr->getForSelect();
                    },
                    'expanded' => false,
                    'multiple' => false,
                )
            )
            ->add('animateur', AnimateurSelectorType::class);

        $formModifier = function (FormInterface $form, Plaine $plaine = null) {
            $form->add(
                'jours',
                EntityType::class,
                array(
                    'class' => PlaineJour::class,
                    'placeholder' => '',
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => true,
                    'query_builder' => function (PlaineJourRepository $cr) use ($plaine) {
                        return $cr->getForSelect(array('plaine' => $plaine));
                    },
                )
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $animateurPlaine = $event->getData();
                $plaine = $animateurPlaine->getPlaine();
                $form = $event->getForm();

                $formModifier($form, $plaine);
            }
        );

        // $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    }

    /**
     * Ajoute la liste des dates quand une plaine est selectionnee
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $animateurPlaine = $event->getData();

        $plaine = $animateurPlaine->getPlaine();
        $form = $event->getForm();

        $form->add(
            'jours',
            EntityType::class,
            array(
                'required' => true,
                'label' => 'Jours de plaine',
                'class' => PlaineJour::class,
                'query_builder' => function (PlaineJourRepository $cr) use ($plaine) {
                    return $cr->getForSelect(array('plaine' => $plaine));
                },
                'expanded' => true,
                'multiple' => true,
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => AnimateurPlaine::class,
            )
        );
    }
}

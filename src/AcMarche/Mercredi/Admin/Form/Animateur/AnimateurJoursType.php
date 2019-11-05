<?php

namespace AcMarche\Mercredi\Admin\Form\Animateur;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimateurJoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'jours',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'Jours de garde',
                    'class' => Jour::class,
                    'query_builder' => function (JourRepository $cr) {
                        return $cr->getForAnimateur(null);
                    },
                    'expanded' => true,
                    'multiple' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Animateur::class,
            ]
        );
    }
}

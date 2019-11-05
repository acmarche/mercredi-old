<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Validator\PlaineDates;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'intitule',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [],
                ]
            )
            ->add(
                'prix1',
                MoneyType::class,
                [
                    'required' => true,
                    'attr' => ['data-help' => ' Uniquement les chiffres'],
                    'label' => 'Prix 1er',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    'required' => true,
                    'attr' => ['data-help' => ' Uniquement les chiffres'],
                    'label' => 'Prix 2iem',
                ]
            )
            ->add(
                'prix3',
                MoneyType::class,
                [
                    'required' => true,
                    'attr' => ['data-help' => ' Uniquement les chiffres'],
                    'label' => 'Prix suivant',
                ]
            )
            ->add(
                'jours',
                CollectionType::class,
                [
                    'entry_type' => PlaineJourType::class,
                    'allow_add' => true,
                    'prototype' => true,
                    'required' => true,
                    'label' => 'Les dates',
                    'allow_delete' => true,
                    'constraints' => [new PlaineDates()],
                ]
            )
            ->add(
                'max',
                CollectionType::class,
                [
                    'entry_type' => PlaineMaxType::class,
                    'label' => 'Maximum par groupe',
                ]
            )
            ->add(
                'premat',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Distinguer les prématernelles pour le listing ?',
                ]
            )
            ->add(
                'inscriptionOuverture',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Les parents peuvent inscrire leurs enfants',
                ]
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 8],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Plaine::class,
            ]
        );
    }
}

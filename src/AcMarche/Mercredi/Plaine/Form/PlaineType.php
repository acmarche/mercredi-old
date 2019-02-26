<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'intitule',
                TextType::class,
                array(
                    'required' => true,
                    'attr' => array(),
                )
            )
            ->add(
                'prix1',
                MoneyType::class,
                array(
                    'required' => true,
                    'attr' => ['data-help' => ' Uniquement les chiffres'],
                    'label' => 'Prix 1er',
                )
            )
            ->add(
                'prix2',
                MoneyType::class,
                array(
                    'required' => true,
                    'attr' => ['data-help' => ' Uniquement les chiffres'],
                    'label' => 'Prix 2iem',
                )
            )
            ->add(
                'prix3',
                MoneyType::class,
                array(
                    'required' => true,
                    'attr' => ['data-help' => ' Uniquement les chiffres'],
                    'label' => 'Prix suivant',
                )
            )
            ->add(
                'jours',
                CollectionType::class,
                array(
                    'entry_type' => PlaineJourType::class,
                    'allow_add' => true,
                    'prototype' => true,
                    'label' => 'Les dates',
                    'allow_delete' => true,
                )
            )
            ->add(
                'max',
                CollectionType::class,
                [
                    'entry_type' => PlaineMaxType::class,
                    'label'=>'Maximum par groupe',
                ]
            )
            ->add(
                'premat',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' => 'Distinguer les prématernelles pour le listing ?',
                )
            )
            ->add(
                'inscriptionOuverture',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' => 'Les parents peuvent inscrire leurs enfants',
                )
            )
            ->add(
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 8),
                )
            )
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Plaine::class,
            )
        );
    }
}

<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sexes = EnfanceData::getSexes();
        $anneesScolaires = ScolaireService::getAnneesScolaires();

        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'numero_national',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => true,
                    'placeholder' => 'Choisissez son école',
                ]
            )
            ->add(
                'annee_scolaire',
                ChoiceType::class,
                [
                    'choices' => $anneesScolaires,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $sexes,
                    'placeholder' => 'Choisissez son sexe',
                ]
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 4],
                ]
            )
            ->add(
                'accompagnateurs',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'entry_options' => [],
                    'prototype' => true,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            )
        /*    ->add(
                'image',
                FileType::class,
                array(
                    'label' => "Photo de l'enfant",
                    'required' => false,
                )
            )*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Enfant::class,
            ]
        );
    }
}

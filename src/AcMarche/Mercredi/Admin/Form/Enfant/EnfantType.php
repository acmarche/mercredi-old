<?php

namespace AcMarche\Mercredi\Admin\Form\Enfant;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sexes = EnfanceData::getSexes();
        $anneesScolaires = ScolaireService::getAnneesScolaires();

        $ordres = EnfanceData::getOrdres();
        $groupes = ScolaireService::getGroupesScolaires();

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
                'birthday',
                DateType::class,
                [
                    'label' => 'Né le',
                    'widget' => 'text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => ['class' => 'birthday-text'],
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
                'sexe',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $sexes,
                    'placeholder' => 'Choisissez son sexe',
                ]
            )
            ->add(
                'ordre',
                ChoiceType::class,
                [
                    'choices' => $ordres,
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
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
                'groupe_scolaire',
                ChoiceType::class,
                [
                    'choices' => $groupes,
                    'required' => false,
                    'label' => 'Forcer le groupe scolaire',
                    'placeholder' => 'Choisissez un groupe',
                    'help' => 'Utilisé pour le listing des présences',
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
                'photoAutorisation',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Diffusion de ses photos',
                    'help' => 'Cochez si les parents autorisent la diffusion',
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
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Fiche santé',
                    'required' => false,
                ]
            )
            ->add(
                'fiche',
                FileType::class,
                [
                    'label' => 'Fiche d\'inscription',
                    'required' => false,
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => "Photo de l'enfant",
                    'required' => false,
                ]
            );
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

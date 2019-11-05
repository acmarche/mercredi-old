<?php

namespace AcMarche\Mercredi\Admin\Form\Tuteur;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sexes = EnfanceData::getSexes();
        $civilites = EnfanceData::getCivilites();

        $builder
            ->add(
                'civilite',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $civilites,
                    'placeholder' => 'Choisissez une civilité',
                ]
            )
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
                'adresse',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'gsm',
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
                'conjoint',
                TextType::class,
                [
                    'label' => 'Relation entre les conjoints',
                    'required' => false,
                    'help' => 'Belle-mère, Maman...',
                ]
            )
            ->add(
                'nom_conjoint',
                TextType::class,
                [
                    'label' => 'Nom du conjoint',
                    'required' => false,
                ]
            )
            ->add(
                'prenom_conjoint',
                TextType::class,
                [
                    'label' => 'Prénom du conjoint',
                    'required' => false,
                ]
            )
            ->add(
                'email_conjoint',
                EmailType::class,
                [
                    'required' => false,
                    'label' => 'Email du conjoint',
                ]
            )
            ->add(
                'telephone_conjoint',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone du conjoint',
                ]
            )
            ->add(
                'telephone_bureau_conjoint',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone du bureau du conjoint',
                ]
            )
            ->add(
                'gsm_conjoint',
                TextType::class,
                [
                    'label' => 'Gsm du conjoint',
                    'required' => false,
                ]
            )
            ->add(
                'composition_menage',
                CheckboxType::class,
                [
                    'label' => 'Composition de ménage',
                    'required' => false,
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
                'data_class' => Tuteur::class,
            ]
        );
    }
}

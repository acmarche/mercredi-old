<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoordonneesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'adresse',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'code_postal',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => true,
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
                    'label' => 'Téléphone du bureau',
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
                    'label' => 'Email',
                ]
            )
            ->add(
                'telephone_conjoint',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone',
                ]
            )
            ->add(
                'telephone_bureau_conjoint',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone du bureau',
                ]
            )
            ->add(
                'gsm_conjoint',
                TextType::class,
                [
                    'label' => 'Gsm',
                    'required' => false,
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

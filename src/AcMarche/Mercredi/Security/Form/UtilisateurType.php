<?php

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'email',
                EmailType::class
            )
            ->add(
                'adresse',
                TextType::class,
                [
                    'required' => false,
                    'help' => "L'adresse est utile pour les comptes écoles",
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
                'telephone',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Le téléphone est utile pour les comptes écoles',
                ]
            )
            ->add(
                'groups',
                EntityType::class,
                [
                    'class' => Group::class,
                    'label' => 'Groupe',
                    'help' => 'Sélectionnez les types de compte',
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}

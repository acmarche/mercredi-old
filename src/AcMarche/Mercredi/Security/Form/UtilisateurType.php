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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'adresse',
                TextType::class,
                array(
                    'required' => false,
                    'help' => "L'adresse est utile pour les comptes écoles",
                )
            )
            ->add(
                'code_postal',
                IntegerType::class,
                array(
                    'required' => false
                )
            )
            ->add(
                'localite',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'telephone',
                TextType::class,
                array(
                    'required' => false,
                    'help' => 'Le téléphone est utile pour les comptes écoles',
                )
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


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }
}

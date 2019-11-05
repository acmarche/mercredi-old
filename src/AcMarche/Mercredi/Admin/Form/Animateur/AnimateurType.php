<?php

namespace AcMarche\Mercredi\Admin\Form\Animateur;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimateurType extends AbstractType
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
                EmailType::class,
                [
                    'required' => true,
                    'help' => 'Un compte sera créé automatiquement',
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

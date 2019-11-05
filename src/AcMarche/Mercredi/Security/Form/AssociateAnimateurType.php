<?php

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Repository\AnimateurRepository;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateAnimateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'animateur',
                EntityType::class,
                [
                    'label' => 'Animateur',
                    'class' => Animateur::class,
                    'placeholder' => 'Sélectionnez l\'animateur',
                    'required' => false,
                    'query_builder' => function (AnimateurRepository $cr) {
                        return $cr->getForAssociateParent();
                    },
                ]
            )
            ->add(
                'dissocier',
                CheckboxType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'help' => 'Cochez la case si vous ne voulez plus lié ce compte à un animteur',
                ]
            )
            ->add(
                'sendmail',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Envoyer un email de création de compte',
                    'mapped' => false,
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

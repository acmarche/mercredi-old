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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'animateur',
                EntityType::class,
                array(
                    'label' => 'Animateur',
                    'class' => Animateur::class,
                    'placeholder' => 'Sélectionnez l\'animateur',
                    'required' => false,
                    'query_builder' => function (AnimateurRepository $cr) {
                        return $cr->getForAssociateParent();
                    },
                )
            )
            ->add(
                'dissocier',
                CheckboxType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'help' => 'Cochez la case si vous ne voulez plus lié ce compte à un animteur',
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

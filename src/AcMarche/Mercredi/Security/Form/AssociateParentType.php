<?php

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateParentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'tuteur',
                EntityType::class,
                array(
                    'label' => 'Parent',
                    'class' => Tuteur::class,
                    'placeholder' => 'Sélectionnez le parent',
                    'required' => false,
                    'query_builder' => function (TuteurRepository $cr) {
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
                    'help' => 'Cochez la case si vous ne voulez plus lié ce compte à un parent',
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
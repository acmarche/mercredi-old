<?php

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ecoles',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'placeholder' => 'Sélectionnez une ou plusieurs écoles',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder' => function (EcoleRepository $cr) {
                        return $cr->getForList();
                    },
                ]
            )->add(
                'sendmail',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Envoyer un email de création de compte',
                    'mapped' => false,
                    'data' => true,
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

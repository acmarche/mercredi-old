<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $group = isset($options['group']) ? $options['group'] : null;

        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'users',
                EntityType::class,
                [
                    'class' => User::class,
                    'label' => 'Utilisateurs',
                    'required' => false,
                    'help' => 'Sélectionnez un ou plusieurs utilisateurs',
                    'query_builder' => function (UserRepository $repository) use ($group) {
                        return $repository->getForList($group);
                    },
                    'multiple' => true,
                ]
            )->add(
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
            )->add(
                'gsm',
                TextType::class,
                [
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
                'data_class' => Ecole::class,
                'group' => null,
            ]
        );

        $resolver->setAllowedTypes('group', Group::class);
    }
}

<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcoleType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $group = isset($options['group']) ? $options['group'] : null;

        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                )
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
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Ecole::class,
                'group' => null,
            )
        );

        $resolver->setAllowedTypes('group', Group::class);
    }
}

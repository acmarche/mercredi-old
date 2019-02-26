<?php

namespace AcMarche\Mercredi\Security\Form;

use AcMarche\Mercredi\Security\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => true
            ))
            ->add('roles', CollectionType::class, array(
                'allow_add' => true,
                'prototype' => true,
                'options' => array(
                    'required' => false,
                    'attr' => array('size' => '40px;')
                ),
                'required' => true,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Group::class
        ));
    }
}

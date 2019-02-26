<?php

namespace AcMarche\Mercredi\Admin\Form\Quick;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurQuickType extends AbstractType
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
                'adresse',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'code_postal',
                IntegerType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'localite',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'required' => false,
                    'help'=>'Si une adresse mail est encodée, un compte sera créé',
                )
            )
            ->add(
                'telephone',
                TextType::class,
                array(
                    'required' => true,
                )
            );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Tuteur::class,
            )
        );
    }
}

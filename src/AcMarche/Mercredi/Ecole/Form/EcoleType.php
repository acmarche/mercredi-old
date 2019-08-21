<?php

namespace AcMarche\Mercredi\Ecole\Form;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        $builder
            ->add(
                'adresse',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'code_postal',
                IntegerType::class,
                array(
                    'required' => false,
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
                'email',
                EmailType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'telephone',
                TextType::class,
                array(
                    'required' => false,
                )
            )->add(
                'gsm',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 8),
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
                'data_class' => Ecole::class,
            )
        );
    }
}

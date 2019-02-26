<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Reduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReductionType extends AbstractType
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
                'pourcentage',
                PercentType::class,
                array(
                    'label' => 'Pourcentage',
                    'required' => true,
                    'type'=>'integer',
                    'help' => 'Uniquement les chiffres',
                )
            )
            ->add(
                'remarques',
                TextareaType::class,
                array(
                    'required' => false,
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
                'data_class' => Reduction::class,
            )
        );
    }
}

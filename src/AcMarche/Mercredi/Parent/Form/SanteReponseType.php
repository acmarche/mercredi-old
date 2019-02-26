<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SanteReponseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array_flip(['Non', 'Oui']);
        $builder
            ->add(
                'reponse',
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'placeholder' => false,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'remarque',
                TextType::class,
                [
                    'required' => false,
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
                'data_class' => SanteQuestion::class,
            )
        );
    }
}

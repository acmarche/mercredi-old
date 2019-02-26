<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPresenceByMonthType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $quois = array('Mercredi et Plaines' => 1, 'Mercredi' => 2, 'Plaines' => 3);

        $builder
            ->add(
                'mois',
                TextType::class,
                array(
                    'attr' => array('placeholder' => 'mm/yyyy'),
                )
            )
            ->add(
                'quoi',
                ChoiceType::class,
                array(
                    'required' => true,
                    'attr' => array('class' => 'sr-only'),
                    'choices' => $quois,
                )
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Rechercher',
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}

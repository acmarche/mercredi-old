<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchTuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Rechercher',
                ]
            )
            ->add(
                'raz',
                SubmitType::class,
                [
                    'label' => 'raz',
                    'attr' => [
                        'class' => 'btn-sm btn-success',
                        'title' => 'Search raz',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}

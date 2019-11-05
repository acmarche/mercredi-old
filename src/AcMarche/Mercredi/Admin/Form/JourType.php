<?php

namespace AcMarche\Mercredi\Admin\Form;

use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $colors = EnfanceData::getColors();

        $builder
            ->add(
                'date_jour',
                DateType::class,
                [
                    'label' => 'Date du jour de garde',
                    'widget' => 'single_text',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                    'attr' => ['class' => 'datepicker', 'placeholder' => '00-00-000', 'autocomplete' => 'off'],
                ]
            )
            ->add(
                'prix1',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 1er enfant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 2iem enfant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix3',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix des suivants',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'color',
                ChoiceType::class,
                [
                    'required' => false,
                    'label' => 'Couleur',
                    'choices' => $colors,
                    'placeholder' => 'Choisissez une couleur',
                ]
            )
            ->add(
                'archive',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Archiver',
                    'help' => 'En archivant la date ne sera plus proposée lors de l\'ajout d\'une présence',
                ]
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Remarques',
                    'help' => 'Cette donnée est visible par les parents et dans le listing des présences',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}

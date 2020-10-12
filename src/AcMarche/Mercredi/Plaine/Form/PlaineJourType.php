<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Form\Type\PlaineSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineJourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date_jour',
                DateType::class,
                [
                    'required' => false,
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'label' => 'Date',
                    'attr' => ['class' => 'datepicker', 'autocomplete' => 'off'],
                ]
            )
            ->add('plaine', PlaineSelectorType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PlaineJour::class,
            ]
        );
    }
}

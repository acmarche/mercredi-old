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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date_jour',
                DateType::class,
                array(
                    'required' => false,
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Date',
                    'attr' => array('class' => 'datepicker', 'autocomplete' => 'off'),
                )
            )
            ->add('plaine', PlaineSelectorType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => PlaineJour::class
            )
        );
    }
}

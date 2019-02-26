<?php

namespace AcMarche\Mercredi\Admin\Form\Tuteur;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Plaine\Form\Type\EnfantSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurSetEnfantType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('enfant', EnfantSelectorType::class);

        $builder->add('autocompletion', TextType::class, array(
            'mapped' => false,
            'label' => ' ',
            'required' => true,
            'attr' => array(
                'placeholder' => 'Nom')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EnfantTuteur::class
        ));
    }
}

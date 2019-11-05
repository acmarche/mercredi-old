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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('enfant', EnfantSelectorType::class);

        $builder->add('autocompletion', TextType::class, [
            'mapped' => false,
            'label' => ' ',
            'required' => true,
            'attr' => [
                'placeholder' => 'Nom', ], ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EnfantTuteur::class,
        ]);
    }
}

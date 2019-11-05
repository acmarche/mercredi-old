<?php

namespace AcMarche\Mercredi\Admin\Form\Quick;

use AcMarche\Mercredi\Admin\Manager\QuickManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'tuteur',
                TuteurQuickType::class
            )
            ->add(
                'enfant',
                EnfantQuickType::class,
                [
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => QuickManager::class,
            ]
        );
    }
}

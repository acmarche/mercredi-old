<?php

namespace AcMarche\Mercredi\Admin\Form\Jour;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Jour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourAnimateursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'animateurs',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'Animateurs',
                    'class' => Animateur::class,
                    'expanded' => true,
                    'multiple' => true,
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

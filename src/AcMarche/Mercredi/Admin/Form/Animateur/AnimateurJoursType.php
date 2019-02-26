<?php

namespace AcMarche\Mercredi\Admin\Form\Animateur;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Jour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AcMarche\Mercredi\Admin\Repository\JourRepository;

class AnimateurJoursType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'jours',
                EntityType::class,
                array(
                    'required' => true,
                    'label' => 'Jours de garde',
                    'class' => Jour::class,
                    'query_builder' => function (JourRepository $cr) {
                        return $cr->getForAnimateur(null);
                    },
                    'expanded' => true,
                    'multiple' => true,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Animateur::class,
            )
        );
    }
}

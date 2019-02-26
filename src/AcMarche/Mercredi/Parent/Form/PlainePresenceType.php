<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Validator\Constraints\PlaineMaxByGroupeScolaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $plaine = $options['plaine'];
        $enfant = $options['enfant'];

        $builder
            ->add(
                'jours',
                EntityType::class,
                array(
                    'class' => PlaineJour::class,
                    'choices' => $plaine->getJours(),
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Date(s)',
                    'constraints' => array(new PlaineMaxByGroupeScolaire(array('enfant' => $enfant))),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array()
        );
        $resolver->setRequired(['plaine', 'enfant']);
        $resolver->setAllowedTypes('plaine', Plaine::class);
        $resolver->setAllowedTypes('enfant', Enfant::class);
    }
}

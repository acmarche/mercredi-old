<?php

/**
 * Pour ajouter une date a un enfant a une plaine
 */

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Form\Type\EnfantSelectorType;
use AcMarche\Mercredi\Plaine\Form\Type\PlaineSelectorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresenceJoursType extends AbstractType
{
    private $jours_plaine;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->jours_plaine = $options['jours_plaine'];
        $builder
            ->add('plaine', PlaineSelectorType::class)
            ->add('enfant', EnfantSelectorType::class)
            ->add('jours', EntityType::class, array(
                'class' => PlaineJour::class,
                'multiple' => true,
                'expanded' => true,
                'choices' => $this->jours_plaine,
                'label' => 'Jour(s)',
                'attr' => array()
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PlainePresence::class,
            'jours_plaine' => null
        ));
    }
}

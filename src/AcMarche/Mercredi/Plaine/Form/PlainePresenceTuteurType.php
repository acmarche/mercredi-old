<?php

/**
 * Pour ajouter une date a un enfant a une plaine.
 */

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresenceTuteurType extends AbstractType
{
    private $plaine_presence;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->plaine_presence = $options['plaine_presence'];
        $tuteurs = $this->plaine_presence->getTuteurs();
        $jours_enfant = $this->plaine_presence->getJours();

        $builder
            ->add('plaine', 'plaine_selector')
            ->add('enfant', 'enfant_selector')
            ->add('tuteur', EntityType::class, [
                'class' => Tuteur::class,
                'multiple' => false,
                'choices' => $tuteurs,
                'label' => 'Tuteur',
            ])
            ->add('jours', EntityType::class, [
                'class' => PlaineJour::class,
                'multiple' => true,
                'choices' => $jours_enfant,
                'label' => 'Jour(s) attribués',
                'attr' => [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlainePresence::class,
            'plaine_presence' => null,
        ]);
    }
}

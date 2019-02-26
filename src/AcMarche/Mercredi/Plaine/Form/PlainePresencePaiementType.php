<?php

/**
 * Pour ajouter une date a un enfant a une plaine
 */

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Form\Type\EnfantSelectorType;
use AcMarche\Mercredi\Plaine\Form\Type\TuteurSelectorType;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresencePaiementType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $plaineEnfant = $options['plaine_enfant'];

        $builder
            ->add('tuteur', TuteurSelectorType::class)
            ->add('enfant', EnfantSelectorType::class)
            ->add(
                'plaine_presences',
                EntityType::class,
                array(
                    'class' => PlainePresence::class,
                    'query_builder' => function (PlainePresenceRepository $cr) use ($plaineEnfant) {
                        return $cr->getPresencesNonPayes2(
                            array(
                                'plaine_enfant' => $plaineEnfant,
                            )
                        );
                    },
                    'label' => 'Jours de présences',
                    'expanded' => true,
                    'multiple' => true,
                )
            )
            ->add('submit', SubmitType::class, array('label' => 'Valider le paiement'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Paiement::class,
                'plaine_enfant' => null,
            )
        );
    }
}

<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Form\Type\PlaineSelectorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresenceType extends AbstractType
{
    private $plaine_presence;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->plaine_presence = $options['plaine_presence'];

        /**
         * @var Plaine
         */
        $plaine = $this->plaine_presence->getPlaine();

        $jours = $plaine->getJours();
        $ordres = EnfanceData::getOrdres();

        $builder
            ->add('plaine', PlaineSelectorType::class)
            ->add(
                'enfant',
                EntityType::class,
                [
                    'class' => Enfant::class,
                    'multiple' => false,
                    'query_builder' => function (EnfantRepository $repo) use ($plaine) {
                        return $repo->getListEnfantsNonInscrits($plaine);
                    },
                    'label' => 'Enfant',
                    'attr' => ['size' => 15],
                ]
            )
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => PlaineJour::class,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $jours,
                    'label' => 'Date(s)',
                    'attr' => [],
                ]
            )
            ->add(
                'ordre',
                ChoiceType::class,
                [
                    'choices' => $ordres,
                    'required' => false,
                    'label' => 'Ordre (Facultatif)',
                    'placeholder' => 'Choisir un ordre',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PlainePresence::class,
                'plaine_presence' => null,
            ]
        );
    }
}

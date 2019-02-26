<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Service\EnfanceData;
use AcMarche\Mercredi\Admin\Entity\Enfant;
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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->plaine_presence = $options['plaine_presence'];

        /**
         * @var Plaine $plaine
         */
        $plaine = $this->plaine_presence->getPlaine();

        $jours = $plaine->getJours();
        $ordres = EnfanceData::getOrdres();

        $builder
            ->add('plaine', PlaineSelectorType::class)
            ->add(
                'enfant',
                EntityType::class,
                array(
                    'class' => Enfant::class,
                    'multiple' => false,
                    'query_builder' => function (EnfantRepository $repo) use ($plaine) {
                        return $repo->getListEnfantsNonInscrits($plaine);
                    },
                    'label' => 'Enfant',
                    'attr' => ['size' => 15],
                )
            )
            ->add(
                'jours',
                EntityType::class,
                array(
                    'class' => PlaineJour::class,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $jours,
                    'label' => 'Date(s)',
                    'attr' => array(),
                )
            )
            ->add(
                'ordre',
                ChoiceType::class,
                array(
                    'choices' => $ordres,
                    'required' => false,
                    'label' => 'Ordre (Facultatif)',
                    'placeholder' => 'Choisir un ordre',
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
                'data_class' => PlainePresence::class,
                'plaine_presence' => null,
            )
        );
    }
}

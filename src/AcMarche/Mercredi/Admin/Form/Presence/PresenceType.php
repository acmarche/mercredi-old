<?php

namespace AcMarche\Mercredi\Admin\Form\Presence;

use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Form\Type\EnfantSelectorType;
use AcMarche\Mercredi\Plaine\Form\Type\TuteurSelectorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceType extends AbstractType
{
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;

    public function __construct(EnfantTuteurRepository $enfantTuteurRepository)
    {
        $this->enfantTuteurRepository = $enfantTuteurRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enfant = $builder->getData()->getEnfant();
        $tuteurs = $this->enfantTuteurRepository->getTuteursByEnfant($enfant);

        $builder
            ->add(
                'enfant',
                EnfantSelectorType::class
            )
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'multiple' => true,
                    'query_builder' => function (JourRepository $cr) use ($enfant) {
                        return $cr->getForList($enfant);
                    },
                    'label' => 'Sélectionnez une ou plusieurs dates',
                    'attr' => ['style' => 'height:150px;'],
                ]
            );

        if (count($tuteurs) > 1) {
            $builder->add(
                'tuteur',
                EntityType::class,
                [
                    'choices' => $tuteurs,
                    'placeholder' => 'Sélectionnez un tuteur',
                    'class' => Tuteur::class,
                ]
            );
        } else {
            $builder->add('tuteur', TuteurSelectorType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Presence::class,
                'tuteurs' => null,
            ]
        );
    }
}

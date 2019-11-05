<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchMessageType extends AbstractType
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;

    public function __construct(
        EcoleRepository $ecoleRepository,
        JourRepository $jourRepository,
        PlaineRepository $plaineRepository
    ) {
        $this->ecoleRepository = $ecoleRepository;
        $this->jourRepository = $jourRepository;
        $this->plaineRepository = $plaineRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jours = $this->jourRepository->getForSearch();
        $ecoles = $this->ecoleRepository->getForSearch();
        $plaines = $this->plaineRepository->getForSearch();

        $builder
            ->add(
                'jour',
                ChoiceType::class,
                [
                    'choices' => $jours,
                    'placeholder' => 'Choisissez une date',
                    'required' => false,
                ]
            )
            ->add(
                'ecole',
                ChoiceType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => ['class' => 'sr-only'],
                    'choices' => $ecoles,
                ]
            )
            ->add(
                'plaine',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisissez une plaine',
                    'choices' => $plaines,
                    'attr' => ['class' => 'sr-only'],
                    'required' => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Rechercher',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}

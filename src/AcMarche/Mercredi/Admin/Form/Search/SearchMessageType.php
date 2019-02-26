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

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jours = $this->jourRepository->getForSearch();
        $ecoles = $this->ecoleRepository->getForSearch();
        $plaines = $this->plaineRepository->getForSearch();

        $builder
            ->add(
                'jour',
                ChoiceType::class,
                array(
                    'choices' => $jours,
                    'placeholder' => 'Choisissez une date',
                    'required' => false,
                )
            )
            ->add(
                'ecole',
                ChoiceType::class,
                array(
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => array('class' => 'sr-only'),
                    'choices' => $ecoles,
                )
            )
            ->add(
                'plaine',
                ChoiceType::class,
                array(
                    'placeholder' => 'Choisissez une plaine',
                    'choices' => $plaines,
                    'attr' => array('class' => 'sr-only'),
                    'required' => false,
                )
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Rechercher',
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}

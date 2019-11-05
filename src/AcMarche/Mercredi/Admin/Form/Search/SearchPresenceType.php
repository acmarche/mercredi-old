<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Service\JourService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchPresenceType extends AbstractType
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;

    /**
     * @var JourService
     */
    private $jourService;

    public function __construct(EcoleRepository $ecoleRepository, JourService $jourService)
    {
        $this->ecoleRepository = $ecoleRepository;
        $this->jourService = $jourService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jours = array_flip($this->jourService->getAllDaysGardesAndPlaines());
        $ecoles = $this->ecoleRepository->getForSearch();

        $builder
            ->add(
                'jour',
                ChoiceType::class,
                [
                    'choices' => $jours,
                    'placeholder' => 'Choisissez une date',
                    'group_by' => function ($choiceValue, $key, $value) {
                        list($date, $jour) = explode(' ', $key);
                        $dateTime = \DateTime::createFromFormat('j-m-Y', $date);

                        return $dateTime->format('Y');
                    },
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
                'remarques',
                CheckboxType::class,
                [
                    'label' => 'Afficher les remarques',
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
}

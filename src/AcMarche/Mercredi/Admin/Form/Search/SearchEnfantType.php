<?php

namespace AcMarche\Mercredi\Admin\Form\Search;

use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEnfantType extends AbstractType
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;

    public function __construct(EcoleRepository $ecoleRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $scolaires = ScolaireService::getAnneesScolaires();
        $ecoles = $this->ecoleRepository->getForSearch();

        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            )
            ->add(
                'ecole',
                ChoiceType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez son école',
                    'choices' => $ecoles,
                    'attr' => [],
                ]
            )
            ->add(
                'annee_scolaire',
                ChoiceType::class,
                [
                    'choices' => $scolaires,
                    'required' => false,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                    'attr' => [],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Rechercher',
                ]
            )
            ->add(
                'raz',
                SubmitType::class,
                [
                    'label' => 'raz',
                    'attr' => [
                        'class' => 'btn-sm btn-success',
                        'title' => 'Search raz',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            []
        );
    }
}
